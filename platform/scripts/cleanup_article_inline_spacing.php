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

$result = $connection->query(
    "SELECT
        ct.content_translation_id,
        ct.language_code,
        ct.title,
        ct.excerpt,
        ct.body_html,
        ct.summary_html,
        sm.meta_title,
        sm.meta_description,
        sm.breadcrumb_title
     FROM content_translations ct
     INNER JOIN content_items ci ON ci.content_item_id = ct.content_item_id
     LEFT JOIN seo_metadata sm ON sm.content_translation_id = ct.content_translation_id
     WHERE ci.content_type = 'article'
       AND ci.lifecycle_status = 'published'"
);
$rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$report = [
    'generated_at' => date('c'),
    'checked' => count($rows),
    'updated' => 0,
    'examples' => [],
];

foreach ($rows as $row) {
    $contentTranslationId = (int) ($row['content_translation_id'] ?? 0);
    $bodyHtml = (string) ($row['body_html'] ?? '');
    $excerpt = (string) ($row['excerpt'] ?? '');
    $summaryHtml = (string) ($row['summary_html'] ?? '');
    $metaTitle = (string) ($row['meta_title'] ?? '');
    $metaDescription = (string) ($row['meta_description'] ?? '');
    $breadcrumbTitle = (string) ($row['breadcrumb_title'] ?? '');

    $cleanedBody = cleanupInlineSpacing($bodyHtml);
    $cleanedExcerpt = cleanupInlineSpacing($excerpt);
    $cleanedSummary = cleanupInlineSpacing($summaryHtml);
    $cleanedMetaTitle = cleanupInlineSpacing($metaTitle);
    $cleanedMetaDescription = cleanupInlineSpacing($metaDescription);
    $cleanedBreadcrumbTitle = cleanupInlineSpacing($breadcrumbTitle);

    if (
        $contentTranslationId <= 0
        || (
            $cleanedBody === $bodyHtml
            && $cleanedExcerpt === $excerpt
            && $cleanedSummary === $summaryHtml
            && $cleanedMetaTitle === $metaTitle
            && $cleanedMetaDescription === $metaDescription
            && $cleanedBreadcrumbTitle === $breadcrumbTitle
        )
    ) {
        continue;
    }

    $statement = $connection->prepare('UPDATE content_translations SET excerpt = ?, body_html = ?, summary_html = ? WHERE content_translation_id = ?');
    $statement->bind_param('sssi', $cleanedExcerpt, $cleanedBody, $cleanedSummary, $contentTranslationId);
    $statement->execute();
    $statement->close();

    $statement = $connection->prepare(
        'UPDATE seo_metadata
         SET meta_title = ?, meta_description = ?, breadcrumb_title = ?
         WHERE content_translation_id = ?'
    );
    $statement->bind_param('sssi', $cleanedMetaTitle, $cleanedMetaDescription, $cleanedBreadcrumbTitle, $contentTranslationId);
    $statement->execute();
    $statement->close();

    $report['updated']++;
    if (count($report['examples']) < 12) {
        $report['examples'][] = [
            'content_translation_id' => $contentTranslationId,
            'language_code' => (string) ($row['language_code'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
        ];
    }
}

$reportPath = $rootPath . '/storage/reports/article_inline_spacing_cleanup_' . date('Ymd_His') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}

file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report + ['report_path' => $reportPath], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function cleanupInlineSpacing(string $html): string
{
    if (trim($html) === '') {
        return $html;
    }

    $inlineTags = '(?:strong|b|em|i|span|small|mark)';
    $html = preg_replace('/(?<=[\p{L}\p{N}])<(' . $inlineTags . ')(\b[^>]*)>/u', ' <$1$2>', $html) ?? $html;
    $html = preg_replace('/<\/(' . $inlineTags . ')>(?=[\p{L}\p{N}])/u', '</$1> ', $html) ?? $html;
    $html = preg_replace('/(?<=[\p{L}\p{N}])(?=(?:aloe|Aloe|Forever|AVC|FLP)\b)/u', ' ', $html) ?? $html;
    $html = preg_replace('/\b[Aa]loevera\b/u', 'aloe vera', $html) ?? $html;
    $html = preg_replace('/\s+([?.!,;:])/u', '$1', $html) ?? $html;
    $html = preg_replace('/([(\[„"“])\s+/u', '$1', $html) ?? $html;
    $html = preg_replace('/\s+([)\]”"])/u', '$1', $html) ?? $html;
    $html = preg_replace('/ {2,}/u', ' ', $html) ?? $html;

    return $html;
}
