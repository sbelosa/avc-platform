<?php

declare(strict_types=1);

use Avc\Core\Database;
use Avc\Services\Content\StructuredContentService;

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

$arguments = array_slice($argv, 1);
$apply = in_array('--apply', $arguments, true);
$limit = (int) (optionValue($arguments, '--limit=') ?? 0);
$routeFilter = optionValue($arguments, '--route=');
$sleepMs = max(0, (int) (optionValue($arguments, '--sleep-ms=') ?? 120));
$shard = parseShard(optionValue($arguments, '--shard='));

$config = require $rootPath . '/config/app.php';
$connection = Database::connection($config);

if ($connection === null) {
    fwrite(STDERR, 'Database connection failed.' . PHP_EOL);
    exit(1);
}

$structuredContent = new StructuredContentService();
$baseUrl = rtrim((string) ($config['base_url'] ?? 'https://aloevera-centar.com'), '/');
$cacheSuffix = $shard !== null ? '_shard_' . (int) $shard['index'] . '_of_' . (int) $shard['total'] : '';
$cachePath = $rootPath . '/storage/cache/google_translate_article_gaps' . $cacheSuffix . '.json';
$cache = loadCache($cachePath);

$report = [
    'generated_at' => date('c'),
    'mode' => $apply ? 'apply' : 'dry-run',
    'provider' => 'translate.googleapis.com',
    'source_language' => 'hr',
    'target_languages' => ['en', 'sl'],
    'limit' => $limit,
    'route_filter' => $routeFilter,
    'shard' => $shard,
    'checked' => 0,
    'translated' => 0,
    'updated' => 0,
    'skipped' => 0,
    'errors' => [],
    'examples' => [],
];

$targets = fetchTargets($connection, $routeFilter, $limit, $shard);

foreach ($targets as $target) {
    $report['checked']++;
    $targetLanguage = strtolower((string) ($target['language_code'] ?? ''));
    $source = fetchSource($connection, (int) ($target['content_item_id'] ?? 0));

    if ($source === null || $targetLanguage === '' || $targetLanguage === 'hr') {
        $report['skipped']++;
        $report['errors'][] = [
            'content_translation_id' => (int) ($target['content_translation_id'] ?? 0),
            'reason' => 'missing_hr_source',
        ];
        continue;
    }

    try {
        $payload = buildTranslatedPayload(
            $connection,
            $source,
            $target,
            $targetLanguage,
            $baseUrl,
            $structuredContent,
            $cache,
            $sleepMs
        );

        $report['translated']++;
        rememberExample($report['examples'], [
            'content_translation_id' => (int) ($target['content_translation_id'] ?? 0),
            'language_code' => $targetLanguage,
            'route_path' => (string) ($target['route_path'] ?? ''),
            'title_before' => (string) ($target['title'] ?? ''),
            'title_after' => $payload['title'],
            'source_text_len' => textLength((string) ($source['body_html'] ?? '')),
            'translated_text_len' => textLength($payload['body_html']),
        ]);

        if ($apply) {
            $connection->begin_transaction();
            saveRevision($connection, $target, 'machine_translation_from_hr');
            updateTranslation($connection, (int) ($target['content_translation_id'] ?? 0), $payload);
            upsertSeo($connection, (int) ($target['content_translation_id'] ?? 0), $payload);
            $connection->commit();
            $report['updated']++;
            saveCache($cachePath, $cache);
        }
    } catch (Throwable $throwable) {
        if ($apply) {
            $connection->rollback();
        }

        $report['errors'][] = [
            'content_translation_id' => (int) ($target['content_translation_id'] ?? 0),
            'language_code' => $targetLanguage,
            'route_path' => (string) ($target['route_path'] ?? ''),
            'message' => $throwable->getMessage(),
        ];
    }
}

saveCache($cachePath, $cache);

$reportPath = $rootPath . '/storage/reports/machine_translation_article_gaps_' . date('Ymd_His') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}

