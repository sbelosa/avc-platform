<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\AbTestRepository;
use Avc\Support\AdminPageRenderer;

final class AbTestsController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $summaries = (new AbTestRepository($this->config))->listSummaries();
        $body = '<div class="admin-panel"><div class="admin-panel-header"><h2>Aktivni A/B testovi</h2></div>'
            . '<div class="admin-panel-body"><p class="admin-muted">Ovdje pratiš koja varijanta stvarno donosi više prijava. Trenutni popup test dijeli promet 50/50 i pamti varijantu po posjetitelju.</p>'
            . '<p><a class="admin-button" href="/forever-proizvodi/?ab_discount_modal_contact=email_only" target="_blank" rel="noopener">Testiraj email varijantu</a> '
            . '<a class="admin-button" href="/forever-proizvodi/?ab_discount_modal_contact=phone_only" target="_blank" rel="noopener">Testiraj mobitel varijantu</a></p></div></div>';

        foreach ($summaries as $summary) {
            $body .= $this->renderTestSummary($summary);
        }

        if ($summaries === []) {
            $body .= '<div class="admin-empty">A/B testovi će se pojaviti ovdje čim se inicijaliziraju tablice.</div>';
        }

        $this->response->html(AdminPageRenderer::render('A/B testovi', 'ab-tests', $body, [
            'eyebrow' => 'Optimizacija konverzija',
            'description' => 'Usporedi varijante popupova, CTA tekstova i budućih landing stranica prema stvarnim prijavama.',
            'actions' => '<a class="admin-button" href="/forever-proizvodi/" target="_blank" rel="noopener">Otvori katalog</a>',
        ]));
    }

    private function renderTestSummary(array $summary): string
    {
        $winner = $summary['winner'] ?? null;
        $winnerText = is_array($winner)
            ? 'Trenutno vodi: <strong>' . $this->e((string) ($winner['label'] ?? $winner['variant_key'] ?? '')) . '</strong> (' . number_format((float) ($winner['conversion_rate'] ?? 0), 2, ',', '.') . '%)'
            : 'Za pouzdan zaključak pričekaj barem 30 prikaza po varijanti.';

        $html = '<div class="admin-panel"><div class="admin-panel-header"><h2>' . $this->e((string) ($summary['title'] ?? 'A/B test')) . '</h2>'
            . '<span class="admin-status">' . $this->e((string) ($summary['status'] ?? 'active')) . '</span></div>'
            . '<div class="admin-panel-body">'
            . '<p class="admin-muted">' . $this->e((string) ($summary['description'] ?? '')) . '</p>'
            . '<p>' . $winnerText . '</p>'
            . '<div class="admin-table-wrap"><table class="admin-list-table"><thead><tr>'
            . '<th>Varijanta</th><th>Udio</th><th>Prikazi</th><th>Posjetitelji</th><th>Prijave</th><th>Email</th><th>Mobitel</th><th>Preskoči</th><th>Konverzija</th><th>Zadnji signal</th>'
            . '</tr></thead><tbody>';

        foreach ((array) ($summary['variants'] ?? []) as $variant) {
            $conversionRate = (float) ($variant['conversion_rate'] ?? 0);
            $html .= '<tr>'
                . '<td><strong>' . $this->e((string) ($variant['label'] ?? $variant['variant_key'] ?? '')) . '</strong><br><span class="admin-muted">' . $this->e((string) ($variant['description'] ?? '')) . '</span></td>'
                . '<td>' . (int) ($variant['weight'] ?? 0) . '%</td>'
                . '<td>' . (int) ($variant['impressions'] ?? 0) . '</td>'
                . '<td>' . (int) ($variant['unique_visitors'] ?? 0) . '</td>'
                . '<td><strong>' . (int) ($variant['conversions'] ?? 0) . '</strong></td>'
                . '<td>' . (int) ($variant['email_leads'] ?? 0) . '</td>'
                . '<td>' . (int) ($variant['phone_leads'] ?? 0) . '</td>'
                . '<td>' . (int) ($variant['skips'] ?? 0) . '</td>'
                . '<td><span class="admin-score ' . ($conversionRate >= 8 ? '' : ($conversionRate >= 3 ? 'admin-score-medium' : 'admin-score-low')) . '">' . number_format($conversionRate, 2, ',', '.') . '%</span></td>'
                . '<td>' . $this->e((string) ($variant['last_event_at'] ?? '')) . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table></div></div></div>';

        return $html;
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
