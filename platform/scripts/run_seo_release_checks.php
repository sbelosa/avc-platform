<?php

declare(strict_types=1);

use Avc\Core\Database;
use Avc\Repositories\SeoRepository;
use Avc\Services\Seo\SeoAuditService;

$rootPath = dirname(__DIR__);

spl_autoload_register(static function (string $class) use ($rootPath): void {
    $prefix = 'Avc\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $path = $rootPath . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($path)) {
        require_once $path;
    }
});

$config = require $rootPath . '/config/app.php';
$httpBase = 'http://localhost';
$maxHtmlChecks = 0;
$summaryOnly = true;

foreach (array_slice($argv, 1) as $argument) {
    if ($argument === '--full-report') {
        $summaryOnly = false;
        continue;
    }

    if ($argument === '--summary-only') {
        $summaryOnly = true;
        continue;
    }

    if (str_starts_with($argument, '--http-base=')) {
        $candidate = rtrim(trim(substr($argument, strlen('--http-base='))), '/');
        if ($candidate !== '') {
            $httpBase = $candidate;
        }
        continue;
    }

    if (str_starts_with($argument, '--max-html-checks=')) {
        $maxHtmlChecks = max(0, (int) trim(substr($argument, strlen('--max-html-checks='))));
    }
}

$connection = Database::connection($config);
if ($connection === null) {
    fwrite(STDERR, 'Database connection failed.' . PHP_EOL);
    exit(1);
}

$productionBase = rtrim((string) ($config['base_url'] ?? 'https://aloevera-centar.com'), '/');
$supportedLanguages = array_values(array_filter(array_map('trim', (array) ($config['supported_languages'] ?? ['hr']))));
$report = [
    'generated_at' => date('c'),
    'http_base' => $httpBase,
    'production_base' => $productionBase,
    'summary' => [
        'failures' => 0,
        'warnings' => 0,
        'sitemap_urls_checked' => 0,
        'html_pages_checked' => 0,
        'unique_images_checked' => 0,
    ],
    'checks' => [],
    'failures' => [],
    'warnings' => [],
];

checkDatabaseIntegrity($connection, $report);
checkSeoAudit($config, $report);
$sitemapRoutes = checkSitemaps($config, $httpBase, $productionBase, $supportedLanguages, $report);
checkHtmlPages($httpBase, $productionBase, $sitemapRoutes, $maxHtmlChecks, $report);
checkImageFiles($rootPath, $connection, $report);

$reportPath = $rootPath . '/storage/reports/seo_release_checks_' . date('Ymd_His') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}
$report['report_path'] = $reportPath;
file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$output = $summaryOnly ? [
    'generated_at' => $report['generated_at'],
    'http_base' => $report['http_base'],
    'production_base' => $report['production_base'],
    'summary' => $report['summary'],
    'failure_sample' => array_slice($report['failures'], 0, 20),
    'warning_sample' => array_slice($report['warnings'], 0, 20),
    'report_path' => $reportPath,
] : $report;

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
exit(((int) $report['summary']['failures']) > 0 ? 1 : 0);

function checkDatabaseIntegrity(mysqli $connection, array &$report): void
{
    $duplicateRoutes = (int) fetchScalar($connection, 'SELECT COUNT(*) FROM (SELECT route_path FROM content_routes GROUP BY route_path HAVING COUNT(*) > 1) duplicates');
    addCheck($report, 'database.duplicate_routes', $duplicateRoutes === 0, ['duplicate_route_groups' => $duplicateRoutes]);

    $duplicateCanonicals = (int) fetchScalar(
        $connection,
        "SELECT COUNT(*)
         FROM (
            SELECT canonical_url
            FROM seo_metadata
            WHERE canonical_url IS NOT NULL
              AND canonical_url != ''
              AND robots_index = 1
            GROUP BY canonical_url
            HAVING COUNT(*) > 1
         ) duplicates"
    );
    addCheck($report, 'database.duplicate_indexable_canonicals', $duplicateCanonicals === 0, ['duplicate_canonical_groups' => $duplicateCanonicals]);

    $routeErrors = (int) fetchScalar(
        $connection,
        "SELECT COUNT(*)
         FROM content_routes cr
         INNER JOIN content_translations ct ON ct.content_translation_id = cr.content_translation_id
         INNER JOIN content_items ci ON ci.content_item_id = ct.content_item_id
         WHERE cr.is_primary = 1
           AND ci.lifecycle_status = 'published'
           AND cr.http_status_code != 200"
    );
    addCheck($report, 'database.published_primary_routes_are_200', $routeErrors === 0, ['route_errors' => $routeErrors]);

    $coverageRows = fetchRows(
        $connection,
        "SELECT ci.content_type, ct.language_code, COUNT(*) AS total
         FROM content_items ci
         INNER JOIN content_translations ct ON ct.content_item_id = ci.content_item_id
         INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
         WHERE ci.lifecycle_status = 'published'
           AND cr.http_status_code = 200
           AND ci.content_type IN ('article', 'product_guide')
         GROUP BY ci.content_type, ct.language_code"
    );
    $coverage = [];
    foreach ($coverageRows as $row) {
        $coverage[(string) $row['content_type']][(string) $row['language_code']] = (int) $row['total'];
    }

    $coverageOk = true;
    foreach (['article', 'product_guide'] as $contentType) {
        $counts = array_values((array) ($coverage[$contentType] ?? []));
        $coverageOk = $coverageOk && count(array_unique($counts)) === 1 && count($counts) >= 3;
    }
    addCheck($report, 'database.language_coverage_articles_and_products', $coverageOk, ['coverage' => $coverage]);
}