file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report + ['report_path' => $reportPath, 'cache_path' => $cachePath], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function optionValue(array $arguments, string $prefix): ?string
{
    foreach ($arguments as $argument) {
        if (str_starts_with($argument, $prefix)) {
            return substr($argument, strlen($prefix));
        }
    }

    return null;
}

function parseShard(?string $value): ?array
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    if (preg_match('/^(\d+)\/(\d+)$/', $value, $matches) !== 1) {
        throw new InvalidArgumentException('Shard must use index/total format, for example --shard=0/4.');
    }

    $index = (int) $matches[1];
    $total = (int) $matches[2];
    if ($total <= 1 || $index < 0 || $index >= $total) {
        throw new InvalidArgumentException('Shard index must be lower than total.');
    }

    return ['index' => $index, 'total' => $total];
}

function fetchTargets(mysqli $connection, ?string $routeFilter, int $limit, ?array $shard): array
{
    $sql = "SELECT
            ci.content_item_id,
            ct.content_translation_id,
            ct.language_code,
            ct.title,
            ct.slug,
            ct.excerpt,
            ct.body_html,
            ct.summary_html,
            ct.faq_json,
            ct.featured_image_path,
            ct.published_at,
            cr.route_path,
            sm.meta_title,
            sm.meta_description,
            sm.canonical_url,
            sm.robots_index,
            sm.robots_follow,
            sm.breadcrumb_title
         FROM content_items ci
         INNER JOIN content_translations ct ON ct.content_item_id = ci.content_item_id
         INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
         LEFT JOIN seo_metadata sm ON sm.content_translation_id = ct.content_translation_id
         WHERE ci.content_type = 'article'
           AND ci.lifecycle_status = 'published'
           AND ct.language_code IN ('en', 'sl')
           AND (ct.source_wp_post_id IS NULL OR ct.source_wp_post_id = 0)
           AND (
                ct.body_html LIKE '%data-avc-seo-hardening%'
             OR ct.body_html LIKE '%This guide looks at%'
             OR ct.body_html LIKE '%Ta vodnik obravnava temo%'
             OR ct.title LIKE 'Practical Guide:%'
             OR ct.title LIKE 'Praktični vodnik:%'
             OR CHAR_LENGTH(REGEXP_REPLACE(COALESCE(ct.body_html, ''), '<[^>]*>', ' ')) < 2500
           )";

    if ($shard !== null) {
        $total = (int) ($shard['total'] ?? 0);
        $index = (int) ($shard['index'] ?? 0);
        $sql .= ' AND MOD(ct.content_translation_id, ' . $total . ') = ' . $index;
    }

    if ($routeFilter !== null && trim($routeFilter) !== '') {
        $sql .= ' AND cr.route_path = ?';
        $statement = $connection->prepare($sql . " ORDER BY FIELD(ct.language_code, 'en', 'sl'), ci.content_item_id");
        $statement->bind_param('s', $routeFilter);
        $statement->execute();
        $rows = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $statement->close();

        return $rows;
    }

    $sql .= " ORDER BY FIELD(ct.language_code, 'en', 'sl'), ci.content_item_id";
    if ($limit > 0) {
        $sql .= ' LIMIT ' . $limit;
    }

    $result = $connection->query($sql);

    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function fetchSource(mysqli $connection, int $contentItemId): ?array
{
    $statement = $connection->prepare(
        "SELECT
            ct.content_translation_id,
            ct.content_item_id,
            ct.language_code,
            ct.title,
            ct.slug,
            ct.excerpt,
            ct.body_html,
            ct.summary_html,
            ct.faq_json,
            ct.featured_image_path,
            ct.published_at,
            cr.route_path
         FROM content_translations ct
         INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
         WHERE ct.content_item_id = ?
           AND ct.language_code = 'hr'
         LIMIT 1"
    );
    $statement->bind_param('i', $contentItemId);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();

    return $row ?: null;
}

function buildTranslatedPayload(
    mysqli $connection,
    array $source,
    array $target,
    string $targetLanguage,
    string $baseUrl,
    StructuredContentService $structuredContent,
    array &$cache,
    int $sleepMs
): array {
    $title = translateText((string) ($source['title'] ?? ''), 'hr', $targetLanguage, $cache, $sleepMs);
    $bodyHtml = translateHtmlFragment((string) ($source['body_html'] ?? ''), 'hr', $targetLanguage, $connection, (int) ($source['content_item_id'] ?? 0), $cache, $sleepMs);
    $plainText = articlePlainText($bodyHtml);
    $excerpt = buildExcerpt($plainText, $targetLanguage, $title);
    $summaryHtml = buildSummaryHtml($targetLanguage, $plainText);
    $faqJson = $structuredContent->encodeFaqItems(buildFaqItems($targetLanguage, $title));
    $routePath = normalizeRoutePath((string) ($target['route_path'] ?? ''));

    return [
        'title' => $title,
        'slug' => (string) ($target['slug'] ?? ''),
        'excerpt' => $excerpt,
        'body_html' => $bodyHtml,
        'summary_html' => $summaryHtml,
        'faq_json' => $faqJson,
        'featured_image_path' => (string) (($target['featured_image_path'] ?? '') ?: ($source['featured_image_path'] ?? '')),
        'route_path' => $routePath,
        'meta_title' => truncateText($title, 68),
        'meta_description' => buildMetaDescription($excerpt, $plainText, $targetLanguage, $title),
        'canonical_url' => $baseUrl . $routePath,
        'robots_index' => 1,
        'robots_follow' => 1,
        'breadcrumb_title' => truncateText($title, 80),
    ];
}

function translateHtmlFragment(
    string $html,
    string $sourceLanguage,
    string $targetLanguage,
    mysqli $connection,
    int $sourceContentItemId,
    array &$cache,
    int $sleepMs
): string {
    $document = new DOMDocument('1.0', 'UTF-8');
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="UTF-8"><div id="avc-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    $root = $document->getElementById('avc-root');
    if (!$root instanceof DOMElement) {
        return $html;
    }

    $segments = [];
    collectTextSegments($root, $segments);
    translateSegments($segments, $sourceLanguage, $targetLanguage, $cache, $sleepMs);
    localizeLinks($document, $connection, $targetLanguage, $sourceContentItemId);

    $output = '';
    foreach ($root->childNodes as $child) {
        $output .= (string) $document->saveHTML($child);
    }

    $output = preg_replace('/>\s+</u', ">\n<", $output) ?? $output;

    return trim($output);
}

function collectTextSegments(DOMNode $node, array &$segments): void
{
    if ($node instanceof DOMElement) {
        $tagName = strtolower($node->tagName);
        if (in_array($tagName, ['script', 'style', 'code', 'pre', 'textarea', 'noscript'], true)) {
            return;
        }
    }

    foreach (iterator_to_array($node->childNodes) as $child) {
        if ($child instanceof DOMText) {
            $value = (string) $child->nodeValue;
            if (trim($value) === '' || !preg_match('/[\p{L}\p{N}]/u', $value)) {
                continue;
            }

            $segments[] = [
                'node' => $child,
                'text' => $value,
            ];
            continue;
        }

        collectTextSegments($child, $segments);
    }
}

function translateSegments(array $segments, string $sourceLanguage, string $targetLanguage, array &$cache, int $sleepMs): void
{
    $chunk = [];
    $chunkLength = 0;

    foreach ($segments as $index => $segment) {
        $text = (string) ($segment['text'] ?? '');
        $trimmed = trim($text);
        if ($trimmed === '') {
            continue;
        }

        $length = mb_strlen($trimmed);
        if ($chunk !== [] && $chunkLength + $length > 4600) {
            translateSegmentChunk($chunk, $sourceLanguage, $targetLanguage, $cache, $sleepMs);
            $chunk = [];
            $chunkLength = 0;
        }

        $chunk[] = ['index' => $index, 'segment' => $segment];
        $chunkLength += $length + 30;
    }

    if ($chunk !== []) {
        translateSegmentChunk($chunk, $sourceLanguage, $targetLanguage, $cache, $sleepMs);
    }
}

function translateSegmentChunk(array $chunk, string $sourceLanguage, string $targetLanguage, array &$cache, int $sleepMs): void
{
    $parts = [];
    foreach ($chunk as $position => $item) {
        $text = trim((string) ($item['segment']['text'] ?? ''));
        if ($position > 0) {
            $parts[] = '###AVC_SEGMENT_' . str_pad((string) $position, 6, '0', STR_PAD_LEFT) . '###';
        }
        $parts[] = $text;
    }

    $translated = translateText(implode("\n", $parts), $sourceLanguage, $targetLanguage, $cache, $sleepMs);
    $split = preg_split('/###\s*AVC_SEGMENT_\d{6}\s*###/u', $translated) ?: [];

    if (count($split) !== count($chunk)) {
        $split = [];
        foreach ($chunk as $item) {
            $split[] = translateText(trim((string) ($item['segment']['text'] ?? '')), $sourceLanguage, $targetLanguage, $cache, $sleepMs);
        }
    }

    foreach ($chunk as $position => $item) {
        $node = $item['segment']['node'] ?? null;
        if (!$node instanceof DOMText) {
            continue;
        }

        $original = (string) $node->nodeValue;
        preg_match('/^\s*/u', $original, $leading);
        preg_match('/\s*$/u', $original, $trailing);
        $node->nodeValue = ($leading[0] ?? '') . trim((string) ($split[$position] ?? '')) . ($trailing[0] ?? '');
    }
}

function translateText(string $text, string $sourceLanguage, string $targetLanguage, array &$cache, int $sleepMs): string
{
    $text = trim($text);
    if ($text === '' || $sourceLanguage === $targetLanguage) {
        return $text;
    }

    if (mb_strlen($text) > 4800) {
        $chunks = splitTextForTranslation($text, 4400);
        $translatedChunks = [];
        foreach ($chunks as $chunk) {
            $translatedChunks[] = translateText($chunk, $sourceLanguage, $targetLanguage, $cache, $sleepMs);
        }

        return trim(implode(' ', $translatedChunks));
    }

    $cacheKey = sha1($sourceLanguage . '|' . $targetLanguage . '|' . $text);
    if (isset($cache[$cacheKey])) {
        return (string) $cache[$cacheKey];
    }

    $query = http_build_query([
        'client' => 'gtx',
        'sl' => $sourceLanguage,
        'tl' => $targetLanguage,
        'dt' => 't',
        'q' => $text,
    ]);
    $url = 'https://translate.googleapis.com/translate_a/single?' . $query;
    $lastError = '';

    for ($attempt = 1; $attempt <= 4; $attempt++) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_USERAGENT => 'AVC-Local-Translation/1.0',
        ]);
        $body = curl_exec($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if (is_string($body) && $httpCode >= 200 && $httpCode < 300) {
            $decoded = json_decode($body, true);
            $translated = '';
            foreach ((array) ($decoded[0] ?? []) as $part) {
                if (is_array($part) && isset($part[0])) {
                    $translated .= (string) $part[0];
                }
            }

            $translated = trim($translated);
            if ($translated !== '') {
                $cache[$cacheKey] = $translated;
                if ($sleepMs > 0) {
                    usleep($sleepMs * 1000);
                }

                return $translated;
            }
        }

        $lastError = $error !== '' ? $error : ('HTTP ' . $httpCode);
        usleep((250 * $attempt + $sleepMs) * 1000);
    }

    throw new RuntimeException('Translation failed: ' . $lastError);
}

