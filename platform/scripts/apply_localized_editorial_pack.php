<?php

declare(strict_types=1);

use Avc\Core\Database;
use Avc\Repositories\ContentRepository;
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

$config = require $rootPath . '/config/app.php';
$packKey = '';

foreach (array_slice($argv, 1) as $argument) {
    if (str_starts_with($argument, '--pack=')) {
        $value = trim(substr($argument, strlen('--pack=')));
        if ($value !== '') {
            $packKey = $value;
        }
    }
}

$packsDirectory = $rootPath . '/data/editorial_packs_localized';
$availablePacks = [];

if (is_dir($packsDirectory)) {
    foreach (glob($packsDirectory . '/*.php') ?: [] as $packPath) {
        $pack = require $packPath;
        $candidateKey = trim((string) ($pack['key'] ?? ''));
        if ($candidateKey !== '') {
            $availablePacks[$candidateKey] = $packPath;
        }
    }
}

if ($packKey === '' || !isset($availablePacks[$packKey])) {
    fwrite(STDERR, 'Unknown localized pack. Available packs: ' . implode(', ', array_keys($availablePacks)) . PHP_EOL);
    exit(1);
}

$connection = Database::connection($config);
if ($connection === null) {
    fwrite(STDERR, 'Database connection failed.' . PHP_EOL);
    exit(1);
}

$pack = require $availablePacks[$packKey];
$repository = new ContentRepository($config);
$structuredContent = new StructuredContentService();
$languagePrefixes = fetchLanguagePrefixes($connection);

$report = [
    'generated_at' => date('c'),
    'pack_key' => (string) ($pack['key'] ?? $packKey),
    'pack_name' => (string) ($pack['name'] ?? $packKey),
    'notes' => (string) ($pack['notes'] ?? ''),
    'updated' => [],
    'created' => [],
    'skipped' => [],
];

foreach ((array) ($pack['entries'] ?? []) as $entry) {
    $sourceTranslationId = (int) ($entry['source_translation_id'] ?? 0);
    $languageCode = strtolower(trim((string) ($entry['language_code'] ?? '')));

    if ($sourceTranslationId <= 0 || $languageCode === '') {
        continue;
    }

    $source = $repository->findForAdminEdit($sourceTranslationId);
    if ($source === null) {
        $report['skipped'][] = [
            'source_translation_id' => $sourceTranslationId,
            'language_code' => $languageCode,
            'reason' => 'missing_source_translation',
        ];
        continue;
    }

    if (!isset($languagePrefixes[$languageCode])) {
        $report['skipped'][] = [
            'source_translation_id' => $sourceTranslationId,
            'language_code' => $languageCode,
            'reason' => 'missing_language_prefix',
        ];
        continue;
    }

    $target = findTranslationByContentItemAndLanguage($connection, (int) ($source['content_item_id'] ?? 0), $languageCode);
    $payload = buildLocalizedPayload($entry, $source, $structuredContent);
    $routePath = buildRoutePath($languagePrefixes[$languageCode], (string) $payload['slug']);

    $connection->begin_transaction();

    try {
        if ($target === null) {
            $contentTranslationId = insertLocalizedTranslation($connection, $source, $languageCode, $payload);
            $report['created'][] = [
                'content_translation_id' => $contentTranslationId,
                'language_code' => $languageCode,
                'route_path' => $routePath,
                'title' => (string) ($payload['title'] ?? ''),
            ];
        } else {
            $contentTranslationId = (int) ($target['content_translation_id'] ?? 0);
            updateLocalizedTranslation($connection, $contentTranslationId, $payload, $source);
            $report['updated'][] = [
                'content_translation_id' => $contentTranslationId,
                'language_code' => $languageCode,
                'route_path' => $routePath,
                'title' => (string) ($payload['title'] ?? ''),
            ];
        }

        upsertRoute($connection, $contentTranslationId, $languageCode, $routePath);
        upsertSeo($connection, $contentTranslationId, $payload);
        $connection->commit();
    } catch (\Throwable $throwable) {
        $connection->rollback();

        $report['skipped'][] = [
            'source_translation_id' => $sourceTranslationId,
            'language_code' => $languageCode,
            'reason' => 'exception',
            'message' => $throwable->getMessage(),
        ];
    }
}

$reportPath = $rootPath . '/storage/reports/localized_editorial_pack_' . preg_replace('/[^a-z0-9_-]+/i', '_', $packKey) . '.json';
$reportDir = dirname($reportPath);
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0775, true);
}

file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function fetchLanguagePrefixes(mysqli $connection): array
{
    $result = $connection->query('SELECT language_code, url_prefix FROM languages');
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $prefixes = [];

    foreach ($rows as $row) {
        $prefixes[strtolower(trim((string) ($row['language_code'] ?? '')))] = trim((string) ($row['url_prefix'] ?? ''));
    }

    return $prefixes;
}

function findTranslationByContentItemAndLanguage(mysqli $connection, int $contentItemId, string $languageCode): ?array
{
    $statement = $connection->prepare(
        'SELECT
            ct.content_translation_id,
            ct.content_item_id,
            ct.language_code,
            ct.slug
         FROM content_translations ct
         WHERE ct.content_item_id = ?
           AND ct.language_code = ?
         LIMIT 1'
    );
    $statement->bind_param('is', $contentItemId, $languageCode);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();

    return $row ?: null;
}