function checkSeoAudit(array $config, array &$report): void
{
    $repository = new SeoRepository($config);
    $audit = new SeoAuditService();
    $rows = array_values(array_filter(
        $repository->listAuditRows(null, null, '', 50000),
        static fn (array $row): bool => (int) ($row['robots_index'] ?? 1) === 1
    ));
    $decorated = $audit->decorateRows($rows);
    $summary = $audit->summarize($decorated);
    $problemRoutes = [];

    foreach ($decorated as $row) {
        if ((int) ($row['issue_count'] ?? 0) <= 0) {
            continue;
        }

        $problemRoutes[] = [
            'route_path' => (string) ($row['route_path'] ?? ''),
            'language_code' => (string) ($row['language_code'] ?? ''),
            'content_type' => (string) ($row['content_type'] ?? ''),
            'seo_score' => (int) ($row['seo_score'] ?? 0),
            'issues' => array_map(static fn (array $issue): string => (string) ($issue['code'] ?? ''), (array) ($row['issues'] ?? [])),
        ];
    }

    addCheck($report, 'seo.audit_indexable_rows', ((int) ($summary['needs_attention_total'] ?? 0)) === 0, [
        'summary' => $summary,
        'problem_routes' => array_slice($problemRoutes, 0, 50),
    ]);
}