function splitTextForTranslation(string $text, int $maxLength): array
{
    $sentences = preg_split('/(?<=[.!?])\s+/u', $text) ?: [$text];
    $chunks = [];
    $current = '';

    foreach ($sentences as $sentence) {
        $sentence = trim($sentence);
        if ($sentence === '') {
            continue;
        }

        if ($current !== '' && mb_strlen($current . ' ' . $sentence) > $maxLength) {
            $chunks[] = $current;
            $current = '';
        }

        if (mb_strlen($sentence) > $maxLength) {
            $chunks[] = mb_substr($sentence, 0, $maxLength);
            $remaining = trim(mb_substr($sentence, $maxLength));
            if ($remaining !== '') {
                $current = $remaining;
            }
            continue;
        }

        $current = trim($current . ' ' . $sentence);
    }

    if ($current !== '') {
        $chunks[] = $current;
    }

    return $chunks !== [] ? $chunks : [$text];
}

function localizeLinks(DOMDocument $document, mysqli $connection, string $targetLanguage, int $sourceContentItemId): void
{
    static $cache = [];
    $links = $document->getElementsByTagName('a');

    foreach ($links as $link) {
        if (!$link instanceof DOMElement) {
            continue;
        }

        $href = trim($link->getAttribute('href'));
        if ($href === '' || preg_match('/^(?:mailto:|tel:|#)/i', $href)) {
            continue;
        }

        $localized = localizeHref($connection, $href, $targetLanguage, $sourceContentItemId, $cache);
        if ($localized !== '') {
            $link->setAttribute('href', $localized);
        }
    }
}

