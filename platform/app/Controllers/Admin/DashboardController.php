<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\AiLeadRepository;
use Avc\Repositories\AnalyticsRepository;
use Avc\Repositories\DiscountLeadRepository;
use Avc\Repositories\SeoRepository;
use Avc\Repositories\SettingsRepository;
use Avc\Services\Referral\ActiveForeverIdService;
use Avc\Services\Seo\SeoAuditService;
use Avc\Support\AdminPageRenderer;

final class DashboardController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $foreverIdService = new ActiveForeverIdService($this->config);
        $referralSettings = (new SettingsRepository($this->config))->getReferralSettings();
        $analytics = (new AnalyticsRepository($this->config))->getDashboardSnapshot();
        $leadRepository = new AiLeadRepository($this->config);
        $latestLeads = $leadRepository->latest(10);
        $leadSummary = $leadRepository->summarizeForAdmin();
        $discountSummary = (new DiscountLeadRepository($this->config))->summarizeForAdmin();
        $seoAuditService = new SeoAuditService();
        $seoSummary = $seoAuditService->summarize($seoAuditService->decorateRows((new SeoRepository($this->config))->listAuditRows(null, null, '', 1500)));
        $saved = (string) $this->request->input('saved', '');
        $notice = $saved === '1' ? '<div class="admin-notice">Referral postavke su spremljene.</div>' : '';

        $body = $notice
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Globalne referral postavke</h2></div><div class="admin-panel-body"><p class="admin-muted">Promjena aktivnog Forever ID-ja ovdje postaje centralna postavka za novu AVC platformu.</p>'
            . '<form method="post" action="/admin/settings/referral" class="admin-form-grid">'
            . '<label>Active Forever ID<input type="text" name="active_forever_id" value="' . htmlspecialchars($foreverIdService->getActiveForeverId(), ENT_QUOTES, 'UTF-8') . '" placeholder="Upiši Forever ID"></label>'
            . '<label>Admin email za AI leadove<input type="email" name="admin_notification_email" value="' . htmlspecialchars($foreverIdService->getAdminNotificationEmail(), ENT_QUOTES, 'UTF-8') . '" placeholder="admin@example.com"></label>'
            . '<label>15% popust<input type="number" min="1" max="80" name="fcc_discount_percent" value="' . (int) ($referralSettings['fcc_discount_percent'] ?? 15) . '"></label>'
            . '<label>FCC short URL<input type="url" name="fcc_short_url" value="' . htmlspecialchars((string) ($referralSettings['fcc_short_url'] ?? ''), ENT_QUOTES, 'UTF-8') . '" placeholder="https://thealoeveraco.shop/..."></label>'
            . '<label>FCC shortenUrl param<input type="text" name="fcc_shorten_url" value="' . htmlspecialchars((string) ($referralSettings['fcc_shorten_url'] ?? ''), ENT_QUOTES, 'UTF-8') . '" placeholder="thealoeveraco.shop/..."></label>'
            . '<label>FCC referralUuid<input type="text" name="fcc_referral_uuid" value="' . htmlspecialchars((string) ($referralSettings['fcc_referral_uuid'] ?? ''), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label>FCC uniqueExtRefID<input type="text" name="fcc_unique_ext_ref_id" value="' . htmlspecialchars((string) ($referralSettings['fcc_unique_ext_ref_id'] ?? ''), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label>discountConfigType<input type="text" name="fcc_discount_config_type" value="' . htmlspecialchars((string) ($referralSettings['fcc_discount_config_type'] ?? '11'), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label>FCC title<input type="text" name="fcc_title" value="' . htmlspecialchars((string) ($referralSettings['fcc_title'] ?? 'FCC'), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label>Napomena promjene<input type="text" name="change_note" placeholder="npr. privremeno prebacivanje referral ID-ja"></label>'
            . '<label><span>Popust aktivan</span><select name="fcc_discount_enabled">' . $this->renderSettingsSelect(['1' => 'Da', '0' => 'Ne'], (bool) ($referralSettings['fcc_discount_enabled'] ?? true) ? '1' : '0') . '</select></label>'
            . '<button class="admin-button admin-button-primary" type="submit">Spremi postavke</button>'
            . '</form></div></div>'
            . '<div class="admin-kpi-grid">'
            . $this->renderMetric('Sadržaji', (string) $analytics['content_total'])
            . $this->renderMetric('Rute', (string) $analytics['route_total'])
            . $this->renderMetric('Klikovi', (string) $analytics['click_total'])
            . $this->renderMetric('Leadovi', (string) $analytics['lead_total'])
            . $this->renderMetric('Ručni product groupovi', (string) ($analytics['manual_market_override_group_total'] ?? 0))
            . $this->renderMetric('SEO avg score', (string) ($seoSummary['average_score'] ?? 0))
            . $this->renderMetric('SEO treba doradu', (string) ($seoSummary['needs_attention_total'] ?? 0))
            . $this->renderMetric('Bez excerpta', (string) ($seoSummary['missing_excerpt_total'] ?? 0))
            . $this->renderMetric('Bez AI summaryja', (string) ($seoSummary['missing_summary_block_total'] ?? 0))
            . $this->renderMetric('Bez FAQ bloka', (string) ($seoSummary['missing_faq_block_total'] ?? 0))
            . $this->renderMetric('Leadovi novi', (string) ($leadSummary['new_total'] ?? 0))
            . $this->renderMetric('Popust leadovi', (string) ($discountSummary['total'] ?? 0))
            . $this->renderMetric('Popust novi', (string) ($discountSummary['new_total'] ?? 0))
            . '</div>'
            . '<div class="admin-card-grid">'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Content ops</h2></div><div class="admin-panel-body"><p class="admin-muted">Najbrži put za sređivanje većeg broja članaka ide kroz content pipeline.</p><p><a class="admin-button" href="/admin/content-pipeline">Otvori pipeline</a></p></div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Lead inbox</h2></div><div class="admin-panel-body"><p class="admin-muted">Novi: <strong>' . (int) ($leadSummary['new_total'] ?? 0) . '</strong><br>Kontaktirani: <strong>' . (int) ($leadSummary['contacted_total'] ?? 0) . '</strong><br>Kvalificirani: <strong>' . (int) ($leadSummary['qualified_total'] ?? 0) . '</strong></p><p><a class="admin-button" href="/admin/ai-leads">Otvori leadove</a></p></div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>15% popust inbox</h2></div><div class="admin-panel-body"><p class="admin-muted">Novi: <strong>' . (int) ($discountSummary['new_total'] ?? 0) . '</strong><br>Poslani u shop: <strong>' . (int) ($discountSummary['sent_total'] ?? 0) . '</strong><br>Kontaktirani: <strong>' . (int) ($discountSummary['contacted_total'] ?? 0) . '</strong></p><p><a class="admin-button" href="/admin/discount-leads">Otvori popuste</a></p></div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Market routing</h2></div><div class="admin-panel-body"><p class="admin-muted">Market-ready mapping: <strong>' . (int) ($analytics['product_market_ready_total'] ?? 0) . '</strong> / ' . (int) ($analytics['product_recommendation_total'] ?? 0) . ' recommendation zapisa.</p><p><a class="admin-button" href="/admin/product-routing">Otvori routing</a></p></div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>SEO audit</h2></div><div class="admin-panel-body"><p class="admin-muted">Tanki sadržaji: <strong>' . (int) ($seoSummary['thin_content_total'] ?? 0) . '</strong><br>Slabi interni linkovi: <strong>' . (int) ($seoSummary['low_internal_linking_total'] ?? 0) . '</strong><br>Bez slike: <strong>' . (int) ($seoSummary['missing_featured_image_total'] ?? 0) . '</strong><br>Bez meta opisa: <strong>' . (int) ($seoSummary['missing_effective_description_total'] ?? 0) . '</strong></p><p><a class="admin-button" href="/admin/seo-audit">Otvori audit</a></p></div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>CTA pozicije</h2></div><div class="admin-panel-body"><p class="admin-muted">Gdje se nalazio gumb koji je poslao korisnika prema Forever shopu.</p>' . $this->renderClickSourceTable($analytics['clicks_by_cta_position'] ?? $analytics['clicks_by_source'] ?? []) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>CTA tekstovi</h2></div><div class="admin-panel-body"><p class="admin-muted">Koji tekst gumba i pozicija dobivaju najviše klikova.</p>' . $this->renderCtaVariantTable($analytics['clicks_by_cta_variant'] ?? []) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Market klikovi</h2></div><div class="admin-panel-body"><p class="admin-muted">Koji market redirect najčešće dobiva klik.</p>' . $this->renderSimpleTable($analytics['clicks_by_market'] ?? [], 'destination_market_code', 'total') . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Top proizvodi po Forever kliku</h2></div><div class="admin-panel-body"><p class="admin-muted">Koji proizvod korisnik najčešće otvara prema službenom Forever shopu.</p>' . $this->renderClickedProductsTable($analytics['top_clicked_products'] ?? []) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Top članci po Forever kliku</h2></div><div class="admin-panel-body"><p class="admin-muted">Članci koji najviše vode korisnika prema kupnji ili popust linku.</p>' . $this->renderOutboundSourceTable($analytics['top_outbound_article_sources'] ?? []) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Top product stranice po Forever kliku</h2></div><div class="admin-panel-body"><p class="admin-muted">Product guide stranice koje najbolje pretvaraju čitanje u odlazak prema Forever shopu.</p>' . $this->renderOutboundSourceTable($analytics['top_outbound_product_sources'] ?? []) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Top države</h2></div><div class="admin-panel-body">' . $this->renderSimpleTable($analytics['top_countries'], 'country_code', 'total') . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Top sadržaj</h2></div><div class="admin-panel-body">' . $this->renderSimpleTable($analytics['top_content'], 'source_path', 'total') . '</div></div>'
            . '</div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Nedavni AI leadovi</h2></div><div class="admin-panel-body">' . $this->renderLeadsTable($latestLeads) . '</div></div>';

        $this->response->html(AdminPageRenderer::render('Nadzorna', 'dashboard', $body, [
            'eyebrow' => 'AVC Admin',
            'description' => 'Referral postavke, sadržaj, SEO, leadovi i routing na jednom mjestu.',
            'actions' => '<a class="admin-button" href="/" target="_blank" rel="noopener">Pogledaj site</a>',
        ]));
    }

    public function updateReferral(): never
    {
        $activeForeverId = trim((string) $this->request->input('active_forever_id', ''));
        $adminNotificationEmail = trim((string) $this->request->input('admin_notification_email', ''));
        $changeNote = trim((string) $this->request->input('change_note', '')) ?: null;

        if ($adminNotificationEmail === '' || !filter_var($adminNotificationEmail, FILTER_VALIDATE_EMAIL)) {
            $this->response->redirect('/admin?saved=0');
        }

        (new ActiveForeverIdService($this->config))->updateDiscountSettings([
            'active_forever_id' => $activeForeverId,
            'admin_notification_email' => $adminNotificationEmail,
            'fcc_discount_enabled' => (string) $this->request->input('fcc_discount_enabled', '0') === '1',
            'fcc_discount_percent' => (int) $this->request->input('fcc_discount_percent', 15),
            'fcc_short_url' => trim((string) $this->request->input('fcc_short_url', '')),
            'fcc_shorten_url' => trim((string) $this->request->input('fcc_shorten_url', '')),
            'fcc_referral_uuid' => trim((string) $this->request->input('fcc_referral_uuid', '')),
            'fcc_unique_ext_ref_id' => trim((string) $this->request->input('fcc_unique_ext_ref_id', '')),
            'fcc_discount_config_type' => trim((string) $this->request->input('fcc_discount_config_type', '11')),
            'fcc_title' => trim((string) $this->request->input('fcc_title', 'FCC')),
        ], null, $changeNote);

        $this->response->redirect('/admin?saved=1');
    }

    private function renderMetric(string $label, string $value): string
    {
        return '<div class="admin-kpi"><span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</strong></div>';
    }

    private function renderSettingsSelect(array $options, string $selectedValue): string
    {
        $html = '';

        foreach ($options as $value => $label) {
            $html .= '<option value="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"' . ((string) $value === $selectedValue ? ' selected' : '') . '>' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</option>';
        }

        return $html;
    }

    private function renderSimpleTable(array $rows, string $labelKey, string $valueKey): string
    {
        if ($rows === []) {
            return '<p class="admin-muted">Još nema podataka.</p>';
        }

        $html = '<table class="admin-list-table"><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr><td>' . htmlspecialchars((string) ($row[$labelKey] ?? '-'), ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars((string) ($row[$valueKey] ?? '0'), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function renderLeadsTable(array $rows): string
    {
        if ($rows === []) {
            return '<div class="admin-empty">Leadovi će se pojaviti ovdje čim AI savjetnik primi prvi upit.</div>';
        }

        $html = '<div class="admin-table-wrap"><table class="admin-list-table"><thead><tr><th>Kontakt</th><th>Pitanje</th><th>Izvor</th><th>Vrijeme</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            $contact = trim((string) ($row['name'] ?? '')) !== '' ? (string) $row['name'] : (string) ($row['email'] ?? '-');
            $question = mb_strimwidth(trim((string) ($row['lead_question'] ?? '')), 0, 120, '…');
            $html .= '<tr><td>' . htmlspecialchars($contact, ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars($question, ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars((string) ($row['source_path'] ?? '-'), ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars((string) ($row['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function renderClickSourceTable(array $rows): string
    {
        if ($rows === []) {
            return '<p class="admin-muted">Još nema CTA klikova.</p>';
        }

        $html = '<table class="admin-list-table"><thead><tr><th>Pozicija CTA-a</th><th>Ključ</th><th>Klikovi</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            $source = (string) ($row['cta_position'] ?? $row['click_source'] ?? 'content_cta');
            $html .= '<tr><td>' . htmlspecialchars($this->clickSourceLabel($source), ENT_QUOTES, 'UTF-8') . '</td><td><code>' . htmlspecialchars($source, ENT_QUOTES, 'UTF-8') . '</code></td><td>' . (int) ($row['total'] ?? 0) . '</td></tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function renderCtaVariantTable(array $rows): string
    {
        if ($rows === []) {
            return '<p class="admin-muted">Varijante teksta će se pojaviti nakon novih klikova.</p>';
        }

        $html = '<table class="admin-list-table"><thead><tr><th>Tekst gumba</th><th>Pozicija</th><th>Varijanta</th><th>Klikovi</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            $position = (string) ($row['cta_position'] ?? 'content_cta');
            $variant = (string) ($row['cta_variant'] ?? 'unknown');
            $label = (string) ($row['cta_label'] ?? $variant);
            $html .= '<tr><td>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars($this->clickSourceLabel($position), ENT_QUOTES, 'UTF-8') . '</td><td><code>' . htmlspecialchars($variant, ENT_QUOTES, 'UTF-8') . '</code></td><td>' . (int) ($row['total'] ?? 0) . '</td></tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function clickSourceLabel(string $source): string
    {
        return [
            'product_hero_cta' => 'Product hero glavni gumb',
            'product_hero_panel_cta' => 'Product hero slika i panel',
            'product_summary_cta' => 'Kupovni blok nakon sažetka',
            'product_body_cta' => 'Kupovni blok nakon teksta',
            'product_sidebar_cta' => 'Sidebar kupovni blok',
            'product_sticky_mobile' => 'Mobilni sticky shop',
            'product_legacy_body_link' => 'Stari shop link u proizvodu',
            'article_top_products' => 'Članak preporuke iznad teksta',
            'article_bottom_products' => 'Članak preporuke ispod teksta',
            'article_legacy_body_link' => 'Stari shop link u članku',
            'home_card_shop' => 'Početna product kartica',
            'inline_related_shop' => 'Inline povezani proizvod',
            'sidebar_related_shop' => 'Sidebar povezani proizvod',
            'advisor_shop' => 'AI savjetnik shop gumb',
            'discount_lead' => '15% popust lead',
            'discount_modal_submit' => '15% popust modal',
            'content_cta' => 'Legacy ili neoznačeni CTA',
        ][$source] ?? $source;
    }

    private function renderClickedProductsTable(array $rows): string
    {
        if ($rows === []) {
            return '<p class="admin-muted">Još nema product klikova.</p>';
        }

        $html = '<table class="admin-list-table"><thead><tr><th>Proizvod</th><th>Klikovi</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            $title = trim((string) ($row['title'] ?? '')) !== ''
                ? html_entity_decode((string) $row['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
                : 'CT #' . (int) ($row['content_translation_id'] ?? 0);
            $html .= '<tr><td>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</td><td>' . (int) ($row['total'] ?? 0) . '</td></tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function renderOutboundSourceTable(array $rows): string
    {
        if ($rows === []) {
            return '<p class="admin-muted">Još nema Forever klikova za ovu vrstu sadržaja.</p>';
        }

        $html = '<table class="admin-list-table"><thead><tr><th>Sadržaj</th><th>URL</th><th>Klikovi</th><th>Posjetitelji</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            $title = html_entity_decode((string) ($row['title'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $sourcePath = (string) ($row['source_path'] ?? '/');
            $html .= '<tr><td>' . htmlspecialchars($title !== '' ? $title : $sourcePath, ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td><a href="' . htmlspecialchars($sourcePath, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">' . htmlspecialchars($sourcePath, ENT_QUOTES, 'UTF-8') . '</a></td>'
                . '<td>' . (int) ($row['total'] ?? 0) . '</td>'
                . '<td>' . (int) ($row['unique_visitors'] ?? 0) . '</td></tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }
}