function buildLocalizedPayload(array $entry, array $source, StructuredContentService $structuredContent): array
{
    $title = trim((string) ($entry['title'] ?? ''));
    $slug = trim((string) ($entry['slug'] ?? ''));
    $excerpt = trim((string) ($entry['excerpt'] ?? ''));
    $summaryHtml = trim((string) ($entry['summary_html'] ?? ''));
    $faqItems = (array) ($entry['faq_items'] ?? []);
    $bodyHtml = trim((string) ($entry['body_html'] ?? ''));

    if ($bodyHtml === '') {
        $bodyHtml = buildLocalizedBodyHtml($title, $excerpt, (array) ($entry['sections'] ?? []));
    }

    return [
        'title' => $title,
        'slug' => $slug,
        'excerpt' => $excerpt,
        'summary_html' => $summaryHtml,
        'faq_json' => $structuredContent->encodeFaqItems($faqItems),
        'body_html' => $bodyHtml,
        'meta_title' => trim((string) ($entry['meta_title'] ?? '')),
        'meta_description' => trim((string) ($entry['meta_description'] ?? '')),
        'canonical_url' => trim((string) ($entry['canonical_url'] ?? '')),
        'breadcrumb_title' => trim((string) ($entry['breadcrumb_title'] ?? '')),
        'robots_index' => (int) ($entry['robots_index'] ?? 1),
        'robots_follow' => (int) ($entry['robots_follow'] ?? 1),
        'featured_image_path' => (string) ($entry['featured_image_path'] ?? $source['featured_image_path'] ?? ''),
        'published_at' => (string) ($entry['published_at'] ?? $source['published_at'] ?? ''),
    ];
}

function buildLocalizedBodyHtml(string $title, string $excerpt, array $sections): string
{
    $html = '';

    if ($excerpt !== '') {
        $html .= '<p>' . htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    foreach ($sections as $section) {
        $heading = trim((string) ($section['heading'] ?? ''));
        $content = trim((string) ($section['html'] ?? ''));

        if ($heading === '' || $content === '') {
            continue;
        }

        $html .= '<h2>' . htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') . '</h2>' . $content;
    }

    if ($html === '') {
        $html = '<p>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    return $html;
}

function buildRoutePath(string $urlPrefix, string $slug): string
{
    $slug = trim($slug, '/');
    $prefix = trim($urlPrefix, '/');

    if ($prefix === '') {
        return '/' . $slug . '/';
    }

    return '/' . $prefix . '/' . $slug . '/';
}

function insertLocalizedTranslation(mysqli $connection, array $source, string $languageCode, array $payload): int
{
    $statement = $connection->prepare(
        'INSERT INTO content_translations (
            content_item_id,
            source_wp_post_id,
            language_code,
            title,
            slug,
            excerpt,
            body_html,
            body_json,
            summary_html,
            faq_json,
            featured_image_path,
            published_at
         ) VALUES (?, NULL, ?, ?, ?, ?, ?, NULL, ?, ?, ?, ?)'
    );
    $statement->bind_param(
        'isssssssss',
        $source['content_item_id'],
        $languageCode,
        $payload['title'],
        $payload['slug'],
        $payload['excerpt'],
        $payload['body_html'],
        $payload['summary_html'],
        $payload['faq_json'],
        $payload['featured_image_path'],
        $payload['published_at']
    );
    $statement->execute();
    $insertId = (int) $connection->insert_id;
    $statement->close();

    return $insertId;
}

function updateLocalizedTranslation(mysqli $connection, int $contentTranslationId, array $payload, array $source): void
{
    $statement = $connection->prepare(
        'UPDATE content_translations
         SET title = ?,
             slug = ?,
             excerpt = ?,
             body_html = ?,
             summary_html = ?,
             faq_json = ?,
             featured_image_path = ?,
             published_at = ?
         WHERE content_translation_id = ?'
    );
    $statement->bind_param(
        'ssssssssi',
        $payload['title'],
        $payload['slug'],
        $payload['excerpt'],
        $payload['body_html'],
        $payload['summary_html'],
        $payload['faq_json'],
        $payload['featured_image_path'],
        $payload['published_at'],
        $contentTranslationId
    );
    $statement->execute();
    $statement->close();
}

function upsertRoute(mysqli $connection, int $contentTranslationId, string $languageCode, string $routePath): void
{
    $delete = $connection->prepare('DELETE FROM content_routes WHERE content_translation_id = ?');
    $delete->bind_param('i', $contentTranslationId);
    $delete->execute();
    $delete->close();

    $statement = $connection->prepare(
        'INSERT INTO content_routes (
            language_code,
            route_path,
            content_translation_id,
            route_type,
            http_status_code,
            redirect_target_path,
            source_system,
            is_primary
         ) VALUES (?, ?, ?, \'content\', 200, NULL, \'avc_localized_pack\', 1)'
    );
    $statement->bind_param('ssi', $languageCode, $routePath, $contentTranslationId);
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