function localizeHref(mysqli $connection, string $href, string $targetLanguage, int $sourceContentItemId, array &$cache): string
{
    $parts = parse_url($href);
    $host = strtolower((string) ($parts['host'] ?? ''));
    if ($host !== '' && !in_array($host, ['aloevera-centar.com', 'www.aloevera-centar.com', 'localhost'], true)) {
        return $href;
    }

    $path = (string) ($parts['path'] ?? '');
    if ($path === '') {
        return $href;
    }

    $normalizedPath = normalizeRoutePath($path);
    $cacheKey = $targetLanguage . '|' . $normalizedPath;
    if (!array_key_exists($cacheKey, $cache)) {
        $cache[$cacheKey] = findLocalizedRouteForPath($connection, $normalizedPath, $targetLanguage, $sourceContentItemId);
    }

    $localizedPath = (string) $cache[$cacheKey];
    if ($localizedPath === '') {
        return $href;
    }

    $suffix = '';
    if (isset($parts['query']) && $parts['query'] !== '') {
        $suffix .= '?' . $parts['query'];
    }
    if (isset($parts['fragment']) && $parts['fragment'] !== '') {
        $suffix .= '#' . $parts['fragment'];
    }

    return $localizedPath . $suffix;
}

function findLocalizedRouteForPath(mysqli $connection, string $sourcePath, string $targetLanguage, int $sourceContentItemId): string
{
    $statement = $connection->prepare(
        'SELECT target_route.route_path
         FROM content_routes source_route
         INNER JOIN content_translations source_translation
            ON source_translation.content_translation_id = source_route.content_translation_id
         INNER JOIN content_translations target_translation
            ON target_translation.content_item_id = source_translation.content_item_id
           AND target_translation.language_code = ?
         INNER JOIN content_routes target_route
            ON target_route.content_translation_id = target_translation.content_translation_id
           AND target_route.is_primary = 1
         WHERE source_route.route_path = ?
           AND source_route.is_primary = 1
         LIMIT 1'
    );
    $statement->bind_param('ss', $targetLanguage, $sourcePath);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();

    if (is_array($row) && !empty($row['route_path'])) {
        return (string) $row['route_path'];
    }

    return $sourcePath;
}