function checkSitemaps(array $config, string $httpBase, string $productionBase, array $supportedLanguages, array &$report): array
{
    $sitemapRoutes = [];
    $indexXml = fetchUrl($httpBase . '/sitemap.xml');
    addCheck($report, 'sitemap.index_http', $indexXml['status'] === 200 && $indexXml['body'] !== '', ['status' => $indexXml['status']]);

    if ($indexXml['body'] !== '') {
        addCheck($report, 'sitemap.index_xml_valid', loadXml($indexXml['body']) !== null);
    }

    $repository = new SeoRepository($config);
    foreach ($supportedLanguages as $languageCode) {
        $entries = $repository->listSitemapEntries((string) $languageCode, 50000);
        $expectedLocs = [];
        foreach ($entries as $entry) {
            $expectedLocs[$productionBase . (string) ($entry['route_path'] ?? '/')] = true;
        }

        $path = '/sitemaps/' . rawurlencode((string) $languageCode) . '.xml';
        $response = fetchUrl($httpBase . $path);
        addCheck($report, 'sitemap.' . $languageCode . '.http', $response['status'] === 200 && $response['body'] !== '', ['status' => $response['status']]);

        $dom = loadXml($response['body']);
        addCheck($report, 'sitemap.' . $languageCode . '.xml_valid', $dom !== null);
        if ($dom === null) {
            continue;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xpath->registerNamespace('xhtml', 'http://www.w3.org/1999/xhtml');
        $locNodes = $xpath->query('//sm:url/sm:loc');
        $alternateNodes = $xpath->query('//xhtml:link[@rel="alternate"]');
        $actualLocs = [];

        foreach ($locNodes ?: [] as $node) {
            $loc = trim((string) $node->textContent);
            if ($loc !== '') {
                $actualLocs[$loc] = true;
                $path = parse_url($loc, PHP_URL_PATH);
                if (is_string($path) && $path !== '') {
                    $sitemapRoutes[$path] = [
                        'loc' => $loc,
                        'language_code' => (string) $languageCode,
                    ];
                }
            }
        }

        $missingLocs = array_values(array_diff_key($expectedLocs, $actualLocs));
        $unexpectedLocs = array_values(array_diff_key($actualLocs, $expectedLocs));
        $alternateCount = $alternateNodes instanceof DOMNodeList ? $alternateNodes->length : 0;
        $urlCount = count($actualLocs);
        $report['summary']['sitemap_urls_checked'] += $urlCount;

        addCheck($report, 'sitemap.' . $languageCode . '.expected_urls', $missingLocs === [] && $unexpectedLocs === [], [
            'expected' => count($expectedLocs),
            'actual' => $urlCount,
            'missing_sample' => array_slice($missingLocs, 0, 20),
            'unexpected_sample' => array_slice($unexpectedLocs, 0, 20),
        ]);
        addCheck($report, 'sitemap.' . $languageCode . '.has_hreflang_links', $alternateCount >= $urlCount, [
            'url_count' => $urlCount,
            'alternate_count' => $alternateCount,
        ]);
    }

    return $sitemapRoutes;
}

function checkHtmlPages(string $httpBase, string $productionBase, array $sitemapRoutes, int $maxHtmlChecks, array &$report): void
{
    ksort($sitemapRoutes);
    $checked = 0;
    $uniqueImages = [];

    foreach ($sitemapRoutes as $routePath => $sitemapInfo) {
        if ($maxHtmlChecks > 0 && $checked >= $maxHtmlChecks) {
            break;
        }

        $response = fetchUrl($httpBase . $routePath);
        $checked++;

        if ($response['status'] !== 200 || $response['body'] === '') {
            addCheck($report, 'html.page_http.' . checkKey($routePath), false, ['route_path' => $routePath, 'status' => $response['status']]);
            continue;
        }

        $html = $response['body'];
        $dom = loadHtml($html);
        if ($dom === null) {
            addCheck($report, 'html.parse.' . checkKey($routePath), false, ['route_path' => $routePath]);
            continue;
        }

        $xpath = new DOMXPath($dom);
        $title = trim((string) (($xpath->query('//title')->item(0)?->textContent) ?? ''));
        $description = trim((string) (($xpath->query('//meta[translate(@name, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="description"]/@content')->item(0)?->textContent) ?? ''));
        $canonical = trim((string) (($xpath->query('//link[translate(@rel, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="canonical"]/@href')->item(0)?->textContent) ?? ''));
        $robots = trim((string) (($xpath->query('//meta[translate(@name, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="robots"]/@content')->item(0)?->textContent) ?? ''));
        $h1Count = $xpath->query('//h1')->length;
        $schemaScripts = $xpath->query('//script[@type="application/ld+json"]');
        $languageLiteral = xpathLiteral((string) $sitemapInfo['language_code']);
        $hreflangSelf = $xpath->query('//link[translate(@rel, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="alternate" and @hreflang=' . $languageLiteral . ']')->length;
        $hreflangDefault = $xpath->query('//link[translate(@rel, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="alternate" and @hreflang="x-default"]')->length;
        $expectedCanonical = $productionBase . $routePath;
        $schemaValid = $schemaScripts->length > 0;

        foreach ($schemaScripts ?: [] as $script) {
            json_decode(trim((string) $script->textContent), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $schemaValid = false;
                break;
            }
        }

        foreach ($xpath->query('//img/@src') ?: [] as $imageNode) {
            $src = trim((string) $imageNode->textContent);
            if ($src === '' || str_starts_with($src, 'data:')) {
                continue;
            }

            $path = parse_url($src, PHP_URL_PATH);
            if (is_string($path) && $path !== '') {
                $uniqueImages[$path] = true;
            }
        }

        $failures = [];
        if ($title === '') {
            $failures[] = 'missing_title';
        }
        if (mb_strlen($description) < 80) {
            $failures[] = 'description_too_short';
        }
        if ($canonical !== $expectedCanonical) {
            $failures[] = 'canonical_mismatch';
        }
        if (!str_contains(strtolower($robots), 'index') || str_contains(strtolower($robots), 'noindex')) {
            $failures[] = 'robots_not_index';
        }
        if ($h1Count < 1) {
            $failures[] = 'missing_h1';
        }
        if (!$schemaValid) {
            $failures[] = 'invalid_schema_json';
        }
        if ($hreflangSelf < 1) {
            $failures[] = 'missing_self_hreflang';
        }
        if ($hreflangDefault < 1) {
            $failures[] = 'missing_x_default_hreflang';
        }
        if (str_contains($html, 'fusion_builder_') || str_contains($html, '%%title%%')) {
            $failures[] = 'legacy_shortcode_or_placeholder_visible';
        }

        if ($failures !== []) {
            addCheck($report, 'html.page_seo.' . checkKey($routePath), false, [
                'route_path' => $routePath,
                'failures' => $failures,
                'title' => $title,
                'description_length' => mb_strlen($description),
                'canonical' => $canonical,
                'expected_canonical' => $expectedCanonical,
                'robots' => $robots,
                'h1_count' => $h1Count,
                'schema_script_count' => $schemaScripts->length,
                'self_hreflang_count' => $hreflangSelf,
                'x_default_count' => $hreflangDefault,
            ]);
        }
    }

    $missingImages = [];
    foreach (array_keys($uniqueImages) as $imagePath) {
        if (!is_file('/var/www/html/public' . $imagePath)) {
            $missingImages[] = $imagePath;
        }
    }

    $report['summary']['html_pages_checked'] = $checked;
    $report['summary']['unique_images_checked'] = count($uniqueImages);
    addCheck($report, 'html.images_referenced_by_pages_exist', $missingImages === [], [
        'unique_images_checked' => count($uniqueImages),
        'missing_sample' => array_slice($missingImages, 0, 50),
        'missing_total' => count($missingImages),
    ]);
}

function checkImageFiles(string $rootPath, mysqli $connection, array &$report): void
{
    $rows = fetchRows(
        $connection,
        "SELECT DISTINCT featured_image_path
         FROM content_translations
         WHERE featured_image_path IS NOT NULL
           AND featured_image_path != ''"
    );
    $missing = [];

    foreach ($rows as $row) {
        $raw = (string) ($row['featured_image_path'] ?? '');
        $path = parse_url($raw, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = $raw;
        }

        if (!is_file($rootPath . '/public' . $path)) {
            $missing[] = $raw;
        }
    }

    addCheck($report, 'images.featured_images_exist', $missing === [], [
        'total_featured_images' => count($rows),
        'missing_total' => count($missing),
        'missing_sample' => array_slice($missing, 0, 50),
    ]);
}

function fetchUrl(string $url): array
{
    $status = 0;
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true,
            'header' => "User-Agent: AVC-SEO-Release-Checks/1.0\r\n",
        ],
    ]);
    $body = @file_get_contents($url, false, $context);

    foreach (($http_response_header ?? []) as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d+)/', $header, $matches) === 1) {
            $status = (int) $matches[1];
            break;
        }
    }

    return [
        'status' => $status,
        'body' => is_string($body) ? $body : '',
    ];
}

