<?php

declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$tsvPath = $argv[1] ?? '/var/www/html/exports/fcc/product_market_links.tsv';

if (!is_file($tsvPath)) {
    fwrite(STDERR, "Missing TSV file: {$tsvPath}" . PHP_EOL);
    exit(1);
}

$dbHost = getenv('AVC_DB_HOST') ?: 'db';
$dbPort = (int) (getenv('AVC_DB_PORT') ?: 3306);
$dbName = getenv('AVC_DB_NAME') ?: 'avc_platform';
$dbUser = getenv('AVC_DB_USER') ?: 'avc_platform';
$dbPassword = getenv('AVC_DB_PASSWORD') ?: 'avc_platform';

$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
$mysqli->set_charset('utf8mb4');

$fccRecords = loadFccRecords($tsvPath);
$indexes = buildIndexes($fccRecords);
$avcItems = loadAvcProductItems($mysqli);

$updateStatement = $mysqli->prepare(
    'UPDATE product_recommendations
     SET market_urls_json = ?, destination_strategy = ?, source_system = ?
     WHERE product_recommendation_id = ?'
);

$matchedItems = 0;
$matchedRows = 0;
$matchStrategyStats = [
    'sku' => 0,
    'slug' => 0,
    'slug_variant' => 0,
    'title' => 0,
];
$unmatchedExamples = [];

