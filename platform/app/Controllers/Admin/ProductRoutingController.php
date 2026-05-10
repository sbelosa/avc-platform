<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\AnalyticsRepository;
use Avc\Repositories\ProductRecommendationRepository;
use Avc\Services\Auth\AdminAuthService;
use Avc\Services\Referral\FccMarketSuggestionService;
use Avc\Support\AdminPageRenderer;

final class ProductRoutingController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $analytics = (new AnalyticsRepository($this->config))->getDashboardSnapshot();
        $report = $this->loadLatestReport();
        $repository = new ProductRecommendationRepository($this->config);
        $unmatchedGroups = $repository->listRoutingGroups(true, false, 30);
        $manualGroups = $repository->listRoutingGroups(false, true, 20);
        $saved = (string) $this->request->input('saved', '');
        $notice = $saved === '1' ? '<div class="admin-notice">Ručni market override je spremljen i sada se koristi u redirect engineu.</div>' : '';

        $body = $notice
            . '<div class="admin-kpi-grid">'
            . $this->renderMetric('Recommendation redci', (string) ($analytics['product_recommendation_total'] ?? 0))
            . $this->renderMetric('Market-ready redci', (string) ($analytics['product_market_ready_total'] ?? 0))
            . $this->renderMetric('Ručni product groupovi', (string) ($analytics['manual_market_override_group_total'] ?? 0))
            . $this->renderMetric('Posljednji FCC sync', htmlspecialchars((string) ($report['generated_at'] ?? 'n/a'), ENT_QUOTES, 'UTF-8'))
            . '</div>'
            . '<div class="admin-card-grid">'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Kako radi</h2></div><div class="admin-panel-body"><p class="admin-muted">AVC prvo koristi sinkronizirane market URL-ove iz FCC-a, a ručni override ima prednost kad postoji.</p></div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Zašto je važno</h2></div><div class="admin-panel-body"><p class="admin-muted">Kad product guide ima resolved market URL, CTA vodi na pravi shop tok umjesto na fallback.</p></div></div>'
            . '</div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Neusklađeni product groupovi</h2></div><div class="admin-panel-body"><p class="admin-muted">Proizvodi koji još nemaju nijedan resolved market URL.</p>' . $this->renderRoutingGroupTable($unmatchedGroups, true) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Ručno spašeni proizvodi</h2></div><div class="admin-panel-body"><p class="admin-muted">Product groupovi gdje je barem jedan market URL ručno dodan kroz admin.</p>' . $this->renderRoutingGroupTable($manualGroups, false) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>FCC match strategije</h2></div><div class="admin-panel-body">' . $this->renderStrategyTable((array) ($report['match_strategy_stats'] ?? [])) . '</div></div>';

        $this->response->html(AdminPageRenderer::render('Product Routing', 'routing', $body, [
            'eyebrow' => 'Market coverage',
            'description' => 'Pregled product mappinga, FCC matcha i ručnih market overrideova.',
            'actions' => '<a class="admin-button" href="/admin/products">Proizvodi</a>',
        ]));
    }

    public function edit(): never
    {
        $productRecommendationId = (int) $this->request->input('id', 0);
        $group = (new ProductRecommendationRepository($this->config))->findRoutingGroupByRecommendationId($productRecommendationId);

        if ($group === null) {
            $this->response->redirect('/admin/product-routing');
        }

        $saved = (string) $this->request->input('saved', '');
        $error = trim((string) $this->request->input('error', ''));
        $suggestions = (new FccMarketSuggestionService($this->config))->suggestForGroup($group, 5);
        $notice = $saved === '1'
            ? '<div class="admin-notice">Override je spremljen. Redirect engine od sada koristi novu market mapu za ovaj proizvod.</div>'
            : ($error !== '' ? '<div class="admin-notice admin-notice-error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>' : '');

        $body = $notice
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Osnovni podaci</h2></div><div class="admin-panel-body">'
            . '<table class="admin-list-table"><tbody>'
            . '<tr><td>Translation group</td><td>' . (int) ($group['translation_group_id'] ?? 0) . '</td></tr>'
            . '<tr><td>Primarni route</td><td><a href="' . htmlspecialchars((string) ($group['route_path'] ?? '/'), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) ($group['route_path'] ?? '/'), ENT_QUOTES, 'UTF-8') . '</a></td></tr>'
            . '<tr><td>SKU</td><td>' . htmlspecialchars((string) ($group['sku'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td></tr>'
            . '<tr><td>Destination strategy</td><td>' . htmlspecialchars((string) ($group['destination_strategy'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td></tr>'
            . '</tbody></table></div></div>'
            . '<div class="admin-card-grid">'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Jezične verzije</h2></div><div class="admin-panel-body">' . $this->renderTranslationsTable((array) ($group['translations'] ?? [])) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Sinkronizirani URL-ovi</h2></div><div class="admin-panel-body">' . $this->renderMarketUrlTable((array) ($group['synced_market_urls'] ?? []), 'Za ovaj proizvod još nema automatski sinkroniziranih market URL-ova.') . '</div></div>'
            . '</div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Ručni override</h2></div><div class="admin-panel-body"><p class="admin-muted">Unesi jedan market po retku u formatu <strong>HR=https://example.com</strong>. Prazan unos briše sve ručne overrideove za ovu grupu proizvoda.</p>'
            . '<form method="post" action="/admin/product-routing/save" class="admin-form-grid">'
            . '<input type="hidden" name="product_recommendation_id" value="' . (int) ($group['product_recommendation_id'] ?? 0) . '">'
            . '<label style="grid-column:1/-1">Manual market URLs<textarea name="manual_market_urls" placeholder="HR=https://...\nSI=https://...\nDE=https://...">' . htmlspecialchars($this->formatManualMarketUrls((array) ($group['manual_market_urls'] ?? [])), ENT_QUOTES, 'UTF-8') . '</textarea></label>'
            . '<button class="admin-button admin-button-primary" type="submit">Spremi override</button>'
            . '</form></div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>FCC prijedlozi</h2></div><div class="admin-panel-body"><p class="admin-muted">Najvjerojatniji matchovi iz FCC kataloga. Primjena sprema njihove market URL-ove kao ručni override za cijelu product grupu.</p>' . $this->renderSuggestionTable($suggestions, (int) ($group['product_recommendation_id'] ?? 0)) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Resolved market URL-ovi</h2></div><div class="admin-panel-body"><p class="admin-muted">Finalna mapa koju redirect engine koristi nakon spajanja automatskih i ručnih unosa.</p>' . $this->renderMarketUrlTable((array) ($group['market_urls'] ?? []), 'Finalna market mapa je još prazna.') . '</div></div>';

        $this->response->html(AdminPageRenderer::render((string) ($group['title'] ?? 'Product routing'), 'routing', $body, [
            'eyebrow' => 'Routing editor',
            'description' => 'Ručni market override za HR, EN i SL verzije iste product grupe.',
            'actions' => '<a class="admin-button" href="/admin/product-routing">Natrag na routing</a>',
        ]));
    }

    public function update(): never
    {
        $productRecommendationId = (int) $this->request->input('product_recommendation_id', 0);
        $returnTo = $this->sanitizeAdminReturnPath((string) $this->request->input('return_to', ''));
        $repository = new ProductRecommendationRepository($this->config);
        $group = $repository->findRoutingGroupByRecommendationId($productRecommendationId);

        if ($group === null) {
            $this->response->redirect('/admin/product-routing');
        }

        [$manualOverrides, $errors] = $this->parseManualMarketUrls((string) $this->request->input('manual_market_urls', ''));
        if ($errors !== []) {
            $errorMessage = implode(' ', $errors);
            $this->response->redirect($returnTo !== ''
                ? $this->appendQueryParameter($returnTo, 'error', $errorMessage)
                : '/admin/product-routing/edit?id=' . (int) ($group['product_recommendation_id'] ?? 0) . '&error=' . rawurlencode($errorMessage));
        }

        $adminUser = (new AdminAuthService($this->config, $this->request))->user();
        $repository->replaceManualMarketOverrides(
            (int) ($group['translation_group_id'] ?? 0),
            $manualOverrides,
            isset($adminUser['admin_user_id']) ? (int) $adminUser['admin_user_id'] : null
        );

        $this->response->redirect($returnTo !== ''
            ? $this->appendQueryParameter($returnTo, 'saved', 'routing')
            : '/admin/product-routing/edit?id=' . (int) ($group['product_recommendation_id'] ?? 0) . '&saved=1');
    }

    public function applySuggestion(): never
    {
        $productRecommendationId = (int) $this->request->input('product_recommendation_id', 0);
        $recordKey = trim((string) $this->request->input('record_key', ''));
        $repository = new ProductRecommendationRepository($this->config);
        $group = $repository->findRoutingGroupByRecommendationId($productRecommendationId);

        if ($group === null) {
            $this->response->redirect('/admin/product-routing');
        }

        $record = (new FccMarketSuggestionService($this->config))->findRecordByKey($recordKey);
        if ($record === null) {
            $this->response->redirect('/admin/product-routing/edit?id=' . (int) ($group['product_recommendation_id'] ?? 0) . '&error=' . rawurlencode('FCC prijedlog nije pronađen.'));
        }

        $adminUser = (new AdminAuthService($this->config, $this->request))->user();
        $repository->replaceManualMarketOverrides(
            (int) ($group['translation_group_id'] ?? 0),
            (array) ($record['market_urls'] ?? []),
            isset($adminUser['admin_user_id']) ? (int) $adminUser['admin_user_id'] : null
        );

        $this->response->redirect('/admin/product-routing/edit?id=' . (int) ($group['product_recommendation_id'] ?? 0) . '&saved=1');
    }

    private function loadLatestReport(): array
    {
        $reportPath = rtrim((string) ($this->config['storage_path'] ?? ''), '/') . '/reports/product_market_link_sync_report.json';

        if ($reportPath === '' || !is_file($reportPath)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($reportPath), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function renderMetric(string $label, string $value): string
    {
        return '<div class="admin-kpi"><span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</strong></div>';
    }

    private function renderStrategyTable(array $rows): string
    {
        if ($rows === []) {
            return '<p class="admin-muted">Sync report još nije dostupan.</p>';
        }

        $html = '<table class="admin-list-table"><thead><tr><th>Strategija</th><th>Ukupno</th></tr></thead><tbody>';

        foreach ($rows as $strategy => $total) {
            $html .= '<tr><td>' . htmlspecialchars((string) $strategy, ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars((string) $total, ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function renderRoutingGroupTable(array $groups, bool $showEmptyState): string
    {
        if ($groups === []) {
            return $showEmptyState
                ? '<div class="admin-empty">Trenutno nema otvorenih groupova bez market URL-a. To je odličan znak.</div>'
                : '<div class="admin-empty">Još nema ručno spremljenih overrideova.</div>';
        }

        $html = '<div class="admin-table-wrap"><table class="admin-list-table"><thead><tr><th>Proizvod</th><th>Ruta</th><th>Auto</th><th>Ručno</th><th>Finalno</th><th>Akcija</th></tr></thead><tbody>';

        foreach ($groups as $group) {
            $html .= '<tr>'
                . '<td><strong>' . htmlspecialchars((string) ($group['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong><br><span class="admin-muted">TG #' . (int) ($group['translation_group_id'] ?? 0) . '</span></td>'
                . '<td>' . htmlspecialchars((string) ($group['route_path'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . (int) ($group['synced_market_count'] ?? 0) . '</td>'
                . '<td>' . (int) ($group['manual_override_count'] ?? 0) . '</td>'
                . '<td>' . (int) ($group['resolved_market_count'] ?? 0) . '</td>'
                . '<td><a class="admin-button" href="/admin/product-routing/edit?id=' . (int) ($group['product_recommendation_id'] ?? 0) . '">Uredi mapping</a></td>'
                . '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function renderMarketUrlTable(array $marketUrls, string $emptyText): string
    {
        if ($marketUrls === []) {
            return '<p class="admin-muted">' . htmlspecialchars($emptyText, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        $html = '<div class="admin-table-wrap"><table class="admin-list-table"><thead><tr><th>Market</th><th>Destination URL</th></tr></thead><tbody>';

        foreach ($marketUrls as $marketCode => $destinationUrl) {
            $html .= '<tr><td>' . htmlspecialchars(strtoupper((string) $marketCode), ENT_QUOTES, 'UTF-8') . '</td><td><a href="' . htmlspecialchars((string) $destinationUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) $destinationUrl, ENT_QUOTES, 'UTF-8') . '</a></td></tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function renderTranslationsTable(array $translations): string
    {
        if ($translations === []) {
            return '<p class="admin-muted">Nema spremljenih prijevoda za ovu product grupu.</p>';
        }

        $html = '<table class="admin-list-table"><thead><tr><th>Jezik</th><th>Naslov</th><th>Ruta</th></tr></thead><tbody>';

        foreach ($translations as $translation) {
            $html .= '<tr>'
                . '<td>' . htmlspecialchars(strtoupper((string) ($translation['language_code'] ?? '')), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars((string) ($translation['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars((string) ($translation['route_path'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function renderSuggestionTable(array $suggestions, int $productRecommendationId): string
    {
        if ($suggestions === []) {
            return '<p class="admin-muted">Trenutno nema dovoljno jakih FCC prijedloga za ovaj proizvod.</p>';
        }

        $html = '<div class="admin-table-wrap"><table class="admin-list-table"><thead><tr><th>FCC proizvod</th><th>Slug</th><th>Score</th><th>Tržišta</th><th>Razlozi</th><th>Akcija</th></tr></thead><tbody>';

        foreach ($suggestions as $suggestion) {
            $html .= '<tr>'
                . '<td><strong>' . htmlspecialchars((string) ($suggestion['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong><br><span class="admin-muted">' . htmlspecialchars(strtoupper((string) ($suggestion['language_code'] ?? '')), ENT_QUOTES, 'UTF-8') . '</span></td>'
                . '<td>' . htmlspecialchars((string) ($suggestion['slug'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . (int) ($suggestion['score'] ?? 0) . '</td>'
                . '<td>' . (int) ($suggestion['market_count'] ?? 0) . '</td>'
                . '<td>' . htmlspecialchars(implode(', ', array_map('strval', (array) ($suggestion['reasons'] ?? []))), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td><form method="post" action="/admin/product-routing/apply-suggestion" class="admin-inline-form">'
                . '<input type="hidden" name="product_recommendation_id" value="' . $productRecommendationId . '">'
                . '<input type="hidden" name="record_key" value="' . htmlspecialchars((string) ($suggestion['record_key'] ?? ''), ENT_QUOTES, 'UTF-8') . '">'
                . '<button class="admin-button" type="submit">Primijeni</button>'
                . '</form></td>'
                . '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function formatManualMarketUrls(array $manualMarketUrls): string
    {
        if ($manualMarketUrls === []) {
            return '';
        }

        $lines = [];

        foreach ($manualMarketUrls as $marketCode => $destinationUrl) {
            $lines[] = strtoupper((string) $marketCode) . '=' . (string) $destinationUrl;
        }

        return implode("\n", $lines);
    }

    private function parseManualMarketUrls(string $input): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $input) ?: [];
        $marketUrls = [];
        $errors = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (!preg_match('/^([A-Za-z0-9_-]{2,10})\s*[:=]\s*(https?:\/\/\S+)$/', $line, $matches)) {
                $errors[] = 'Redak ' . ($index + 1) . ' nije u ispravnom formatu.';
                continue;
            }

            $marketCode = strtolower(trim((string) ($matches[1] ?? '')));
            $destinationUrl = trim((string) ($matches[2] ?? ''));

            if (!filter_var($destinationUrl, FILTER_VALIDATE_URL)) {
                $errors[] = 'URL na retku ' . ($index + 1) . ' nije valjan.';
                continue;
            }

            $marketUrls[$marketCode] = $destinationUrl;
        }

        ksort($marketUrls);

        return [$marketUrls, $errors];
    }

    private function sanitizeAdminReturnPath(string $returnTo): string
    {
        $returnTo = trim($returnTo);

        if ($returnTo === '' || str_contains($returnTo, "\n") || str_contains($returnTo, "\r")) {
            return '';
        }

        if (!str_starts_with($returnTo, '/admin/') || str_starts_with($returnTo, '//')) {
            return '';
        }

        return $returnTo;
    }

    private function appendQueryParameter(string $path, string $key, string $value): string
    {
        return $path . (str_contains($path, '?') ? '&' : '?') . rawurlencode($key) . '=' . rawurlencode($value);
    }
}
