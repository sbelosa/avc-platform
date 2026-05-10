<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\AiLeadRepository;
use Avc\Support\AdminPageRenderer;

final class AiLeadsController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $repository = new AiLeadRepository($this->config);
        $status = trim((string) $this->request->input('status', ''));
        $language = trim((string) $this->request->input('lang', ''));
        $country = strtoupper(trim((string) $this->request->input('country', '')));
        $query = trim((string) $this->request->input('q', ''));
        $rows = $repository->listForAdmin($status !== 'all' ? $status : null, $language !== 'all' ? $language : null, $country !== 'ALL' ? $country : null, $query, 160);
        $summary = $repository->summarizeForAdmin();
        $saved = (string) $this->request->input('saved', '');
        $notice = $saved === '1' ? '<div class="admin-notice">Status leada je spremljen.</div>' : '';

        $html = $notice
            . '<div class="admin-kpi-grid">'
            . $this->renderMetric('Ukupno', (string) ($summary['total'] ?? 0))
            . $this->renderMetric('Novi', (string) ($summary['new_total'] ?? 0))
            . $this->renderMetric('Kontaktirani', (string) ($summary['contacted_total'] ?? 0))
            . $this->renderMetric('Kvalificirani', (string) ($summary['qualified_total'] ?? 0))
            . $this->renderMetric('Zatvoreni', (string) ($summary['closed_total'] ?? 0))
            . '</div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Filteri</h2></div><div class="admin-panel-body"><form class="admin-filter-bar admin-filter-wide" method="get" action="/admin/ai-leads">'
            . '<label>Status<select name="status">' . $this->renderSelectOptions([
                'all' => 'Svi',
                'new' => 'Novi',
                'contacted' => 'Kontaktirani',
                'qualified' => 'Kvalificirani',
                'closed' => 'Zatvoreni',
            ], $status !== '' ? $status : 'all') . '</select></label>'
            . '<label>Jezik<select name="lang">' . $this->renderSelectOptions([
                'all' => 'Svi',
                'hr' => 'HR',
                'en' => 'EN',
                'sl' => 'SL',
            ], $language !== '' ? $language : 'all') . '</select></label>'
            . '<label>Država<input type="text" name="country" value="' . htmlspecialchars($country, ENT_QUOTES, 'UTF-8') . '" placeholder="npr. HR"></label>'
            . '<label>Pretraga<input type="text" name="q" value="' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . '" placeholder="email, telefon, pitanje, ruta"></label>'
            . '<button class="admin-button admin-button-primary" type="submit">Filtriraj</button>'
            . '</form></div></div>'
            . '<div class="admin-card-grid">'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Top države</h2></div><div class="admin-panel-body">' . $this->renderMiniTable((array) ($summary['top_countries'] ?? [])) . '</div></div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Jezici</h2></div><div class="admin-panel-body">' . $this->renderMiniTable((array) ($summary['top_languages'] ?? [])) . '</div></div>'
            . '</div>'
            . '<div class="admin-panel"><div class="admin-panel-header"><h2>Leadovi</h2></div><div class="admin-panel-body">' . $this->renderTable($rows) . '</div></div>';

        $this->response->html(AdminPageRenderer::render('AI Leadovi', 'leads', $html, [
            'eyebrow' => 'Inbox',
            'description' => 'Svi upiti iz AI savjetnika, s filtriranjem i statusima za obradu.',
            'actions' => '<a class="admin-button" href="/admin">Dashboard</a>',
        ]));
    }

    public function updateStatus(): never
    {
        $aiLeadId = (int) $this->request->input('ai_lead_id', 0);
        $status = (string) $this->request->input('lead_status', 'new');

        (new AiLeadRepository($this->config))->updateStatus($aiLeadId, $status);

        $query = [
            'saved' => 1,
            'status' => (string) $this->request->input('return_status', ''),
            'lang' => (string) $this->request->input('return_lang', ''),
            'country' => (string) $this->request->input('return_country', ''),
            'q' => (string) $this->request->input('return_q', ''),
        ];

        $this->response->redirect('/admin/ai-leads?' . http_build_query($query));
    }

    private function renderMetric(string $label, string $value): string
    {
        return '<div class="admin-kpi"><span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</strong></div>';
    }

    private function renderTable(array $rows): string
    {
        if ($rows === []) {
            return '<div class="admin-empty">Još nema leadova.</div>';
        }

        $statusFilter = (string) $this->request->input('status', '');
        $langFilter = (string) $this->request->input('lang', '');
        $countryFilter = (string) $this->request->input('country', '');
        $queryFilter = (string) $this->request->input('q', '');

        $html = '<div class="admin-table-wrap"><table class="admin-list-table"><thead><tr><th>ID</th><th>Kontakt</th><th>Jezik/Država</th><th>Pitanje</th><th>Izvor</th><th>Status</th><th>Market</th><th>Obavijest</th><th>Vrijeme</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            $contactParts = array_filter([
                trim((string) ($row['name'] ?? '')),
                trim((string) ($row['email'] ?? '')),
                trim((string) ($row['phone'] ?? '')),
            ]);

            $html .= '<tr>'
                . '<td>' . (int) ($row['ai_lead_id'] ?? 0) . '</td>'
                . '<td>' . htmlspecialchars(implode(' / ', $contactParts), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars(strtoupper((string) ($row['language_code'] ?? '')) . ' / ' . strtoupper((string) ($row['country_code'] ?? '')), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars((string) ($row['lead_question'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td><a href="' . htmlspecialchars((string) ($row['source_path'] ?? '/'), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) ($row['source_path'] ?? ''), ENT_QUOTES, 'UTF-8') . '</a></td>'
                . '<td><form method="post" action="/admin/ai-leads/status" class="admin-inline-form">'
                . '<input type="hidden" name="ai_lead_id" value="' . (int) ($row['ai_lead_id'] ?? 0) . '">'
                . '<input type="hidden" name="return_status" value="' . htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8') . '">'
                . '<input type="hidden" name="return_lang" value="' . htmlspecialchars($langFilter, ENT_QUOTES, 'UTF-8') . '">'
                . '<input type="hidden" name="return_country" value="' . htmlspecialchars($countryFilter, ENT_QUOTES, 'UTF-8') . '">'
                . '<input type="hidden" name="return_q" value="' . htmlspecialchars($queryFilter, ENT_QUOTES, 'UTF-8') . '">'
                . '<select name="lead_status">' . $this->renderSelectOptions([
                    'new' => 'new',
                    'contacted' => 'contacted',
                    'qualified' => 'qualified',
                    'closed' => 'closed',
                ], (string) ($row['lead_status'] ?? 'new')) . '</select>'
                . '<button class="admin-button" type="submit">Spremi</button>'
                . '</form></td>'
                . '<td>' . htmlspecialchars(strtoupper((string) ($row['recommended_market_code'] ?? '')), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars((string) (($row['admin_notified_at'] ?? null) ? 'sent/logged' : 'pending'), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars((string) ($row['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function renderSelectOptions(array $options, string $selectedValue): string
    {
        $html = '';

        foreach ($options as $value => $label) {
            $html .= '<option value="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"' . ((string) $value === $selectedValue ? ' selected' : '') . '>' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</option>';
        }

        return $html;
    }

    private function renderMiniTable(array $rows): string
    {
        if ($rows === []) {
            return '<p class="admin-muted">Još nema podataka.</p>';
        }

        $html = '<table class="admin-list-table"><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr><td>' . htmlspecialchars((string) ($row['label'] ?? '—'), ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars((string) ($row['total'] ?? 0), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }
}
