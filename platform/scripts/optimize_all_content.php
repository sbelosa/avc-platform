<?php

declare(strict_types=1);

use Avc\Repositories\ContentRepository;
use Avc\Repositories\SeoRepository;
use Avc\Services\Content\SeoDraftGeneratorService;
use Avc\Services\Content\StructuredContentService;
use Avc\Services\Content\StructuredDraftGeneratorService;
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

$limit = null;
foreach (array_slice($argv, 1) as $argument) {
    if (str_starts_with($argument, '--limit=')) {
        $value = (int) substr($argument, strlen('--limit='));
        $limit = $value > 0 ? $value : null;
    }
}

$contentRepository = new ContentRepository($config);
$seoRepository = new SeoRepository($config);
$auditService = new SeoAuditService();
$seoGenerator = new SeoDraftGeneratorService();
$structuredGenerator = new StructuredDraftGeneratorService();
$structuredContent = new StructuredContentService();

$rows = $auditService->decorateRows($seoRepository->listAuditRows(null, null, '', 50000));
$processed = 0;
$updated = 0;
$skipped = 0;
$excluded = 0;
$fieldUpdates = [
    'excerpt' => 0,
    'meta_title' => 0,
    'meta_description' => 0,
    'summary_html' => 0,
    'faq_json' => 0,
];
$samples = [];

$hasIssue = static function (array $row, array $codes): bool {
    foreach ((array) ($row['issues'] ?? []) as $issue) {
        if (in_array((string) ($issue['code'] ?? ''), $codes, true)) {
            return true;
        }
    }

    return false;
};

$isExcludedRoute = static function (string $routePath): bool {
    return (bool) preg_match('#^/(en/|sl/)?(checkout(?:-[^/]+)?|my-account|cart|shop|shopping-cart|kosarica[^/]*|placanje|blagajna|nakupovalni-vozicek)(/|$)#i', $routePath)
        || str_contains(strtolower($routePath), '/trgovina/');
};

foreach ($rows as $row) {
    if ($limit !== null && $processed >= $limit) {
        break;
    }

    $routePath = (string) ($row['route_path'] ?? '');
    if ($routePath !== '' && $isExcludedRoute($routePath)) {
        $excluded++;
        continue;
    }

    $processed++;
    $contentTranslationId = (int) ($row['content_translation_id'] ?? 0);
    $existing = $contentRepository->findForAdminEdit($contentTranslationId);

    if ($existing === null) {
        $skipped++;
        continue;
    }

    $payload = [
        'title' => (string) ($existing['title'] ?? ''),
        'excerpt' => (string) ($existing['excerpt'] ?? ''),
        'summary_html' => (string) ($existing['summary_html'] ?? ''),
        'faq_json' => (string) ($existing['faq_json'] ?? ''),
        'body_html' => (string) ($existing['body_html'] ?? ''),
        'meta_title' => (string) ($existing['meta_title'] ?? ''),
        'meta_description' => (string) ($existing['meta_description'] ?? ''),
        'canonical_url' => (string) ($existing['canonical_url'] ?? ''),
        'breadcrumb_title' => (string) ($existing['breadcrumb_title'] ?? ''),
        'robots_index' => (int) ($existing['robots_index'] ?? 1),
        'robots_follow' => (int) ($existing['robots_follow'] ?? 1),
    ];

    $dirtyFields = [];
    $existingExcerptLength = mb_strlen(trim((string) ($existing['excerpt'] ?? '')));
    $seoSource = $existing;

    if ($existingExcerptLength === 0 || $existingExcerptLength < 90) {
        $seoSource['excerpt'] = '';
    }

    if ($hasIssue($row, ['missing_effective_description', 'description_too_short', 'description_too_long'])) {
        $seoSource['meta_description'] = '';
    }

    $seoDraft = $seoGenerator->generate($seoSource);
    $structuredDraft = $structuredGenerator->generate($existing);

    if (($existingExcerptLength === 0 || $existingExcerptLength < 90) && trim((string) ($seoDraft['excerpt'] ?? '')) !== '') {
        $payload['excerpt'] = (string) ($seoDraft['excerpt'] ?? '');
        $dirtyFields[] = 'excerpt';
    }

    if (trim((string) ($existing['meta_title'] ?? '')) === '' && trim((string) ($seoDraft['meta_title'] ?? '')) !== '') {
        $payload['meta_title'] = (string) ($seoDraft['meta_title'] ?? '');
        $dirtyFields[] = 'meta_title';
    }

    if (
        $hasIssue($row, ['missing_effective_description', 'description_too_short', 'description_too_long'])
        && trim((string) ($seoDraft['meta_description'] ?? '')) !== ''
    ) {
        $payload['meta_description'] = (string) ($seoDraft['meta_description'] ?? '');
        $dirtyFields[] = 'meta_description';
    }

    if ($hasIssue($row, ['missing_summary_block']) && trim((string) ($structuredDraft['summary_html'] ?? '')) !== '') {
        $payload['summary_html'] = (string) ($structuredDraft['summary_html'] ?? '');
        $dirtyFields[] = 'summary_html';
    }

    if ($hasIssue($row, ['missing_faq_block']) && !empty($structuredDraft['faq_items'])) {
        $payload['faq_json'] = $structuredContent->encodeFaqItems((array) ($structuredDraft['faq_items'] ?? []));
        $dirtyFields[] = 'faq_json';
    }

    if ($dirtyFields === []) {
        $skipped++;
        continue;
    }

    $contentRepository->updateTranslationAndSeo($contentTranslationId, $payload);
    $updated++;

    foreach (array_unique($dirtyFields) as $field) {
        $fieldUpdates[$field]++;
    }

    if (count($samples) < 20) {
        $samples[] = [
            'content_translation_id' => $contentTranslationId,
            'route_path' => (string) ($row['route_path'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'fields' => array_values(array_unique($dirtyFields)),
        ];
    }
}

$report = [
    'generated_at' => date('c'),
    'processed' => $processed,
    'updated' => $updated,
    'skipped' => $skipped,
    'excluded' => $excluded,
    'field_updates' => $fieldUpdates,
    'samples' => $samples,
];

$reportPath = $rootPath . '/storage/reports/content_optimization_report.json';
$reportDir = dirname($reportPath);
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0775, true);
}

file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