foreach ($avcItems as $contentItemId => $item) {
    $match = findBestMatch($item, $indexes);

    if ($match === null) {
        if (count($unmatchedExamples) < 15) {
            $unmatchedExamples[] = [
                'content_item_id' => $contentItemId,
                'slugs' => array_values(array_filter(array_map(static fn(array $row): string => (string) ($row['slug'] ?? ''), $item['translations']))),
                'titles' => array_values(array_filter(array_map(static fn(array $row): string => (string) ($row['title'] ?? ''), $item['translations']))),
            ];
        }

        continue;
    }

    $marketUrlsJson = json_encode($match['record']['market_urls'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $destinationStrategy = inferDestinationStrategy($match['record']['market_urls'], (string) ($item['translations'][0]['external_url'] ?? ''));
    $sourceSystem = 'wordpress+fcc_market_sync:' . $match['strategy'];

    foreach ($item['translations'] as $translation) {
        $productRecommendationId = (int) ($translation['product_recommendation_id'] ?? 0);

        if ($productRecommendationId <= 0) {
            continue;
        }

        $updateStatement->bind_param(
            'sssi',
            $marketUrlsJson,
            $destinationStrategy,
            $sourceSystem,
            $productRecommendationId
        );
        $updateStatement->execute();
        $matchedRows++;
    }

    $matchedItems++;
    $matchStrategyStats[$match['strategy']]++;
}

$updateStatement->close();

$report = [
    'status' => 'ok',
    'generated_at' => gmdate(DATE_ATOM),
    'fcc_records' => count($fccRecords),
    'avc_product_items' => count($avcItems),
    'matched_items' => $matchedItems,
    'matched_rows' => $matchedRows,
    'match_strategy_stats' => $matchStrategyStats,
    'unmatched_examples' => $unmatchedExamples,
];

$reportDirectory = '/var/www/html/storage/reports';
if (!is_dir($reportDirectory)) {
    mkdir($reportDirectory, 0775, true);
}

$reportPath = $reportDirectory . '/product_market_link_sync_report.json';
file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function loadFccRecords(string $tsvPath): array
{
    $records = [];
    $handle = fopen($tsvPath, 'rb');

    if ($handle === false) {
        throw new RuntimeException("Unable to open TSV file: {$tsvPath}");
    }

    while (($line = fgets($handle)) !== false) {
        $line = rtrim($line, "\r\n");

        if ($line === '') {
            continue;
        }

        $parts = explode("\t", $line, 5);
        if (count($parts) < 5) {
            continue;
        }

        [$slug, $languageLabel, $title, $sku, $webshopLinksBase64] = $parts;

        $decodedJson = base64_decode($webshopLinksBase64, true);
        if ($decodedJson === false) {
            continue;
        }

        $marketUrls = json_decode($decodedJson, true);
        if (!is_array($marketUrls) || $marketUrls === []) {
            continue;
        }

        $normalizedMarkets = [];

        foreach ($marketUrls as $marketCode => $marketUrl) {
            $marketCode = strtolower(trim((string) $marketCode));
            $marketUrl = trim((string) $marketUrl);

            if ($marketCode === '' || $marketUrl === '' || !filter_var($marketUrl, FILTER_VALIDATE_URL)) {
                continue;
            }

            $normalizedMarkets[$marketCode] = $marketUrl;
        }

        if ($normalizedMarkets === []) {
            continue;
        }

        $records[] = [
            'slug' => trim($slug),
            'language_code' => mapFccLanguage($languageLabel),
            'title' => trim($title),
            'sku' => trim($sku),
            'market_urls' => $normalizedMarkets,
        ];
    }

    fclose($handle);

    return $records;
}

function buildIndexes(array $fccRecords): array
{
    $indexes = [
        'by_sku' => [],
        'by_slug' => [],
        'by_slug_variant' => [],
        'by_title' => [],
    ];

    foreach ($fccRecords as $record) {
        $sku = trim((string) ($record['sku'] ?? ''));
        if ($sku !== '') {
            $indexes['by_sku'][$sku][] = $record;
        }

        $slug = trim((string) ($record['slug'] ?? ''));
        if ($slug !== '') {
            $indexes['by_slug'][$slug][] = $record;
        }

        foreach (buildSlugVariants($slug) as $variant) {
            $indexes['by_slug_variant'][$variant][] = $record;
        }

        $titleKey = normalizeTitle((string) ($record['title'] ?? ''));
        if ($titleKey !== '') {
            $indexes['by_title'][$titleKey][] = $record;
        }
    }

    return $indexes;
}

function loadAvcProductItems(mysqli $mysqli): array
{
    $result = $mysqli->query(
        "SELECT
            pr.product_recommendation_id,
            ct.content_translation_id,
            ct.content_item_id,
            ct.language_code,
            ct.slug,
            ct.title,
            pr.sku,
            pr.external_url
         FROM product_recommendations pr
         INNER JOIN content_translations ct
           ON ct.content_translation_id = pr.content_translation_id
         ORDER BY ct.content_item_id ASC, ct.language_code ASC, ct.content_translation_id ASC"
    );

    $items = [];

    while ($row = $result->fetch_assoc()) {
        $contentItemId = (int) ($row['content_item_id'] ?? 0);
        $items[$contentItemId]['content_item_id'] = $contentItemId;
        $items[$contentItemId]['translations'][] = $row;
    }

    return $items;
}

function findBestMatch(array $item, array $indexes): ?array
{
    foreach ($item['translations'] as $translation) {
        $sku = trim((string) ($translation['sku'] ?? ''));

        if ($sku !== '' && !empty($indexes['by_sku'][$sku])) {
            return [
                'strategy' => 'sku',
                'record' => choosePreferredRecord($indexes['by_sku'][$sku], $item['translations']),
            ];
        }
    }

    foreach ($item['translations'] as $translation) {
        $slug = trim((string) ($translation['slug'] ?? ''));

        if ($slug !== '' && !empty($indexes['by_slug'][$slug])) {
            return [
                'strategy' => 'slug',
                'record' => choosePreferredRecord($indexes['by_slug'][$slug], $item['translations']),
            ];
        }
    }

    foreach ($item['translations'] as $translation) {
        foreach (buildSlugVariants((string) ($translation['slug'] ?? '')) as $variant) {
            if ($variant !== '' && !empty($indexes['by_slug_variant'][$variant])) {
                return [
                    'strategy' => 'slug_variant',
                    'record' => choosePreferredRecord($indexes['by_slug_variant'][$variant], $item['translations']),
                ];
            }
        }
    }

    foreach ($item['translations'] as $translation) {
        $titleKey = normalizeTitle((string) ($translation['title'] ?? ''));

        if ($titleKey !== '' && !empty($indexes['by_title'][$titleKey])) {
            return [
                'strategy' => 'title',
                'record' => choosePreferredRecord($indexes['by_title'][$titleKey], $item['translations']),
            ];
        }
    }

    return null;
}

function choosePreferredRecord(array $records, array $translations): array
{
    $preferredLanguages = array_values(array_unique(array_filter(array_map(
        static fn(array $translation): string => strtolower(trim((string) ($translation['language_code'] ?? ''))),
        $translations
    ))));

    foreach ($preferredLanguages as $languageCode) {
        foreach ($records as $record) {
            if ((string) ($record['language_code'] ?? '') === $languageCode) {
                return $record;
            }
        }
    }

    return $records[0];
}

function buildSlugVariants(string $slug): array
{
    $slug = normalizeSlug($slug);

    if ($slug === '') {
        return [];
    }

    $variants = [$slug];

    if (str_starts_with($slug, 'forever-')) {
        $variants[] = substr($slug, strlen('forever-'));
    }

    $trimmedCopy = preg_replace('/-(copy|kopiraj|kopija)$/', '', $slug) ?? $slug;
    if ($trimmedCopy !== '') {
        $variants[] = $trimmedCopy;
    }

    $withoutForeverEverywhere = trim(str_replace('-forever-', '-', '-' . $slug . '-'), '-');
    if ($withoutForeverEverywhere !== '') {
        $variants[] = $withoutForeverEverywhere;
    }

    return array_values(array_unique(array_filter($variants)));
}

function normalizeSlug(string $slug): string
{
    $slug = strtolower(trim($slug));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? $slug;

    return trim($slug, '-');
}

function normalizeTitle(string $title): string
{
    $title = strtolower(trim($title));
    $title = str_replace(['™', '®'], ' ', $title);

    if (function_exists('iconv')) {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title);
        if ($transliterated !== false) {
            $title = $transliterated;
        }
    }

    $title = preg_replace('/\b(forever|living|products|produkti|proizvodi|dodatak prehrani|a dietary supplement)\b/u', ' ', $title) ?? $title;
    $title = preg_replace('/[^a-z0-9]+/', ' ', $title) ?? $title;
    $title = preg_replace('/\s+/', ' ', $title) ?? $title;

    return trim($title);
}

function mapFccLanguage(string $languageLabel): string
{
    $normalized = strtolower(trim($languageLabel));

    return match ($normalized) {
        'english' => 'en',
        'slovenian', 'slovenski', 'slovenščina' => 'sl',
        default => 'hr',
    };
}

function inferDestinationStrategy(array $marketUrls, string $fallbackExternalUrl): string
{
    foreach ($marketUrls as $marketUrl) {
        $host = parse_url($marketUrl, PHP_URL_HOST);
        $host = is_string($host) ? strtolower(preg_replace('/^www\./', '', $host) ?? $host) : '';

        if ($host === '') {
            continue;
        }

        if (str_contains($host, 'foreverliving') || str_contains($host, 'foreveralbania') || str_contains($host, 'flpshop')) {
            return 'forever_official';
        }
    }

    if ($fallbackExternalUrl !== '' && filter_var($fallbackExternalUrl, FILTER_VALIDATE_URL)) {
        return 'passthrough';
    }

    return 'forever_official';
}
