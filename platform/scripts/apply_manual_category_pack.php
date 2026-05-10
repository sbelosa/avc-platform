<?php

declare(strict_types=1);

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

$packKey = 'imunitet-detoks-hr';
foreach (array_slice($argv, 1) as $argument) {
    if (str_starts_with($argument, '--pack=')) {
        $value = trim(substr($argument, strlen('--pack=')));
        if ($value !== '') {
            $packKey = $value;
        }
    }
}

$availablePacks = [];
$packsDirectory = $rootPath . '/data/editorial_packs';

if (is_dir($packsDirectory)) {
    foreach (glob($packsDirectory . '/*.php') ?: [] as $packPath) {
        $pack = require $packPath;
        $candidateKey = trim((string) ($pack['key'] ?? ''));

        if ($candidateKey === '') {
            continue;
        }

        $availablePacks[$candidateKey] = $packPath;
    }
}

if (!isset($availablePacks[$packKey]) || !is_file($availablePacks[$packKey])) {
    fwrite(STDERR, 'Unknown pack. Available packs: ' . implode(', ', array_keys($availablePacks)) . PHP_EOL);
    exit(1);
}

$pack = require $availablePacks[$packKey];
$repository = new ContentRepository($config);
$structuredContent = new StructuredContentService();

$report = [
    'generated_at' => date('c'),
    'pack_key' => (string) ($pack['key'] ?? $packKey),
    'pack_name' => (string) ($pack['name'] ?? $packKey),
    'notes' => (string) ($pack['notes'] ?? ''),
    'updated' => [],
    'skipped' => [],
];

foreach ((array) ($pack['entries'] ?? []) as $entry) {
    $contentTranslationId = (int) ($entry['content_translation_id'] ?? 0);
    if ($contentTranslationId <= 0) {
        continue;
    }

    $existing = $repository->findForAdminEdit($contentTranslationId);
    if ($existing === null) {
        $report['skipped'][] = [
            'content_translation_id' => $contentTranslationId,
            'reason' => 'missing_translation',
        ];
        continue;
    }

    $payload = [
        'title' => (string) ($entry['title'] ?? $existing['title'] ?? ''),
        'excerpt' => (string) ($entry['excerpt'] ?? $existing['excerpt'] ?? ''),
        'summary_html' => (string) ($entry['summary_html'] ?? $existing['summary_html'] ?? ''),
        'faq_json' => array_key_exists('faq_items', $entry)
            ? $structuredContent->encodeFaqItems((array) ($entry['faq_items'] ?? []))
            : (string) ($entry['faq_json'] ?? $existing['faq_json'] ?? ''),
        'body_html' => (string) ($entry['body_html'] ?? $existing['body_html'] ?? ''),
        'meta_title' => (string) ($entry['meta_title'] ?? $existing['meta_title'] ?? ''),
        'meta_description' => (string) ($entry['meta_description'] ?? $existing['meta_description'] ?? ''),
        'canonical_url' => (string) ($entry['canonical_url'] ?? $existing['canonical_url'] ?? ''),
        'breadcrumb_title' => (string) ($entry['breadcrumb_title'] ?? $existing['breadcrumb_title'] ?? ''),
        'robots_index' => (int) ($entry['robots_index'] ?? $existing['robots_index'] ?? 1),
        'robots_follow' => (int) ($entry['robots_follow'] ?? $existing['robots_follow'] ?? 1),
    ];

    $repository->updateTranslationAndSeo($contentTranslationId, $payload);

    $report['updated'][] = [
        'content_translation_id' => $contentTranslationId,
        'title' => (string) ($payload['title'] ?? ''),
        'route_path' => (string) ($existing['route_path'] ?? ''),
    ];
}

$reportPath = $rootPath . '/storage/reports/manual_editorial_pack_' . preg_replace('/[^a-z0-9_-]+/i', '_', $packKey) . '.json';
$reportDir = dirname($reportPath);
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0775, true);
}

file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
