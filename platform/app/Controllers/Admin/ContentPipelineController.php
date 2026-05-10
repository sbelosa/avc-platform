<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\ContentRepository;
use Avc\Repositories\SeoRepository;
use Avc\Services\Content\SeoDraftGeneratorService;
use Avc\Services\Content\StructuredContentService;
use Avc\Services\Content\StructuredDraftGeneratorService;
use Avc\Services\Seo\SeoAuditService;
use Avc\Support\AdminPageRenderer;

final class ContentPipelineController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $filters = $this->readFilters();
        $auditService = new SeoAuditService();
        $rows = $auditService->decorateRows((new SeoRepository($this->config))->listAuditRows(
            $filters['language'] !== 'all' ? $filters['language'] : null,
            $filters['content_type'] !== 'all' ? $filters['content_type'] : null,
            $filters['query'],
            240
        ));
        $rows = $this->applyFocusFilter($rows, $filters['focus']);
        $summary = $auditService->summarize($rows);
        $pipelineSummary = $this->summarizePipeline($rows);
        $rows = array_slice($rows, 0, 80);
        $notice = $this->renderNotice();

        $body = $notice
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Opseg i prioritet</h2></div><div class="admin-panel-body"><form class="admin-filter-bar admin-filter-wide" method="get" action="/admin/content-pipeline">'
            . '<label>Tip sadržaja<select name="type">' . $this->renderSelectOptions([
                'all' => 'Sve',
                'article' => 'Blog članci',
                'product_guide' => 'Product guideovi',
                'page' => 'Stranice',
            ], $filters['content_type']) . '</select></label>'
            . '<label>Jezik<select name="lang">' . $this->renderSelectOptions([
                'all' => 'Svi',
                'hr' => 'HR',
                'en' => 'EN',
                'sl' => 'SL',
            ], $filters['language']) . '</select></label>'
            . '<label>Fokus<select name="focus">' . $this->renderSelectOptions([
                'all' => 'Sve u scopeu',
                'top_priority' => 'Top prioriteti',
                'seo_basics' => 'SEO osnove',
                'ai_ready' => 'AI blokovi',
            ], $filters['focus']) . '</select></label>'
            . '<label>Pretraga<input type="text" name="q" value="' . htmlspecialchars($filters['query'], ENT_QUOTES, 'UTF-8') . '" placeholder="Naslov, ruta ili meta title"></label>'
            . '<button class="admin-button admin-button-primary" type="submit">Filtriraj</button>'
            . '</form></div></div>'
            . '<div class="admin-kpi-grid">'
            . $this->renderMetric('U scopeu', (string) ($pipelineSummary['scope_total'] ?? 0))
            . $this->renderMetric('Auto-popravljivo', (string) ($pipelineSummary['auto_fixable_total'] ?? 0))
            . $this->renderMetric('Top prioriteti', (string) ($pipelineSummary['top_priority_total'] ?? 0))
            . $this->renderMetric('SEO avg score', (string) ($summary['average_score'] ?? 0))
            . $this->renderMetric('Bez excerpta', (string) ($summary['missing_excerpt_total'] ?? 0))
            . $this->renderMetric('Bez meta titlea', (string) ($summary['missing_custom_meta_title_total'] ?? 0))
            . $this->renderMetric('Bez meta descriptiona', (string) ($summary['missing_effective_description_total'] ?? 0))
            . $this->renderMetric('Bez AI summaryja', (string) ($summary['missing_summary_block_total'] ?? 0))
            . $this->renderMetric('Bez FAQ bloka', (string) ($summary['missing_faq_block_total'] ?? 0))
            . '</div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Batch akcije</h2></div><div class="admin-panel-body"><p class="admin-muted">Pokreni obradu nad najprioritetnijim URL-ovima iz trenutnog filtera. Batch puni samo prazna SEO/AI polja i ne dira ručno napisane vrijednosti.</p>'
            . '<div class="admin-quick-list">'
            . $this->renderBatchActionCard('ai_blocks', 'Generiraj AI blokove', 'Dodaje AI summary i FAQ tamo gdje nedostaju.', $filters)
            . $this->renderBatchActionCard('seo_basics', 'Generiraj SEO osnove', 'Popunjava excerpt, custom meta title i meta description samo kad su prazni.', $filters)
            . $this->renderBatchActionCard('full_boost', 'Pokreni puni boost', 'Spaja oba koraka i priprema najbolji početni draft za uređivanje.', $filters)
            . '</div></div></div>'
            . '<div class="admin-card-grid">'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Radni tok</h2></div><div class="admin-panel-body"><p class="admin-muted">Filtriraj prioritet, pokreni batch nad 10-30 URL-ova, zatim otvori editor i ručno dotjeraj najvažnije komade.</p></div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Sigurnost</h2></div><div class="admin-panel-body"><p class="admin-muted">Generator preskače postojeća ručna polja i puni samo prazne SEO/AI vrijednosti.</p></div></div>'
            . '</div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Prioritetne stranice</h2></div><div class="admin-panel-body">' . $this->renderRowsTable($rows) . '</div></div>';

        $this->response->html(AdminPageRenderer::render('Content Pipeline', 'pipeline', $body, [
            'eyebrow' => 'Batch obrada',
            'description' => 'Operativni ekran za masovnu SEO i AI pripremu sadržaja prije ručnog uređivanja.',
            'actions' => '<a class="admin-button" href="/admin/seo-audit">SEO audit</a><a class="admin-button" href="/admin/posts">Sadržaj</a>',
        ]));
    }

    public function run(): never
    {
        $filters = $this->readFilters();
        $action = trim((string) $this->request->input('action', 'full_boost'));
        $limit = max(1, min((int) $this->request->input('limit', 20), 60));

        if (!in_array($action, ['ai_blocks', 'seo_basics', 'full_boost'], true)) {
            $this->response->redirect('/admin/content-pipeline?' . http_build_query([
                'error' => 'Nepoznata batch akcija.',
                'type' => $filters['content_type'],
                'lang' => $filters['language'],
                'focus' => $filters['focus'],
                'q' => $filters['query'],
            ]));
        }

        $auditService = new SeoAuditService();
        $rows = $auditService->decorateRows((new SeoRepository($this->config))->listAuditRows(
            $filters['language'] !== 'all' ? $filters['language'] : null,
            $filters['content_type'] !== 'all' ? $filters['content_type'] : null,
            $filters['query'],
            300
        ));
        $rows = $this->applyFocusFilter($rows, $filters['focus']);
        $rows = array_values(array_filter($rows, fn (array $row): bool => $this->matchesBatchAction($row, $action)));
        $rows = array_slice($rows, 0, $limit);

        $contentRepository = new ContentRepository($this->config);
        $structuredGenerator = new StructuredDraftGeneratorService();
        $seoGenerator = new SeoDraftGeneratorService();
        $structuredContent = new StructuredContentService();
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
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
                'featured_image_path' => (string) ($existing['featured_image_path'] ?? ''),
                'meta_title' => (string) ($existing['meta_title'] ?? ''),
                'meta_description' => (string) ($existing['meta_description'] ?? ''),
                'canonical_url' => (string) ($existing['canonical_url'] ?? ''),
                'breadcrumb_title' => (string) ($existing['breadcrumb_title'] ?? ''),
                'robots_index' => (int) ($existing['robots_index'] ?? 1),
                'robots_follow' => (int) ($existing['robots_follow'] ?? 1),
            ];
            $dirty = false;

            if (in_array($action, ['ai_blocks', 'full_boost'], true)) {
                $structuredDraft = $structuredGenerator->generate($existing);

                if (trim((string) ($existing['summary_html'] ?? '')) === '' && trim((string) ($structuredDraft['summary_html'] ?? '')) !== '') {
                    $payload['summary_html'] = (string) ($structuredDraft['summary_html'] ?? '');
                    $dirty = true;
                }

                $existingFaq = $structuredContent->decodeFaqJson((string) ($existing['faq_json'] ?? ''));
                if ($existingFaq === [] && !empty($structuredDraft['faq_items'])) {
                    $payload['faq_json'] = $structuredContent->encodeFaqItems((array) ($structuredDraft['faq_items'] ?? []));
                    $dirty = true;
                }
            }

            if (in_array($action, ['seo_basics', 'full_boost'], true)) {
                $seoDraft = $seoGenerator->generate($existing);

                if (trim((string) ($existing['excerpt'] ?? '')) === '' && trim((string) ($seoDraft['excerpt'] ?? '')) !== '') {
                    $payload['excerpt'] = (string) ($seoDraft['excerpt'] ?? '');
                    $dirty = true;
                }

                if (trim((string) ($existing['meta_title'] ?? '')) === '' && trim((string) ($seoDraft['meta_title'] ?? '')) !== '') {
                    $payload['meta_title'] = (string) ($seoDraft['meta_title'] ?? '');
                    $dirty = true;
                }

                if (trim((string) ($existing['meta_description'] ?? '')) === '' && trim((string) ($seoDraft['meta_description'] ?? '')) !== '') {
                    $payload['meta_description'] = (string) ($seoDraft['meta_description'] ?? '');
                    $dirty = true;
                }
            }

            if (!$dirty) {
                $skipped++;
                continue;
            }

            $contentRepository->updateTranslationAndSeo($contentTranslationId, $payload);
            $updated++;
        }

        $this->response->redirect('/admin/content-pipeline?' . http_build_query([
            'saved_batch' => '1',
            'batch_action' => $action,
            'updated' => $updated,
            'skipped' => $skipped,
            'limit' => $limit,
            'type' => $filters['content_type'],
            'lang' => $filters['language'],
            'focus' => $filters['focus'],
            'q' => $filters['query'],
        ]));
    }

    private function readFilters(): array
    {
        $contentType = trim((string) $this->request->input('type', 'all'));
        $language = trim((string) $this->request->input('lang', 'all'));
        $focus = trim((string) $this->request->input('focus', 'all'));
        $query = trim((string) $this->request->input('q', ''));

        if (!in_array($contentType, ['all', 'article', 'product_guide', 'page'], true)) {
            $contentType = 'all';
        }

        if (!in_array($language, ['all', 'hr', 'en', 'sl'], true)) {
            $language = 'all';
        }

        if (!in_array($focus, ['all', 'top_priority', 'seo_basics', 'ai_ready'], true)) {
            $focus = 'all';
        }

        return [
            'content_type' => $contentType,
            'language' => $language,
            'focus' => $focus,
            'query' => $query,
        ];
    }

    private function applyFocusFilter(array $rows, string $focus): array
    {
        return array_values(array_filter($rows, function (array $row) use ($focus): bool {
            return match ($focus) {
                'top_priority' => $this->isTopPriorityRow($row),
                'seo_basics' => $this->matchesBatchAction($row, 'seo_basics'),
                'ai_ready' => $this->matchesBatchAction($row, 'ai_blocks'),
                default => true,
            };
        }));
    }

    private function summarizePipeline(array $rows): array
    {
        $summary = [
            'scope_total' => count($rows),
            'auto_fixable_total' => 0,
            'top_priority_total' => 0,
        ];

        foreach ($rows as $row) {
            if ($this->matchesBatchAction($row, 'full_boost')) {
                $summary['auto_fixable_total']++;
            }

            if ($this->isTopPriorityRow($row)) {
                $summary['top_priority_total']++;
            }
        }

        return $summary;
    }

    private function matchesBatchAction(array $row, string $action): bool
    {
        return match ($action) {
            'ai_blocks' => $this->hasAnyIssue($row, ['missing_summary_block', 'missing_faq_block']),
            'seo_basics' => $this->hasAnyIssue($row, ['missing_excerpt', 'missing_custom_meta_title', 'missing_effective_description']),
            'full_boost' => $this->hasAnyIssue($row, ['missing_excerpt', 'missing_custom_meta_title', 'missing_effective_description', 'missing_summary_block', 'missing_faq_block']),
            default => false,
        };
    }

    private function isTopPriorityRow(array $row): bool
    {
        if ((int) ($row['seo_score'] ?? 100) <= 70) {
            return true;
        }

        foreach ((array) ($row['issues'] ?? []) as $issue) {
            if ((string) ($issue['severity'] ?? '') === 'high') {
                return true;
            }
        }

        return false;
    }

    private function hasAnyIssue(array $row, array $codes): bool
    {
        foreach ((array) ($row['issues'] ?? []) as $issue) {
            if (in_array((string) ($issue['code'] ?? ''), $codes, true)) {
                return true;
            }
        }

        return false;
    }

    private function renderRowsTable(array $rows): string
    {
        if ($rows === []) {
            return '<div class="admin-empty">Za odabrani filter trenutno nema sadržaja u ovom scopeu.</div>';
        }

        $html = '<div class="admin-table-wrap"><table class="admin-list-table"><thead><tr><th>Score</th><th>Sadržaj</th><th>Auto draft</th><th>Issuei</th><th>Akcije</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            $contentTranslationId = (int) ($row['content_translation_id'] ?? 0);
            $html .= '<tr>'
                . '<td><span class="' . $this->scoreClass((int) ($row['seo_score'] ?? 0)) . '">' . (int) ($row['seo_score'] ?? 0) . '</span></td>'
                . '<td><strong>' . htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong><br><span class="admin-muted">' . htmlspecialchars((string) ($row['route_path'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span><br><span class="admin-muted">' . htmlspecialchars(strtoupper((string) ($row['language_code'] ?? '')), ENT_QUOTES, 'UTF-8') . ' / ' . htmlspecialchars((string) ($row['content_type'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span></td>'
                . '<td>' . $this->renderAutoDraftBadges($row) . '</td>'
                . '<td>' . $this->renderIssueBadges((array) ($row['issues'] ?? [])) . '</td>'
                . '<td><div class="admin-actions"><a class="admin-button" href="/admin/content/edit?id=' . $contentTranslationId . '">Uredi</a><a class="admin-button" href="' . htmlspecialchars((string) ($row['route_path'] ?? '/'), ENT_QUOTES, 'UTF-8') . '">Javno</a></div></td>'
                . '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function renderAutoDraftBadges(array $row): string
    {
        $items = [];

        if ($this->hasAnyIssue($row, ['missing_excerpt'])) {
            $items[] = 'Excerpt';
        }

        if ($this->hasAnyIssue($row, ['missing_custom_meta_title'])) {
            $items[] = 'Meta title';
        }

        if ($this->hasAnyIssue($row, ['missing_effective_description'])) {
            $items[] = 'Meta description';
        }

        if ($this->hasAnyIssue($row, ['missing_summary_block'])) {
            $items[] = 'AI summary';
        }

        if ($this->hasAnyIssue($row, ['missing_faq_block'])) {
            $items[] = 'FAQ';
        }

        if ($items === []) {
            return '<span class="admin-badge admin-badge-good">Bez auto akcije</span>';
        }

        $html = '<div class="admin-badge-row">';

        foreach ($items as $item) {
            $html .= '<span class="admin-badge">' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</span>';
        }

        $html .= '</div>';

        return $html;
    }

    private function renderIssueBadges(array $issues): string
    {
        if ($issues === []) {
            return '<span class="admin-badge admin-badge-good">Spremno</span>';
        }

        $html = '<div class="admin-badge-row">';

        foreach ($issues as $issue) {
            $severity = (string) ($issue['severity'] ?? 'low');
            $class = match ($severity) {
                'high' => 'admin-badge admin-badge-high',
                'medium' => 'admin-badge admin-badge-medium',
                default => 'admin-badge',
            };

            $html .= '<span class="' . $class . '">' . htmlspecialchars((string) ($issue['label'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span>';
        }

        $html .= '</div>';

        return $html;
    }

    private function renderBatchActionCard(string $action, string $title, string $description, array $filters): string
    {
        return '<div class="admin-quick-row"><strong>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</strong>'
            . '<span>' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '</span>'
            . '<form method="post" action="/admin/content-pipeline/run" class="admin-inline-form">'
            . '<input type="hidden" name="action" value="' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '">'
            . '<input type="hidden" name="type" value="' . htmlspecialchars($filters['content_type'], ENT_QUOTES, 'UTF-8') . '">'
            . '<input type="hidden" name="lang" value="' . htmlspecialchars($filters['language'], ENT_QUOTES, 'UTF-8') . '">'
            . '<input type="hidden" name="focus" value="' . htmlspecialchars($filters['focus'], ENT_QUOTES, 'UTF-8') . '">'
            . '<input type="hidden" name="q" value="' . htmlspecialchars($filters['query'], ENT_QUOTES, 'UTF-8') . '">'
            . '<label style="min-width:140px">Koliko URL-ova<input type="number" min="1" max="60" name="limit" value="20"></label>'
            . '<button class="admin-button admin-button-primary" type="submit">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</button>'
            . '</form></div>';
    }

    private function renderNotice(): string
    {
        $savedBatch = trim((string) $this->request->input('saved_batch', ''));
        $error = trim((string) $this->request->input('error', ''));

        if ($savedBatch === '1') {
            $action = match ((string) $this->request->input('batch_action', 'full_boost')) {
                'ai_blocks' => 'AI blokovi',
                'seo_basics' => 'SEO osnove',
                default => 'puni boost',
            };

            return '<div class="admin-notice">Batch akcija <strong>' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '</strong> je završena. Ažurirano: <strong>' . (int) $this->request->input('updated', 0) . '</strong>, preskočeno: <strong>' . (int) $this->request->input('skipped', 0) . '</strong>.</div>';
        }

        if ($error !== '') {
            return '<div class="admin-notice admin-notice-error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
        }

        return '';
    }

    private function renderMetric(string $label, string $value): string
    {
        return '<div class="admin-kpi"><span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</strong></div>';
    }

    private function scoreClass(int $score): string
    {
        if ($score < 55) {
            return 'admin-score admin-score-low';
        }

        if ($score < 75) {
            return 'admin-score admin-score-medium';
        }

        return 'admin-score';
    }

    private function renderSelectOptions(array $options, string $selectedValue): string
    {
        $html = '';

        foreach ($options as $value => $label) {
            $html .= '<option value="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"' . ((string) $value === $selectedValue ? ' selected' : '') . '>' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</option>';
        }

        return $html;
    }
}