function saveRevision(mysqli $connection, array $row, string $revisionType): void
{
    $snapshot = [
        'content_translation' => [
            'content_translation_id' => (int) ($row['content_translation_id'] ?? 0),
            'language_code' => (string) ($row['language_code'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'slug' => (string) ($row['slug'] ?? ''),
            'excerpt' => (string) ($row['excerpt'] ?? ''),
            'body_html' => (string) ($row['body_html'] ?? ''),
            'summary_html' => (string) ($row['summary_html'] ?? ''),
            'faq_json' => (string) ($row['faq_json'] ?? ''),
            'featured_image_path' => (string) ($row['featured_image_path'] ?? ''),
        ],
        'route_path' => (string) ($row['route_path'] ?? ''),
        'seo' => [
            'meta_title' => (string) ($row['meta_title'] ?? ''),
            'meta_description' => (string) ($row['meta_description'] ?? ''),
            'canonical_url' => (string) ($row['canonical_url'] ?? ''),
            'robots_index' => (int) ($row['robots_index'] ?? 1),
            'robots_follow' => (int) ($row['robots_follow'] ?? 1),
            'breadcrumb_title' => (string) ($row['breadcrumb_title'] ?? ''),
        ],
    ];
    $snapshotJson = (string) json_encode($snapshot, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $contentTranslationId = (int) ($row['content_translation_id'] ?? 0);
    $title = (string) ($row['title'] ?? '');

    $statement = $connection->prepare(
        'INSERT INTO content_revisions (
            content_translation_id,
            title,
            revision_type,
            snapshot_json,
            changed_by_admin_user_id
        ) VALUES (?, ?, ?, ?, NULL)'
    );
    $statement->bind_param('isss', $contentTranslationId, $title, $revisionType, $snapshotJson);
    $statement->execute();
    $statement->close();
}

function updateTranslation(mysqli $connection, int $contentTranslationId, array $payload): void
{
    $statement = $connection->prepare(
        'UPDATE content_translations
         SET title = ?,
             excerpt = ?,
             body_html = ?,
             summary_html = ?,
             faq_json = ?,
             featured_image_path = ?
         WHERE content_translation_id = ?'
    );
    $statement->bind_param(
        'ssssssi',
        $payload['title'],
        $payload['excerpt'],
        $payload['body_html'],
        $payload['summary_html'],
        $payload['faq_json'],
        $payload['featured_image_path'],
        $contentTranslationId
    );
    $statement->execute();
    $statement->close();
}

function upsertSeo(mysqli $connection, int $contentTranslationId, array $payload): void
{
    $statement = $connection->prepare(
        'INSERT INTO seo_metadata (
            content_translation_id,
            meta_title,
            meta_description,
            canonical_url,
            robots_index,
            robots_follow,
            breadcrumb_title
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            meta_title = VALUES(meta_title),
            meta_description = VALUES(meta_description),
            canonical_url = VALUES(canonical_url),
            robots_index = VALUES(robots_index),
            robots_follow = VALUES(robots_follow),
            breadcrumb_title = VALUES(breadcrumb_title)'
    );
    $statement->bind_param(
        'isssiis',
        $contentTranslationId,
        $payload['meta_title'],
        $payload['meta_description'],
        $payload['canonical_url'],
        $payload['robots_index'],
        $payload['robots_follow'],
        $payload['breadcrumb_title']
    );
    $statement->execute();
    $statement->close();
}

function buildSummaryHtml(string $languageCode, string $plainText): string
{
    $sentences = extractSentences($plainText, 2);
    $items = [
        $sentences[0] ?? match ($languageCode) {
            'en' => 'This article gives practical context before choosing the next step.',
            'sl' => 'Ta članek daje praktičen kontekst pred naslednjim korakom.',
            default => 'Ovaj članak daje praktičan kontekst prije sljedećeg koraka.',
        },
        $sentences[1] ?? match ($languageCode) {
            'en' => 'Use it to compare the topic with your routine and needs.',
            'sl' => 'Uporabi ga za primerjavo teme s svojo rutino in potrebami.',
            default => 'Koristi ga za usporedbu teme sa svojom rutinom i potrebama.',
        },
        match ($languageCode) {
            'en' => 'For a personal choice, continue with a recommendation after reading.',
            'sl' => 'Za osebno izbiro po branju nadaljuj s priporočilom.',
            default => 'Za osobni odabir nakon čitanja nastavi s preporukom.',
        },
    ];

    return '<ul>' . implode('', array_map(static fn (string $item): string => '<li>' . htmlspecialchars(truncateText($item, 170), ENT_QUOTES, 'UTF-8') . '</li>', $items)) . '</ul>';
}

function buildFaqItems(string $languageCode, string $title): array
{
    $shortTitle = truncateText($title, 90);

    if ($languageCode === 'en') {
        return [
            ['question' => 'What should I take from this article?', 'answer' => 'Use it to understand the topic in practical terms and compare it with your current routine, needs and next step.'],
            ['question' => 'When does it make sense to ask for a recommendation?', 'answer' => 'Ask when the topic feels relevant, but you are not sure which Forever Living Products product or routine fits you best.'],
            ['question' => 'Does this article replace professional advice?', 'answer' => $shortTitle . ' is educational content. For medical conditions, medication, pregnancy or persistent symptoms, include qualified professional advice.'],
        ];
    }

    return [
        ['question' => 'Kaj naj odnesem iz tega članka?', 'answer' => 'Uporabi ga za praktično razumevanje teme in primerjavo s svojo rutino, potrebami in naslednjim korakom.'],
        ['question' => 'Kdaj je smiselno vprašati za priporočilo?', 'answer' => 'Vprašaj, ko je tema zate pomembna, vendar nisi prepričan, kateri Forever Living Products izdelek ali rutina ti najbolj ustreza.'],
        ['question' => 'Ali članek nadomešča strokovni nasvet?', 'answer' => $shortTitle . ' je izobraževalna vsebina. Pri zdravstvenih težavah, zdravilih, nosečnosti ali trdovratnih simptomih vključi strokovni nasvet.'],
    ];
}

function buildExcerpt(string $plainText, string $languageCode, string $title): string
{
    $sentences = extractSentences($plainText, 2);
    $excerpt = trim(implode(' ', $sentences));

    if ($excerpt === '') {
        $excerpt = match ($languageCode) {
            'en' => 'Read a practical Aloe Vera Centar article about ' . $title . ' before choosing the next step.',
            'sl' => 'Preberi praktičen članek Aloe Vera Centra o temi ' . $title . ' pred naslednjim korakom.',
            default => 'Pročitaj praktičan članak Aloe Vera Centra o temi ' . $title . ' prije sljedećeg koraka.',
        };
    }

    return truncateText($excerpt, 180);
}

function buildMetaDescription(string $excerpt, string $plainText, string $languageCode, string $title): string
{
    $description = trim($excerpt);
    if (mb_strlen($description) < 110) {
        $description = buildExcerpt($plainText, $languageCode, $title);
    }

    return truncateText($description, 168);
}

function extractSentences(string $text, int $limit): array
{
    $sentences = preg_split('/(?<=[.!?])\s+/u', cleanText($text)) ?: [];
    $result = [];

    foreach ($sentences as $sentence) {
        $sentence = trim($sentence);
        if ($sentence === '' || mb_strlen($sentence) < 35) {
            continue;
        }

        $result[] = $sentence;
        if (count($result) >= $limit) {
            break;
        }
    }

    return $result;
}

function articlePlainText(string $html): string
{
    $html = preg_replace('/^\s*<h[1-3]\b[^>]*>.*?<\/h[1-3]>\s*/isu', '', $html, 1) ?? $html;

    return cleanText($html);
}

function cleanText(string $value): string
{
    $value = preg_replace('/<br\s*\/?>/iu', ' ', $value) ?? $value;
    $value = preg_replace('/<\/(?:p|h1|h2|h3|h4|h5|h6|li|blockquote|div|section|article|tr|td|th)>/iu', '$0 ', $value) ?? $value;
    $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = preg_replace('/\[[^\]]+\]/u', ' ', $value) ?? $value;
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

    return trim($value);
}

function truncateText(string $value, int $limit): string
{
    $value = cleanText($value);
    if (mb_strlen($value) <= $limit) {
        return $value;
    }

    return rtrim(mb_substr($value, 0, max(1, $limit - 3)), " \t\n\r\0\x0B.,;:-") . '...';
}

function textLength(string $html): int
{
    return mb_strlen(cleanText($html));
}

function normalizeRoutePath(string $routePath): string
{
    $routePath = '/' . trim($routePath, '/') . '/';

    return $routePath === '//' ? '/' : $routePath;
}

function loadCache(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $decoded = json_decode((string) file_get_contents($path), true);

    return is_array($decoded) ? $decoded : [];
}

function saveCache(string $path, array $cache): void
{
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0775, true);
    }

    file_put_contents($path, json_encode($cache, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function rememberExample(array &$examples, array $example): void
{
    if (count($examples) >= 12) {
        return;
    }

    $examples[] = $example;
}
