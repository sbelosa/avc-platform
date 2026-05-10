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

$rows = fetchTranslationGaps($connection);
$summary = [
    'generated_at' => date('c'),
    'total_translation_gaps' => count($rows),
    'by_language' => [],
    'by_source' => [
        'has_hr_source' => 0,
        'has_en_source' => 0,
        'has_export_source' => 0,
    ],
    'rows' => $rows,
];

foreach ($rows as $row) {
    $languageCode = (string) ($row['language_code'] ?? '');
    $summary['by_language'][$languageCode] = (int) ($summary['by_language'][$languageCode] ?? 0) + 1;

    if ((int) ($row['hr_text_len'] ?? 0) > 2500) {
        $summary['by_source']['has_hr_source']++;
    }

    if ((int) ($row['en_text_len'] ?? 0) > 2500) {
        $summary['by_source']['has_en_source']++;
    }

    if ((int) ($row['source_wp_post_id'] ?? 0) > 0) {
        $summary['by_source']['has_export_source']++;
    }
}

$reportPath = $rootPath . '/storage/reports/article_translation_gap_report_' . date('Ymd_His') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}

file_put_contents($reportPath, json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$csvPath = preg_replace('/\.json$/', '.csv', $reportPath) ?: ($reportPath . '.csv');
$csv = fopen($csvPath, 'wb');
if ($csv !== false) {
    fputcsv($csv, [
        'content_item_id',
        'content_translation_id',
        'language_code',
        'route_path',
        'title',
        'source_wp_post_id',
        'current_text_len',
        'hr_route_path',
        'hr_title',
        'hr_text_len',
        'en_route_path',
        'en_title',
        'en_text_len',
        'recommended_source_language',
        'priority',
    ]);

    foreach ($rows as $row) {
        fputcsv($csv, [
            $row['content_item_id'] ?? '',
            $row['content_translation_id'] ?? '',
            $row['language_code'] ?? '',
            $row['route_path'] ?? '',
            $row['title'] ?? '',
            $row['source_wp_post_id'] ?? '',
            $row['current_text_len'] ?? '',
            $row['hr_route_path'] ?? '',
            $row['hr_title'] ?? '',
            $row['hr_text_len'] ?? '',
            $row['en_route_path'] ?? '',
            $row['en_title'] ?? '',
            $row['en_text_len'] ?? '',
            $row['recommended_source_language'] ?? '',
            $row['priority'] ?? '',
        ]);
    }

    fclose($csv);
}

$consoleSummary = $summary;
unset($consoleSummary['rows']);

echo json_encode($consoleSummary + ['report_path' => $reportPath, 'csv_path' => $csvPath], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function fetchTranslationGaps(mysqli $connection): array
{
    $result = $connection->query(
        "SELECT
            target.content_item_id,
            target.content_translation_id,
            target.language_code,
            target.source_wp_post_id,
            target.title,
            target.route_path,
            target.current_text_len,
            target.current_body_is_generated,
            hr.content_translation_id AS hr_translation_id,
            hr.title AS hr_title,
            hr.route_path AS hr_route_path,
            hr.text_len AS hr_text_len,
            en.content_translation_id AS en_translation_id,
            en.title AS en_title,
            en.route_path AS en_route_path,
            en.text_len AS en_text_len
         FROM (
            SELECT
                ci.content_item_id,
                ct.content_translation_id,
                ct.language_code,
                COALESCE(ct.source_wp_post_id, 0) AS source_wp_post_id,
                ct.title,
                cr.route_path,
                CHAR_LENGTH(REGEXP_REPLACE(COALESCE(ct.body_html, ''), '<[^>]*>', ' ')) AS current_text_len,
                CASE
                    WHEN ct.body_html LIKE '%data-avc-seo-hardening%'
                      OR ct.body_html LIKE '%This guide looks at%'
                      OR ct.body_html LIKE '%Ta vodnik obravnava temo%'
                      OR ct.title LIKE 'Practical Guide:%'
                      OR ct.title LIKE 'Praktični vodnik:%'
                    THEN 1 ELSE 0
                END AS current_body_is_generated
             FROM content_items ci
             INNER JOIN content_translations ct ON ct.content_item_id = ci.content_item_id
             INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
             WHERE ci.content_type = 'article'
               AND ci.lifecycle_status = 'published'
               AND ct.language_code IN ('en', 'sl')
               AND (ct.source_wp_post_id IS NULL OR ct.source_wp_post_id = 0)
         ) target
         LEFT JOIN (
            SELECT
                ct.content_item_id,
                ct.content_translation_id,
                ct.title,
                cr.route_path,
                CHAR_LENGTH(REGEXP_REPLACE(COALESCE(ct.body_html, ''), '<[^>]*>', ' ')) AS text_len
             FROM content_translations ct
             INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
             WHERE ct.language_code = 'hr'
         ) hr ON hr.content_item_id = target.content_item_id
         LEFT JOIN (
            SELECT
                ct.content_item_id,
                ct.content_translation_id,
                ct.title,
                cr.route_path,
                CHAR_LENGTH(REGEXP_REPLACE(COALESCE(ct.body_html, ''), '<[^>]*>', ' ')) AS text_len
             FROM content_translations ct
             INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
             WHERE ct.language_code = 'en'
               AND ct.source_wp_post_id IS NOT NULL
               AND ct.source_wp_post_id > 0
         ) en ON en.content_item_id = target.content_item_id
         WHERE target.current_body_is_generated = 1
            OR target.current_text_len < 2500
         ORDER BY target.language_code ASC, hr.text_len DESC, target.route_path ASC"
    );

    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    foreach ($rows as &$row) {
        $languageCode = strtolower((string) ($row['language_code'] ?? ''));
        $hasEnglishSource = (int) ($row['en_text_len'] ?? 0) > 2500;
        $hasCroatianSource = (int) ($row['hr_text_len'] ?? 0) > 2500;
        $currentTextLength = (int) ($row['current_text_len'] ?? 0);

        if ($languageCode === 'sl' && $hasEnglishSource) {
            $row['recommended_source_language'] = 'en';
        } elseif ($hasCroatianSource) {
            $row['recommended_source_language'] = 'hr';
        } else {
            $row['recommended_source_language'] = 'manual_review';
        }

        $row['priority'] = $currentTextLength < 1800 ? 'high' : 'medium';
    }
    unset($row);

    return $rows;
}
