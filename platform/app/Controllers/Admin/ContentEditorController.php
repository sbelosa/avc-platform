<?php

declare(strict_types=1);

namespace Avc\Controllers\Admin;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\ContentRepository;
use Avc\Repositories\ContentRevisionRepository;
use Avc\Repositories\ProductRecommendationRepository;
use Avc\Repositories\SeoRepository;
use Avc\Services\Auth\AdminAuthService;
use Avc\Services\Advisor\AdvisorRecommendationService;
use Avc\Services\Content\SeoDraftGeneratorService;
use Avc\Services\Content\StructuredDraftGeneratorService;
use Avc\Services\Content\StructuredContentService;
use Avc\Services\Seo\SeoAuditService;
use Avc\Support\AdminPageRenderer;

final class ContentEditorController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $this->response->redirect('/admin/posts');
    }

    public function edit(): never
    {
        $contentTranslationId = (int) $this->request->input('id', 0);
        $repository = new ContentRepository($this->config);
        $record = $repository->findForAdminEdit($contentTranslationId);

        if ($record === null) {
            $this->response->redirect('/admin/content');
        }

        $translations = $repository->findAdminTranslationsForContentItem((int) ($record['content_item_id'] ?? 0));
        $revisions = (new ContentRevisionRepository($this->config))->listForContentTranslationId($contentTranslationId, 6);
        $audit = (new SeoAuditService())->auditRow((new SeoRepository($this->config))->findAuditRow($contentTranslationId) ?? $record);
        $structuredContent = new StructuredContentService();
        $linkSuggestions = (new AdvisorRecommendationService($this->config))->recommend(
            $this->buildLinkSuggestionQuery($record),
            (string) ($record['language_code'] ?? 'hr'),
            (string) ($record['route_path'] ?? '/')
        );
        $productRecommendation = (string) ($record['content_type'] ?? '') === 'product_guide'
            ? (new ProductRecommendationRepository($this->config))->findByContentTranslationId($contentTranslationId)
            : null;
        $saved = (string) $this->request->input('saved', '');
        $error = trim((string) $this->request->input('error', ''));
        $notice = $this->renderEditorNotice($saved, $error);
        $title = trim((string) ($record['title'] ?? 'Sadržaj'));
        $routePath = (string) ($record['route_path'] ?? '/');
        $actions = '<a class="admin-button" href="/admin/' . $this->activeItemForRecord($record) . '">Natrag na listu</a>'
            . '<a class="admin-button" href="' . htmlspecialchars($routePath, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">Preview</a>'
            . '<button class="admin-button admin-button-primary" type="submit" form="content-editor-form">Spremi</button>';

        $this->response->html(AdminPageRenderer::render(
            'Uredi: ' . ($title !== '' ? $title : 'Sadržaj'),
            $this->activeItemForRecord($record),
            $this->renderEditorPage($record, $notice, $audit, $linkSuggestions, $translations, $revisions, $structuredContent, $productRecommendation),
            [
                'eyebrow' => strtoupper((string) ($record['language_code'] ?? 'hr')) . ' / ' . (string) ($record['content_type'] ?? 'content'),
                'description' => 'Route: ' . $routePath,
                'actions' => $actions,
                'extra_head' => '<style>' . $this->editorCss() . '</style><script>' . $this->editorScript() . '</script>',
            ]
        ));
    }

    private function renderEditorPage(
        array $record,
        string $notice,
        array $audit,
        array $linkSuggestions,
        array $translations,
        array $revisions,
        StructuredContentService $structuredContent,
        ?array $productRecommendation
    ): string {
        $contentTranslationId = (int) ($record['content_translation_id'] ?? 0);
        $routePath = (string) ($record['route_path'] ?? '/');
        $bodyHtml = (string) ($record['body_html'] ?? '');
        $faqInput = $structuredContent->formatFaqItemsForEditor($structuredContent->decodeFaqJson((string) ($record['faq_json'] ?? '')));

        return $notice
            . '<form id="content-editor-form" class="admin-editor-form" method="post" action="/admin/content/save">'
            . '<input type="hidden" name="content_translation_id" value="' . $contentTranslationId . '">'
            . '<div class="admin-editor-grid">'
            . '<section class="admin-editor-main">'
            . '<div class="admin-title-panel"><input type="text" name="title" required value="' . htmlspecialchars((string) ($record['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '" aria-label="Naslov">'
            . '<div class="admin-permalink"><span>Permalink:</span><a href="' . htmlspecialchars($routePath, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">' . htmlspecialchars($routePath, ENT_QUOTES, 'UTF-8') . '</a></div></div>'
            . '<div class="admin-editor-box">'
            . '<div class="admin-editor-box-head"><h2>Sadržaj</h2><div class="admin-editor-tabs-inline"><button type="button" class="is-active" data-editor-mode="visual" aria-pressed="true">Visual</button><button type="button" data-editor-mode="html" aria-pressed="false">HTML</button></div></div>'
            . '<div class="admin-editor-wrap" data-editor-wrap>'
            . '<div class="admin-editor-toolbar" aria-label="Alati za formatiranje">'
            . '<button type="button" data-command="bold" title="Bold"><strong>B</strong></button>'
            . '<button type="button" data-command="italic" title="Italic"><em>I</em></button>'
            . '<button type="button" data-command="formatBlock" data-value="h2" title="Heading 2">H2</button>'
            . '<button type="button" data-command="formatBlock" data-value="h3" title="Heading 3">H3</button>'
            . '<button type="button" data-command="insertUnorderedList" title="Lista">UL</button>'
            . '<button type="button" data-command="insertOrderedList" title="Numerirana lista">OL</button>'
            . '<button type="button" data-command="createLink" title="Link">Link</button>'
            . '<button type="button" data-command="unlink" title="Ukloni link">Unlink</button>'
            . '</div>'
            . '<div id="body-visual" class="admin-wysiwyg" data-visual-editor contenteditable="true"></div>'
            . '<textarea id="body-html" class="admin-code-editor" name="body_html" data-html-editor>' . htmlspecialchars($bodyHtml, ENT_QUOTES, 'UTF-8') . '</textarea>'
            . '</div></div>'
            . '<div class="admin-editor-box"><div class="admin-editor-box-head"><h2>Excerpt</h2></div><textarea name="excerpt" class="admin-textarea admin-textarea-small">' . htmlspecialchars((string) ($record['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8') . '</textarea></div>'
            . '<div class="admin-editor-box"><div class="admin-editor-box-head"><h2>AI summary HTML</h2></div><textarea name="summary_html" class="admin-textarea admin-textarea-small">' . htmlspecialchars((string) ($record['summary_html'] ?? ''), ENT_QUOTES, 'UTF-8') . '</textarea></div>'
            . '<div class="admin-editor-box"><div class="admin-editor-box-head"><h2>FAQ blokovi</h2></div><textarea name="faq_input" class="admin-textarea admin-textarea-medium" placeholder="Q: Koje su glavne koristi?\nA: Kratak jasan odgovor.\n\nQ: Kada koristiti proizvod?\nA: Odgovor s konkretnim savjetom.">' . htmlspecialchars($faqInput, ENT_QUOTES, 'UTF-8') . '</textarea></div>'
            . '</section>'
            . '<aside class="admin-editor-sidebar">'
            . $this->renderPublishBox($record)
            . $this->renderCopilotBox($record, $audit, $linkSuggestions, $productRecommendation, $revisions)
            . $this->renderSeoBox($record, $audit)
            . $this->renderFeaturedImageBox($record)
            . $this->renderProductBox($productRecommendation)
            . $this->renderAiBox($contentTranslationId)
            . $this->renderRevisionsBox($revisions)
            . '<div class="admin-editor-box"><div class="admin-editor-box-head"><h2>Prijevodi</h2></div><div class="admin-editor-box-body">' . $this->renderTranslations($translations, $contentTranslationId) . '</div></div>'
            . '<div class="admin-editor-box"><div class="admin-editor-box-head"><h2>Internal linking</h2></div><div class="admin-editor-box-body">' . $this->renderLinkSuggestions($linkSuggestions) . '</div></div>'
            . '</aside>'
            . '</div></form>'
            . '<form id="generate-structured-form" method="post" action="/admin/content/generate-structured"><input type="hidden" name="content_translation_id" value="' . $contentTranslationId . '"></form>'
            . '<form id="generate-seo-basics-form" method="post" action="/admin/content/generate-seo-basics"><input type="hidden" name="content_translation_id" value="' . $contentTranslationId . '"></form>'
            . '<form id="generate-full-draft-form" method="post" action="/admin/content/generate-full-draft"><input type="hidden" name="content_translation_id" value="' . $contentTranslationId . '"></form>'
            . ($productRecommendation !== null
                ? '<form id="product-routing-inline-form" method="post" action="/admin/product-routing/save"><input type="hidden" name="product_recommendation_id" value="' . (int) ($productRecommendation['product_recommendation_id'] ?? 0) . '"><input type="hidden" name="return_to" value="/admin/content/edit?id=' . $contentTranslationId . '"></form>'
                : '')
            . $this->renderRevisionRestoreForms($revisions, $contentTranslationId);
    }

    private function renderPublishBox(array $record): string
    {
        $status = trim((string) ($record['lifecycle_status'] ?? 'published'));
        $updatedAt = trim((string) ($record['updated_at'] ?? ''));
        $publishedAt = trim((string) ($record['published_at'] ?? ''));
        $language = strtoupper((string) ($record['language_code'] ?? 'hr'));
        $template = trim((string) ($record['editor_template'] ?? ''));
        $slug = trim((string) ($record['slug'] ?? ''));

        return '<div class="admin-editor-box admin-publish-box">'
            . '<div class="admin-editor-box-head"><h2>Publish</h2></div>'
            . '<div class="admin-editor-box-body">'
            . '<button class="admin-button admin-button-primary admin-save-wide" type="submit">Spremi</button>'
            . '<dl class="admin-meta-list">'
            . '<div><dt>Status</dt><dd><span class="admin-status admin-status-' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</span></dd></div>'
            . '<div><dt>Jezik</dt><dd>' . htmlspecialchars($language, ENT_QUOTES, 'UTF-8') . '</dd></div>'
            . '<div><dt>Slug</dt><dd>' . htmlspecialchars($slug !== '' ? $slug : '-', ENT_QUOTES, 'UTF-8') . '</dd></div>'
            . '<div><dt>Template</dt><dd>' . htmlspecialchars($template !== '' ? $template : '-', ENT_QUOTES, 'UTF-8') . '</dd></div>'
            . '<div><dt>Objavljeno</dt><dd>' . htmlspecialchars($publishedAt !== '' ? $publishedAt : '-', ENT_QUOTES, 'UTF-8') . '</dd></div>'
            . '<div><dt>Ažurirano</dt><dd>' . htmlspecialchars($updatedAt !== '' ? $updatedAt : '-', ENT_QUOTES, 'UTF-8') . '</dd></div>'
            . '</dl></div></div>';
    }

    private function renderCopilotBox(array $record, array $audit, array $linkSuggestions, ?array $productRecommendation, array $revisions): string
    {
        $items = [];
        $contentTranslationId = (int) ($record['content_translation_id'] ?? 0);

        foreach (array_slice((array) ($audit['issues'] ?? []), 0, 3) as $issue) {
            $items[] = [
                'label' => (string) ($issue['label'] ?? 'SEO zadatak'),
                'detail' => (string) ($issue['help'] ?? ''),
                'href' => '#',
            ];
        }

        if (trim((string) ($record['featured_image_path'] ?? '')) === '') {
            $items[] = [
                'label' => 'Dodaj featured image',
                'detail' => 'Sadržaj bez slike slabije radi u listama i dijeljenju.',
                'href' => '/admin/media?select_for=' . $contentTranslationId,
            ];
        }

        if ($productRecommendation !== null && (array) ($productRecommendation['market_urls'] ?? []) === []) {
            $items[] = [
                'label' => 'Popuni product routing',
                'detail' => 'CTA treba imati barem jedan resolved market URL.',
                'href' => '/admin/product-routing/edit?id=' . (int) ($productRecommendation['product_recommendation_id'] ?? 0),
            ];
        }

        $suggestionTotal = count((array) ($linkSuggestions['products'] ?? [])) + count((array) ($linkSuggestions['articles'] ?? []));
        if ($suggestionTotal > 0) {
            $items[] = [
                'label' => 'Dodaj interne linkove',
                'detail' => $suggestionTotal . ' prijedloga čeka u internal linking panelu.',
                'href' => '#',
            ];
        }

        if ($revisions === []) {
            $items[] = [
                'label' => 'Prva revizija',
                'detail' => 'Sljedeće spremanje automatski čuva trenutno stanje.',
                'href' => '#',
            ];
        }

        if ($items === []) {
            $items[] = [
                'label' => 'Nema kritičnih zadataka',
                'detail' => 'Sadržaj ima zdrave osnovne signale.',
                'href' => '#',
            ];
        }

        $html = '<div class="admin-editor-box admin-copilot-box"><div class="admin-editor-box-head"><h2>AVC Copilot</h2></div><div class="admin-editor-box-body"><div class="admin-copilot-list">';

        foreach (array_slice($items, 0, 5) as $item) {
            $href = (string) ($item['href'] ?? '#');
            $html .= '<a class="admin-copilot-item" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"><strong>' . htmlspecialchars((string) ($item['label'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong><span>' . htmlspecialchars((string) ($item['detail'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span></a>';
        }

        return $html . '</div></div></div>';
    }

    private function renderSeoBox(array $record, array $audit): string
    {
        return '<div class="admin-editor-box">'
            . '<div class="admin-editor-box-head"><h2>SEO</h2><span class="' . $this->scoreClass((int) ($audit['seo_score'] ?? 0)) . '">' . (int) ($audit['seo_score'] ?? 0) . '</span></div>'
            . '<div class="admin-editor-box-body">'
            . '<label class="admin-field">Meta title<input type="text" name="meta_title" value="' . htmlspecialchars((string) ($record['meta_title'] ?? ''), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label class="admin-field">Meta description<textarea name="meta_description" class="admin-textarea admin-textarea-seo">' . htmlspecialchars((string) ($record['meta_description'] ?? ''), ENT_QUOTES, 'UTF-8') . '</textarea></label>'
            . '<label class="admin-field">Canonical URL<input type="text" name="canonical_url" value="' . htmlspecialchars((string) ($record['canonical_url'] ?? ''), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label class="admin-field">Breadcrumb title<input type="text" name="breadcrumb_title" value="' . htmlspecialchars((string) ($record['breadcrumb_title'] ?? ''), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<div class="admin-two-fields">'
            . '<label class="admin-field">Robots index<select name="robots_index">' . $this->renderSelectOptions(['1' => 'index', '0' => 'noindex'], (string) ((int) ($record['robots_index'] ?? 1))) . '</select></label>'
            . '<label class="admin-field">Robots follow<select name="robots_follow">' . $this->renderSelectOptions(['1' => 'follow', '0' => 'nofollow'], (string) ((int) ($record['robots_follow'] ?? 1))) . '</select></label>'
            . '</div>'
            . '<div class="admin-seo-snapshot">' . $this->renderAuditSnapshot($audit) . '</div>'
            . '</div></div>';
    }

    private function renderFeaturedImageBox(array $record): string
    {
        $featuredImagePath = trim((string) ($record['featured_image_path'] ?? ''));
        $contentTranslationId = (int) ($record['content_translation_id'] ?? 0);

        return '<div class="admin-editor-box">'
            . '<div class="admin-editor-box-head"><h2>Featured image</h2></div>'
            . '<div class="admin-editor-box-body">'
            . ($featuredImagePath !== ''
                ? '<div class="admin-featured-image"><img src="' . htmlspecialchars($featuredImagePath, ENT_QUOTES, 'UTF-8') . '" alt=""></div><p class="admin-muted">' . htmlspecialchars($featuredImagePath, ENT_QUOTES, 'UTF-8') . '</p>'
                : '<p class="admin-muted">Nije postavljena naslovna slika.</p>')
            . '<label class="admin-field">Image URL<input type="text" name="featured_image_path" value="' . htmlspecialchars($featuredImagePath, ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<a class="admin-button" href="/admin/media?select_for=' . $contentTranslationId . '">Odaberi iz media libraryja</a>'
            . '</div></div>';
    }

    private function renderProductBox(?array $productRecommendation): string
    {
        if ($productRecommendation === null) {
            return '';
        }

        $marketUrls = (array) ($productRecommendation['market_urls'] ?? []);
        $manualMarketUrls = (array) ($productRecommendation['manual_market_urls'] ?? []);
        $marketRows = '';
        foreach (array_slice($marketUrls, 0, 5, true) as $marketCode => $destinationUrl) {
            $marketRows .= '<div><dt>' . htmlspecialchars(strtoupper((string) $marketCode), ENT_QUOTES, 'UTF-8') . '</dt><dd><a href="' . htmlspecialchars((string) $destinationUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">URL</a></dd></div>';
        }

        return '<div class="admin-editor-box">'
            . '<div class="admin-editor-box-head"><h2>Proizvod</h2></div>'
            . '<div class="admin-editor-box-body">'
            . '<dl class="admin-meta-list">'
            . '<div><dt>SKU</dt><dd>' . htmlspecialchars(trim((string) ($productRecommendation['sku'] ?? '')) !== '' ? (string) $productRecommendation['sku'] : '-', ENT_QUOTES, 'UTF-8') . '</dd></div>'
            . '<div><dt>Strategija</dt><dd>' . htmlspecialchars((string) ($productRecommendation['destination_strategy'] ?? 'passthrough'), ENT_QUOTES, 'UTF-8') . '</dd></div>'
            . '<div><dt>Market URL-ovi</dt><dd>' . count($marketUrls) . '</dd></div>'
            . $marketRows
            . '</dl>'
            . '<label class="admin-field admin-routing-field">Manual override<textarea name="manual_market_urls" form="product-routing-inline-form" class="admin-textarea admin-textarea-routing" placeholder="HR=https://...\nSI=https://...\nDE=https://...">' . htmlspecialchars($this->formatMarketUrlsForEditor($manualMarketUrls), ENT_QUOTES, 'UTF-8') . '</textarea></label>'
            . '<div class="admin-inline-actions"><button class="admin-button admin-button-primary" type="submit" form="product-routing-inline-form">Spremi routing</button>'
            . '<a class="admin-button" href="/admin/product-routing/edit?id=' . (int) ($productRecommendation['product_recommendation_id'] ?? 0) . '">Uredi routing</a>'
            . '</div>'
            . '</div></div>';
    }

    private function renderAiBox(int $contentTranslationId): string
    {
        return '<div class="admin-editor-box">'
            . '<div class="admin-editor-box-head"><h2>AI alati</h2></div>'
            . '<div class="admin-editor-box-body admin-ai-actions">'
            . '<button class="admin-button" type="submit" form="generate-structured-form">Generiraj AI blokove</button>'
            . '<button class="admin-button" type="submit" form="generate-seo-basics-form">Generiraj SEO osnove</button>'
            . '<button class="admin-button admin-button-primary" type="submit" form="generate-full-draft-form">Generiraj puni draft</button>'
            . '</div></div>';
    }

    private function renderRevisionsBox(array $revisions): string
    {
        if ($revisions === []) {
            return '<div class="admin-editor-box"><div class="admin-editor-box-head"><h2>Revizije</h2></div><div class="admin-editor-box-body"><p class="admin-muted">Još nema spremljenih revizija.</p></div></div>';
        }

        $html = '<div class="admin-editor-box"><div class="admin-editor-box-head"><h2>Revizije</h2></div><div class="admin-editor-box-body"><div class="admin-revision-list">';

        foreach ($revisions as $revision) {
            $revisionId = (int) ($revision['content_revision_id'] ?? 0);
            $createdAt = (string) ($revision['created_at'] ?? '');
            $revisionType = (string) ($revision['revision_type'] ?? 'manual_save');
            $snapshot = (array) ($revision['snapshot'] ?? []);
            $title = trim((string) ($snapshot['title'] ?? ($revision['title'] ?? 'Revizija')));
            $html .= '<div class="admin-revision-row"><div><strong>' . htmlspecialchars($title !== '' ? $title : 'Revizija', ENT_QUOTES, 'UTF-8') . '</strong>'
                . '<span>' . htmlspecialchars($createdAt . ' / ' . $revisionType, ENT_QUOTES, 'UTF-8') . '</span></div>'
                . '<button class="admin-button" type="submit" form="restore-revision-' . $revisionId . '">Vrati</button></div>';
        }

        return $html . '</div></div></div>';
    }

    private function renderRevisionRestoreForms(array $revisions, int $contentTranslationId): string
    {
        $html = '';

        foreach ($revisions as $revision) {
            $revisionId = (int) ($revision['content_revision_id'] ?? 0);
            if ($revisionId <= 0) {
                continue;
            }

            $html .= '<form id="restore-revision-' . $revisionId . '" method="post" action="/admin/content/revision/restore">'
                . '<input type="hidden" name="content_translation_id" value="' . $contentTranslationId . '">'
                . '<input type="hidden" name="content_revision_id" value="' . $revisionId . '">'
                . '</form>';
        }

        return $html;
    }

    private function renderEditorNotice(string $saved, string $error): string
    {
        if ($error !== '') {
            return '<div class="admin-notice admin-notice-error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
        }

        return match ($saved) {
            '1' => '<div class="admin-notice">Sadržaj i SEO polja su spremljeni.</div>',
            'draft' => '<div class="admin-notice">Generiran je početni draft AI summary i FAQ blokova. Pregledaj ga i doradi prije objave.</div>',
            'seo-draft' => '<div class="admin-notice">Generirane su SEO osnove za prazna polja. Pregledaj excerpt, meta title i meta description prije objave.</div>',
            'full-draft' => '<div class="admin-notice">Generiran je puni draft za prazna SEO i AI polja. Pregledaj ga i ručno dotjeraj najvažnije dijelove.</div>',
            'routing' => '<div class="admin-notice">Product routing je spremljen i sada se koristi u redirect engineu.</div>',
            'media' => '<div class="admin-notice">Featured image je spremljena.</div>',
            'restored' => '<div class="admin-notice">Revizija je vraćena. Trenutno stanje je prije vraćanja spremljeno kao nova revizija.</div>',
            default => '',
        };
    }

    private function isAllowedFeaturedImagePath(string $featuredImagePath): bool
    {
        if ($featuredImagePath === '') {
            return true;
        }

        return str_starts_with($featuredImagePath, '/wp-content/uploads/')
            || str_starts_with($featuredImagePath, '/media/')
            || str_starts_with($featuredImagePath, 'https://aloavera-centar.com/wp-content/uploads/')
            || str_starts_with($featuredImagePath, 'https://aloevera-centar.com/wp-content/uploads/');
    }

    private function formatMarketUrlsForEditor(array $marketUrls): string
    {
        if ($marketUrls === []) {
            return '';
        }

        $lines = [];
        ksort($marketUrls);

        foreach ($marketUrls as $marketCode => $destinationUrl) {
            $lines[] = strtoupper((string) $marketCode) . '=' . (string) $destinationUrl;
        }

        return implode("\n", $lines);
    }

    private function activeItemForRecord(array $record): string
    {
        return match ((string) ($record['content_type'] ?? 'article')) {
            'product_guide' => 'products',
            'page' => 'pages',
            default => 'posts',
        };
    }

    private function currentAdminUserId(): ?int
    {
        $adminUser = (new AdminAuthService($this->config, $this->request))->user();
        $adminUserId = (int) ($adminUser['admin_user_id'] ?? 0);

        return $adminUserId > 0 ? $adminUserId : null;
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

    private function editorCss(): string
    {
        return <<<'CSS'
            .admin-editor-form{display:block}
            .admin-editor-grid{display:grid;grid-template-columns:minmax(0,1fr) 330px;gap:18px;align-items:start}
            .admin-editor-main{display:grid;gap:16px;min-width:0}
            .admin-editor-sidebar{display:grid;gap:14px;position:sticky;top:18px;align-self:start}
            .admin-title-panel{background:#fff;border:1px solid #c3c4c7;box-shadow:0 1px 1px rgba(0,0,0,.04);padding:12px}
            .admin-title-panel input{width:100%;min-height:46px;border:1px solid #8c8f94;border-radius:3px;padding:6px 10px;font-size:26px;line-height:1.25;font-weight:400;color:#1d2327}
            .admin-permalink{display:flex;gap:8px;flex-wrap:wrap;margin-top:9px;color:#646970;font-size:13px}
            .admin-editor-box{background:#fff;border:1px solid #c3c4c7;box-shadow:0 1px 1px rgba(0,0,0,.04)}
            .admin-editor-box-head{display:flex;justify-content:space-between;align-items:center;gap:10px;min-height:42px;padding:0 12px;border-bottom:1px solid #dcdcde;background:#fff}
            .admin-editor-box-head h2{margin:0;font-size:14px;font-weight:600;color:#1d2327}
            .admin-editor-box-body{padding:12px}
            .admin-editor-tabs-inline{display:flex;align-self:stretch;align-items:flex-end}
            .admin-editor-tabs-inline button{min-width:68px;border:0;border-left:1px solid #dcdcde;background:#f6f7f7;color:#50575e;font:inherit;cursor:pointer}
            .admin-editor-tabs-inline button.is-active{background:#fff;color:#1d2327;border-top:3px solid #2271b1}
            .admin-editor-toolbar{display:flex;gap:4px;flex-wrap:wrap;padding:8px;border-bottom:1px solid #dcdcde;background:#f6f7f7}
            .admin-editor-toolbar button{min-width:34px;min-height:30px;border:1px solid #8c8f94;border-radius:3px;background:#fff;color:#1d2327;font:inherit;cursor:pointer}
            .admin-editor-toolbar button:hover{border-color:#2271b1;color:#135e96}
            .admin-wysiwyg{min-height:560px;padding:18px;background:#fff;color:#1d2327;outline:none;font-size:16px;line-height:1.6}
            .admin-wysiwyg:focus{box-shadow:inset 0 0 0 2px #2271b1}
            .admin-wysiwyg h2{font-size:26px;line-height:1.25;margin:28px 0 10px}
            .admin-wysiwyg h3{font-size:21px;line-height:1.3;margin:22px 0 8px}
            .admin-wysiwyg p{margin:0 0 16px}
            .admin-wysiwyg ul,.admin-wysiwyg ol{padding-left:24px}
            .admin-code-editor{display:none;width:100%;min-height:560px;padding:14px;border:0;resize:vertical;font:13px/1.55 Menlo,Monaco,Consolas,"Liberation Mono",monospace;color:#1d2327;background:#fff}
            .admin-editor-wrap.is-html .admin-editor-toolbar,.admin-editor-wrap.is-html .admin-wysiwyg{display:none}
            .admin-editor-wrap.is-html .admin-code-editor{display:block}
            .admin-textarea{width:100%;border:0;padding:12px;resize:vertical;font:14px/1.5 inherit;color:#1d2327}
            .admin-textarea-small{min-height:132px}
            .admin-textarea-medium{min-height:190px}
            .admin-textarea-seo{min-height:96px;border:1px solid #8c8f94;border-radius:3px;padding:7px 8px}
            .admin-textarea-routing{min-height:116px;border:1px solid #8c8f94;border-radius:3px;padding:7px 8px;font:12px/1.5 Menlo,Monaco,Consolas,"Liberation Mono",monospace}
            .admin-field{display:grid;gap:5px;margin-bottom:10px;color:#50575e;font-size:13px}
            .admin-field input,.admin-field select{width:100%;min-height:32px;padding:5px 8px;border:1px solid #8c8f94;border-radius:3px;background:#fff;color:#1d2327;font:inherit}
            .admin-routing-field{margin-top:12px}
            .admin-inline-actions{display:flex;gap:8px;flex-wrap:wrap}
            .admin-two-fields{display:grid;grid-template-columns:1fr 1fr;gap:8px}
            .admin-save-wide{width:100%;margin-bottom:12px}
            .admin-copilot-list{display:grid;gap:8px}
            .admin-copilot-item{display:grid;gap:3px;padding:9px;border:1px solid #dcdcde;background:#f6f7f7;color:#1d2327;text-decoration:none}
            .admin-copilot-item:hover{border-color:#2271b1;background:#fff;text-decoration:none}
            .admin-copilot-item strong{font-size:13px}
            .admin-copilot-item span{font-size:12px;color:#646970}
            .admin-meta-list{display:grid;gap:8px;margin:0;color:#50575e;font-size:13px}
            .admin-meta-list div{display:grid;grid-template-columns:96px minmax(0,1fr);gap:10px}
            .admin-meta-list dt{font-weight:600;color:#1d2327}
            .admin-meta-list dd{margin:0;overflow-wrap:anywhere}
            .admin-seo-snapshot{border-top:1px solid #dcdcde;margin-top:12px;padding-top:12px}
            .admin-featured-image{border:1px solid #dcdcde;background:#f6f7f7}
            .admin-featured-image img{display:block;width:100%;height:auto}
            .admin-ai-actions{display:grid;gap:8px}
            .admin-revision-list{display:grid;gap:8px}
            .admin-revision-row{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px;align-items:center;border-bottom:1px solid #dcdcde;padding-bottom:8px}
            .admin-revision-row:last-child{border-bottom:0;padding-bottom:0}
            .admin-revision-row strong,.admin-revision-row span{display:block;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
            .admin-revision-row strong{font-size:13px;color:#1d2327}
            .admin-revision-row span{font-size:12px;color:#646970}
            .admin-notice{margin:0 0 14px;padding:10px 12px;border-left:4px solid #00a32a;background:#fff;color:#1d2327;box-shadow:0 1px 1px rgba(0,0,0,.04)}
            .admin-notice-error{border-left-color:#b32d2e;background:#fcf0f1}
            @media (max-width: 1120px){
                .admin-editor-grid{grid-template-columns:1fr}
                .admin-editor-sidebar{position:static}
            }
            @media (max-width: 640px){
                .admin-title-panel input{font-size:21px}
                .admin-two-fields{grid-template-columns:1fr}
                .admin-meta-list div{grid-template-columns:1fr}
                .admin-wysiwyg,.admin-code-editor{min-height:420px}
            }
        CSS;
    }

    private function editorScript(): string
    {
        return <<<'JS'
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-editor-wrap]').forEach(function (wrap) {
                    const visual = wrap.querySelector('[data-visual-editor]');
                    const html = wrap.querySelector('[data-html-editor]');
                    const form = document.getElementById('content-editor-form');
                    const modeButtons = wrap.parentElement.querySelectorAll('[data-editor-mode]');
                    let mode = 'visual';

                    if (!visual || !html) {
                        return;
                    }

                    visual.innerHTML = html.value || '';

                    function syncToHtml() {
                        html.value = visual.innerHTML;
                    }

                    function syncToVisual() {
                        visual.innerHTML = html.value || '';
                    }

                    function setMode(nextMode) {
                        if (nextMode === mode) {
                            return;
                        }

                        if (nextMode === 'html') {
                            syncToHtml();
                            wrap.classList.add('is-html');
                        } else {
                            syncToVisual();
                            wrap.classList.remove('is-html');
                        }

                        mode = nextMode;
                        modeButtons.forEach(function (button) {
                            const isActive = button.dataset.editorMode === mode;
                            button.classList.toggle('is-active', isActive);
                            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                        });
                    }

                    modeButtons.forEach(function (button) {
                        button.addEventListener('click', function () {
                            setMode(button.dataset.editorMode || 'visual');
                        });
                    });

                    wrap.querySelectorAll('[data-command]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            setMode('visual');
                            visual.focus();

                            const command = button.dataset.command || '';
                            let value = button.dataset.value || null;

                            if (command === 'createLink') {
                                value = window.prompt('URL za link', 'https://');
                                if (!value) {
                                    return;
                                }
                            }

                            document.execCommand(command, false, value);
                            syncToHtml();
                        });
                    });

                    if (form) {
                        form.addEventListener('submit', function () {
                            if (mode !== 'html') {
                                syncToHtml();
                            }
                        });
                    }
                });
            });
        JS;
    }

    public function update(): never
    {
        $contentTranslationId = (int) $this->request->input('content_translation_id', 0);
        $repository = new ContentRepository($this->config);
        $existing = $repository->findForAdminEdit($contentTranslationId);

        if ($existing === null) {
            $this->response->redirect('/admin/content?error=missing');
        }

        $payload = $this->request->all();
        $title = array_key_exists('title', $payload) ? trim((string) $payload['title']) : trim((string) ($existing['title'] ?? ''));
        $canonicalUrl = array_key_exists('canonical_url', $payload)
            ? trim((string) $payload['canonical_url'])
            : trim((string) ($existing['canonical_url'] ?? ''));

        if ($contentTranslationId <= 0 || $title === '') {
            $this->response->redirect('/admin/content?error=invalid');
        }

        if ($canonicalUrl !== '' && !filter_var($canonicalUrl, FILTER_VALIDATE_URL)) {
            $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&error=' . rawurlencode('Canonical URL mora biti valjan apsolutni URL ili prazno polje.'));
        }

        $structuredContent = new StructuredContentService();
        $faqParse = $structuredContent->parseFaqInput((string) ($payload['faq_input'] ?? ''));
        if (($faqParse['error'] ?? null) !== null) {
            $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&error=' . rawurlencode((string) $faqParse['error']));
        }

        (new ContentRevisionRepository($this->config))->createFromRecord($existing, $this->currentAdminUserId(), 'manual_save');
        $repository->updateTranslationAndSeo($contentTranslationId, [
            'title' => $title,
            'excerpt' => array_key_exists('excerpt', $payload) ? (string) $payload['excerpt'] : (string) ($existing['excerpt'] ?? ''),
            'summary_html' => array_key_exists('summary_html', $payload) ? (string) $payload['summary_html'] : (string) ($existing['summary_html'] ?? ''),
            'faq_json' => $structuredContent->encodeFaqItems((array) ($faqParse['items'] ?? [])),
            'body_html' => array_key_exists('body_html', $payload) ? (string) $payload['body_html'] : (string) ($existing['body_html'] ?? ''),
            'featured_image_path' => array_key_exists('featured_image_path', $payload) ? (string) $payload['featured_image_path'] : (string) ($existing['featured_image_path'] ?? ''),
            'meta_title' => array_key_exists('meta_title', $payload) ? (string) $payload['meta_title'] : (string) ($existing['meta_title'] ?? ''),
            'meta_description' => array_key_exists('meta_description', $payload) ? (string) $payload['meta_description'] : (string) ($existing['meta_description'] ?? ''),
            'canonical_url' => $canonicalUrl,
            'breadcrumb_title' => array_key_exists('breadcrumb_title', $payload) ? (string) $payload['breadcrumb_title'] : (string) ($existing['breadcrumb_title'] ?? ''),
            'robots_index' => array_key_exists('robots_index', $payload) ? ((int) $payload['robots_index'] === 1 ? 1 : 0) : (int) ($existing['robots_index'] ?? 1),
            'robots_follow' => array_key_exists('robots_follow', $payload) ? ((int) $payload['robots_follow'] === 1 ? 1 : 0) : (int) ($existing['robots_follow'] ?? 1),
        ]);

        $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&saved=1');
    }

    public function updateFeaturedImage(): never
    {
        $contentTranslationId = (int) $this->request->input('content_translation_id', 0);
        $featuredImagePath = trim((string) $this->request->input('featured_image_path', ''));

        if ($contentTranslationId <= 0 || !$this->isAllowedFeaturedImagePath($featuredImagePath)) {
            $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&error=' . rawurlencode('Odabrana slika nije valjana.'));
        }

        $repository = new ContentRepository($this->config);
        $existing = $repository->findForAdminEdit($contentTranslationId);
        if ($existing !== null) {
            (new ContentRevisionRepository($this->config))->createFromRecord($existing, $this->currentAdminUserId(), 'media_change');
        }

        $repository->updateFeaturedImage($contentTranslationId, $featuredImagePath);

        $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&saved=media');
    }

    public function restoreRevision(): never
    {
        $contentTranslationId = (int) $this->request->input('content_translation_id', 0);
        $contentRevisionId = (int) $this->request->input('content_revision_id', 0);
        $repository = new ContentRepository($this->config);
        $existing = $repository->findForAdminEdit($contentTranslationId);
        $revision = (new ContentRevisionRepository($this->config))->findForContentTranslationId($contentRevisionId, $contentTranslationId);

        if ($existing === null || $revision === null || (array) ($revision['snapshot'] ?? []) === []) {
            $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&error=' . rawurlencode('Revizija nije pronađena.'));
        }

        (new ContentRevisionRepository($this->config))->createFromRecord($existing, $this->currentAdminUserId(), 'before_restore');
        $repository->updateTranslationAndSeo($contentTranslationId, (array) ($revision['snapshot'] ?? []));

        $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&saved=restored');
    }

    public function generateStructured(): never
    {
        $contentTranslationId = (int) $this->request->input('content_translation_id', 0);
        $repository = new ContentRepository($this->config);
        $existing = $repository->findForAdminEdit($contentTranslationId);

        if ($existing === null) {
            $this->response->redirect('/admin/content?error=missing');
        }

        $payload = $this->buildPersistedPayload($existing);
        $this->applyStructuredDraft($existing, $payload, true);
        $repository->updateTranslationAndSeo($contentTranslationId, $payload);

        $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&saved=draft');
    }

    public function generateSeoBasics(): never
    {
        $contentTranslationId = (int) $this->request->input('content_translation_id', 0);
        $repository = new ContentRepository($this->config);
        $existing = $repository->findForAdminEdit($contentTranslationId);

        if ($existing === null) {
            $this->response->redirect('/admin/content?error=missing');
        }

        $payload = $this->buildPersistedPayload($existing);
        $this->applySeoDraft($existing, $payload);
        $repository->updateTranslationAndSeo($contentTranslationId, $payload);

        $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&saved=seo-draft');
    }

    public function generateFullDraft(): never
    {
        $contentTranslationId = (int) $this->request->input('content_translation_id', 0);
        $repository = new ContentRepository($this->config);
        $existing = $repository->findForAdminEdit($contentTranslationId);

        if ($existing === null) {
            $this->response->redirect('/admin/content?error=missing');
        }

        $payload = $this->buildPersistedPayload($existing);
        $this->applySeoDraft($existing, $payload);
        $this->applyStructuredDraft($existing, $payload, false);
        $repository->updateTranslationAndSeo($contentTranslationId, $payload);

        $this->response->redirect('/admin/content/edit?id=' . $contentTranslationId . '&saved=full-draft');
    }

    private function renderTable(array $rows): string
    {
        if ($rows === []) {
            return '<p class="muted">Nema sadržaja za zadane filtere.</p>';
        }

        $html = '<table><thead><tr><th>Naslov</th><th>Tip</th><th>Jezik</th><th>Ruta</th><th>SEO title</th><th>Akcija</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            $seoTitle = trim((string) ($row['meta_title'] ?? ''));
            $html .= '<tr>'
                . '<td><strong>' . htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong></td>'
                . '<td>' . htmlspecialchars((string) ($row['content_type'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars(strtoupper((string) ($row['language_code'] ?? '')), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars((string) ($row['route_path'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars($seoTitle !== '' ? $seoTitle : '—', ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td><a class="button button-secondary" href="/admin/content/edit?id=' . (int) ($row['content_translation_id'] ?? 0) . '">Uredi</a></td>'
                . '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function renderTranslations(array $translations, int $currentContentTranslationId): string
    {
        if ($translations === []) {
            return '<p class="muted">Nema dodatnih prijevoda.</p>';
        }

        $html = '<div class="feature-list">';

        foreach ($translations as $translation) {
            $isCurrent = (int) ($translation['content_translation_id'] ?? 0) === $currentContentTranslationId;
            $html .= '<div class="feature-row"><strong>' . htmlspecialchars(strtoupper((string) ($translation['language_code'] ?? '')), ENT_QUOTES, 'UTF-8') . '</strong>'
                . '<span class="muted">' . htmlspecialchars((string) ($translation['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span>'
                . ($isCurrent
                    ? '<span class="badge">trenutno</span>'
                    : '<div class="card-actions"><a class="button button-secondary" href="/admin/content/edit?id=' . (int) ($translation['content_translation_id'] ?? 0) . '">Otvori</a></div>')
                . '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    private function renderAuditSnapshot(array $audit): string
    {
        $html = '<p class="muted">SEO score: <strong>' . (int) ($audit['seo_score'] ?? 0) . '</strong></p>'
            . '<p class="muted">Opis: <strong>' . (int) ($audit['effective_description_length'] ?? 0) . '</strong> znakova<br>Tekst: <strong>' . (int) ($audit['body_text_length'] ?? 0) . '</strong><br>Interni linkovi: <strong>' . (int) ($audit['internal_link_count'] ?? 0) . '</strong><br>FAQ: <strong>' . (int) ($audit['faq_count'] ?? 0) . '</strong></p>';

        if (!empty($audit['issues'])) {
            $html .= '<div class="feature-list">';

            foreach ((array) $audit['issues'] as $issue) {
                $html .= '<div class="feature-row"><strong>' . htmlspecialchars((string) ($issue['label'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong><span class="muted">' . htmlspecialchars((string) ($issue['help'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span></div>';
            }

            $html .= '</div>';

            return $html;
        }

        return $html . '<div class="notice">Ova stranica trenutno izgleda zdravo po glavnim SEO signalima.</div>';
    }

    private function renderLinkSuggestions(array $recommendations): string
    {
        $products = (array) ($recommendations['products'] ?? []);
        $articles = (array) ($recommendations['articles'] ?? []);

        if ($products === [] && $articles === []) {
            return '<p class="muted">Trenutno nema dodatnih prijedloga. Nakon dorade teksta audit i recommendation engine će biti još precizniji.</p>';
        }

        $html = '<p class="muted">Dodaj 1-3 kontekstualna linka prema vodičima proizvoda i povezanim člancima kako bi članak bolje vodio korisnika kroz sadržaj i kupovni tok.</p>';

        if ($products !== []) {
            $html .= '<div class="feature-list">';
            foreach ($products as $product) {
                $html .= '<div class="feature-row"><strong>' . htmlspecialchars((string) ($product['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong><span class="muted">' . htmlspecialchars((string) ($product['summary'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span><div class="card-actions"><a class="button button-secondary" href="' . htmlspecialchars((string) ($product['route_path'] ?? '/'), ENT_QUOTES, 'UTF-8') . '">Otvori vodič</a></div></div>';
            }
            $html .= '</div>';
        }

        if ($articles !== []) {
            $html .= '<div class="feature-list" style="margin-top:14px">';
            foreach ($articles as $article) {
                $html .= '<div class="feature-row"><strong>' . htmlspecialchars((string) ($article['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong><span class="muted">' . htmlspecialchars((string) ($article['summary'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span><div class="card-actions"><a class="button button-secondary" href="' . htmlspecialchars((string) ($article['route_path'] ?? '/'), ENT_QUOTES, 'UTF-8') . '">Otvori članak</a></div></div>';
            }
            $html .= '</div>';
        }

        return $html;
    }

    private function buildLinkSuggestionQuery(array $record): string
    {
        $parts = array_filter([
            trim((string) ($record['title'] ?? '')),
            trim((string) ($record['meta_description'] ?? '')),
            trim((string) ($record['excerpt'] ?? '')),
            mb_substr(trim(strip_tags((string) ($record['body_html'] ?? ''))), 0, 700),
        ], static fn (string $value): bool => $value !== '');

        return implode(' ', $parts);
    }

    private function renderSelectOptions(array $options, string $selectedValue): string
    {
        $html = '';

        foreach ($options as $value => $label) {
            $html .= '<option value="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"' . ((string) $value === $selectedValue ? ' selected' : '') . '>' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</option>';
        }

        return $html;
    }

    private function buildPersistedPayload(array $existing): array
    {
        return [
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
    }

    private function applySeoDraft(array $existing, array &$payload): void
    {
        $draft = (new SeoDraftGeneratorService())->generate($existing);

        if (trim((string) ($existing['excerpt'] ?? '')) === '' && trim((string) ($draft['excerpt'] ?? '')) !== '') {
            $payload['excerpt'] = (string) ($draft['excerpt'] ?? '');
        }

        if (trim((string) ($existing['meta_title'] ?? '')) === '' && trim((string) ($draft['meta_title'] ?? '')) !== '') {
            $payload['meta_title'] = (string) ($draft['meta_title'] ?? '');
        }

        if (trim((string) ($existing['meta_description'] ?? '')) === '' && trim((string) ($draft['meta_description'] ?? '')) !== '') {
            $payload['meta_description'] = (string) ($draft['meta_description'] ?? '');
        }
    }

    private function applyStructuredDraft(array $existing, array &$payload, bool $overwriteSummaryAndFaq): void
    {
        $draft = (new StructuredDraftGeneratorService())->generate($existing);
        $structuredContent = new StructuredContentService();
        $existingFaq = $structuredContent->decodeFaqJson((string) ($existing['faq_json'] ?? ''));

        if (($overwriteSummaryAndFaq || trim((string) ($existing['summary_html'] ?? '')) === '') && trim((string) ($draft['summary_html'] ?? '')) !== '') {
            $payload['summary_html'] = (string) ($draft['summary_html'] ?? '');
        }

        if (($overwriteSummaryAndFaq || $existingFaq === []) && !empty($draft['faq_items'])) {
            $payload['faq_json'] = $structuredContent->encodeFaqItems((array) ($draft['faq_items'] ?? []));
        }
    }
}
