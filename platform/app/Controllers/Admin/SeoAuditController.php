<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\SeoRepository;
use Avc\Services\Seo\SeoAuditService;
use Avc\Support\AdminPageRenderer;

final class SeoAuditController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $language = trim((string) $this->request->input('lang', ''));
        $contentType = trim((string) $this->request->input('type', ''));
        $query = trim((string) $this->request->input('q', ''));
        $repository = new SeoRepository($this->config);
        $auditService = new SeoAuditService();
        $rows = $auditService->decorateRows($repository->listAuditRows(
            $language !== 'all' ? $language : null,
            $contentType !== 'all' ? $contentType : null,
            $query,
            180
        ));
        $summary = $auditService->summarize($rows);

        $body = '<div class="admin-kpi-grid">'
            . $this->renderMetric('Prosječni score', (string) ($summary['average_score'] ?? 0))
            . $this->renderMetric('Treba doradu', (string) ($summary['needs_attention_total'] ?? 0))
            . $this->renderMetric('Tanki sadržaji', (string) ($summary['thin_content_total'] ?? 0))
            . $this->renderMetric('Slabi interni linkovi', (string) ($summary['low_internal_linking_total'] ?? 0))
            . $this->renderMetric('Bez excerpta', (string) ($summary['missing_excerpt_total'] ?? 0))
            . $this->renderMetric('Bez AI sažetka', (string) ($summary['missing_summary_block_total'] ?? 0))
            . $this->renderMetric('Bez FAQ bloka', (string) ($summary['missing_faq_block_total'] ?? 0))
            . '</div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Filteri</h2></div><div class="admin-panel-body"><form class="admin-filter-bar" method="get" action="/admin/seo-audit">'
            . '<label>Tip sadržaja<select name="type">' . $this->renderSelectOptions([
                'all' => 'Sve',
                'article' => 'Blog članci',
                'product_guide' => 'Product guideovi',
                'page' => 'Stranice',
            ], $contentType !== '' ? $contentType : 'all') . '</select></label>'
            . '<label>Jezik<select name="lang">' . $this->renderSelectOptions([
                'all' => 'Svi',
                'hr' => 'HR',
                'en' => 'EN',
                'sl' => 'SL',
            ], $language !== '' ? $language : 'all') . '</select></label>'
            . '<label>Pretraga<input type="text" name="q" value="' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . '" placeholder="Naslov, ruta ili meta title"></label>'
            . '<button class="admin-button admin-button-primary" type="submit">Filtriraj</button>'
            . '</form></div></div>'
            . '<div class="admin-card-grid">'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Brzi fokus</h2></div><div class="admin-panel-body"><p class="admin-muted">Prvo riješi stranice s najnižim scoreom, tankim sadržajem i slabim internim linkingom.</p></div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Batch obrada</h2></div><div class="admin-panel-body"><p class="admin-muted">Za više URL-ova koristi content pipeline, pa ručno doradi najvažnije stranice.</p><p><a class="admin-button" href="/admin/content-pipeline">Otvori pipeline</a></p></div></div>'
            . '</div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Prioritetne stranice</h2></div><div class="admin-panel-body">' . $this->renderAuditTable($rows) . '</div></div>';

        $this->response->html(AdminPageRenderer::render('SEO Audit', 'seo', $body, [
            'eyebrow' => 'SEO',
            'description' => 'Prioriteti dorade prema tekstu, meta signalima, internim linkovima, slikama i AI blokovima.',
            'actions' => '<a class="admin-button" href="/admin/content-pipeline">Pipeline</a><a class="admin-button" href="/admin/posts">Članci</a>',
        ]));
    }

    private function renderMetric(string $label, string $value): string
    {
        return '<div class="admin-kpi"><span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</strong></div>';
    }

    private function renderAuditTable(array $rows): string
    {
        if ($rows === []) {
            return '<div class="admin-empty">Nema sadržaja za zadane filtere.</div>';
        }

        $html = '<div class="admin-table-wrap"><table class="admin-list-table"><thead><tr><th>Score</th><th>Sadržaj</th><th>Jezik</th><th>Issuei</th><th>Signali</th><th>Akcija</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>'
                . '<td><span class="' . $this->scoreClass((int) ($row['seo_score'] ?? 0)) . '">' . (int) ($row['seo_score'] ?? 0) . '</span></td>'
                . '<td><strong>' . htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong><br><span class="admin-muted">' . htmlspecialchars((string) ($row['route_path'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span></td>'
                . '<td>' . htmlspecialchars(strtoupper((string) ($row['language_code'] ?? '')), ENT_QUOTES, 'UTF-8') . '<br><span class="admin-muted">' . htmlspecialchars((string) ($row['content_type'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span></td>'
                . '<td>' . $this->renderIssueBadges((array) ($row['issues'] ?? [])) . '</td>'
                . '<td><span class="admin-muted">Opis: ' . (int) ($row['effective_description_length'] ?? 0) . ' znakova</span><br><span class="admin-muted">Tekst: ' . (int) ($row['body_text_length'] ?? 0) . '</span><br><span class="admin-muted">Linkovi: ' . (int) ($row['internal_link_count'] ?? 0) . '</span></td>'
                . '<td>' . $this->renderActions($row) . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function renderActions(array $row): string
    {
        $contentTranslationId = (int) ($row['content_translation_id'] ?? 0);
        $hasStructuredGap = false;
        $hasSeoGap = false;

        foreach ((array) ($row['issues'] ?? []) as $issue) {
            $code = (string) ($issue['code'] ?? '');
            if (in_array($code, ['missing_summary_block', 'missing_faq_block'], true)) {
                $hasStructuredGap = true;
            }

            if (in_array($code, ['missing_excerpt', 'missing_custom_meta_title', 'missing_effective_description'], true)) {
                $hasSeoGap = true;
            }
        }

        $html = '<div class="admin-actions"><a class="admin-button" href="/admin/content/edit?id=' . $contentTranslationId . '">Uredi</a>'
            . '<a class="admin-button" href="' . htmlspecialchars((string) ($row['route_path'] ?? '/'), ENT_QUOTES, 'UTF-8') . '">Javno</a>';

        if ($hasStructuredGap || $hasSeoGap) {
            $html .= '<form method="post" action="/admin/content/generate-full-draft" class="admin-inline-form">'
                . '<input type="hidden" name="content_translation_id" value="' . $contentTranslationId . '">'
                . '<button class="admin-button" type="submit">Puni draft</button>'
                . '</form>';
        }

        if ($hasStructuredGap) {
            $html .= '<form method="post" action="/admin/content/generate-structured" class="admin-inline-form">'
                . '<input type="hidden" name="content_translation_id" value="' . $contentTranslationId . '">'
                . '<button class="admin-button" type="submit">AI draft</button>'
                . '</form>';
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
