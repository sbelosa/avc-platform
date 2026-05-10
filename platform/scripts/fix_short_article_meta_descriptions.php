<?php

declare(strict_types=1);

use Avc\Core\Database;

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
$connection = Database::connection($config);

if ($connection === null) {
    fwrite(STDERR, 'Database connection failed.' . PHP_EOL);
    exit(1);
}

$rows = fetchRows($connection);
$report = [
    'generated_at' => date('c'),
    'checked' => count($rows),
    'updated' => 0,
    'examples' => [],
];

foreach ($rows as $row) {
    $description = buildMetaDescription($row);
    if (mb_strlen($description) < 110) {
        continue;
    }

    upsertSeoDescription($connection, (int) ($row['content_translation_id'] ?? 0), $description);
    $report['updated']++;

    if (count($report['examples']) < 10) {
        $report['examples'][] = [
            'route_path' => (string) ($row['route_path'] ?? ''),
            'language_code' => (string) ($row['language_code'] ?? ''),
            'description' => $description,
        ];
    }
}

$reportPath = $rootPath . '/storage/reports/short_article_meta_descriptions_' . date('Ymd_His') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}
file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report + ['report_path' => $reportPath], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function fetchRows(mysqli $connection): array
{
    $result = $connection->query(
        "SELECT
            ct.content_translation_id,
            ct.language_code,
            ct.title,
            ct.excerpt,
            ct.body_html,
            cr.route_path,
            sm.meta_title,
            sm.meta_description,
            sm.canonical_url,
            sm.robots_index,
            sm.robots_follow,
            sm.breadcrumb_title
         FROM content_translations ct
         INNER JOIN content_items ci ON ci.content_item_id = ct.content_item_id
         INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
         LEFT JOIN seo_metadata sm ON sm.content_translation_id = ct.content_translation_id
         WHERE ci.content_type = 'article'
           AND ci.lifecycle_status = 'published'
           AND cr.http_status_code = 200
           AND COALESCE(sm.robots_index, 1) = 1
           AND CHAR_LENGTH(TRIM(COALESCE(sm.meta_description, ct.excerpt, ''))) < 110"
    );

    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function buildMetaDescription(array $row): string
{
    $languageCode = strtolower((string) ($row['language_code'] ?? 'hr')) ?: 'hr';
    $text = cleanText((string) ($row['excerpt'] ?? ''));

    if (mb_strlen($text) < 110) {
        $body = preg_replace('/^\s*<h[1-3]\b[^>]*>.*?<\/h[1-3]>\s*/isu', '', (string) ($row['body_html'] ?? ''), 1) ?? (string) ($row['body_html'] ?? '');
        $text = implode(' ', extractSentences(cleanText($body), 2));
    }

    if (mb_strlen($text) < 110) {
        $title = cleanText((string) ($row['title'] ?? ''));
        $text = match ($languageCode) {
            'en' => 'Read a practical Aloe Vera Centar article about ' . $title . ', with clear context, useful cautions and the next step when you want a recommendation.',
            'sl' => 'Preberi praktičen članek Aloe Vera Centra o temi ' . $title . ', z jasnim kontekstom, koristnimi opombami in naslednjim korakom.',
            default => 'Pročitaj praktičan članak Aloe Vera Centra o temi ' . $title . ', uz jasan kontekst, korisne napomene i sljedeći korak prema preporuci.',
        };
    }

    return truncateText($text, 168);
}

function extractSentences(string $text, int $limit): array
{
    $sentences = preg_split('/(?<=[.!?])\s+/u', $text) ?: [];
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

function upsertSeoDescription(mysqli $connection, int $contentTranslationId, string $description): void
{
    $statement = $connection->prepare(
        'UPDATE seo_metadata
         SET meta_description = ?
         WHERE content_translation_id = ?'
    );
    $statement->bind_param('si', $description, $contentTranslationId);
    $statement->execute();
    $affected = $statement->affected_rows;
    $statement->close();

    if ($affected >= 0) {
        return;
    }

    $metaTitle = '';
    $canonicalUrl = '';
    $breadcrumbTitle = '';
    $robotsIndex = 1;
    $robotsFollow = 1;
    $statement = $connection->prepare(
        'INSERT INTO seo_metadata (
            content_translation_id,
            meta_title,
            meta_description,
            canonical_url,
            robots_index,
            robots_follow,
            breadcrumb_title
        ) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $statement->bind_param('isssiis', $contentTranslationId, $metaTitle, $description, $canonicalUrl, $robotsIndex, $robotsFollow, $breadcrumbTitle);
    $statement->execute();
    $statement->close();
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
