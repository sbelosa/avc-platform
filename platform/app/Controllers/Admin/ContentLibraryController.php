<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\ContentRepository;
use Avc\Repositories\SeoRepository;
use Avc\Services\Seo\SeoAuditService;
use Avc\Support\AdminPageRenderer;

final class ContentLibraryController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function posts(): never
    {
        $this->renderLibrary('article', 'posts', 'Clanci', 'Urednicki pregled blog clanaka, SEO stanja i brzih akcija.');
    }

    public function products(): never
    {
        $this->renderLibrary('product_guide', 'products', 'Proizvodi', 'Product guideovi s preporukama, shop routingom i SEO stanjem.');
    }

    public function pages(): never
    {
        $this->renderLibrary('page', 'pages', 'Stranice', 'Stalne stranice, landing stranice i jezicne varijante.');
    }

    public function content(): never
    {
        $this->response->redirect('/admin/posts');
    }

    private function renderLibrary(string $contentType, string $activeItem, string $title, string $description): never
    {
        $language = $this->normalizedLanguage();
        $query = trim((string) $this->request->input('q', ''));
        $auditService = new SeoAuditService();
        $rows = $auditService->decorateRows((new SeoRepository($this->config))->listAuditRows(
            $language !== 'all' ? $language : null,
            $contentType,
            $query,
            160
        ));
        $summary = $this->summarizeRows($rows);
        $counts = $this->contentCounts();
        $basePath = $this->basePathForType($contentType);

        $body = $this->renderTabs($contentType, $counts)
            . '<section class="admin-kpi-grid">'
            . $this->renderKpi('U prikazu', (string) count($rows))
            . $this->renderKpi('SEO prosjek', (string) ($summary['average_score'] ?? 0))
            . $this->renderKpi('Treba paznju', (string) ($summary['needs_attention_total'] ?? 0))
            . $this->renderKpi('Bez slike', (string) ($summary['missing_featured_image_total'] ?? 0))
            . '</section>'
            . '<section class="admin-panel"><div class="admin-panel-header"><h2>Filteri</h2></div><div class="admin-panel-body">'
            . '<form class="admin-filter-bar" method="get" action="' . htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') . '">'
            . '<label>Jezik<select name="lang">' . $this->renderSelectOptions([
                'all' => 'Svi jezici',
                'hr' => 'HR',
                'en' => 'EN',
                'sl' => 'SL',
            ], $language) . '</select></label>'
            . '<label>Status<select name="status"><option selected>published</option></select></label>'
            . '<label>Pretraga<input type="search" name="q" value="' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . '" placeholder="Naslov, slug, URL ili meta opis"></label>'
            . '<button class="admin-button admin-button-primary" type="submit">Filtriraj</button>'
            . '</form></div></section>'
            . '<section class="admin-panel"><div class="admin-panel-header"><h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>'
            . '<span class="admin-muted">Prikazano najvise 160 rezultata</span></div>'
            . '<div class="admin-table-wrap">' . $this->renderTable($rows, $contentType) . '</div></section>';

        $actions = '<a class="admin-button" href="/admin/content-pipeline">Batch dorada</a>'
            . '<a class="admin-button" href="/admin/seo-audit?type=' . rawurlencode($contentType) . '">SEO audit</a>';

        $this->response->html(AdminPageRenderer::render($title, $activeItem, $body, [
            'eyebrow' => 'Sadrzaj',
            'description' => $description,
            'actions' => $actions,
        ]));
    }

    private function renderTabs(string $activeContentType, array $counts): string
    {
        $tabs = [
            'article' => ['/admin/posts', 'Clanci', (int) ($counts['article'] ?? 0)],
            'product_guide' => ['/admin/products', 'Proizvodi', (int) ($counts['product_guide'] ?? 0)],
            'page' => ['/admin/pages', 'Stranice', (int) ($counts['page'] ?? 0)],
        ];

        $html = '<nav class="admin-tabs" aria-label="Vrste sadrzaja">';

        foreach ($tabs as $type => [$href, $label, $count]) {
            $html .= '<a class="' . ($type === $activeContentType ? 'is-active' : '') . '" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ' <span class="admin-muted">(' . $count . ')</span></a>';
        }

        return $html . '</nav>';
    }

    private function renderTable(array $rows, string $contentType): string
    {
        if ($rows === []) {
            return '<div class="admin-empty">Nema sadrzaja za zadane filtere.</div>';
        }

        $html = '<table class="admin-list-table"><thead><tr>'
            . '<th style="width:34px"><input type="checkbox" aria-label="Odaberi sve"></th>'
            . '<th>Naslov</th>'
            . '<th style="width:110px">Status</th>'
            . '<th style="width:90px">Jezik</th>'
            . '<th style="width:90px">SEO</th>'
            . '<th>URL</th>'
            . '<th style="width:150px">Zadnja izmjena</th>'
            . '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $contentTranslationId = (int) ($row['content_translation_id'] ?? 0);
            $routePath = (string) ($row['route_path'] ?? '/');
            $title = (string) ($row['title'] ?? '');
            $status = (string) ($row['lifecycle_status'] ?? 'published');
            $score = (int) ($row['seo_score'] ?? 0);
            $updatedAt = (string) (($row['seo_updated_at'] ?? '') ?: ($row['translation_updated_at'] ?? ''));
            $issues = (array) ($row['issues'] ?? []);

            $html .= '<tr>'
                . '<td><input type="checkbox" aria-label="Odaberi ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"></td>'
                . '<td class="admin-title-cell"><strong>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</strong>'
                . '<div class="admin-muted">' . htmlspecialchars($this->typeLabel($contentType), ENT_QUOTES, 'UTF-8') . '</div>'
                . '<div class="admin-row-actions">'
                . '<a href="/admin/content/edit?id=' . $contentTranslationId . '">Uredi</a>'
                . '<a href="' . htmlspecialchars($routePath, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">Pregled</a>'
                . '<a href="/admin/seo-audit?type=' . rawurlencode($contentType) . '&q=' . rawurlencode($title) . '">SEO</a>'
                . ($contentType === 'product_guide' ? '<a href="/admin/product-routing">Routing</a>' : '')
                . '</div></td>'
                . '<td><span class="admin-status admin-status-' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</span></td>'
                . '<td>' . htmlspecialchars(strtoupper((string) ($row['language_code'] ?? '')), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . $this->renderScore($score, $issues) . '</td>'
                . '<td><code>' . htmlspecialchars($routePath, ENT_QUOTES, 'UTF-8') . '</code></td>'
                . '<td><span class="admin-muted">' . htmlspecialchars($this->formatDate($updatedAt), ENT_QUOTES, 'UTF-8') . '</span></td>'
                . '</tr>';
        }

        return $html . '</tbody></table>';
    }

    private function renderScore(int $score, array $issues): string
    {
        $class = $score < 70 ? 'admin-score-low' : ($score < 90 ? 'admin-score-medium' : '');
        $label = $issues === [] ? 'SEO OK' : implode(', ', array_slice(array_map(static fn (array $issue): string => (string) ($issue['label'] ?? ''), $issues), 0, 3));

        return '<span class="admin-score ' . $class . '" title="' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '">' . $score . '</span>';
    }

    private function renderKpi(string $label, string $value): string
    {
        return '<div class="admin-kpi"><span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</strong></div>';
    }

    private function summarizeRows(array $rows): array
    {
        if ($rows === []) {
            return [
                'average_score' => 0,
                'needs_attention_total' => 0,
                'missing_featured_image_total' => 0,
            ];
        }

        $scoreTotal = 0;
        $needsAttention = 0;
        $missingImage = 0;

        foreach ($rows as $row) {
            $score = (int) ($row['seo_score'] ?? 0);
            $scoreTotal += $score;

            if ($score < 90 || !empty($row['issues'])) {
                $needsAttention++;
            }

            if (trim((string) ($row['featured_image_path'] ?? '')) === '') {
                $missingImage++;
            }
        }

        return [
            'average_score' => (int) round($scoreTotal / count($rows)),
            'needs_attention_total' => $needsAttention,
            'missing_featured_image_total' => $missingImage,
        ];
    }

    private function contentCounts(): array
    {
        return (new ContentRepository($this->config))->countByContentTypeForAdmin();
    }

    private function normalizedLanguage(): string
    {
        $language = trim((string) $this->request->input('lang', 'all'));

        return in_array($language, ['all', 'hr', 'en', 'sl'], true) ? $language : 'all';
    }

    private function basePathForType(string $contentType): string
    {
        return match ($contentType) {
            'product_guide' => '/admin/products',
            'page' => '/admin/pages',
            default => '/admin/posts',
        };
    }

    private function typeLabel(string $contentType): string
    {
        return match ($contentType) {
            'product_guide' => 'Product guide',
            'page' => 'Stranica',
            default => 'Clanak',
        };
    }

    private function formatDate(string $value): string
    {
        if (trim($value) === '') {
            return '-';
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? $value : date('d.m.Y H:i', $timestamp);
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