function addCheck(array &$report, string $name, bool $ok, array $details = []): void
{
    $entry = [
        'name' => $name,
        'ok' => $ok,
        'details' => $details,
    ];
    $report['checks'][] = $entry;

    if (!$ok) {
        $report['summary']['failures']++;
        $report['failures'][] = $entry;
    }
}

function fetchScalar(mysqli $connection, string $sql): mixed
{
    $result = $connection->query($sql);
    $row = $result ? $result->fetch_row() : null;

    return $row[0] ?? null;
}

function fetchRows(mysqli $connection, string $sql): array
{
    $result = $connection->query($sql);

    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function loadXml(string $xml): ?DOMDocument
{
    if (trim($xml) === '') {
        return null;
    }

    $dom = new DOMDocument();
    return @$dom->loadXML($xml) ? $dom : null;
}

function loadHtml(string $html): ?DOMDocument
{
    if (trim($html) === '') {
        return null;
    }

    $dom = new DOMDocument();
    return @$dom->loadHTML('<?xml encoding="UTF-8">' . $html) ? $dom : null;
}

function checkKey(string $value): string
{
    $value = trim($value, '/');
    $value = preg_replace('/[^a-z0-9_-]+/i', '_', $value) ?? 'root';

    return $value !== '' ? strtolower($value) : 'root';
}

function xpathLiteral(string $value): string
{
    if (!str_contains($value, "'")) {
        return "'" . $value . "'";
    }

    if (!str_contains($value, '"')) {
        return '"' . $value . '"';
    }

    $parts = array_map(static fn (string $part): string => "'" . $part . "'", explode("'", $value));
    return 'concat(' . implode(', "\'", ', $parts) . ')';
}
