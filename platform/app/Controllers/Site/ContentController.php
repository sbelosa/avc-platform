<?php

declare(strict_types=1);

namespace Avc\Controllers\Site;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\ContentRepository;
use Avc\Repositories\ProductRecommendationRepository;
use Avc\Services\Content\StructuredContentService;
use Avc\Services\Content\WordPressContentTransformer;
use Avc\Services\Geo\CountryDetector;
use Avc\Services\Geo\MarketResolver;
use Avc\Support\PageRenderer;

final class ContentController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function show(array $contentRecord): never
    {
        $title = trim((string) ($contentRecord['meta_title'] ?? $contentRecord['title'] ?? 'AVC'));
        $description = trim((string) ($contentRecord['meta_description'] ?? $contentRecord['excerpt'] ?? ''));
        $canonicalUrl = trim((string) ($contentRecord['canonical_url'] ?? (($this->config['base_url'] ?? '') . ($contentRecord['route_path'] ?? '/'))));
        $robotsDirectives = [
            ((int) ($contentRecord['robots_index'] ?? 1) === 1) ? 'index' : 'noindex',
            ((int) ($contentRecord['robots_follow'] ?? 1) === 1) ? 'follow' : 'nofollow',
        ];
        $contentType = (string) ($contentRecord['content_type'] ?? 'page');
        $languageCode = trim((string) ($contentRecord['language_code'] ?? 'hr')) ?: 'hr';
        $routePath = (string) ($contentRecord['route_path'] ?? '/');
        $transformer = new WordPressContentTransformer($this->config);
        $transformedBody = $transformer->transform((string) ($contentRecord['body_html'] ?? ''), $languageCode);
        $transformedSummary = $transformer->transform((string) ($contentRecord['summary_html'] ?? ''), $languageCode);
        $faqItems = (new StructuredContentService())->decodeFaqJson((string) ($contentRecord['faq_json'] ?? ''));
        $copy = $this->copy($languageCode);
        $faqItems = $this->preparePublicFaqItems($faqItems, $contentRecord, $contentType, $copy);
        $isHomeRoute = $this->isHomeRoute($routePath);

        if ($isHomeRoute) {
            $routePath = $this->homePathForLanguage($languageCode);
            $title = (string) ($copy['home_meta_title'] ?? $copy['home_title']);
            $description = (string) ($copy['home_meta_description'] ?? $copy['home_subtitle']);
            $canonicalUrl = $this->absoluteUrl($routePath);
            $homeHeroAssetPath = $this->brandLogoPath('stacked');
            $homeOpenGraphAssetPath = '/media/branding/avc-home-premium-og-wide.png';
            $contentRecord['route_path'] = $routePath;
            $contentRecord['title'] = $copy['home_title'];
            $contentRecord['meta_title'] = $title;
            $contentRecord['meta_description'] = $description;
            $contentRecord['canonical_url'] = $canonicalUrl;
            $contentRecord['open_graph_title'] = $title;
            $contentRecord['open_graph_description'] = $description;
            $contentRecord['featured_image_path'] = $homeHeroAssetPath;
            $contentRecord['open_graph_image_path'] = $homeOpenGraphAssetPath;
            $contentRecord['robots_index'] = 1;
            $contentRecord['robots_follow'] = 1;
            $robotsDirectives = ['index', 'follow'];
        }

        if (!$isHomeRoute) {
            $description = $this->humanizePublicText($description);
            if ($description === '') {
                $description = $this->humanizePublicText((string) ($contentRecord['excerpt'] ?? ''));
            }
            if ($description === '') {
                $description = $this->displayText((string) ($contentRecord['title'] ?? 'AVC'));
            }
            if ($contentType === 'product_guide') {
                $productDescription = $this->humanProductCardSummary($contentRecord, $description);
                if ($productDescription !== '') {
                    $description = $productDescription;
                }

                $productTitle = trim($this->displayText((string) ($contentRecord['title'] ?? '')));
                if ($productTitle !== '') {
                    $title = $productTitle . ' | ' . $this->brandName();
                    $contentRecord['open_graph_title'] = $title;
                }
            }

            $description = $this->ensureSearchDescription($description, $contentRecord, $contentType, $languageCode);
            $contentRecord['meta_description'] = $description;
            $contentRecord['meta_title'] = $title;
            $openGraphDescription = $this->humanizePublicText((string) ($contentRecord['open_graph_description'] ?? ''));
            if ($contentType === 'product_guide') {
                $openGraphDescription = $description;
            }
            $openGraphDescription = $this->ensureSearchDescription($openGraphDescription !== '' ? $openGraphDescription : $description, $contentRecord, $contentType, $languageCode);
            if ($openGraphDescription !== '') {
                $contentRecord['open_graph_description'] = $openGraphDescription;
            }
        }

        $contentRepository = new ContentRepository($this->config);
        $productRecommendation = isset($contentRecord['content_translation_id'])
            ? (new ProductRecommendationRepository($this->config))->findByContentTranslationId((int) $contentRecord['content_translation_id'])
            : null;
        $alternateLinks = $isHomeRoute
            ? $this->buildHomeAlternateLinks($languageCode)
            : $this->buildAlternateLinks((int) ($contentRecord['content_item_id'] ?? 0));
        $languageSwitcher = $this->renderLanguageSwitcher($alternateLinks, $languageCode);
        $recentArticles = $contentRepository->findCardsByType('article', $languageCode, 6, (int) ($contentRecord['content_item_id'] ?? 0));
        $recentProducts = $contentRepository->findCardsByType('product_guide', $languageCode, 6, (int) ($contentRecord['content_item_id'] ?? 0), true);
        if ($isHomeRoute) {
            $curated = $this->loadHomeEditorialCards($contentRepository, $languageCode, $recentProducts, $recentArticles);
            $recentProducts = $curated['products'];
            $recentArticles = $curated['articles'];
        }
        $homeGoalCards = $isHomeRoute ? $this->buildHomeGoalCards($contentRepository, $languageCode) : [];
        $marketContext = $this->buildMarketContext($productRecommendation, $recentProducts);
        $conversionProducts = !$isHomeRoute
            ? $this->buildConversionProducts($contentRecord, $languageCode, $recentProducts, $productRecommendation)
            : [];

        $body = $isHomeRoute
            ? $this->renderHomePage($contentRecord, $transformedBody, $copy, $languageSwitcher, $recentProducts, $recentArticles, $marketContext, $homeGoalCards)
            : $this->renderContentPage($contentRecord, $transformedBody, $transformedSummary, $faqItems, $copy, $languageSwitcher, $productRecommendation, $recentProducts, $recentArticles, $marketContext, $conversionProducts);

        $schemaJson = json_encode($this->buildSchema($contentRecord, $title, $description, $canonicalUrl, $faqItems, $recentProducts, $recentArticles, $homeGoalCards, $productRecommendation), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->response->html(PageRenderer::render($title, $body, [
            'lang' => $languageCode,
            'meta_description' => $description,
            'canonical_url' => $canonicalUrl,
            'robots' => implode(',', $robotsDirectives),
            'alternate_links' => $alternateLinks,
            'open_graph' => $this->buildOpenGraph($contentRecord, $title, $description, $canonicalUrl),
            'schema_json' => $schemaJson,
            'body_class' => $isHomeRoute ? 'site-public site-home' : 'site-public',
        ]));
    }

    private function renderHomePage(array $contentRecord, string $transformedBody, array $copy, string $languageSwitcher, array $recentProducts, array $recentArticles, array $marketContext, array $homeGoalCards): string
    {
        $languageCode = (string) ($contentRecord['language_code'] ?? 'hr');
        $routePath = (string) ($contentRecord['route_path'] ?? $this->homePathForLanguage($languageCode));
        $catalogPath = $this->catalogPathForLanguage($languageCode);
        $headline = (string) ($copy['home_headline'] ?? $contentRecord['title'] ?? $copy['home_title']);
        $description = trim((string) ($copy['home_subtitle'] ?? $contentRecord['meta_description'] ?? $contentRecord['excerpt'] ?? ''));
        $featuredProducts = array_slice($recentProducts, 0, 3);
        $featuredArticles = array_slice($recentArticles, 0, 3);

        $body = '<div class="shell">'
            . $this->renderSiteHeader($copy, false, $languageCode)
            . '<section class="hero"><div class="hero-panel home-hero-panel"><div class="home-hero-grid">'
            . '<div class="home-hero-copy"><span class="hero-kicker">' . htmlspecialchars($copy['home_kicker'], ENT_QUOTES, 'UTF-8') . '</span>'
            . '<div class="content-prose"><h1>' . htmlspecialchars($headline, ENT_QUOTES, 'UTF-8') . '</h1></div>'
            . '<p class="muted hero-intro">' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<div class="hero-note"><span class="badge">' . htmlspecialchars($copy['home_proof_badge_1'], ENT_QUOTES, 'UTF-8') . '</span><span class="badge">' . htmlspecialchars($copy['home_proof_badge_2'], ENT_QUOTES, 'UTF-8') . '</span><span class="badge">' . htmlspecialchars($copy['home_proof_badge_3'], ENT_QUOTES, 'UTF-8') . '</span></div>'
            . '<div class="hero-actions"><a class="button button-primary" href="' . htmlspecialchars($catalogPath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($copy['home_primary_cta'], ENT_QUOTES, 'UTF-8') . '</a><a class="button button-secondary" href="#ai-advisor">' . htmlspecialchars($copy['home_secondary_cta'], ENT_QUOTES, 'UTF-8') . '</a></div>'
            . $languageSwitcher
            . '</div>'
            . $this->renderHomeHeroPanel($contentRecord, $copy, $marketContext, $recentProducts, $recentArticles, $homeGoalCards)
            . '</div></div></section>'
            . '<section class="section-stack">'
            . '<div class="content-card" id="featured-products"><div class="section-heading"><div class="eyebrow">' . htmlspecialchars($copy['products_section_title'], ENT_QUOTES, 'UTF-8') . '</div><div class="content-prose"><h2>' . htmlspecialchars($copy['products_section_heading'], ENT_QUOTES, 'UTF-8') . '</h2></div><p>' . htmlspecialchars($copy['products_section_text'], ENT_QUOTES, 'UTF-8') . '</p></div>' . $this->renderCardGrid($featuredProducts === [] ? $recentProducts : $featuredProducts, $copy, 'product_guide', $routePath, 'home_card_shop') . '</div>'
            . $this->renderHomeAdvisorSection($contentRecord, $copy, $languageCode)
            . '<div class="content-card" id="latest-articles"><div class="section-heading"><div class="eyebrow">' . htmlspecialchars($copy['articles_section_title'], ENT_QUOTES, 'UTF-8') . '</div><div class="content-prose"><h2>' . htmlspecialchars($copy['articles_section_heading'], ENT_QUOTES, 'UTF-8') . '</h2></div><p>' . htmlspecialchars($copy['articles_section_text'], ENT_QUOTES, 'UTF-8') . '</p></div>' . $this->renderCardGrid($featuredArticles === [] ? $recentArticles : $featuredArticles, $copy, 'article', $routePath, 'home_article_card') . '</div>'
            . '</section>'
            . $this->renderSiteFooter($copy, $languageCode)
            . '</div>'
            . $this->renderDiscountLeadModal($copy)
            . $this->renderAiLeadScript($copy);

        return $body;
    }

    private function loadHomeEditorialCards(ContentRepository $contentRepository, string $languageCode, array $fallbackProducts, array $fallbackArticles): array
    {
        $curatedIds = $this->homeEditorialContentIds();
        $curatedProducts = $contentRepository->findCardsByContentItemIds($curatedIds['products'], $languageCode, true);
        $curatedArticles = $contentRepository->findCardsByContentItemIds($curatedIds['articles'], $languageCode);

        return [
            'products' => $this->mergeCuratedCards($curatedProducts, $fallbackProducts, 3),
            'articles' => $this->mergeCuratedCards($curatedArticles, $fallbackArticles, 3),
        ];
    }

    private function homeEditorialContentIds(): array
    {
        return [
            'products' => [456, 505, 511],
            'articles' => [447, 421, 453],
        ];
    }

    private function homeGoalContentIds(): array
    {
        return [
            'digestion' => 456,
            'skin' => 505,
            'energy' => 479,
            'immunity' => 471,
            'care' => 502,
        ];
    }

    private function buildHomeGoalCards(ContentRepository $contentRepository, string $languageCode): array
    {
        $goalContentIds = $this->homeGoalContentIds();
        $goalRows = $contentRepository->findCardsByContentItemIds(array_values($goalContentIds), $languageCode, true);
        $goalRowsById = [];

        foreach ($goalRows as $row) {
            $goalRowsById[(int) ($row['content_item_id'] ?? 0)] = $row;
        }

        $copy = $this->copy($languageCode);
        $cards = [];

        foreach ($goalContentIds as $goalKey => $contentItemId) {
            $row = $goalRowsById[$contentItemId] ?? null;
            $cards[] = [
                'key' => $goalKey,
                'title' => (string) ($copy['goal_' . $goalKey . '_title'] ?? ''),
                'text' => (string) ($copy['goal_' . $goalKey . '_text'] ?? ''),
                'href' => $this->goalLandingPath($languageCode, (string) $goalKey) ?: (string) ($row['route_path'] ?? '#featured-products'),
                'action' => (string) ($copy['goal_product_action'] ?? $copy['open_guide_button']),
                'product_title' => $row !== null ? $this->displayText((string) ($row['title'] ?? '')) : '',
            ];
        }

        $cards[] = [
            'key' => 'unsure',
            'title' => (string) ($copy['goal_unsure_title'] ?? $copy['home_secondary_cta']),
            'text' => (string) ($copy['goal_unsure_text'] ?? $copy['advisor_text']),
            'href' => $this->goalLandingPath($languageCode, 'unsure') ?: '#ai-advisor',
            'action' => (string) ($copy['goal_unsure_action'] ?? $copy['ask_ai_button']),
            'product_title' => '',
        ];

        return $cards;
    }

    private function goalLandingPath(string $languageCode, string $goalKey): string
    {
        $paths = [
            'hr' => [
                'digestion' => '/cilj/probava/',
                'skin' => '/cilj/koza/',
                'energy' => '/cilj/energija/',
                'immunity' => '/cilj/imunitet/',
                'care' => '/cilj/njega/',
                'unsure' => '/cilj/nisam-siguran/',
            ],
            'en' => [
                'digestion' => '/en/goal/digestion/',
                'skin' => '/en/goal/skin/',
                'energy' => '/en/goal/energy/',
                'immunity' => '/en/goal/immunity/',
                'care' => '/en/goal/care/',
                'unsure' => '/en/goal/not-sure/',
            ],
            'sl' => [
                'digestion' => '/sl/cilj/prebava/',
                'skin' => '/sl/cilj/koza/',
                'energy' => '/sl/cilj/energija/',
                'immunity' => '/sl/cilj/imunost/',
                'care' => '/sl/cilj/nega/',
                'unsure' => '/sl/cilj/nisem-preprican/',
            ],
        ];

        return (string) ($paths[strtolower($languageCode)][$goalKey] ?? '');
    }

    private function mergeCuratedCards(array $preferred, array $fallback, int $limit): array
    {
        $items = [];
        $seen = [];

        foreach (array_merge($preferred, $fallback) as $row) {
            $contentItemId = (int) ($row['content_item_id'] ?? 0);
            if ($contentItemId <= 0 || isset($seen[$contentItemId])) {
                continue;
            }

            $seen[$contentItemId] = true;
            $items[] = $row;

            if (count($items) >= $limit) {
                break;
            }
        }

        return $items;
    }

    private function renderContentPage(
        array $contentRecord,
        string $transformedBody,
        string $transformedSummary,
        array $faqItems,
        array $copy,
        string $languageSwitcher,
        ?array $productRecommendation,
        array $recentProducts,
        array $recentArticles,
        array $marketContext,
        array $conversionProducts
    ): string {
        $contentType = (string) ($contentRecord['content_type'] ?? 'page');
        $routePath = (string) ($contentRecord['route_path'] ?? '/');
        $languageCode = (string) ($contentRecord['language_code'] ?? 'hr');
        $title = $this->displayText((string) ($contentRecord['title'] ?? 'AVC'));
        $description = $this->humanizePublicText((string) ($contentRecord['meta_description'] ?? $contentRecord['excerpt'] ?? ''));
        $publishedAt = trim((string) ($contentRecord['published_at'] ?? ''));
        $formattedPublishedAt = $this->formatDisplayDate($publishedAt, $languageCode);
        $headingBadge = $contentType === 'product_guide' ? $copy['product_badge'] : ($contentType === 'article' ? $copy['article_badge'] : $copy['page_badge']);
        $shopButtonLabel = $this->copyValue($copy, 'shop_button', 'Nastavi na Forever shop');
        $sidebarShopButtonLabel = $this->copyValue($copy, 'sidebar_shop_button', $shopButtonLabel);
        $stickyShopPrimaryLabel = $this->copyValue($copy, 'sticky_shop_primary', $shopButtonLabel);
        $ctaUrl = isset($contentRecord['content_translation_id'])
            ? $this->buildProductCtaUrl((int) $contentRecord['content_translation_id'], $languageCode, $routePath, 'product_hero_cta', $shopButtonLabel)
            : null;
        $sidebarCtaUrl = isset($contentRecord['content_translation_id'])
            ? $this->buildProductCtaUrl((int) $contentRecord['content_translation_id'], $languageCode, $routePath, 'product_sidebar_cta', $sidebarShopButtonLabel)
            : null;
        $panelCtaUrl = isset($contentRecord['content_translation_id'])
            ? $this->buildProductCtaUrl((int) $contentRecord['content_translation_id'], $languageCode, $routePath, 'product_hero_panel_cta', $shopButtonLabel)
            : null;
        $stickyCtaUrl = isset($contentRecord['content_translation_id'])
            ? $this->buildProductCtaUrl((int) $contentRecord['content_translation_id'], $languageCode, $routePath, 'product_sticky_mobile', $stickyShopPrimaryLabel)
            : null;
        $legacyReplacementUrl = '';

        if ($contentType === 'product_guide' && isset($contentRecord['content_translation_id'])) {
            $legacyReplacementUrl = $this->buildProductCtaUrl((int) $contentRecord['content_translation_id'], $languageCode, $routePath, 'product_legacy_body_link', 'Legacy shop link');
        } elseif ($contentType === 'article' && $conversionProducts !== []) {
            $legacyReplacementUrl = $this->buildProductCtaUrl(
                (int) ($conversionProducts[0]['content_translation_id'] ?? 0),
                (string) ($conversionProducts[0]['language_code'] ?? $languageCode),
                $routePath,
                'article_legacy_body_link',
                'Legacy shop link'
            );
        }

        if ($legacyReplacementUrl !== '') {
            $transformedBody = $this->rewriteLegacyShopLinks($transformedBody, $legacyReplacementUrl);
            $transformedSummary = $this->rewriteLegacyShopLinks($transformedSummary, $legacyReplacementUrl);
        }

        $transformedBody = $this->polishLegacyBodyCopy($transformedBody);

        $body = '<div class="shell">'
            . $this->renderSiteHeader($copy, false, $languageCode)
            . '<section class="hero"><div class="hero-panel split-hero">'
            . '<div class="hero-copy"><span class="hero-kicker">' . htmlspecialchars($headingBadge, ENT_QUOTES, 'UTF-8') . '</span>'
            . '<div class="content-prose"><h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1></div>'
            . '<p class="muted">' . htmlspecialchars($description !== '' ? $description : $copy['hero_fallback'], ENT_QUOTES, 'UTF-8') . '</p>'
            . $languageSwitcher;

        if ($contentType === 'product_guide') {
            $body .= '<div class="badge-row hero-meta-row"><span class="badge">' . htmlspecialchars($copy['market_detected_label'], ENT_QUOTES, 'UTF-8') . ': ' . htmlspecialchars((string) ($marketContext['market_label'] ?? '—'), ENT_QUOTES, 'UTF-8') . '</span><span class="badge">' . htmlspecialchars($copy['country_detected_label'], ENT_QUOTES, 'UTF-8') . ': ' . htmlspecialchars((string) ($marketContext['country_label'] ?? '—'), ENT_QUOTES, 'UTF-8') . '</span></div>';
        }

        if ($formattedPublishedAt !== '') {
            $body .= '<div class="badge-row hero-meta-row"><span class="badge">' . htmlspecialchars($copy['published_label'], ENT_QUOTES, 'UTF-8') . ': ' . htmlspecialchars($formattedPublishedAt, ENT_QUOTES, 'UTF-8') . '</span></div>';
        }

        if ($contentType === 'product_guide' && $ctaUrl !== null) {
            $body .= '<div class="cta-stack"><a class="button button-primary" href="' . htmlspecialchars($ctaUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($copy['shop_button'], ENT_QUOTES, 'UTF-8') . '</a><a class="button button-secondary" href="#ai-advisor">' . htmlspecialchars($copy['ask_ai_button'], ENT_QUOTES, 'UTF-8') . '</a></div>';
        }

        $body .= '</div>'
            . '<div class="glass-panel product-hero-card">' . $this->renderHeroSidePanel($contentRecord, $contentType, $productRecommendation, $copy, (string) $panelCtaUrl) . '</div>'
            . '</div></section>'
            . $this->renderBreadcrumbBar($contentRecord, $copy)
            . '<section class="layout">'
            . '<article class="content-card"><div class="content-prose">';

        if ($contentType !== 'product_guide' && !empty($contentRecord['featured_image_path'])) {
            $body .= '<p><img src="' . htmlspecialchars((string) $contentRecord['featured_image_path'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" loading="eager" decoding="async"></p>';
        }

        if ($transformedSummary !== '') {
            $body .= $this->renderSummaryBlock($transformedSummary, $copy, $contentType);
        }

        if ($contentType === 'product_guide' && $ctaUrl !== null) {
            $summaryCtaLabel = $this->copyValue($copy, 'buy_box_primary', $copy['shop_button'] ?? 'Nastavi');
            $body .= $this->renderProductDecisionBlock($copy, $this->buildProductCtaUrl((int) $contentRecord['content_translation_id'], $languageCode, $routePath, 'product_summary_cta', $summaryCtaLabel), '#ai-advisor', 'primary', $title);
        } elseif ($contentType === 'article') {
            $body .= $this->renderArticleProductCtaSection($conversionProducts, $copy, $routePath, $languageCode, 'article_top_products');
        }

        $body .= $transformedBody !== '' ? $transformedBody : '<p>' . htmlspecialchars($copy['empty_body'], ENT_QUOTES, 'UTF-8') . '</p>';

        if ($contentType === 'product_guide' && $ctaUrl !== null) {
            $bodyCtaLabel = $this->copyValue($copy, 'buy_box_primary', $copy['shop_button'] ?? 'Nastavi');
            $body .= $this->renderProductDecisionBlock($copy, $this->buildProductCtaUrl((int) $contentRecord['content_translation_id'], $languageCode, $routePath, 'product_body_cta', $bodyCtaLabel), '#ai-advisor', 'secondary', $title);
        } elseif ($contentType === 'article') {
            $body .= $this->renderArticleProductCtaSection($conversionProducts, $copy, $routePath, $languageCode, 'article_bottom_products');
        }

        if ($faqItems !== []) {
            $body .= $this->renderFaqBlock($faqItems, $copy);
        }

        $body .= $this->renderSupportCtaSection($copy, $languageCode)
            . $this->renderInlineRecommendationSection($copy['inline_products_title'], array_slice($recentProducts, 0, 3), $copy, 'product_guide', $routePath, 'inline_related_shop')
            . $this->renderInlineRecommendationSection($copy['inline_articles_title'], array_slice($recentArticles, 0, 3), $copy, 'article');

        $body .= '</div></article><aside class="sidebar">';

        if ($contentType === 'product_guide') {
            $body .= '<div class="sidebar-card sidebar-info-card"><div class="eyebrow">' . htmlspecialchars($copy['market_preview_label'], ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars((string) ($marketContext['market_label'] ?? '—'), ENT_QUOTES, 'UTF-8') . '</h3><p class="muted">' . htmlspecialchars($copy['market_preview_text'], ENT_QUOTES, 'UTF-8') . '</p></div>';
        } elseif ($contentType === 'article') {
            $body .= '<div class="sidebar-card sidebar-info-card"><div class="eyebrow">' . htmlspecialchars($this->copyValue($copy, 'article_sidebar_eyebrow', 'Dok čitaš'), ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars($this->copyValue($copy, 'article_sidebar_title', 'Kreni od svoje situacije'), ENT_QUOTES, 'UTF-8') . '</h3><p class="muted">' . htmlspecialchars($this->copyValue($copy, 'article_sidebar_text', 'Ne moraš zapamtiti sve. Dovoljno je prepoznati što se odnosi na tvoju rutinu, navike ili pitanje zbog kojeg si otvorio članak.'), ENT_QUOTES, 'UTF-8') . '</p></div>';
        }

        if ($contentType === 'product_guide' && $sidebarCtaUrl !== null) {
            $body .= '<div class="sidebar-card sidebar-buy-card"><div class="eyebrow">' . htmlspecialchars($copy['discount_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars($copy['discount_title'], ENT_QUOTES, 'UTF-8') . '</h3><p class="muted">' . htmlspecialchars($copy['discount_text'], ENT_QUOTES, 'UTF-8') . '</p><div class="cta-stack"><a class="button button-primary" href="' . htmlspecialchars($sidebarCtaUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($sidebarShopButtonLabel, ENT_QUOTES, 'UTF-8') . '</a></div></div>';
        }

        if ($contentType === 'product_guide') {
            $body .= '<div class="sidebar-card sidebar-info-card"><div class="eyebrow">' . htmlspecialchars($this->copyValue($copy, 'sidebar_reassurance_eyebrow', $copy['trust_eyebrow']), ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars($this->copyValue($copy, 'sidebar_reassurance_title', $copy['trust_title']), ENT_QUOTES, 'UTF-8') . '</h3><p class="muted">' . htmlspecialchars($this->copyValue($copy, 'sidebar_reassurance_text', $copy['trust_text']), ENT_QUOTES, 'UTF-8') . '</p></div>';
        } elseif ($contentType === 'article') {
            $body .= '<div class="sidebar-card sidebar-info-card"><div class="eyebrow">' . htmlspecialchars($this->copyValue($copy, 'article_sidebar_next_eyebrow', 'Ako želiš dalje'), ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars($this->copyValue($copy, 'article_sidebar_next_title', 'Poveži temu s praksom'), ENT_QUOTES, 'UTF-8') . '</h3><p class="muted">' . htmlspecialchars($this->copyValue($copy, 'article_sidebar_next_text', 'Kad ti se tema razjasni, možeš usporediti povezane proizvode ili postaviti pitanje ako još nisi siguran koji korak ima smisla.'), ENT_QUOTES, 'UTF-8') . '</p></div>';
        } else {
            $body .= '<div class="sidebar-card sidebar-info-card"><div class="eyebrow">' . htmlspecialchars($copy['trust_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars($copy['trust_title'], ENT_QUOTES, 'UTF-8') . '</h3><p class="muted">' . htmlspecialchars($copy['trust_text'], ENT_QUOTES, 'UTF-8') . '</p></div>';
        }

        if ($recentProducts !== []) {
            $body .= '<div class="sidebar-card sidebar-related-card sidebar-related-products"><div class="eyebrow">' . htmlspecialchars($copy['related_products_title'], ENT_QUOTES, 'UTF-8') . '</div>' . $this->renderCompactList($recentProducts, $copy, 'product_guide', $routePath, 'sidebar_related_shop') . '</div>';
        }

        if ($recentArticles !== []) {
            $body .= '<div class="sidebar-card sidebar-related-card sidebar-related-articles"><div class="eyebrow">' . htmlspecialchars($copy['related_articles_title'], ENT_QUOTES, 'UTF-8') . '</div>' . $this->renderCompactList($recentArticles, $copy, 'article') . '</div>';
        }

        $body .= '<div class="sidebar-card sidebar-advisor-card" id="ai-advisor"><div class="eyebrow">' . htmlspecialchars($copy['advisor_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars($copy['advisor_title'], ENT_QUOTES, 'UTF-8') . '</h3><p class="muted">' . htmlspecialchars($copy['advisor_text'], ENT_QUOTES, 'UTF-8') . '</p>'
            . $this->renderAiLeadNotice($copy)
            . $this->renderAiLeadForm($contentRecord, $copy, $languageCode)
            . '</div>'
            . '</aside></section>'
            . $this->renderSiteFooter($copy, $languageCode)
            . '</div>'
            . $this->renderDiscountLeadModal($copy)
            . ($contentType === 'product_guide' && $stickyCtaUrl !== null ? $this->renderStickyMobileCta($copy, $stickyCtaUrl, '#ai-advisor', $title) : '')
            . $this->renderAiLeadScript($copy);

        return $body;
    }

    private function renderSiteHeader(array $copy, bool $showAdminLink, string $languageCode = 'hr'): string
    {
        $adminLink = $showAdminLink ? '<a href="/admin/login">' . htmlspecialchars($copy['nav_admin'], ENT_QUOTES, 'UTF-8') . '</a>' : '';
        $homePath = $this->homePathForLanguage($languageCode);
        $catalogPath = $this->catalogPathForLanguage($languageCode);
        $articlePath = $this->articleCatalogPathForLanguage($languageCode);
        $brandName = $this->brandName();
        $brandLogo = $this->brandLogoPath('horizontal');

        return '<header class="site-header"><div class="header-card">'
            . '<a class="brand" href="' . htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8') . '"><span class="brand-lockup"><img class="brand-logo" src="' . htmlspecialchars($brandLogo, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8') . '" loading="eager" decoding="async"><span class="brand-copy"><strong class="brand-name">' . htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8') . '</strong><span class="brand-tagline">' . htmlspecialchars($copy['brand_tagline'], ENT_QUOTES, 'UTF-8') . '</span></span></span></a>'
            . '<nav class="header-links"><a href="' . htmlspecialchars($homePath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($copy['nav_home'], ENT_QUOTES, 'UTF-8') . '</a><a href="' . htmlspecialchars($catalogPath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($copy['nav_guides'], ENT_QUOTES, 'UTF-8') . '</a><a href="' . htmlspecialchars($articlePath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($copy['nav_articles'], ENT_QUOTES, 'UTF-8') . '</a><a href="' . htmlspecialchars($homePath . '#ai-advisor', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($copy['nav_support'], ENT_QUOTES, 'UTF-8') . '</a>' . $adminLink . '</nav>'
            . '</div></header>';
    }

    private function renderSiteFooter(array $copy, string $languageCode = 'hr'): string
    {
        $links = $this->authorityFooterLinks($languageCode);
        $html = '<footer class="site-footer"><div class="content-card"><strong>' . htmlspecialchars($copy['footer_title'], ENT_QUOTES, 'UTF-8') . '</strong><p class="muted">' . htmlspecialchars($copy['footer_text'], ENT_QUOTES, 'UTF-8') . '</p><div class="footer-links">';

        foreach ($links as $label => $path) {
            $html .= '<a href="' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
        }

        return $html . '</div></div></footer>';
    }

    private function authorityFooterLinks(string $languageCode): array
    {
        return match (strtolower(trim($languageCode))) {
            'en' => [
                'About AVC' => '/en/about/',
                'How recommendations work' => '/en/how-recommendations-work/',
                'Editorial policy' => '/en/editorial-policy/',
            ],
            'sl' => [
                'O nas' => '/sl/o-nas/',
                'Kako delujejo priporočila' => '/sl/kako-delujejo-priporocila/',
                'Uredniška politika' => '/sl/uredniska-politika/',
            ],
            default => [
                'O nama' => '/o-nama/',
                'Kako radimo preporuke' => '/kako-rade-preporuke/',
                'Urednička politika' => '/urednicka-politika/',
            ],
        };
    }

    private function renderCardGrid(array $rows, array $copy, string $type, string $sourcePath = '/', string $shopSource = 'card_shop'): string
    {
        if ($rows === []) {
            return '<p class="muted">' . htmlspecialchars($copy['cards_empty'], ENT_QUOTES, 'UTF-8') . '</p>';
        }

        $html = '<div class="card-grid card-grid-' . htmlspecialchars($type === 'article' ? 'articles' : 'guides', ENT_QUOTES, 'UTF-8') . '">';

        foreach ($rows as $row) {
            $title = $this->displayText((string) ($row['title'] ?? ''));
            $description = $this->summarizeCard($row, $type);
            $detailPath = (string) ($row['route_path'] ?? '#');
            $publishedAt = $type === 'article'
                ? $this->formatDisplayDate((string) ($row['published_at'] ?? ''), (string) ($row['language_code'] ?? 'hr'))
                : '';
            $cardClass = $type === 'article' ? 'story-card story-card-article' : 'story-card story-card-guide';
            $shopButtonLabel = $this->copyValue($copy, 'shop_button', 'Nastavi na Forever shop');
            $secondaryAction = $type === 'product_guide'
                ? '<a class="card-link" href="' . htmlspecialchars($this->buildProductCtaUrl((int) ($row['content_translation_id'] ?? 0), (string) ($row['language_code'] ?? 'hr'), $sourcePath, $shopSource, $shopButtonLabel), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($shopButtonLabel, ENT_QUOTES, 'UTF-8') . '</a>'
                : '';

            $html .= '<article class="' . htmlspecialchars($cardClass, ENT_QUOTES, 'UTF-8') . '">'
                . '<div class="badge-row"><span class="badge">' . htmlspecialchars($type === 'product_guide' ? $copy['product_badge'] : $copy['article_badge'], ENT_QUOTES, 'UTF-8') . '</span></div>';

            if (!empty($row['featured_image_path'])) {
                $html .= '<a class="card-image' . ($type === 'article' ? ' card-image-article' : '') . '" href="' . htmlspecialchars($detailPath, ENT_QUOTES, 'UTF-8') . '"><img src="' . htmlspecialchars((string) $row['featured_image_path'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" loading="lazy" decoding="async">' . ($type === 'article' ? '<span class="card-image-overlay"><span class="badge">' . htmlspecialchars($copy['article_badge'], ENT_QUOTES, 'UTF-8') . '</span></span>' : '') . '</a>';
            }

            $html .= '<h3><a href="' . htmlspecialchars($detailPath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</a></h3>'
                . ($publishedAt !== '' ? '<p class="card-meta">' . htmlspecialchars($publishedAt, ENT_QUOTES, 'UTF-8') . '</p>' : '')
                . '<p>' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '</p>'
                . '<div class="card-actions"><a class="' . htmlspecialchars($type === 'product_guide' ? 'button button-secondary' : 'card-link', ENT_QUOTES, 'UTF-8') . '" href="' . htmlspecialchars($detailPath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($type === 'product_guide' ? $copy['open_guide_button'] : $copy['open_article_button'], ENT_QUOTES, 'UTF-8') . '</a>' . $secondaryAction . '</div>'
                . '</article>';
        }

        $html .= '</div>';

        return $html;
    }

    private function renderCompactList(array $rows, array $copy, string $type, string $sourcePath = '/', string $shopSource = 'compact_shop'): string
    {
        $isSidebarList = str_starts_with($shopSource, 'sidebar_');
        $listClass = $type === 'article' ? 'feature-list feature-list-articles' : 'feature-list feature-list-products';
        $listClass .= $isSidebarList ? ' feature-list-sidebar' : ' feature-list-inline';
        $html = '<div class="' . htmlspecialchars($listClass, ENT_QUOTES, 'UTF-8') . '">';

        foreach (array_slice($rows, 0, 4) as $row) {
            $detailPath = (string) ($row['route_path'] ?? '#');
            $title = $this->displayText((string) ($row['title'] ?? ''));
            $thumbnail = $this->renderContentThumbnail($row, $title, $detailPath, $type);
            $rowTypeClass = $type === 'article' ? 'feature-row-article' : 'feature-row-product';
            $rowClass = ($thumbnail !== '' ? 'feature-row feature-row-media' : 'feature-row') . ' ' . $rowTypeClass . ($isSidebarList ? ' feature-row-sidebar' : '');
            $shopButtonLabel = $isSidebarList
                ? $this->copyValue($copy, 'sidebar_related_shop_button', $this->copyValue($copy, 'sidebar_shop_button', $this->copyValue($copy, 'shop_button', 'Nastavi na Forever shop')))
                : $this->copyValue($copy, 'shop_button', 'Nastavi na Forever shop');
            $detailButtonLabel = $isSidebarList
                ? $this->copyValue($copy, 'sidebar_open_guide_button', $copy['open_guide_button'])
                : $copy['open_guide_button'];
            $cta = $type === 'product_guide'
                ? '<div class="card-actions feature-actions feature-actions-product"><a class="button button-secondary" href="' . htmlspecialchars($detailPath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($detailButtonLabel, ENT_QUOTES, 'UTF-8') . '</a><a class="button button-primary" href="' . htmlspecialchars($this->buildProductCtaUrl((int) ($row['content_translation_id'] ?? 0), (string) ($row['language_code'] ?? 'hr'), $sourcePath, $shopSource, $shopButtonLabel), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($shopButtonLabel, ENT_QUOTES, 'UTF-8') . '</a></div>'
                : '<div class="card-actions feature-actions"><a class="button button-secondary" href="' . htmlspecialchars($detailPath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($copy['open_article_button'], ENT_QUOTES, 'UTF-8') . '</a></div>';

            $html .= '<div class="' . $rowClass . '">' . $thumbnail . '<div class="feature-row-copy"><strong>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</strong><span class="muted">' . htmlspecialchars($this->summarizeCard($row, $type), ENT_QUOTES, 'UTF-8') . '</span>' . $cta . '</div></div>';
        }

        $html .= '</div>';

        return $html;
    }

    private function renderContentThumbnail(array $row, string $title, string $detailPath, string $type, string $extraClass = ''): string
    {
        $imagePath = trim((string) ($row['featured_image_path'] ?? ''));
        if ($imagePath === '') {
            return '';
        }

        $classes = trim('feature-thumb ' . ($type === 'product_guide' ? 'feature-thumb-product' : 'feature-thumb-article') . ' ' . $extraClass);

        return '<a class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '" href="' . htmlspecialchars($detailPath, ENT_QUOTES, 'UTF-8') . '"><img src="' . htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" loading="lazy" decoding="async"></a>';
    }

    private function renderHeroSidePanel(array $contentRecord, string $contentType, ?array $productRecommendation, array $copy, string $ctaUrl = ''): string
    {
        if ($contentType !== 'product_guide' || $productRecommendation === null) {
            return $this->renderArticleHeroSidePanel($copy);
        }

        $title = $this->displayText((string) ($contentRecord['title'] ?? ''));
        $imagePath = trim((string) ($contentRecord['featured_image_path'] ?? ''));
        $html = '';

        if ($imagePath !== '') {
            $image = '<img src="' . htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" loading="eager" decoding="async">';
            $html .= $ctaUrl !== ''
                ? '<a class="product-hero-media" href="' . htmlspecialchars($ctaUrl, ENT_QUOTES, 'UTF-8') . '">' . $image . '</a>'
                : '<div class="product-hero-media">' . $image . '</div>';
        }

        $html .= '<div class="feature-list product-panel-stats">';

        if (!empty($productRecommendation['sale_price']) || !empty($productRecommendation['price'])) {
            $price = (float) ($productRecommendation['sale_price'] ?: $productRecommendation['price']);
            $html .= '<div class="feature-row product-panel-row product-panel-price"><strong>' . htmlspecialchars($copy['price_from'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars(number_format($price, 2), ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars((string) ($productRecommendation['currency_code'] ?? 'EUR'), ENT_QUOTES, 'UTF-8') . '</strong><span class="muted">' . htmlspecialchars($copy['price_help'], ENT_QUOTES, 'UTF-8') . '</span></div>';
        }

        $html .= '<div class="feature-row product-panel-row product-panel-safe"><strong>' . htmlspecialchars($copy['discount_eyebrow'], ENT_QUOTES, 'UTF-8') . '</strong><span class="muted">' . htmlspecialchars($copy['discount_text'], ENT_QUOTES, 'UTF-8') . '</span></div></div>';

        if ($ctaUrl !== '') {
            $html .= '<div class="cta-stack product-hero-panel-cta"><a class="button button-primary" href="' . htmlspecialchars($ctaUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($copy['shop_button'], ENT_QUOTES, 'UTF-8') . '</a></div>';
        }

        return $html;
    }

    private function renderArticleHeroSidePanel(array $copy): string
    {
        return '<div class="article-hero-assist">'
            . '<div class="eyebrow">' . htmlspecialchars($this->copyValue($copy, 'article_panel_eyebrow', 'Za lakše čitanje'), ENT_QUOTES, 'UTF-8') . '</div>'
            . '<h3>' . htmlspecialchars($this->copyValue($copy, 'article_panel_title', 'Uzmi ono što ti pomaže'), ENT_QUOTES, 'UTF-8') . '</h3>'
            . '<p class="muted">' . htmlspecialchars($this->copyValue($copy, 'article_panel_text', 'Članak je tu da razjasni temu i pomogne ti prepoznati što ima smisla za tvoju situaciju.'), ENT_QUOTES, 'UTF-8') . '</p>'
            . '<div class="article-assist-list">'
            . '<div class="article-assist-card"><strong>' . htmlspecialchars($this->copyValue($copy, 'article_panel_point_1_title', 'Kreni od pitanja'), ENT_QUOTES, 'UTF-8') . '</strong><span>' . htmlspecialchars($this->copyValue($copy, 'article_panel_point_1_text', 'Pročitaj dio koji se odnosi na tvoju potrebu i preskoči ono što ti nije važno.'), ENT_QUOTES, 'UTF-8') . '</span></div>'
            . '<div class="article-assist-card"><strong>' . htmlspecialchars($this->copyValue($copy, 'article_panel_point_2_title', 'Kad želiš sljedeći korak'), ENT_QUOTES, 'UTF-8') . '</strong><span>' . htmlspecialchars($this->copyValue($copy, 'article_panel_point_2_text', 'Ispod članka možeš vidjeti povezane proizvode ili pitati za osobniji smjer.'), ENT_QUOTES, 'UTF-8') . '</span></div>'
            . '</div>'
            . '</div>';
    }

    private function renderHomeHeroPanel(array $contentRecord, array $copy, array $marketContext, array $recentProducts, array $recentArticles, array $homeGoalCards): string
    {
        $featuredProduct = $recentProducts[0] ?? null;
        $featuredArticle = $recentArticles[0] ?? null;
        $brandLogo = $this->brandLogoPath('horizontal');
        $spotlightHtml = '';
        if ($featuredProduct !== null || $featuredArticle !== null) {
            $spotlight = $featuredProduct ?? $featuredArticle;
            $spotlightType = $featuredProduct !== null ? 'product_guide' : 'article';
            $spotlightLink = (string) ($spotlight['route_path'] ?? ($spotlightType === 'product_guide' ? '/#featured-products' : '/#latest-articles'));
            $spotlightAction = $spotlightType === 'product_guide' ? $copy['open_guide_button'] : $copy['open_article_button'];
            $spotlightLabel = $spotlightType === 'product_guide' ? $copy['home_spotlight_label'] : $copy['articles_section_title'];
            $spotlightImage = trim((string) ($spotlight['featured_image_path'] ?? ''));
            $spotlightImageHtml = $spotlightImage !== ''
                ? '<a class="showcase-feature-media" href="' . htmlspecialchars($spotlightLink, ENT_QUOTES, 'UTF-8') . '"><img src="' . htmlspecialchars($spotlightImage, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($this->displayText((string) ($spotlight['title'] ?? $copy['home_showcase_alt'])), ENT_QUOTES, 'UTF-8') . '" loading="eager" decoding="async"></a>'
                : '';

            $spotlightHtml = '<article class="showcase-feature' . ($spotlightImageHtml === '' ? ' showcase-feature-text-only' : '') . '">'
                . $spotlightImageHtml
                . '<div class="showcase-feature-copy"><div class="eyebrow">' . htmlspecialchars($spotlightLabel, ENT_QUOTES, 'UTF-8') . '</div><strong>' . htmlspecialchars($this->displayText((string) ($spotlight['title'] ?? '')), ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars(mb_strimwidth($this->summarizeCard($spotlight, $spotlightType), 0, 110, '…'), ENT_QUOTES, 'UTF-8') . '</p><a href="' . htmlspecialchars($spotlightLink, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($spotlightAction, ENT_QUOTES, 'UTF-8') . '</a></div></article>';
        }

        $html = '<div class="home-showcase">'
            . '<div class="showcase-media"><div class="showcase-header"><img class="showcase-brand-logo" src="' . htmlspecialchars($brandLogo, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($this->brandName(), ENT_QUOTES, 'UTF-8') . '" loading="eager" decoding="async"><div class="showcase-brand-copy"><strong>' . htmlspecialchars($copy['home_showcase_title'], ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($copy['home_showcase_text'], ENT_QUOTES, 'UTF-8') . '</p></div></div>'
            . $this->renderHomeGoalGrid($homeGoalCards, $copy)
            . $spotlightHtml . '</div></div>';

        return $html;
    }

    private function renderHomeGoalGrid(array $goalCards, array $copy): string
    {
        if ($goalCards === []) {
            return '';
        }

        $html = '<div class="goal-selector"><div><div class="eyebrow">' . htmlspecialchars($copy['home_goal_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><strong>' . htmlspecialchars($copy['home_goal_title'], ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($copy['home_goal_text'], ENT_QUOTES, 'UTF-8') . '</p></div><div class="goal-grid">';

        foreach ($goalCards as $card) {
            $html .= $this->renderHomeGoalCard($card);
        }

        $html .= '</div></div>';

        return $html;
    }

    private function renderHomeGoalCard(array $card): string
    {
        $productTitle = trim((string) ($card['product_title'] ?? ''));
        $productHtml = $productTitle !== ''
            ? '<span class="goal-card-product">' . htmlspecialchars($productTitle, ENT_QUOTES, 'UTF-8') . '</span>'
            : '';

        return '<a class="goal-card goal-card-' . htmlspecialchars((string) ($card['key'] ?? 'item'), ENT_QUOTES, 'UTF-8') . '" href="' . htmlspecialchars((string) ($card['href'] ?? '#'), ENT_QUOTES, 'UTF-8') . '">'
            . '<span class="goal-card-title">' . htmlspecialchars((string) ($card['title'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span>'
            . '<span class="goal-card-text">' . htmlspecialchars((string) ($card['text'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span>'
            . $productHtml
            . '<span class="goal-card-action">' . htmlspecialchars((string) ($card['action'] ?? ''), ENT_QUOTES, 'UTF-8') . '</span>'
            . '</a>';
    }

    private function renderHowItWorksSection(array $copy): string
    {
        return '<div class="content-card" id="how-it-works"><div class="section-heading"><div class="eyebrow">' . htmlspecialchars($copy['trust_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><div class="content-prose"><h2>' . htmlspecialchars($copy['how_it_works_title'], ENT_QUOTES, 'UTF-8') . '</h2></div><p>' . htmlspecialchars($copy['trust_text'], ENT_QUOTES, 'UTF-8') . '</p></div><div class="step-grid">'
            . $this->renderHowStepCard('1', $copy['how_step_1_title'], $copy['how_step_1_text'])
            . $this->renderHowStepCard('2', $copy['how_step_2_title'], $copy['how_step_2_text'])
            . $this->renderHowStepCard('3', $copy['how_step_3_title'], $copy['how_step_3_text'])
            . '</div></div>';
    }

    private function renderSupportCtaSection(array $copy, string $languageCode): string
    {
        $catalogPath = $this->catalogPathForLanguage($languageCode);

        return '<section class="summary-box support-cta-box"><div class="eyebrow">' . htmlspecialchars($copy['support_cta_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><div class="content-prose"><h2>' . htmlspecialchars($copy['support_cta_title'], ENT_QUOTES, 'UTF-8') . '</h2><p>' . htmlspecialchars($copy['support_cta_text'], ENT_QUOTES, 'UTF-8') . '</p></div><div class="card-actions"><a class="button button-primary" href="#ai-advisor">' . htmlspecialchars($copy['support_cta_primary'], ENT_QUOTES, 'UTF-8') . '</a><a class="button button-secondary" href="' . htmlspecialchars($catalogPath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($copy['support_cta_secondary'], ENT_QUOTES, 'UTF-8') . '</a></div></section>';
    }

    private function renderHomeTrustStrip(array $copy, array $marketContext): string
    {
        return '<section class="proof-bar">'
            . '<article class="proof-point"><div class="eyebrow">' . htmlspecialchars($copy['home_proof_badge_1'], ENT_QUOTES, 'UTF-8') . '</div><strong>' . htmlspecialchars($copy['home_feature_1_title'], ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($copy['brand_tagline'], ENT_QUOTES, 'UTF-8') . '</p></article>'
            . '<article class="proof-point"><div class="eyebrow">' . htmlspecialchars($copy['home_proof_badge_2'], ENT_QUOTES, 'UTF-8') . '</div><strong>' . htmlspecialchars($copy['discount_title'], ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($copy['discount_text'], ENT_QUOTES, 'UTF-8') . '</p></article>'
            . '<article class="proof-point"><div class="eyebrow">' . htmlspecialchars($copy['home_proof_badge_3'], ENT_QUOTES, 'UTF-8') . '</div><strong>' . htmlspecialchars($copy['market_detected_label'], ENT_QUOTES, 'UTF-8') . ': ' . htmlspecialchars((string) ($marketContext['market_label'] ?? 'AUTO'), ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($copy['home_feature_3_text'], ENT_QUOTES, 'UTF-8') . '</p></article>'
            . '</section>';
    }

    private function renderHomeIntentGrid(array $copy): string
    {
        return '<div class="intent-grid">'
            . $this->renderIntentCard('#featured-products', $copy['intent_guides_eyebrow'], $copy['home_primary_cta'], $copy['products_section_text'], $copy['open_guide_button'])
            . $this->renderIntentCard('#ai-advisor', $copy['intent_advisor_eyebrow'], $copy['advisor_title'], $copy['advisor_text'], $copy['ask_ai_button'])
            . $this->renderIntentCard('#latest-articles', $copy['intent_articles_eyebrow'], $copy['articles_section_heading'], $copy['articles_section_text'], $copy['open_article_button'])
            . '</div>';
    }

    private function renderIntentCard(string $href, string $eyebrow, string $title, string $text, string $linkLabel): string
    {
        return '<a class="intent-card" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"><span class="eyebrow">' . htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</p><span class="intent-link">' . htmlspecialchars($linkLabel, ENT_QUOTES, 'UTF-8') . '</span></a>';
    }

    private function renderShowcaseShortcut(string $href, string $eyebrow, string $title, string $text): string
    {
        return '<a class="showcase-shortcut" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"><span class="eyebrow">' . htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</p></a>';
    }

    private function renderHowStepCard(string $number, string $title, string $text): string
    {
        $title = preg_replace('/^\d+\.\s*/u', '', $title) ?? $title;

        return '<article class="step-card"><span class="step-number">' . htmlspecialchars($number, ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</p></article>';
    }

    private function renderHomeAdvisorSection(array $contentRecord, array $copy, string $languageCode): string
    {
        return '<div class="content-card" id="ai-advisor"><div class="advisor-layout"><div class="advisor-copy"><div class="section-heading"><div class="eyebrow">' . htmlspecialchars($copy['advisor_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><div class="content-prose"><h2>' . htmlspecialchars($copy['advisor_title'], ENT_QUOTES, 'UTF-8') . '</h2></div><p>' . htmlspecialchars($copy['advisor_text'], ENT_QUOTES, 'UTF-8') . '</p></div><div class="advisor-benefit-grid">'
            . '<article class="advisor-benefit"><div class="eyebrow">' . htmlspecialchars($copy['advisor_example_1_label'], ENT_QUOTES, 'UTF-8') . '</div><strong>' . htmlspecialchars($copy['advisor_example_1_title'], ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($copy['advisor_example_1_text'], ENT_QUOTES, 'UTF-8') . '</p></article>'
            . '<article class="advisor-benefit"><div class="eyebrow">' . htmlspecialchars($copy['advisor_example_2_label'], ENT_QUOTES, 'UTF-8') . '</div><strong>' . htmlspecialchars($copy['advisor_example_2_title'], ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($copy['advisor_example_2_text'], ENT_QUOTES, 'UTF-8') . '</p></article>'
            . '<article class="advisor-benefit"><div class="eyebrow">' . htmlspecialchars($copy['advisor_example_3_label'], ENT_QUOTES, 'UTF-8') . '</div><strong>' . htmlspecialchars($copy['advisor_example_3_title'], ENT_QUOTES, 'UTF-8') . '</strong><p>' . htmlspecialchars($copy['advisor_example_3_text'], ENT_QUOTES, 'UTF-8') . '</p></article>'
            . '</div></div><div class="advisor-surface">' . $this->renderAiLeadForm($contentRecord, $copy, $languageCode) . '</div></div></div>';
    }

    private function renderHomeAuthoritySection(array $copy, array $marketContext): string
    {
        return '<div class="content-card"><div class="section-heading"><div class="eyebrow">' . htmlspecialchars($copy['trust_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><div class="content-prose"><h2>' . htmlspecialchars($copy['trust_title'], ENT_QUOTES, 'UTF-8') . '</h2></div><p>' . htmlspecialchars($copy['trust_text'], ENT_QUOTES, 'UTF-8') . '</p></div><div class="authority-grid">'
            . '<article class="authority-card"><div class="eyebrow">' . htmlspecialchars($copy['discount_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars($copy['discount_title'], ENT_QUOTES, 'UTF-8') . '</h3><p>' . htmlspecialchars($copy['discount_text'], ENT_QUOTES, 'UTF-8') . '</p></article>'
            . '<article class="authority-card"><div class="eyebrow">' . htmlspecialchars($copy['market_preview_label'], ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars((string) ($marketContext['market_label'] ?? 'AUTO'), ENT_QUOTES, 'UTF-8') . '</h3><p>' . htmlspecialchars($copy['market_preview_text'], ENT_QUOTES, 'UTF-8') . '</p></article>'
            . '<article class="authority-card"><div class="eyebrow">' . htmlspecialchars($copy['support_cta_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><h3>' . htmlspecialchars($copy['support_cta_title'], ENT_QUOTES, 'UTF-8') . '</h3><p>' . htmlspecialchars($copy['support_cta_text'], ENT_QUOTES, 'UTF-8') . '</p></article>'
            . '</div></div>';
    }

    private function renderInlineRecommendationSection(string $title, array $rows, array $copy, string $type, string $sourcePath = '/', string $shopSource = 'inline_shop'): string
    {
        if ($rows === []) {
            return '';
        }

        $sectionClass = $type === 'article' ? 'inline-section inline-section-articles' : 'inline-section inline-section-products';

        return '<section class="' . htmlspecialchars($sectionClass, ENT_QUOTES, 'UTF-8') . '"><div class="content-prose"><h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2></div>' . $this->renderCompactList($rows, $copy, $type, $sourcePath, $shopSource) . '</section>';
    }

    private function renderProductDecisionBlock(array $copy, string $ctaUrl, string $advisorHref, string $variant, string $productTitle = ''): string
    {
        if ($ctaUrl === '') {
            return '';
        }

        $productTitle = trim($productTitle);
        $title = $variant === 'primary'
            ? $this->copyWithProduct($copy, 'buy_box_title', 'Je li %s dobar izbor za tebe?', $productTitle)
            : $this->copyWithProduct($copy, 'buy_box_repeat_title', '%s - kad si spreman za narudžbu', $productTitle);
        $text = $variant === 'primary'
            ? $this->copyWithProduct($copy, 'buy_box_text', 'Ako ti opis proizvoda odgovara, klik vodi na službeni Forever shop za tvoje tržište. Ako još nisi siguran, prvo postavi pitanje.', $productTitle)
            : $this->copyWithProduct($copy, 'buy_box_repeat_text', 'Narudžbu završavaš na Forever shopu. AVC prije toga pomaže da ne odeš na krivi link ili pogrešno tržište.', $productTitle);

        return '<section class="conversion-box conversion-box-' . htmlspecialchars($variant, ENT_QUOTES, 'UTF-8') . '">'
            . '<div><div class="eyebrow">' . htmlspecialchars($this->copyValue($copy, 'buy_box_eyebrow', 'Narudžba'), ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div class="content-prose"><h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2><p>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</p></div></div>'
            . '<div class="conversion-actions"><a class="button button-primary" href="' . htmlspecialchars($ctaUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($this->copyValue($copy, 'buy_box_primary', $copy['shop_button'] ?? 'Nastavi'), ENT_QUOTES, 'UTF-8') . '</a>'
            . '<a class="button button-secondary" href="' . htmlspecialchars($advisorHref, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($this->copyValue($copy, 'buy_box_secondary', $copy['ask_ai_button'] ?? 'Zatraži preporuku'), ENT_QUOTES, 'UTF-8') . '</a></div>'
            . '</section>';
    }

    private function renderArticleProductCtaSection(array $rows, array $copy, string $sourcePath, string $languageCode, string $shopSource): string
    {
        if ($rows === []) {
            return '';
        }

        $html = '<section class="conversion-box article-product-block">'
            . '<div><div class="eyebrow">' . htmlspecialchars($this->copyValue($copy, 'article_products_eyebrow', 'Ako želiš konkretan korak'), ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div class="content-prose"><h2>' . htmlspecialchars($this->copyValue($copy, 'article_products_title', 'Proizvodi koje vrijedi pogledati uz ovu temu'), ENT_QUOTES, 'UTF-8') . '</h2><p>' . htmlspecialchars($this->copyValue($copy, 'article_products_text', 'Ovo nisu obavezne kupnje, nego najbliže opcije za usporedbu ako želiš prijeći iz čitanja u odluku.'), ENT_QUOTES, 'UTF-8') . '</p></div></div>'
            . '<div class="conversion-product-grid">';

        foreach (array_slice($rows, 0, 3) as $row) {
            $title = $this->displayText((string) ($row['title'] ?? ''));
            $detailPath = (string) ($row['route_path'] ?? '#');
            $thumbnail = $this->renderContentThumbnail($row, $title, $detailPath, 'product_guide', 'conversion-product-thumb');
            $primaryLabel = $this->copyValue($copy, 'article_products_primary', $copy['shop_button'] ?? 'Naruči');
            $shopUrl = $this->buildProductCtaUrl(
                (int) ($row['content_translation_id'] ?? 0),
                (string) ($row['language_code'] ?? $languageCode),
                $sourcePath,
                $shopSource,
                $primaryLabel
            );

            $html .= '<article class="conversion-product-card">' . $thumbnail . '<strong>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</strong>'
                . '<p>' . htmlspecialchars($this->summarizeCard($row, 'product_guide'), ENT_QUOTES, 'UTF-8') . '</p>'
                . '<span class="recommendation-reason">' . htmlspecialchars($this->buildRecommendationReason($row, $sourcePath, $languageCode), ENT_QUOTES, 'UTF-8') . '</span>'
                . '<div class="card-actions"><a class="button button-secondary" href="' . htmlspecialchars($detailPath, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($this->copyValue($copy, 'article_products_secondary', $copy['open_guide_button'] ?? 'Pogledaj vodič'), ENT_QUOTES, 'UTF-8') . '</a>'
                . '<a class="button button-primary" href="' . htmlspecialchars($shopUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($primaryLabel, ENT_QUOTES, 'UTF-8') . '</a></div></article>';
        }

        return $html . '</div></section>';
    }

    private function renderStickyMobileCta(array $copy, string $ctaUrl, string $advisorHref, string $productTitle = ''): string
    {
        if ($ctaUrl === '') {
            return '';
        }

        $stickyText = trim($productTitle) !== ''
            ? $this->copyWithProduct($copy, 'sticky_shop_text_product', '%s - kad si spreman', $productTitle)
            : $this->copyValue($copy, 'sticky_shop_text', 'Kad si spreman za narudžbu');

        return '<div class="mobile-shop-bar">'
            . '<div class="mobile-shop-copy"><span>' . htmlspecialchars($this->copyValue($copy, 'sticky_shop_prefix', 'Narudžba'), ENT_QUOTES, 'UTF-8') . '</span><strong>' . htmlspecialchars($stickyText, ENT_QUOTES, 'UTF-8') . '</strong></div>'
            . '<div class="mobile-shop-actions"><a class="button button-primary" href="' . htmlspecialchars($ctaUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($this->copyValue($copy, 'sticky_shop_primary', $copy['shop_button'] ?? 'Shop'), ENT_QUOTES, 'UTF-8') . '</a>'
            . '<a class="button button-secondary" href="' . htmlspecialchars($advisorHref, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($this->copyValue($copy, 'sticky_shop_secondary', $copy['ask_ai_button'] ?? 'Pitaj'), ENT_QUOTES, 'UTF-8') . '</a></div>'
            . '</div>';
    }

    private function buildProductCtaUrl(int $contentTranslationId, string $languageCode, string $sourcePath, string $source, string $ctaLabel = ''): string
    {
        if ($contentTranslationId <= 0) {
            return '#ai-advisor';
        }

        $normalizedSource = $this->normalizeClickSource($source);
        $ctaLabel = $this->normalizeWhitespace($this->displayText(strip_tags($ctaLabel)));
        $params = [
            'id' => $contentTranslationId,
            'lang' => strtolower(trim($languageCode)) ?: 'hr',
            'source_path' => trim($sourcePath) !== '' ? trim($sourcePath) : '/',
            'source' => $normalizedSource,
            'cta_position' => $normalizedSource,
        ];

        if ($ctaLabel !== '') {
            $params['cta_variant'] = $this->normalizeCtaVariant($ctaLabel);
            $params['cta_label'] = mb_substr($ctaLabel, 0, 180);
        }

        return '/go/product?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    private function rewriteLegacyShopLinks(string $html, string $replacementUrl): string
    {
        if (trim($html) === '' || trim($replacementUrl) === '') {
            return $html;
        }

        return preg_replace_callback('/href=(["\'])([^"\']+)\1/i', function (array $matches) use ($replacementUrl): string {
            $quote = (string) ($matches[1] ?? '"');
            $href = html_entity_decode((string) ($matches[2] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if (!$this->isLegacyShopUrl($href)) {
                return (string) ($matches[0] ?? '');
            }

            return 'href=' . $quote . htmlspecialchars($replacementUrl, ENT_QUOTES, 'UTF-8') . $quote;
        }, $html) ?? $html;
    }

    private function isLegacyShopUrl(string $href): bool
    {
        $parsed = parse_url(trim($href));
        if (!is_array($parsed)) {
            return false;
        }

        $host = strtolower((string) ($parsed['host'] ?? ''));
        $path = strtolower((string) ($parsed['path'] ?? ''));

        if (in_array($host, ['forevercard.club', 'www.forevercard.club', 'thealoeveraco.shop', 'www.thealoeveraco.shop', 'flpshop.ba', 'www.flpshop.ba', 'foreveralbania.com', 'www.foreveralbania.com'], true)) {
            return true;
        }

        return ($host === 'foreverliving.com' || $host === 'www.foreverliving.com' || str_ends_with($host, '.foreverliving.com'))
            && str_starts_with($path, '/shop/');
    }

    private function buildConversionProducts(array $contentRecord, string $languageCode, array $recentProducts, ?array $productRecommendation): array
    {
        if ((string) ($contentRecord['content_type'] ?? '') !== 'article') {
            return [];
        }

        $haystack = $this->normalizeSearchText(
            implode(' ', [
                (string) ($contentRecord['title'] ?? ''),
                (string) ($contentRecord['excerpt'] ?? ''),
                strip_tags((string) ($contentRecord['body_html'] ?? '')),
            ])
        );
        $intentMap = $this->productIntentMap();
        $scores = [];

        foreach ($intentMap as $intentKey => $intent) {
            $score = 0;
            foreach ((array) ($intent['keywords'] ?? []) as $keyword) {
                $normalizedKeyword = $this->normalizeSearchText((string) $keyword);
                if ($normalizedKeyword !== '' && $this->containsSearchKeyword($haystack, $normalizedKeyword)) {
                    $score++;
                }
            }

            if ($score > 0) {
                $scores[$intentKey] = $score;
            }
        }

        arsort($scores);
        $contentItemIds = [];

        foreach (array_keys($scores) as $intentKey) {
            foreach ((array) ($intentMap[$intentKey]['content_item_ids'] ?? []) as $contentItemId) {
                $contentItemIds[] = (int) $contentItemId;
            }
        }

        $contentItemIds = array_merge($this->directProductIntentContentIds($haystack), $contentItemIds);

        if ($contentItemIds === []) {
            $contentItemIds = [456, 505, 511];
        }

        $rows = (new ContentRepository($this->config))->findCardsByContentItemIds(array_values(array_unique($contentItemIds)), $languageCode, true);
        if ($rows === []) {
            $rows = $recentProducts;
        }

        $merged = [];
        $seen = [];

        foreach (array_merge($rows, $recentProducts) as $row) {
            $contentItemId = (int) ($row['content_item_id'] ?? 0);
            if ($contentItemId <= 0 || isset($seen[$contentItemId])) {
                continue;
            }

            $seen[$contentItemId] = true;
            $merged[] = $row;

            if (count($merged) >= 3) {
                break;
            }
        }

        return $merged;
    }

    private function productIntentMap(): array
    {
        return [
            'digestion' => [
                'keywords' => ['probava', 'prebava', 'digestion', 'crijeva', 'crevesje', 'gut', 'zeludac', 'želudac', 'stomach', 'fiber', 'vlakna', 'probiotic', 'probiotik', 'zgaravica', 'žgaravica', 'povracanje', 'povraćanje', 'vomiting', 'proljev', 'diarrhea', 'hemoroidi', 'hemorrhoids', 'kefir', 'jogurt', 'microbiom', 'mikrobiom'],
                'content_item_ids' => [456, 457, 480, 483],
            ],
            'skin' => [
                'keywords' => ['koza', 'koža', 'skin', 'nega', 'njega', 'collagen', 'kolagen', 'gelly', 'lice', 'face', 'acne', 'akne', 'osjetljiva', 'sensitive skin', 'dermatitis', 'ekcem', 'eczema', 'pristici', 'prištici', 'prištići', 'crvene fleke', 'red spots', 'psorijaza', 'psoriasis', 'opekline', 'burns', 'osip', 'hand gel'],
                'content_item_ids' => [505, 502, 500, 504],
            ],
            'energy' => [
                'keywords' => ['energija', 'energy', 'fokus', 'focus', 'umor', 'fatigue', 'vitalnost', 'vitality', 'argi', 'omega', 'stres', 'stress', 'koncentracija', 'concentration'],
                'content_item_ids' => [479, 465, 471, 466],
            ],
            'immunity' => [
                'keywords' => ['imunitet', 'immunity', 'immune', 'otpornost', 'prehlada', 'cink', 'zinc', 'vitamin', 'sezona', 'alergija', 'allergy', 'virus', 'virusi'],
                'content_item_ids' => [471, 464, 508, 456],
            ],
            'weight' => [
                'keywords' => ['mrsav', 'mršav', 'tezina', 'težina', 'hujš', 'weight', 'c9', 'clean 9', 'garcinia', 'detox', 'apetit', 'appetite', 'lite ultra', 'keto', 'ketogena', 'ketogenic', 'dieta', 'diet', 'jedilnik', 'hujšanje', 'mrsavljenje', 'mršavljenje', 'clean9', 'f15'],
                'content_item_ids' => [481, 488, 486, 484],
            ],
            'oral' => [
                'keywords' => ['zubi', 'zub', 'tooth', 'toothgel', 'pasta', 'desni', 'gums', 'oral'],
                'content_item_ids' => [511, 502, 456],
            ],
            'heart' => [
                'keywords' => ['srce', 'heart', 'omega', 'cholesterol', 'kolesterol', 'brain', 'mozak', 'krvotok'],
                'content_item_ids' => [465, 479, 480],
            ],
            'respiratory' => [
                'keywords' => ['kasalj', 'kašalj', 'cough', 'grlo', 'throat', 'sirup', 'luk', 'med', 'honey', 'prehlada', 'hladnoća', 'hladnoca', 'bronh', 'sinus', 'respiratory', 'propolis', 'timijan', 'thyme'],
                'content_item_ids' => [489, 492, 509, 471, 464],
            ],
            'thyroid_minerals' => [
                'keywords' => ['tsh', 'stitnjaca', 'štitnjača', 'thyroid', 'jod', 'iodine', 'selen', 'selenium', 'hormon', 'hormoni', 'hormone'],
                'content_item_ids' => [476, 477, 466, 462, 463],
            ],
            'aloe_purchase' => [
                'keywords' => ['gdje kupiti', 'kje kupiti', 'where to buy', 'buy aloe', 'kupiti aloe', 'aloe vera gdje kupiti', 'aloe u stanu', 'aloe vera u stanu', 'list aloe', 'rezati list', 'biljka aloe', 'aloe plant'],
                'content_item_ids' => [456, 502, 500, 504],
            ],
            'inflammation_natural' => [
                'keywords' => ['upala', 'upale', 'inflammation', 'kurkuma', 'turmeric', 'dumbir', 'ginger', 'cimet', 'cinnamon', 'detoks', 'detox', 'jetra', 'liver', 'oregano'],
                'content_item_ids' => [510, 509, 471, 491],
            ],
            'joints_mobility' => [
                'keywords' => ['zglob', 'zglobovi', 'joint', 'mobility', 'pokretljivost', 'kosti', 'bones', 'msm', 'koljeno', 'kolena', 'bol u ledima', 'back pain'],
                'content_item_ids' => [469, 461, 468, 503, 478],
            ],
            'hormone_vitality' => [
                'keywords' => ['maca', 'libido', 'plodnost', 'fertility', 'hormoni', 'menopauza', 'perimenopauza', 'valunzi', 'thyroid', 'umor', 'stres'],
                'content_item_ids' => [506, 462, 463, 466, 477],
            ],
        ];
    }

    private function directProductIntentContentIds(string $haystack): array
    {
        $productPatterns = [
            488 => ['garcinia', 'forever garcinia', 'garcinia cambogia'],
            481 => ['clean 9', 'clean9', 'c9 forever', 'c9 program', 'c9 recept', 'c9 detox'],
            486 => ['lite ultra', 'forever lite', 'ultra chocolate', 'protein shake'],
            484 => ['forever lean', 'lean'],
            487 => ['forever therm', 'therm'],
            505 => ['marine collagen', 'forever collagen', 'collagen', 'kolagen'],
            502 => ['aloe vera gelly', 'forever gelly', 'gelly'],
            504 => ['propolis creme', 'propolis krema', 'dermatitis', 'ekcem', 'eczema'],
            500 => ['aloe first', 'opekline', 'burns', 'osip', 'first aid'],
            503 => ['aloe msm', 'msm gel', 'bol u ledima', 'zglob'],
            456 => ['forever aloe vera gel', 'aloe vera gel'],
            457 => ['aloe berry nectar', 'berry nectar'],
            458 => ['aloe peaches', 'forever aloe peaches'],
            511 => ['toothgel', 'zubna pasta', 'pasta za zube', 'tooth gel'],
            465 => ['arctic sea', 'omega'],
            479 => ['argi', 'arginin', 'l-arginine'],
            471 => ['a-beta-care', 'beta care', 'a beta care'],
            480 => ['active pro b', 'active pro-b', 'probiotic', 'probiotik'],
            483 => ['fields of greens', 'fiber', 'vlakna'],
            506 => ['maca', 'multi maca', 'forever multi maca'],
            489 => ['bee honey', 'med', 'honey', 'sirup od luka', 'luk i med'],
            492 => ['bee propolis', 'propolis'],
            509 => ['garlic thyme', 'garlic-thyme', 'timijan', 'thyme', 'cesnjak', 'češnjak', 'kasalj', 'kašalj'],
            510 => ['aloeturm', 'aloe turm', 'kurkuma', 'turmeric'],
            476 => ['nature min', 'jod', 'iodine', 'selen', 'selenium', 'stitnjaca', 'štitnjača', 'tsh'],
            477 => ['forever daily', 'multivitamin'],
            508 => ['absorbent d', 'vitamin d'],
            464 => ['absorbent c', 'vitamin c'],
            491 => ['blossom herbal tea', 'herbal tea', 'čaj', 'caj'],
            468 => ['active ha', 'hyaluronic', 'hijaluron'],
            469 => ['esm complex', 'esm', 'zglobovi'],
            461 => ['forever freedom', 'freedom'],
            462 => ['vitolize women', 'menopauza', 'perimenopauza', 'valunzi'],
            463 => ['vitolize men'],
        ];
        $matches = [];

        foreach ($productPatterns as $contentItemId => $patterns) {
            foreach ($patterns as $pattern) {
                $normalizedPattern = $this->normalizeSearchText($pattern);
                if ($normalizedPattern !== '' && $this->containsSearchKeyword($haystack, $normalizedPattern)) {
                    $matches[] = (int) $contentItemId;
                    break;
                }
            }
        }

        return array_values(array_unique($matches));
    }

    private function buildRecommendationReason(array $row, string $sourcePath, string $languageCode): string
    {
        $haystack = $this->normalizeSearchText(implode(' ', [
            (string) ($row['title'] ?? ''),
            (string) ($row['slug'] ?? ''),
            (string) ($row['route_path'] ?? ''),
            (string) ($row['meta_description'] ?? ''),
            (string) ($row['excerpt'] ?? ''),
        ]));

        $languageCode = strtolower(trim($languageCode));
        $knownReason = $this->knownProductReason($haystack, $languageCode);
        if ($knownReason !== '') {
            return $knownReason;
        }

        $reasons = [
            'garcinia' => [
                'hr' => 'Najbliži je temi apetita, porcija i regulacije tjelesne težine.',
                'en' => 'Closest to appetite, portions and weight-management support.',
                'sl' => 'Najbližje je temi apetita, porcij in uravnavanja telesne teže.',
            ],
            'c9' => [
                'hr' => 'Dobar je za korisnike koji žele strukturirani početak i jasniji plan prvih dana.',
                'en' => 'Useful for people who want a structured start and a clearer first-days plan.',
                'sl' => 'Primeren je za tiste, ki želijo strukturiran začetek in jasnejši načrt prvih dni.',
            ],
            'lite' => [
                'hr' => 'Ima smisla kada želiš praktičniji obrok ili proteinsku podršku uz rutinu.',
                'en' => 'Helpful when you want a practical meal option or protein support in your routine.',
                'sl' => 'Smiselno je, ko želiš praktičen obrok ali beljakovinsko podporo v rutini.',
            ],
            'aloe_gel' => [
                'hr' => 'Najčešći je početak za svakodnevnu aloe rutinu i podršku probavi.',
                'en' => 'A common starting point for a daily aloe routine and digestion support.',
                'sl' => 'Pogost začetek za vsakodnevno aloe rutino in podporo prebavi.',
            ],
            'collagen' => [
                'hr' => 'Povezuje se s rutinom za kožu, kosu, nokte i osjećaj potpore zglobovima.',
                'en' => 'Connected with routines for skin, hair, nails and joint-support habits.',
                'sl' => 'Povezuje se z rutino za kožo, lase, nohte in podporo sklepom.',
            ],
            'toothgel' => [
                'hr' => 'Praktičan je izbor kada tema ide prema svakodnevnoj oralnoj njezi.',
                'en' => 'A practical choice when the topic is everyday oral care.',
                'sl' => 'Praktična izbira, kadar je tema vsakodnevna ustna nega.',
            ],
            'default' => [
                'hr' => 'Uvršten je jer se dobro nadovezuje na temu i pomaže lakše usporediti opcije.',
                'en' => 'Included because it connects with the topic and makes comparison easier.',
                'sl' => 'Dodano je, ker se dobro navezuje na temo in olajša primerjavo možnosti.',
            ],
        ];

        $key = 'default';
        if (str_contains($haystack, 'garcinia')) {
            $key = 'garcinia';
        } elseif (str_contains($haystack, 'c9') || str_contains($haystack, 'clean 9')) {
            $key = 'c9';
        } elseif (str_contains($haystack, 'lite ultra') || str_contains($haystack, 'protein') || str_contains($haystack, 'aminotein')) {
            $key = 'lite';
        } elseif (str_contains($haystack, 'aloe vera gel')) {
            $key = 'aloe_gel';
        } elseif (str_contains($haystack, 'collagen') || str_contains($haystack, 'kolagen')) {
            $key = 'collagen';
        } elseif (str_contains($haystack, 'toothgel') || str_contains($haystack, 'zub')) {
            $key = 'toothgel';
        }

        return $reasons[$key][$languageCode] ?? $reasons[$key]['hr'];
    }

    private function knownProductReason(string $haystack, string $languageCode): string
    {
        $languageCode = strtolower(trim($languageCode)) ?: 'hr';
        $definitions = [
            [
                'patterns' => ['c9', 'clean 9'],
                'copy' => [
                    'hr' => 'Vrijedi ga usporediti ako želiš strukturirani početak i jasan plan prvih dana.',
                    'en' => 'Worth comparing if you want a structured start and a clear first-days plan.',
                    'sl' => 'Vredno primerjave, če želiš strukturiran začetek in jasen načrt prvih dni.',
                ],
            ],
            [
                'patterns' => ['garcinia', 'therm', 'forever lean', 'lite ultra', 'fiber', 'vlakna'],
                'copy' => [
                    'hr' => 'Dobro se veže uz temu apetita, porcija, energije ili rutine kontrole težine.',
                    'en' => 'It fits topics such as appetite, portions, energy or a weight-management routine.',
                    'sl' => 'Dobro se navezuje na apetit, porcije, energijo ali rutino uravnavanja teže.',
                ],
            ],
            [
                'patterns' => ['aloe activator', 'gelly', 'aloe first', 'aloe msm', 'toothgel', 'collagen', 'kolagen', 'jojoba', 'liquid soap', 'ever shield', 'gentlemen', 'sunscreen', 'propolis creme', 'bakuchiol'],
                'copy' => [
                    'hr' => 'Najbliži je rutini njege, kože, kose ili svakodnevne osobne njege.',
                    'en' => 'Closest to a care routine for skin, hair or everyday personal care.',
                    'sl' => 'Najbližje je rutini nege kože, las ali vsakodnevne osebne nege.',
                ],
            ],
            [
                'patterns' => ['aloe berry', 'berry nectar', 'aloe peaches', 'aloe vera gel', 'active pro b', 'fields of greens'],
                'copy' => [
                    'hr' => 'Ima smisla ako temu gledaš kroz probavu i jednostavniju svakodnevnu rutinu.',
                    'en' => 'It makes sense if you are looking at the topic through digestion and a simpler daily routine.',
                    'sl' => 'Smiselno je, če temo gledaš skozi prebavo in preprostejšo dnevno rutino.',
                ],
            ],
            [
                'patterns' => ['freedom', 'active ha', 'esm complex', 'calcium', 'nature min'],
                'copy' => [
                    'hr' => 'Koristan je za usporedbu kada su tema zglobovi, kosti ili pokretljivost.',
                    'en' => 'Useful to compare when joints, bones or mobility are the topic.',
                    'sl' => 'Koristen za primerjavo, kadar so tema sklepi, kosti ali gibljivost.',
                ],
            ],
            [
                'patterns' => ['absorbent c', 'absorbent d', 'b12', 'daily', 'immublend', 'immune gummy', 'kids multivitamini'],
                'copy' => [
                    'hr' => 'Povezuje se s dnevnom podrškom imunitetu, energiji ili osnovnoj prehrani.',
                    'en' => 'Connected with daily support for immunity, energy or baseline nutrition.',
                    'sl' => 'Povezuje se z dnevno podporo odpornosti, energiji ali osnovni prehrani.',
                ],
            ],
            [
                'patterns' => ['arctic sea', 'omega', 'nutra q10', 'argi', 'arginin', 'focus', 'maca', 'vitolize'],
                'copy' => [
                    'hr' => 'Dobar je za usporedbu kada tražiš podršku za energiju, fokus ili vitalnost.',
                    'en' => 'Useful to compare when you are looking for energy, focus or vitality support.',
                    'sl' => 'Dober za primerjavo, ko iščeš podporo za energijo, fokus ali vitalnost.',
                ],
            ],
            [
                'patterns' => ['gelly', 'aloe first', 'aloe msm', 'toothgel', 'collagen', 'kolagen', 'jojoba', 'liquid soap', 'ever shield', 'gentlemen', 'sunscreen', 'aloe activator', 'propolis creme', 'bakuchiol'],
                'copy' => [
                    'hr' => 'Najbliži je rutini njege, kože, kose ili svakodnevne osobne njege.',
                    'en' => 'Closest to a care routine for skin, hair or everyday personal care.',
                    'sl' => 'Najbližje je rutini nege kože, las ali vsakodnevne osebne nege.',
                ],
            ],
            [
                'patterns' => ['bee honey', 'bee pollen', 'bee propolis', 'royal jelly', 'lycium', 'blossom herbal tea', 'garlic thyme', 'garlic-thyme', 'aloeturm', 'aloe turm'],
                'copy' => [
                    'hr' => 'Dobra je usporedba ako želiš prirodniju dnevnu podršku i mirniji ritual.',
                    'en' => 'A good comparison if you want natural daily support and a calmer ritual.',
                    'sl' => 'Dobra primerjava, če želiš naravnejšo dnevno podporo in mirnejši ritual.',
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            foreach ((array) $definition['patterns'] as $pattern) {
                $pattern = $this->normalizeSearchText((string) $pattern);
                if ($pattern !== '' && str_contains($haystack, $pattern)) {
                    $copy = (array) $definition['copy'];

                    return (string) ($copy[$languageCode] ?? $copy['hr'] ?? '');
                }
            }
        }

        return '';
    }

    private function containsSearchKeyword(string $haystack, string $needle): bool
    {
        $needle = $this->normalizeSearchText($needle);
        if ($needle === '') {
            return false;
        }

        if (str_contains($needle, ' ') || mb_strlen($needle) >= 5) {
            return str_contains($haystack, $needle);
        }

        return preg_match('/(?:^|[^\p{L}\p{N}])' . preg_quote($needle, '/') . '(?:$|[^\p{L}\p{N}])/u', $haystack) === 1;
    }

    private function normalizeSearchText(string $text): string
    {
        $text = $this->displayText(strip_tags($text));
        $text = mb_strtolower($text);
        $text = strtr($text, [
            'č' => 'c',
            'ć' => 'c',
            'đ' => 'd',
            'š' => 's',
            'ž' => 'z',
        ]);

        return $this->normalizeWhitespace($text);
    }

    private function ensureSearchDescription(string $description, array $contentRecord, string $contentType, string $languageCode): string
    {
        $description = $this->normalizeWhitespace($this->displayText(strip_tags($description)));
        if ($description === '') {
            $description = $this->normalizeWhitespace($this->displayText((string) ($contentRecord['title'] ?? $this->brandName())));
        }

        if (mb_strlen($description) >= 110) {
            return $description;
        }

        $suffix = match (strtolower($languageCode)) {
            'en' => match ($contentType) {
                'product_guide' => 'See who it may suit, how to fit it into a routine, and where to continue to the official Forever shop.',
                'article' => 'Read the practical context, key cautions, and related guides before deciding what fits your routine.',
                default => 'Find clear information, related guides, and the next useful step on Aloe Vera Centar.',
            },
            'sl' => match ($contentType) {
                'product_guide' => 'Poglej, komu lahko ustreza, kako ga vključiti v rutino in kje nadaljevati v uradni Forever trgovini.',
                'article' => 'Preberi praktičen kontekst, ključne opombe in povezane vodiče, preden se odločiš, kaj ustreza tvoji rutini.',
                default => 'Poišči jasne informacije, povezane vodiče in naslednji smiseln korak na Aloe Vera Centru.',
            },
            default => match ($contentType) {
                'product_guide' => 'Pogledaj kome može odgovarati, kako ga uklopiti u rutinu i gdje nastaviti prema službenom Forever shopu.',
                'article' => 'Pročitaj praktičan kontekst, važne napomene i povezane vodiče prije nego odlučiš što ima smisla za tvoju rutinu.',
                default => 'Pronađi jasne informacije, povezane vodiče i sljedeći korak na Aloe Vera Centru.',
            },
        };

        return $this->normalizeWhitespace($description . ' ' . $suffix);
    }

    private function normalizeClickSource(string $source): string
    {
        $source = strtolower(trim($source));
        $source = preg_replace('/[^a-z0-9_-]+/', '_', $source) ?? '';

        return trim($source, '_') !== '' ? mb_substr(trim($source, '_'), 0, 50) : 'content_cta';
    }

    private function normalizeCtaVariant(string $label): string
    {
        $label = mb_strtolower($this->normalizeWhitespace($this->displayText(strip_tags($label))));
        $label = strtr($label, [
            'č' => 'c',
            'ć' => 'c',
            'đ' => 'd',
            'š' => 's',
            'ž' => 'z',
        ]);
        $label = preg_replace('/[^a-z0-9_-]+/u', '_', $label) ?? '';
        $label = trim($label, '_');

        return $label !== '' ? mb_substr($label, 0, 80) : 'unlabeled_cta';
    }

    private function copyValue(array $copy, string $key, string $fallback): string
    {
        return (string) ($copy[$key] ?? $fallback);
    }

    private function copyWithProduct(array $copy, string $key, string $fallback, string $productTitle): string
    {
        $template = $this->copyValue($copy, $key, $fallback);
        if (!str_contains($template, '%s')) {
            return $template;
        }

        $productTitle = trim($productTitle) !== '' ? trim($productTitle) : $this->copyValue($copy, 'generic_product_name', 'ovaj proizvod');

        return sprintf($template, $productTitle);
    }

    private function renderBreadcrumbBar(array $contentRecord, array $copy): string
    {
        $items = $this->buildBreadcrumbItems($contentRecord, (string) ($contentRecord['title'] ?? ''));
        if (count($items) <= 1) {
            return '';
        }

        $html = '<div class="shell"><div class="content-card" style="padding:16px 24px"><nav class="crumbs" aria-label="Breadcrumb">';

        foreach ($items as $index => $item) {
            if ($index > 0) {
                $html .= '<span class="muted">/</span>';
            }

            $label = htmlspecialchars((string) ($item['label'] ?? ''), ENT_QUOTES, 'UTF-8');
            $href = trim((string) ($item['href'] ?? ''));

            $html .= $href !== ''
                ? '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">' . $label . '</a>'
                : '<span>' . $label . '</span>';
        }

        $html .= '</nav></div></div>';

        return $html;
    }

    private function buildBreadcrumbItems(array $contentRecord, string $title): array
    {
        $languageCode = strtolower(trim((string) ($contentRecord['language_code'] ?? 'hr'))) ?: 'hr';
        $homePath = $this->homePathForLanguage($languageCode);
        $homeLabel = match ($languageCode) {
            'en' => 'Home',
            'sl' => 'Domov',
            default => 'Početna',
        };

        return [
            ['label' => $homeLabel, 'href' => $homePath],
            ['label' => $title, 'href' => ''],
        ];
    }

    private function buildMarketContext(?array $productRecommendation, array $recentProducts): array
    {
        $countryCode = (new CountryDetector())->detect($this->request);
        $acceptLanguage = (string) $this->request->header('Accept-Language', '');
        $availableMarketCodes = [];

        if ($productRecommendation !== null) {
            $availableMarketCodes = array_keys((array) ($productRecommendation['market_urls'] ?? []));
        }

        if ($availableMarketCodes === []) {
            foreach ($recentProducts as $row) {
                foreach (array_keys((array) ($row['market_urls'] ?? [])) as $marketCode) {
                    $availableMarketCodes[] = (string) $marketCode;
                }
            }
        }

        $availableMarketCodes = array_values(array_unique(array_filter(array_map(static fn (string $value): string => strtolower(trim($value)), $availableMarketCodes))));
        $marketCode = (new MarketResolver())->resolvePreferredMarket($countryCode, $acceptLanguage, $availableMarketCodes);

        return [
            'country_code' => $countryCode !== null ? strtoupper($countryCode) : null,
            'country_label' => $countryCode !== null ? strtoupper($countryCode) : 'AUTO',
            'market_code' => $marketCode,
            'market_label' => strtoupper($marketCode),
        ];
    }

    private function renderAiLeadForm(array $contentRecord, array $copy, string $languageCode): string
    {
        $routePath = (string) ($contentRecord['route_path'] ?? '/');
        $sourceType = (string) ($contentRecord['content_type'] ?? 'page');
        $sourceTitle = $this->displayText((string) ($contentRecord['title'] ?? 'AVC'));
        $assistantName = $this->copyValue($copy, 'advisor_assistant_name', 'AVC savjetnik');
        $assistantStatus = $this->copyValue($copy, 'advisor_assistant_status', 'Tu sam da ti pomognem odabrati mirno i konkretno.');
        $quickPrompts = [
            [$this->copyValue($copy, 'advisor_quick_1_label', $copy['advisor_example_1_title'] ?? 'Probava'), $this->copyValue($copy, 'advisor_quick_1_text', $copy['advisor_example_1_text'] ?? '')],
            [$this->copyValue($copy, 'advisor_quick_2_label', $copy['advisor_example_2_title'] ?? 'Koža'), $this->copyValue($copy, 'advisor_quick_2_text', $copy['advisor_example_2_text'] ?? '')],
            [$this->copyValue($copy, 'advisor_quick_3_label', $copy['advisor_example_3_title'] ?? 'Energija'), $this->copyValue($copy, 'advisor_quick_3_text', $copy['advisor_example_3_text'] ?? '')],
        ];
        $promptButtons = '';

        foreach ($quickPrompts as [$label, $sample]) {
            $label = trim((string) $label);
            $sample = trim((string) $sample);
            if ($label === '' || $sample === '') {
                continue;
            }

            $promptButtons .= '<button type="button" data-advisor-sample="' . htmlspecialchars($sample, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</button>';
        }

        return '<div class="advisor-chat js-advisor-chat"'
            . ' data-content-translation-id="' . (int) ($contentRecord['content_translation_id'] ?? 0) . '"'
            . ' data-language-code="' . htmlspecialchars($languageCode, ENT_QUOTES, 'UTF-8') . '"'
            . ' data-source-path="' . htmlspecialchars($routePath, ENT_QUOTES, 'UTF-8') . '"'
            . ' data-source-type="' . htmlspecialchars($sourceType, ENT_QUOTES, 'UTF-8') . '"'
            . ' data-source-title="' . htmlspecialchars($sourceTitle, ENT_QUOTES, 'UTF-8') . '"'
            . ' data-assistant-name="' . htmlspecialchars($assistantName, ENT_QUOTES, 'UTF-8') . '"'
            . ' data-user-label="' . htmlspecialchars($this->copyValue($copy, 'advisor_user_label', 'Ti'), ENT_QUOTES, 'UTF-8') . '"'
            . ' data-typing-label="' . htmlspecialchars($this->copyValue($copy, 'advisor_typing', 'AVC savjetnik piše...'), ENT_QUOTES, 'UTF-8') . '"'
            . ' data-ready-label="' . htmlspecialchars($this->copyValue($copy, 'advisor_ready', 'Napiši što te zanima.'), ENT_QUOTES, 'UTF-8') . '">'
            . '<div class="advisor-chat-header"><span class="advisor-avatar">AVC</span><div><strong>' . htmlspecialchars($assistantName, ENT_QUOTES, 'UTF-8') . '</strong><span>' . htmlspecialchars($assistantStatus, ENT_QUOTES, 'UTF-8') . '</span></div></div>'
            . '<div class="notice advisor-guide">' . htmlspecialchars($copy['advisor_welcome_hint'], ENT_QUOTES, 'UTF-8') . '</div>'
            . ($promptButtons !== '' ? '<div class="advisor-prompt-chips">' . $promptButtons . '</div>' : '')
            . '<div class="advisor-messages js-advisor-messages"></div>'
            . '<div class="advisor-composer">'
            . '<div class="muted js-advisor-status">' . htmlspecialchars($copy['advisor_loading'], ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div class="advisor-input-row">'
            . '<textarea class="js-advisor-input" placeholder="' . htmlspecialchars($copy['advisor_message_placeholder'], ENT_QUOTES, 'UTF-8') . '"></textarea>'
            . '</div>'
            . '<div class="advisor-action-row"><button class="button button-secondary js-advisor-toggle-lead" type="button">' . htmlspecialchars($copy['advisor_personal_followup'], ENT_QUOTES, 'UTF-8') . '</button>'
            . '<button class="button button-primary js-advisor-send" type="button">' . htmlspecialchars($copy['advisor_send'], ENT_QUOTES, 'UTF-8') . '</button></div>'
            . '</div>'
            . '<form class="js-advisor-lead-form advisor-hidden">'
            . '<label>' . htmlspecialchars($copy['field_name'], ENT_QUOTES, 'UTF-8') . '<input type="text" name="name" placeholder="' . htmlspecialchars($copy['field_name_placeholder'], ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label>' . htmlspecialchars($copy['field_email'], ENT_QUOTES, 'UTF-8') . '<input type="email" name="email" required placeholder="' . htmlspecialchars($copy['field_email_placeholder'], ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label>' . htmlspecialchars($copy['field_phone'], ENT_QUOTES, 'UTF-8') . '<input type="text" name="phone" placeholder="' . htmlspecialchars($copy['field_phone_placeholder'], ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label>' . htmlspecialchars($copy['field_question'], ENT_QUOTES, 'UTF-8') . '<textarea name="question" placeholder="' . htmlspecialchars($copy['field_question_placeholder'], ENT_QUOTES, 'UTF-8') . '"></textarea></label>'
            . '<button class="button button-primary" type="submit">' . htmlspecialchars($copy['advisor_contact_submit'], ENT_QUOTES, 'UTF-8') . '</button>'
            . '</form>'
            . '</div>';
    }

    private function renderAiLeadNotice(array $copy): string
    {
        return '';
    }

    private function renderDiscountLeadModal(array $copy): string
    {
        return '<div class="discount-modal js-discount-modal" hidden aria-hidden="true">'
            . '<div class="discount-modal-backdrop js-discount-close" aria-hidden="true"></div>'
            . '<section class="discount-modal-card" role="dialog" aria-modal="true" aria-labelledby="discount-modal-title">'
            . '<button class="discount-modal-close js-discount-close" type="button" aria-label="' . htmlspecialchars($this->copyValue($copy, 'discount_modal_close', 'Zatvori'), ENT_QUOTES, 'UTF-8') . '">×</button>'
            . '<div class="discount-modal-head"><div class="eyebrow">' . htmlspecialchars($this->copyValue($copy, 'discount_modal_eyebrow', 'Forever Card popust'), ENT_QUOTES, 'UTF-8') . '</div>'
            . '<h2 id="discount-modal-title">' . htmlspecialchars($this->copyValue($copy, 'discount_modal_title', 'Aktiviraj 15% popusta prije odlaska u shop'), ENT_QUOTES, 'UTF-8') . '</h2>'
            . '<p class="muted">' . htmlspecialchars($this->copyValue($copy, 'discount_modal_text', 'Ostavi email ili mobitel i spremit ćemo ti link s popustom za ovaj proizvod. Nakon toga te odmah vodimo na službeni Forever Living Products shop.'), ENT_QUOTES, 'UTF-8') . '</p></div>'
            . '<form class="discount-form js-discount-form">'
            . '<label>' . htmlspecialchars($this->copyValue($copy, 'discount_field_name', $copy['field_name'] ?? 'Ime'), ENT_QUOTES, 'UTF-8') . '<input type="text" name="name" autocomplete="name" placeholder="' . htmlspecialchars($this->copyValue($copy, 'discount_field_name_placeholder', 'Kako se zoveš?'), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<div class="discount-contact-grid">'
            . '<label>' . htmlspecialchars($this->copyValue($copy, 'discount_field_email', $copy['field_email'] ?? 'Email'), ENT_QUOTES, 'UTF-8') . '<input type="email" name="email" autocomplete="email" placeholder="' . htmlspecialchars($this->copyValue($copy, 'discount_field_email_placeholder', 'Email za link s popustom'), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '<label>' . htmlspecialchars($this->copyValue($copy, 'discount_field_phone', $copy['field_phone'] ?? 'Telefon'), ENT_QUOTES, 'UTF-8') . '<input type="tel" name="phone" autocomplete="tel" placeholder="' . htmlspecialchars($this->copyValue($copy, 'discount_field_phone_placeholder', 'Mobitel ako ti je lakše'), ENT_QUOTES, 'UTF-8') . '"></label>'
            . '</div>'
            . '<div class="discount-status js-discount-status" role="status"></div>'
            . '<div class="discount-actions"><button class="button button-primary js-discount-submit" type="submit">' . htmlspecialchars($this->copyValue($copy, 'discount_modal_submit', 'Aktiviraj 15% popusta'), ENT_QUOTES, 'UTF-8') . '</button>'
            . '<button class="button button-secondary js-discount-skip" type="button">' . htmlspecialchars($this->copyValue($copy, 'discount_modal_skip', 'Nastavi bez popusta'), ENT_QUOTES, 'UTF-8') . '</button></div>'
            . '<p class="discount-note">' . htmlspecialchars($this->copyValue($copy, 'discount_modal_note', 'Kupnju završavaš na službenom Forever shopu. AVC samo sprema kontakt i vodi te na pravi link.'), ENT_QUOTES, 'UTF-8') . '</p>'
            . '</form></section></div>';
    }

    private function renderAiLeadScript(array $copy): string
    {
        return '<script>
            function avcEscapeHtml(value){
                return String(value || "")
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/\'/g, "&#039;");
            }

            function avcRenderAdvisorSection(title, items, detailLabel, shopLabel) {
                if(!items || !items.length) return "";

                return "<div class=\"feature-list\"><div class=\"eyebrow\">" + avcEscapeHtml(title) + "</div>" + items.map(function(item){
                    var actions = "<div class=\"card-actions\"><a class=\"button button-secondary\" href=\"" + avcEscapeHtml(item.route_path || "/") + "\">" + avcEscapeHtml(detailLabel) + "</a>";
                    if(item.shop_url) {
                        actions += "<a class=\"button button-primary\" href=\"" + avcEscapeHtml(item.shop_url) + "\">" + avcEscapeHtml(shopLabel) + "</a>";
                    }
                    actions += "</div>";

                    var reason = item.match_reason ? "<span class=\"recommendation-reason\">" + avcEscapeHtml(item.match_reason || "") + "</span>" : "";
                    var imageClass = item.content_type === "product_guide" ? " feature-thumb-product" : " feature-thumb-article";
                    var image = item.featured_image_path ? "<a class=\"feature-thumb" + imageClass + "\" href=\"" + avcEscapeHtml(item.route_path || "/") + "\"><img src=\"" + avcEscapeHtml(item.featured_image_path || "") + "\" alt=\"" + avcEscapeHtml(item.title || "") + "\" loading=\"lazy\" decoding=\"async\"></a>" : "";
                    var rowClass = image ? "feature-row feature-row-media" : "feature-row";
                    return "<div class=\"" + rowClass + "\">" + image + "<div class=\"feature-row-copy\"><strong>" + avcEscapeHtml(item.title || "") + "</strong><span class=\"muted\">" + avcEscapeHtml(item.summary || "") + "</span>" + reason + actions + "</div></div>";
                }).join("") + "</div>";
            }

            function avcRenderAdvisorExtras(payload, root) {
                var recommendations = (payload && payload.recommendations) || {};
                var html = "";

                html += avcRenderAdvisorSection(
                    recommendations.products_title || root.dataset.productsTitle || "Recommended products",
                    recommendations.products || [],
                    recommendations.open_guide_label || root.dataset.openGuideLabel || "Open guide",
                    recommendations.shop_label || root.dataset.shopLabel || "Go to shop"
                );

                html += avcRenderAdvisorSection(
                    recommendations.articles_title || root.dataset.articlesTitle || "Helpful articles",
                    recommendations.articles || [],
                    recommendations.open_article_label || root.dataset.openArticleLabel || "Open article",
                    recommendations.shop_label || root.dataset.shopLabel || "Go to shop"
                );

                if(payload && payload.lead_capture && payload.lead_capture.recommended) {
                    html += "<div class=\"notice\"><strong>" + avcEscapeHtml(payload.lead_capture.headline || "") + "</strong><br>" + avcEscapeHtml(payload.lead_capture.text || "") + "</div>";
                }

                return html;
            }

            function avcRenderAssistantTail(message, root) {
                var html = "";
                var payload = message.payload || {};
                var extras = avcRenderAdvisorExtras(payload, root);

                if(extras) {
                    html += "<div class=\"advisor-message-extra\">" + extras + "</div>";
                }

                if((payload || {}).kind !== "welcome" && (payload || {}).kind !== "lead_saved") {
                    html += "<div class=\"advisor-meta\"><span>" + avcEscapeHtml(root.dataset.feedbackPrompt || "") + "</span><div class=\"advisor-feedback\">"
                        + "<button type=\"button\" data-feedback-value=\"up\"" + ((message.feedback_value || "") === "up" ? " class=\"is-active\"" : "") + ">" + avcEscapeHtml(root.dataset.feedbackHelpful || "Helpful") + "</button>"
                        + "<button type=\"button\" data-feedback-value=\"down\"" + ((message.feedback_value || "") === "down" ? " class=\"is-active\"" : "") + ">" + avcEscapeHtml(root.dataset.feedbackNotHelpful || "Not helpful") + "</button>"
                        + "</div></div>";
                }

                return html;
            }

            function avcRenderMessage(message, root, options) {
                options = options || {};
                var role = message.role === "user" ? "user" : "assistant";
                var extraClass = options.extraClass ? " " + options.extraClass : "";
                var label = role === "user" ? (root.dataset.userLabel || "You") : (root.dataset.assistantName || "AVC");
                var html = "<article class=\"advisor-message " + role + extraClass + "\" data-message-id=\"" + avcEscapeHtml(message.message_public_id || "") + "\">";
                html += "<div class=\"advisor-message-label\">" + avcEscapeHtml(label) + "</div>";
                html += "<div class=\"advisor-message-body\">" + (options.emptyBody ? "" : avcEscapeHtml(message.body || "")) + "</div>";
                if(role === "assistant" && !options.deferTail) {
                    html += avcRenderAssistantTail(message, root);
                }
                html += "</article>";

                return html;
            }

            function avcInsertTypingIndicator(root) {
                var host = root.querySelector(".js-advisor-messages");
                if(!host) return null;

                var html = "<article class=\"advisor-message assistant advisor-message-typing\">"
                    + "<div class=\"advisor-message-label\">" + avcEscapeHtml(root.dataset.assistantName || "AVC") + "</div>"
                    + "<div class=\"advisor-typing-dots\" aria-label=\"" + avcEscapeHtml(root.dataset.typingLabel || "") + "\"><span></span><span></span><span></span></div>"
                    + "</article>";
                host.insertAdjacentHTML("beforeend", html);
                avcScrollMessages(host);

                return host.lastElementChild;
            }

            function avcTypeText(root, element, text, done) {
                var body = element ? element.querySelector(".advisor-message-body") : null;
                var host = root.querySelector(".js-advisor-messages");
                var parts = String(text || "").split(/(\\s+)/);
                var index = 0;
                var current = "";
                var batchSize = parts.length > 90 ? 4 : (parts.length > 42 ? 3 : 2);
                var delay = parts.length > 90 ? 28 : 42;

                function step() {
                    var added = 0;
                    while(index < parts.length && added < batchSize) {
                        current += parts[index];
                        if(!/^\\s+$/u.test(parts[index])) {
                            added += 1;
                        }
                        index += 1;
                    }

                    if(body) {
                        body.textContent = current;
                    }
                    avcScrollMessages(host);

                    if(index < parts.length) {
                        window.setTimeout(step, delay);
                    } else if(typeof done === "function") {
                        done();
                    }
                }

                window.setTimeout(step, 220);
            }

            function avcAppendAssistantMessage(root, message, done) {
                var host = root.querySelector(".js-advisor-messages");
                if(!host) {
                    if(typeof done === "function") done();
                    return;
                }

                host.insertAdjacentHTML("beforeend", avcRenderMessage(message, root, {
                    emptyBody: true,
                    deferTail: true,
                    extraClass: "is-streaming"
                }));

                var element = host.lastElementChild;
                avcScrollMessages(host);
                avcTypeText(root, element, message.body || "", function(){
                    if(element) {
                        element.classList.remove("is-streaming");
                        element.insertAdjacentHTML("beforeend", avcRenderAssistantTail(message, root));
                        element.querySelectorAll(".advisor-message-extra .feature-list,.advisor-message-extra .notice,.advisor-meta").forEach(function(node){
                            node.classList.add("is-revealed");
                        });
                    }
                    avcScrollMessages(host);
                    if(typeof done === "function") done();
                });
            }

            function avcScrollMessages(host) {
                if(!host) return;
                host.scrollTop = host.scrollHeight;
            }

            function avcRenderMessages(root, messages) {
                var host = root.querySelector(".js-advisor-messages");
                if(!host) return;
                messages = messages || [];
                host.innerHTML = "";

                if(messages.length === 1 && messages[0].role !== "user" && ((messages[0].payload || {}).kind === "welcome")) {
                    var typingNode = avcInsertTypingIndicator(root);
                    setTimeout(function(){
                        if(typingNode && typingNode.parentNode) {
                            typingNode.parentNode.removeChild(typingNode);
                        }
                        avcAppendAssistantMessage(root, messages[0], function(){
                            var statusNode = root.querySelector(".js-advisor-status");
                            if(statusNode) {
                                statusNode.textContent = root.dataset.readyLabel || "";
                            }
                        });
                    }, 420);
                    return;
                }

                host.innerHTML = messages.map(function(message){
                    return avcRenderMessage(message, root);
                }).join("");
                avcScrollMessages(host);
            }

            function avcJson(url, payload) {
                return fetch(url, {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload || {})
                }).then(function(response){
                    return response.json().then(function(data){
                        return {ok: response.ok, data: data || {}};
                    });
                });
            }

            document.querySelectorAll(".js-advisor-chat").forEach(function(root){
                if(!window.fetch) return;

                var status = root.querySelector(".js-advisor-status");
                var input = root.querySelector(".js-advisor-input");
                var sendButton = root.querySelector(".js-advisor-send");
                var toggleLeadButton = root.querySelector(".js-advisor-toggle-lead");
                var leadForm = root.querySelector(".js-advisor-lead-form");
                var conversationId = "";

                root.dataset.productsTitle = ' . json_encode((string) ($copy['products_title'] ?? 'Preporučeni proizvodi'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                root.dataset.articlesTitle = ' . json_encode((string) ($copy['articles_title'] ?? 'Korisni članci'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                root.dataset.openGuideLabel = ' . json_encode((string) ($copy['open_guide_button'] ?? 'Otvori vodič'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                root.dataset.openArticleLabel = ' . json_encode((string) ($copy['open_article_button'] ?? 'Otvori članak'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                root.dataset.shopLabel = ' . json_encode((string) ($copy['shop_button'] ?? 'Idi na kupnju'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                root.dataset.feedbackPrompt = ' . json_encode((string) ($copy['advisor_feedback_prompt'] ?? 'Je li ovo pomoglo?'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                root.dataset.feedbackHelpful = ' . json_encode((string) ($copy['advisor_feedback_helpful'] ?? 'Da'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                root.dataset.feedbackNotHelpful = ' . json_encode((string) ($copy['advisor_feedback_not_helpful'] ?? 'Ne baš'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';

                function setStatus(text) {
                    if(status) status.textContent = text || "";
                }

                function openLeadForm() {
                    if(!leadForm) return;
                    leadForm.classList.remove("advisor-hidden");
                    leadForm.scrollIntoView({behavior:"smooth", block:"start"});
                }

                function bootstrapConversation() {
                    setStatus(' . json_encode((string) ($copy['advisor_loading'] ?? 'Učitavam AI savjetnika...'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');
                    return avcJson("/api/advisor/conversation", {
                        content_translation_id: Number(root.dataset.contentTranslationId || 0),
                        language_code: root.dataset.languageCode || "hr",
                        source_path: root.dataset.sourcePath || "/",
                        source_type: root.dataset.sourceType || "page",
                        source_title: root.dataset.sourceTitle || "AVC"
                    }).then(function(result){
                        if(!result.ok || result.data.status !== "ok") {
                            throw new Error(result.data.message || "Conversation bootstrap failed.");
                        }

                        conversationId = result.data.conversation_public_id || "";
                        var bootstrapMessages = result.data.messages || [];
                        avcRenderMessages(root, bootstrapMessages);
                        if(!(bootstrapMessages.length === 1 && bootstrapMessages[0].role !== "user" && ((bootstrapMessages[0].payload || {}).kind === "welcome"))) {
                            setStatus(root.dataset.readyLabel || "");
                        }
                    }).catch(function(){
                        setStatus(' . json_encode((string) ($copy['advisor_error'] ?? 'AI savjetnik trenutno nije dostupan. Pokušaj ponovno za koji trenutak.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');
                    });
                }

                function sendCurrentMessage() {
                    if(!conversationId || !input) return;
                    var value = String(input.value || "").trim();
                    if(!value) return;
                    if(window.avcTrackEvent) {
                        window.avcTrackEvent("advisor_message_sent", {
                            source_path: root.dataset.sourcePath || window.location.pathname || "/",
                            source_type: root.dataset.sourceType || "page",
                            language_code: root.dataset.languageCode || document.documentElement.lang || "hr"
                        });
                    }

                    var host = root.querySelector(".js-advisor-messages");
                    var localUserMessage = {
                        message_public_id: "local-" + Date.now(),
                        role: "user",
                        body: value,
                        payload: {}
                    };
                    if(host) {
                        host.insertAdjacentHTML("beforeend", avcRenderMessage(localUserMessage, root));
                        avcScrollMessages(host);
                    }

                    input.value = "";
                    sendButton.disabled = true;
                    setStatus(root.dataset.typingLabel || ' . json_encode((string) ($copy['advisor_thinking'] ?? 'Uspoređujem proizvode s onim što si napisao...'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');
                    var typingNode = avcInsertTypingIndicator(root);

                    avcJson("/api/advisor/message", {
                        conversation_public_id: conversationId,
                        message: value
                    }).then(function(result){
                        if(!result.ok || result.data.status !== "ok") {
                            throw new Error(result.data.message || "Message failed.");
                        }

                        if(typingNode && typingNode.parentNode) {
                            typingNode.parentNode.removeChild(typingNode);
                        }

                        avcAppendAssistantMessage(root, result.data.message || {}, function(){
                            if(result.data.message && result.data.message.payload && result.data.message.payload.lead_capture && result.data.message.payload.lead_capture.recommended) {
                                openLeadForm();
                            }
                            setStatus(root.dataset.readyLabel || "");
                            sendButton.disabled = false;
                        });
                    }).catch(function(){
                        if(typingNode && typingNode.parentNode) {
                            typingNode.parentNode.removeChild(typingNode);
                        }
                        setStatus(' . json_encode((string) ($copy['advisor_error'] ?? 'AI savjetnik trenutno nije dostupan. Pokušaj ponovno za koji trenutak.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');
                        sendButton.disabled = false;
                    });
                }

                if(sendButton) {
                    sendButton.addEventListener("click", sendCurrentMessage);
                }

                if(input) {
                    input.addEventListener("keydown", function(event){
                        if(event.key === "Enter" && !event.shiftKey) {
                            event.preventDefault();
                            sendCurrentMessage();
                        }
                    });
                }

                if(toggleLeadButton) {
                    toggleLeadButton.addEventListener("click", function(){
                        openLeadForm();
                    });
                }

                root.querySelectorAll("[data-advisor-sample]").forEach(function(button){
                    button.addEventListener("click", function(){
                        if(!input) return;
                        input.value = button.getAttribute("data-advisor-sample") || "";
                        input.focus();
                        setStatus(root.dataset.readyLabel || "");
                    });
                });

                if(leadForm) {
                    leadForm.addEventListener("submit", function(event){
                        event.preventDefault();
                        if(!conversationId) return;
                        var formData = new FormData(leadForm);
                        setStatus(' . json_encode((string) ($copy['advisor_contact_saving'] ?? 'Spremam kontakt...'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');

                        avcJson("/api/advisor/lead", {
                            conversation_public_id: conversationId,
                            name: formData.get("name") || "",
                            email: formData.get("email") || "",
                            phone: formData.get("phone") || "",
                            question: formData.get("question") || ""
                        }).then(function(result){
                            if(!result.ok || result.data.status !== "ok") {
                                throw new Error(result.data.message || "Lead save failed.");
                            }

                            setStatus(result.data.message || "");
                            leadForm.classList.add("advisor-hidden");
                            if(window.avcTrackEvent) {
                                window.avcTrackEvent("advisor_lead_submit", {
                                    source_path: root.dataset.sourcePath || window.location.pathname || "/",
                                    source_type: root.dataset.sourceType || "page",
                                    language_code: root.dataset.languageCode || document.documentElement.lang || "hr"
                                });
                            }
                            bootstrapConversation();
                        }).catch(function(){
                            setStatus(' . json_encode((string) ($copy['advisor_lead_error'] ?? 'Kontakt trenutno nije spremljen. Pokušaj ponovno.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');
                        });
                    });
                }

                root.addEventListener("click", function(event){
                    var target = event.target;
                    if(!(target instanceof HTMLElement)) return;
                    var feedbackValue = target.getAttribute("data-feedback-value");
                    if(!feedbackValue) return;

                    var messageHost = target.closest(".advisor-message");
                    if(!messageHost || !conversationId) return;

                    avcJson("/api/advisor/feedback", {
                        conversation_public_id: conversationId,
                        message_public_id: messageHost.getAttribute("data-message-id") || "",
                        value: feedbackValue
                    }).then(function(result){
                        if(result.ok && result.data.status === "ok") {
                            messageHost.querySelectorAll("[data-feedback-value]").forEach(function(button){
                                button.classList.toggle("is-active", button.getAttribute("data-feedback-value") === feedbackValue);
                            });
                        }
                    });
                });

                bootstrapConversation();
            });

            (function(){
                var modal = document.querySelector(".js-discount-modal");
                if(!modal || !window.fetch) return;

                var form = modal.querySelector(".js-discount-form");
                var status = modal.querySelector(".js-discount-status");
                var submitButton = modal.querySelector(".js-discount-submit");
                var skipButton = modal.querySelector(".js-discount-skip");
                var pendingHref = "";
                var pendingPayload = null;
                var contactRequiredMessage = ' . json_encode((string) ($copy['discount_modal_contact_required'] ?? 'Upiši email ili mobitel kako bismo ti spremili link s popustom.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                var loadingMessage = ' . json_encode((string) ($copy['discount_modal_loading'] ?? 'Spremamo popust i otvaramo službeni shop...'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
                var genericErrorMessage = ' . json_encode((string) ($copy['discount_modal_error'] ?? 'Popust trenutno nije spremljen. Pokušaj ponovno ili nastavi bez popusta.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';

                function setDiscountStatus(message, isError) {
                    if(!status) return;
                    status.textContent = message || "";
                    status.classList.toggle("is-error", !!isError);
                }

                function parseProductLink(href) {
                    var url;
                    try {
                        url = new URL(href, window.location.origin);
                    } catch(error) {
                        return null;
                    }

                    if(url.pathname !== "/go/product") {
                        return null;
                    }

                    var contentTranslationId = Number(url.searchParams.get("id") || 0);
                    if(!contentTranslationId) {
                        return null;
                    }

                    return {
                        content_translation_id: contentTranslationId,
                        language_code: url.searchParams.get("lang") || document.documentElement.lang || "hr",
                        source_path: url.searchParams.get("source_path") || window.location.pathname || "/",
                        source: url.searchParams.get("source") || "content_cta",
                        cta_position: url.searchParams.get("cta_position") || url.searchParams.get("source") || "content_cta",
                        cta_variant: url.searchParams.get("cta_variant") || "discount_15_modal",
                        cta_label: url.searchParams.get("cta_label") || ""
                    };
                }

                function openDiscountModal(href, payload) {
                    pendingHref = href;
                    pendingPayload = payload;
                    if(window.avcTrackEvent) {
                        window.avcTrackEvent("discount_modal_open", Object.assign({}, payload, { event_source: "content_page" }));
                    }
                    modal.hidden = false;
                    modal.setAttribute("aria-hidden", "false");
                    document.body.classList.add("discount-modal-open");
                    setDiscountStatus("", false);
                    if(form) {
                        form.reset();
                        var firstInput = form.querySelector("input[name=\"email\"]") || form.querySelector("input");
                        window.setTimeout(function(){
                            if(firstInput) firstInput.focus();
                        }, 80);
                    }
                }

                function closeDiscountModal() {
                    modal.hidden = true;
                    modal.setAttribute("aria-hidden", "true");
                    document.body.classList.remove("discount-modal-open");
                    pendingHref = "";
                    pendingPayload = null;
                    setDiscountStatus("", false);
                }

                function hasPhone(value) {
                    return String(value || "").replace(/\\D+/g, "").length >= 6;
                }

                document.addEventListener("click", function(event){
                    var target = event.target;
                    if(!(target instanceof Element)) return;

                    var closeTarget = target.closest(".js-discount-close");
                    if(closeTarget) {
                        event.preventDefault();
                        closeDiscountModal();
                        return;
                    }

                    var link = target.closest("a[href]");
                    if(!link || link.getAttribute("target") || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                        return;
                    }

                    var payload = parseProductLink(link.getAttribute("href") || "");
                    if(!payload || link.getAttribute("data-discount-bypass") === "1") {
                        return;
                    }

                    event.preventDefault();
                    openDiscountModal(link.href || link.getAttribute("href") || "", payload);
                });

                document.addEventListener("keydown", function(event){
                    if(event.key === "Escape" && !modal.hidden) {
                        closeDiscountModal();
                    }
                });

                if(skipButton) {
                    skipButton.addEventListener("click", function(){
                        if(pendingHref) {
                            var redirect = pendingHref;
                            if(window.avcTrackEvent && pendingPayload) {
                                window.avcTrackEvent("forever_outbound_click", Object.assign({}, pendingPayload, {
                                    event_source: "discount_skip",
                                    click_type: "continue_without_discount"
                                }), function(){ window.location.href = redirect; });
                            } else {
                                window.location.href = redirect;
                            }
                        } else {
                            closeDiscountModal();
                        }
                    });
                }

                if(form) {
                    form.addEventListener("submit", function(event){
                        event.preventDefault();
                        if(!pendingPayload) return;

                        var formData = new FormData(form);
                        var email = String(formData.get("email") || "").trim();
                        var phone = String(formData.get("phone") || "").trim();

                        if(!email && !hasPhone(phone)) {
                            setDiscountStatus(contactRequiredMessage, true);
                            return;
                        }

                        if(submitButton) submitButton.disabled = true;
                        setDiscountStatus(loadingMessage, false);

                        var payload = Object.assign({}, pendingPayload, {
                            name: formData.get("name") || "",
                            email: email,
                            phone: phone,
                            consent_contact: true
                        });

                        avcJson("/api/discount-leads", payload).then(function(result){
                            if(!result.ok || result.data.status !== "ok") {
                                throw new Error(result.data.message || genericErrorMessage);
                            }

                            setDiscountStatus(result.data.message || loadingMessage, false);
                            var redirect = result.data.redirect_url || pendingHref;
                            if(window.avcTrackEvent) {
                                window.avcTrackEvent("discount_lead_submit", Object.assign({}, pendingPayload, {
                                    event_source: "content_page",
                                    discount_lead_id: result.data.discount_lead_id || "",
                                    customer_notified: !!result.data.customer_notified
                                }));
                                window.avcTrackEvent("forever_outbound_click", Object.assign({}, pendingPayload, {
                                    event_source: "discount_lead",
                                    click_type: "discount_submit"
                                }), function(){ window.location.href = redirect; });
                            } else {
                                window.location.href = redirect;
                            }
                        }).catch(function(error){
                            setDiscountStatus(error.message || genericErrorMessage, true);
                            if(submitButton) submitButton.disabled = false;
                        });
                    });
                }
            })();
        </script>';
    }

    private function buildAlternateLinks(int $contentItemId): array
    {
        if ($contentItemId <= 0) {
            return [];
        }

        $alternates = (new ContentRepository($this->config))->findAlternatesForContentItem($contentItemId);
        $mapped = array_map(function (array $alternate): array {
            $routePath = (string) ($alternate['route_path'] ?? '/');
            $href = $this->absoluteUrl((string) ($alternate['route_path'] ?? '/'));

            return [
                'hreflang' => (string) ($alternate['language_code'] ?? 'hr'),
                'href' => $href,
                'local_href' => $routePath !== '' ? $routePath : '/',
            ];
        }, $alternates);

        $defaultLanguage = strtolower((string) ($this->config['default_language'] ?? 'hr'));
        foreach ($mapped as $alternate) {
            if (strtolower((string) ($alternate['hreflang'] ?? '')) !== $defaultLanguage) {
                continue;
            }

            $mapped[] = [
                'hreflang' => 'x-default',
                'href' => (string) ($alternate['href'] ?? ''),
                'local_href' => (string) ($alternate['local_href'] ?? '/'),
            ];
            break;
        }

        return $mapped;
    }

    private function buildHomeAlternateLinks(string $currentLanguageCode): array
    {
        $paths = [
            'hr' => '/',
            'en' => '/en/',
            'sl' => '/sl/',
        ];

        $mapped = [];
        foreach ($paths as $languageCode => $path) {
            $mapped[] = [
                'hreflang' => $languageCode,
                'href' => $this->absoluteUrl($path),
                'local_href' => $path,
            ];
        }

        $defaultLanguage = strtolower((string) ($this->config['default_language'] ?? 'hr'));
        $defaultPath = $paths[$defaultLanguage] ?? '/';
        $mapped[] = [
            'hreflang' => 'x-default',
            'href' => $this->absoluteUrl($defaultPath),
            'local_href' => $defaultPath,
        ];

        return $mapped;
    }

    private function renderLanguageSwitcher(array $alternateLinks, string $currentLanguageCode): string
    {
        if ($alternateLinks === []) {
            return '';
        }

        $html = '<div class="locale-switcher">';

        foreach ($alternateLinks as $alternateLink) {
            $label = strtoupper((string) ($alternateLink['hreflang'] ?? ''));
            $href = (string) ($alternateLink['local_href'] ?? $alternateLink['href'] ?? '#');
            $isActive = strtolower((string) ($alternateLink['hreflang'] ?? '')) === strtolower($currentLanguageCode);

            if (strtolower((string) ($alternateLink['hreflang'] ?? '')) === 'x-default') {
                continue;
            }

            $html .= '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"' . ($isActive ? ' aria-current="page"' : '') . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
        }

        $html .= '</div>';

        return $html;
    }

    private function buildSchema(array $contentRecord, string $title, string $description, string $canonicalUrl, array $faqItems = [], array $recentProducts = [], array $recentArticles = [], array $homeGoalCards = [], ?array $productRecommendation = null): array
    {
        if ($this->isHomeRoute((string) ($contentRecord['route_path'] ?? '/'))) {
            $languageCode = strtolower((string) ($contentRecord['language_code'] ?? 'hr'));
            $featuredGuidesName = match ($languageCode) {
                'en' => 'Popular Forever products',
                'sl' => 'Priljubljeni Forever izdelki',
                default => 'Popularni Forever proizvodi',
            };
            $featuredArticlesName = match ($languageCode) {
                'en' => 'Helpful articles before a decision',
                'sl' => 'Koristni članki pred odločitvijo',
                default => 'Korisni članci prije odluke',
            };
            $goalListName = match ($languageCode) {
                'en' => 'Forever product goals',
                'sl' => 'Cilji za izbiro Forever izdelkov',
                default => 'Ciljevi za odabir Forever proizvoda',
            };
            $graph = [
                array_filter([
                    '@type' => 'WebPage',
                    'name' => $title,
                    'headline' => $title,
                    'description' => $description !== '' ? $description : null,
                    'url' => $canonicalUrl !== '' ? $canonicalUrl : null,
                    'inLanguage' => (string) ($contentRecord['language_code'] ?? 'hr'),
                ], static fn (mixed $value): bool => $value !== null && $value !== []),
                [
                    '@type' => 'Organization',
                    'name' => $this->brandName(),
                    'url' => rtrim((string) ($this->config['base_url'] ?? ''), '/') . '/',
                    'description' => $description !== '' ? $description : null,
                ],
                [
                    '@type' => 'WebSite',
                    'name' => $this->brandName(),
                    'url' => rtrim((string) ($this->config['base_url'] ?? ''), '/') . '/',
                    'inLanguage' => (string) ($contentRecord['language_code'] ?? 'hr'),
                ],
            ];

            if ($recentProducts !== []) {
                $featuredProductRows = array_slice($recentProducts, 0, 3);
                $graph[] = [
                    '@type' => 'ItemList',
                    'name' => $featuredGuidesName,
                    'url' => $canonicalUrl . '#featured-products',
                    'itemListElement' => array_map(function (array $item, int $index): array {
                        return [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'url' => $this->absoluteUrl((string) ($item['route_path'] ?? '/')),
                            'name' => $this->displayText((string) ($item['title'] ?? '')),
                        ];
                    }, $featuredProductRows, array_keys($featuredProductRows)),
                ];
            }

            if ($homeGoalCards !== []) {
                $goalRows = array_values(array_filter($homeGoalCards, static fn (array $item): bool => trim((string) ($item['title'] ?? '')) !== ''));
                $graph[] = [
                    '@type' => 'ItemList',
                    'name' => $goalListName,
                    'url' => $canonicalUrl !== '' ? $canonicalUrl : null,
                    'itemListElement' => array_map(function (array $item, int $index) use ($canonicalUrl): array {
                        $href = (string) ($item['href'] ?? '#');

                        return [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'url' => str_starts_with($href, '#') ? $canonicalUrl . $href : $this->absoluteUrl($href),
                            'name' => (string) ($item['title'] ?? ''),
                        ];
                    }, $goalRows, array_keys($goalRows)),
                ];
            }

            if ($recentArticles !== []) {
                $featuredArticleRows = array_slice($recentArticles, 0, 3);
                $graph[] = [
                    '@type' => 'ItemList',
                    'name' => $featuredArticlesName,
                    'url' => $canonicalUrl . '#latest-articles',
                    'itemListElement' => array_map(function (array $item, int $index): array {
                        return [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'url' => $this->absoluteUrl((string) ($item['route_path'] ?? '/')),
                            'name' => $this->displayText((string) ($item['title'] ?? '')),
                        ];
                    }, $featuredArticleRows, array_keys($featuredArticleRows)),
                ];
            }

            if ($faqItems !== []) {
                $graph[] = [
                    '@type' => 'FAQPage',
                    'mainEntity' => array_map(function (array $item): array {
                        return [
                            '@type' => 'Question',
                            'name' => (string) ($item['question'] ?? ''),
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => $this->humanizePublicText((string) ($item['answer'] ?? '')),
                            ],
                        ];
                    }, $faqItems),
                ];
            }

            return [
                '@context' => 'https://schema.org',
                '@graph' => array_values(array_filter($graph, static fn (mixed $value): bool => $value !== null)),
            ];
        }

        $schemaType = (string) ($contentRecord['content_type'] ?? 'page') === 'article'
            ? 'Article'
            : ((string) ($contentRecord['content_type'] ?? 'page') === 'product_guide' ? 'Product' : 'WebPage');

        $primary = array_filter([
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'headline' => $title,
            'name' => $title,
            'description' => $description !== '' ? $description : null,
            'url' => $canonicalUrl !== '' ? $canonicalUrl : null,
            'datePublished' => !empty($contentRecord['published_at']) ? (string) $contentRecord['published_at'] : null,
            'inLanguage' => (string) ($contentRecord['language_code'] ?? 'hr'),
            'image' => !empty($contentRecord['featured_image_path']) ? [(string) $contentRecord['featured_image_path']] : null,
            'publisher' => [
                '@type' => 'Organization',
                'name' => $this->brandName(),
                'url' => (string) ($this->config['base_url'] ?? ''),
            ],
        ], static fn(mixed $value): bool => $value !== null && $value !== []);

        if ($schemaType === 'Product') {
            $primary['brand'] = [
                '@type' => 'Brand',
                'name' => 'Forever Living',
            ];

            $sku = trim((string) ($productRecommendation['sku'] ?? ''));
            if ($sku !== '') {
                $primary['sku'] = $sku;
            }

            $price = (float) ($productRecommendation['sale_price'] ?? 0);
            if ($price <= 0) {
                $price = (float) ($productRecommendation['price'] ?? 0);
            }

            if ($productRecommendation !== null && $price > 0) {
                $availability = match (strtolower(trim((string) ($productRecommendation['stock_status'] ?? '')))) {
                    'instock', 'in_stock', 'in-stock' => 'https://schema.org/InStock',
                    'outofstock', 'out_of_stock', 'out-of-stock' => 'https://schema.org/OutOfStock',
                    default => null,
                };

                $primary['offers'] = array_filter([
                    '@type' => 'Offer',
                    'url' => $canonicalUrl !== '' ? $canonicalUrl : null,
                    'priceCurrency' => trim((string) ($productRecommendation['currency_code'] ?? 'EUR')) ?: 'EUR',
                    'price' => number_format($price, 2, '.', ''),
                    'availability' => $availability,
                    'seller' => [
                        '@type' => 'Organization',
                        'name' => 'Forever Living Products',
                    ],
                ], static fn (mixed $value): bool => $value !== null && $value !== []);
            }
        }

        $primaryWithoutContext = $primary;
        unset($primaryWithoutContext['@context']);

        $graph = [$primaryWithoutContext];

        $breadcrumbItems = $this->buildBreadcrumbItems($contentRecord, $title);
        if (count($breadcrumbItems) > 1) {
            $graph[] = [
                '@type' => 'BreadcrumbList',
                'itemListElement' => array_map(function (array $item, int $index): array {
                    $payload = [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => (string) ($item['label'] ?? ''),
                    ];

                    if (!empty($item['href'])) {
                        $payload['item'] = $this->absoluteUrl((string) $item['href']);
                    }

                    return $payload;
                }, $breadcrumbItems, array_keys($breadcrumbItems)),
            ];
        }

        if ($faqItems !== []) {
            $graph[] = [
                '@type' => 'FAQPage',
                'mainEntity' => array_map(function (array $item): array {
                    return [
                        '@type' => 'Question',
                        'name' => (string) ($item['question'] ?? ''),
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $this->humanizePublicText((string) ($item['answer'] ?? '')),
                        ],
                    ];
                }, $faqItems),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ];
    }

    private function buildOpenGraph(array $contentRecord, string $title, string $description, string $canonicalUrl): array
    {
        $contentType = (string) ($contentRecord['content_type'] ?? 'page');
        $featuredImagePath = trim((string) ($contentRecord['featured_image_path'] ?? ''));
        $openGraphImagePath = trim((string) ($contentRecord['open_graph_image_path'] ?? ''));
        $openGraphTitle = trim((string) ($contentRecord['open_graph_title'] ?? ''));
        $openGraphDescription = trim((string) ($contentRecord['open_graph_description'] ?? ''));
        $openGraphDescription = $this->humanizePublicText($openGraphDescription);
        $imagePath = $openGraphImagePath !== '' ? $openGraphImagePath : $featuredImagePath;
        $image = $imagePath !== '' ? $this->absoluteUrl($imagePath) : '';
        $localeMap = [
            'hr' => 'hr_HR',
            'en' => 'en_US',
            'sl' => 'sl_SI',
        ];
        $languageCode = strtolower((string) ($contentRecord['language_code'] ?? 'hr'));

        return [
            'type' => $contentType === 'article' ? 'article' : 'website',
            'site_name' => $this->brandName(),
            'title' => $openGraphTitle !== '' ? $openGraphTitle : $title,
            'description' => $openGraphDescription !== '' ? $openGraphDescription : $description,
            'url' => $canonicalUrl,
            'image' => $image,
            'locale' => $localeMap[$languageCode] ?? 'hr_HR',
        ];
    }

    private function summarizeCard(array $row, ?string $contentType = null): string
    {
        $candidate = trim((string) ($row['meta_description'] ?? ''));

        if ($candidate === '') {
            $candidate = trim(strip_tags((string) ($row['excerpt'] ?? '')));
        }

        if ($candidate === '') {
            $candidate = (string) ($row['title'] ?? '');
        }

        $candidate = $this->displayText($candidate);
        $candidate = $this->normalizeWhitespace($candidate);
        $candidate = $this->humanizePublicText($candidate);

        if ($contentType === 'product_guide') {
            $candidate = $this->stripPromotionalSummaryFragments($candidate);
            $humanSummary = $this->humanProductCardSummary($row, $candidate);
            if ($humanSummary !== '') {
                $candidate = $humanSummary;
            }
        }

        if ($candidate === '') {
            $candidate = $this->normalizeWhitespace($this->displayText((string) ($row['title'] ?? '')));
        }

        $limit = match ($contentType) {
            'article' => 88,
            'product_guide' => 108,
            default => 180,
        };

        return mb_strimwidth($candidate, 0, $limit, '…');
    }

    private function humanProductCardSummary(array $row, string $fallback): string
    {
        $languageCode = strtolower(trim((string) ($row['language_code'] ?? 'hr'))) ?: 'hr';
        $title = $this->displayText((string) ($row['title'] ?? ''));
        $haystack = $this->normalizeSearchText(implode(' ', array_filter([
            $title,
            (string) ($row['slug'] ?? ''),
            (string) ($row['route_path'] ?? ''),
            (string) ($row['meta_description'] ?? ''),
            (string) ($row['excerpt'] ?? ''),
            $fallback,
        ])));
        $knownSummary = $this->knownProductSummary($haystack, $languageCode, $title);
        if ($knownSummary !== '') {
            return $knownSummary;
        }

        if (str_contains($haystack, 'c9') || str_contains($haystack, 'clean 9')) {
            return match ($languageCode) {
                'en' => 'A structured starter program when you want a clearer plan for the first days of your routine.',
                'sl' => 'Strukturiran začetni program, ko želiš jasnejši načrt za prve dni rutine.',
                default => 'Strukturirani program za početak kada želiš jasniji plan prvih dana rutine.',
            };
        }

        if (str_contains($haystack, 'garcinia')) {
            return match ($languageCode) {
                'en' => 'Support for a weight-management routine when appetite, portions and consistency are the main topics.',
                'sl' => 'Podpora rutini uravnavanja teže, ko so glavna tema apetit, porcije in doslednost.',
                default => 'Podrška uz rutinu regulacije težine kada su tema apetit, porcije i dosljednost.',
            };
        }

        if (str_contains($haystack, 'therm')) {
            return match ($languageCode) {
                'en' => 'Worth considering when you want energy support inside a weight-management routine.',
                'sl' => 'Smiselno za razmislek, ko želiš podporo energiji znotraj rutine uravnavanja teže.',
                default => 'Može se razmotriti kada želiš podršku za energiju unutar rutine kontrole težine.',
            };
        }

        if (str_contains($haystack, 'lite ultra') || str_contains($haystack, 'protein') || str_contains($haystack, 'aminotein')) {
            return match ($languageCode) {
                'en' => 'A practical protein option when you want a simpler meal or snack in your routine.',
                'sl' => 'Praktična beljakovinska možnost, ko želiš preprostejši obrok ali prigrizek v rutini.',
                default => 'Praktična proteinska podrška kada želiš jednostavniji obrok ili međuobrok.',
            };
        }

        if (str_contains($haystack, 'gelly') || str_contains($haystack, 'aloe first') || str_contains($haystack, 'msm')) {
            return match ($languageCode) {
                'en' => 'A practical outer-care product when the goal is skin comfort and a simpler care routine.',
                'sl' => 'Praktičen izdelek za zunanjo nego, ko je cilj udobje kože in preprostejša rutina nege.',
                default => 'Praktičan proizvod za vanjsku njegu kada je cilj ugodnija koža i jednostavnija rutina.',
            };
        }

        if (str_contains($haystack, 'aloe vera gel')) {
            return match ($languageCode) {
                'en' => 'A daily aloe drink for people who want simple support for digestion and routine.',
                'sl' => 'Vsakodnevni aloe napitek za tiste, ki želijo preprosto podporo prebavi in rutini.',
                default => 'Svakodnevni aloe napitak za korisnike koji žele jednostavnu podršku probavi i rutini.',
            };
        }

        if (str_contains($haystack, 'collagen') || str_contains($haystack, 'kolagen')) {
            return match ($languageCode) {
                'en' => 'A good fit when the goal is a routine for skin, hair, nails or joint-support habits.',
                'sl' => 'Dobra izbira, ko je cilj rutina za kožo, lase, nohte ali podporo sklepom.',
                default => 'Dobar izbor kada je cilj rutina za kožu, kosu, nokte ili podršku zglobovima.',
            };
        }

        if (str_contains($haystack, 'toothgel') || str_contains($haystack, 'zub')) {
            return match ($languageCode) {
                'en' => 'Everyday oral care with aloe and propolis, without complicating the routine.',
                'sl' => 'Vsakodnevna ustna nega z aloe vero in propolisom, brez zapletanja rutine.',
                default => 'Svakodnevna oralna njega s aloe verom i propolisom, bez nepotrebnog kompliciranja.',
            };
        }

        if ($this->isOverheatedMarketingText($fallback) && trim($title) !== '') {
            return match ($languageCode) {
                'en' => 'A product guide for understanding when ' . $title . ' makes sense in a daily routine.',
                'sl' => 'Vodič za razumevanje, kdaj ima ' . $title . ' smisel v dnevni rutini.',
                default => 'Vodič za razumjeti kada ' . $title . ' ima smisla u svakodnevnoj rutini.',
            };
        }

        return $fallback;
    }

    private function knownProductSummary(string $haystack, string $languageCode, string $title = ''): string
    {
        $languageCode = strtolower(trim($languageCode)) ?: 'hr';
        $title = trim($title);

        $definitions = [
            [
                'patterns' => ['c9', 'clean 9'],
                'copy' => [
                    'hr' => 'Strukturirani program za početak kada želiš jasniji plan prvih dana rutine.',
                    'en' => 'A structured starter program when you want a clearer plan for the first days of your routine.',
                    'sl' => 'Strukturiran začetni program, ko želiš jasnejši načrt za prve dni rutine.',
                ],
            ],
            [
                'patterns' => ['garcinia'],
                'copy' => [
                    'hr' => 'Podrška uz rutinu regulacije težine kada su tema apetit, porcije i dosljednost.',
                    'en' => 'Support for a weight-management routine when appetite, portions and consistency are the main topics.',
                    'sl' => 'Podpora rutini uravnavanja teže, ko so glavna tema apetit, porcije in doslednost.',
                ],
            ],
            [
                'patterns' => ['therm'],
                'copy' => [
                    'hr' => 'Može se razmotriti kada želiš podršku za energiju unutar rutine kontrole težine.',
                    'en' => 'Worth considering when you want energy support inside a weight-management routine.',
                    'sl' => 'Smiselno za razmislek, ko želiš podporo energiji znotraj rutine uravnavanja teže.',
                ],
            ],
            [
                'patterns' => ['forever lean'],
                'copy' => [
                    'hr' => 'Dodatak za rutinu kontrole težine kada želiš paziti na obroke, porcije i dosljednost.',
                    'en' => 'A supplement for a weight-management routine focused on meals, portions and consistency.',
                    'sl' => 'Dodatek za rutino uravnavanja teže, ko želiš paziti na obroke, porcije in doslednost.',
                ],
            ],
            [
                'patterns' => ['lite ultra', 'protein', 'aminotein'],
                'copy' => [
                    'hr' => 'Praktična proteinska podrška kada želiš jednostavniji obrok ili međuobrok.',
                    'en' => 'A practical protein option when you want a simpler meal or snack in your routine.',
                    'sl' => 'Praktična beljakovinska možnost, ko želiš preprostejši obrok ali prigrizek v rutini.',
                ],
            ],
            [
                'patterns' => ['fiber', 'vlakna'],
                'copy' => [
                    'hr' => 'Jednostavan dodatak vlakana kada želiš podržati probavu i osjećaj sitosti kroz dan.',
                    'en' => 'A simple fiber add-on when you want digestion and satiety support during the day.',
                    'sl' => 'Preprost dodatek vlaknin, ko želiš podpreti prebavo in občutek sitosti čez dan.',
                ],
            ],
            [
                'patterns' => ['aloe berry', 'berry nectar'],
                'copy' => [
                    'hr' => 'Aloe napitak s brusnicom i jabukom za one kojima više odgovara voćniji okus.',
                    'en' => 'An aloe drink with cranberry and apple for people who prefer a fruitier taste.',
                    'sl' => 'Aloe napitek z brusnico in jabolkom za tiste, ki jim bolj ustreza sadnejši okus.',
                ],
            ],
            [
                'patterns' => ['aloe peaches', 'peaches 330'],
                'copy' => [
                    'hr' => 'Aloe napitak s okusom breskve kada želiš blaži, voćniji ulaz u aloe rutinu.',
                    'en' => 'An aloe drink with peach flavor when you want a milder, fruitier aloe routine.',
                    'sl' => 'Aloe napitek z okusom breskve, ko želiš blažjo, sadnejšo aloe rutino.',
                ],
            ],
            [
                'patterns' => ['aloe activator'],
                'copy' => [
                    'hr' => 'Višenamjenska tekućina s aloe verom za čišćenje, osvježenje ili pripremu kože za njegu.',
                    'en' => 'A multipurpose aloe liquid for cleansing, refreshing or preparing skin for care.',
                    'sl' => 'Večnamenska tekočina z aloe vero za čiščenje, osvežitev ali pripravo kože na nego.',
                ],
            ],
            [
                'patterns' => ['aloe vera gelly', 'gelly', 'aloe first', 'aloe msm gel'],
                'copy' => [
                    'hr' => 'Praktičan proizvod za vanjsku njegu kada je cilj ugodnija koža i jednostavnija rutina.',
                    'en' => 'A practical outer-care product when the goal is skin comfort and a simpler care routine.',
                    'sl' => 'Praktičen izdelek za zunanjo nego, ko je cilj udobje kože in preprostejša rutina nege.',
                ],
            ],
            [
                'patterns' => ['aloe vera gel'],
                'copy' => [
                    'hr' => 'Svakodnevni aloe napitak za korisnike koji žele jednostavnu podršku probavi i rutini.',
                    'en' => 'A daily aloe drink for people who want simple support for digestion and routine.',
                    'sl' => 'Vsakodnevni aloe napitek za tiste, ki želijo preprosto podporo prebavi in rutini.',
                ],
            ],
            [
                'patterns' => ['active pro b', 'pro b', 'probiotic', 'probiotik'],
                'copy' => [
                    'hr' => 'Probiotička podrška kada želiš jednostavnije brinuti o probavi iz dana u dan.',
                    'en' => 'Probiotic support when you want an easier daily way to care for digestion.',
                    'sl' => 'Probiotična podpora, ko želiš preprosteje skrbeti za prebavo iz dneva v dan.',
                ],
            ],
            [
                'patterns' => ['fields of greens'],
                'copy' => [
                    'hr' => 'Zelena dnevna podrška kada želiš u rutinu dodati ječam, pšenicu i jednostavan biljni dodatak.',
                    'en' => 'Daily greens support when you want barley, wheatgrass and a simple plant add-on in your routine.',
                    'sl' => 'Zelena dnevna podpora, ko želiš v rutino dodati ječmen, pšenico in preprost rastlinski dodatek.',
                ],
            ],
            [
                'patterns' => ['aloeturm', 'aloe turm'],
                'copy' => [
                    'hr' => 'Aloe i kurkuma u praktičnom dodatku kada želiš podršku probavi, zglobovima ili dnevnoj ravnoteži.',
                    'en' => 'Aloe and turmeric in a practical add-on for digestion, joints or daily balance routines.',
                    'sl' => 'Aloe vera in kurkuma v praktičnem dodatku za prebavo, sklepe ali dnevno ravnovesje.',
                ],
            ],
            [
                'patterns' => ['freedom', 'active ha', 'esm complex'],
                'copy' => [
                    'hr' => 'Podrška rutini pokretljivosti kada su tema zglobovi, kretanje i svakodnevna fleksibilnost.',
                    'en' => 'Support for a mobility routine when joints, movement and everyday flexibility are the topic.',
                    'sl' => 'Podpora rutini gibljivosti, ko so tema sklepi, gibanje in vsakodnevna prožnost.',
                ],
            ],
            [
                'patterns' => ['calcium', 'nature min'],
                'copy' => [
                    'hr' => 'Mineralna podrška kada želiš jednostavnije brinuti o kostima i svakodnevnoj prehrani.',
                    'en' => 'Mineral support when you want an easier way to care for bones and daily nutrition.',
                    'sl' => 'Mineralna podpora, ko želiš preprosteje skrbeti za kosti in dnevno prehrano.',
                ],
            ],
            [
                'patterns' => ['absorbent c', 'absorbent d', 'b12', 'daily', 'immublend', 'immune gummy', 'kids multivitamini'],
                'copy' => [
                    'hr' => 'Dnevna vitaminska podrška kada želiš uredniju rutinu imuniteta, energije ili prehrane.',
                    'en' => 'Daily vitamin support when you want a steadier immunity, energy or nutrition routine.',
                    'sl' => 'Dnevna vitaminska podpora, ko želiš urejenejšo rutino odpornosti, energije ali prehrane.',
                ],
            ],
            [
                'patterns' => ['arctic sea', 'omega', 'nutra q10', 'argi', 'arginin'],
                'copy' => [
                    'hr' => 'Dodatak za rutinu energije, srca i cirkulacije kada želiš dugoročniju dnevnu podršku.',
                    'en' => 'A supplement for energy, heart and circulation routines when you want longer-term daily support.',
                    'sl' => 'Dodatek za rutino energije, srca in cirkulacije, ko želiš dolgoročnejšo dnevno podporo.',
                ],
            ],
            [
                'patterns' => ['focus'],
                'copy' => [
                    'hr' => 'Podrška za dane kada želiš više mentalne jasnoće, fokusa i stabilniji radni ritam.',
                    'en' => 'Support for days when you want more mental clarity, focus and a steadier work rhythm.',
                    'sl' => 'Podpora za dneve, ko želiš več miselne jasnosti, fokusa in stabilnejši delovni ritem.',
                ],
            ],
            [
                'patterns' => ['maca', 'vitolize men', 'vitolize women'],
                'copy' => [
                    'hr' => 'Ciljana podrška vitalnosti kada želiš proizvod povezan s energijom i muškom ili ženskom rutinom.',
                    'en' => 'Targeted vitality support when you want a product connected with energy and men’s or women’s routines.',
                    'sl' => 'Ciljana podpora vitalnosti, ko želiš izdelek, povezan z energijo ter moško ali žensko rutino.',
                ],
            ],
            [
                'patterns' => ['ivision'],
                'copy' => [
                    'hr' => 'Dodatak za rutinu vida kada su tema oči, ekrani i svakodnevno opterećenje vida.',
                    'en' => 'A supplement for an eye-care routine when screens and daily visual strain are the topic.',
                    'sl' => 'Dodatek za rutino vida, ko so tema oči, zasloni in vsakodnevna obremenitev vida.',
                ],
            ],
            [
                'patterns' => ['bee honey', 'bee pollen', 'bee propolis', 'royal jelly', 'lycium', 'garlic thyme', 'garlic-thyme'],
                'copy' => [
                    'hr' => 'Prirodna dnevna podrška kada želiš proizvod iz pčelinje ili biljne linije za energiju i otpornost.',
                    'en' => 'Natural daily support when you want a bee-derived or botanical product for energy and resilience.',
                    'sl' => 'Naravna dnevna podpora, ko želiš čebelji ali rastlinski izdelek za energijo in odpornost.',
                ],
            ],
            [
                'patterns' => ['toothgel', 'zub'],
                'copy' => [
                    'hr' => 'Svakodnevna oralna njega s aloe verom i propolisom, bez nepotrebnog kompliciranja.',
                    'en' => 'Everyday oral care with aloe and propolis, without complicating the routine.',
                    'sl' => 'Vsakodnevna ustna nega z aloe vero in propolisom, brez zapletanja rutine.',
                ],
            ],
            [
                'patterns' => ['collagen', 'kolagen'],
                'copy' => [
                    'hr' => 'Dobar izbor kada je cilj rutina za kožu, kosu, nokte ili podršku zglobovima.',
                    'en' => 'A good fit when the goal is a routine for skin, hair, nails or joint-support habits.',
                    'sl' => 'Dobra izbira, ko je cilj rutina za kožo, lase, nohte ali podporo sklepom.',
                ],
            ],
            [
                'patterns' => ['jojoba shampoo', 'jojoba conditioner', 'ever shield', 'gentlemen', 'sunscreen', 'aloe activator', 'propolis creme', 'propolis cream', 'bakuchiol'],
                'copy' => [
                    'hr' => 'Proizvod za njegu kada želiš jednostavniju rutinu za kožu, kosu ili svakodnevnu svježinu.',
                    'en' => 'A care product when you want a simpler routine for skin, hair or everyday freshness.',
                    'sl' => 'Izdelek za nego, ko želiš preprostejšo rutino za kožo, lase ali vsakodnevno svežino.',
                ],
            ],
            [
                'patterns' => ['blossom herbal tea'],
                'copy' => [
                    'hr' => 'Biljni čaj bez kofeina za mirniji dnevni ritual, topao ili hladan.',
                    'en' => 'A caffeine-free herbal tea for a calmer daily ritual, hot or cold.',
                    'sl' => 'Zeliščni čaj brez kofeina za mirnejši dnevni ritual, topel ali hladen.',
                ],
            ],
            [
                'patterns' => ['aloe liquid soap'],
                'copy' => [
                    'hr' => 'Blagi tekući sapun za svakodnevno pranje ruku i tijela bez osjećaja pretjeranog isušivanja.',
                    'en' => 'A gentle liquid soap for daily hand and body washing without an overly dry feel.',
                    'sl' => 'Blago tekoče milo za vsakodnevno umivanje rok in telesa brez pretiranega občutka izsušenosti.',
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            foreach ((array) $definition['patterns'] as $pattern) {
                $pattern = $this->normalizeSearchText((string) $pattern);
                if ($pattern !== '' && str_contains($haystack, $pattern)) {
                    $copy = (array) $definition['copy'];

                    return (string) ($copy[$languageCode] ?? $copy['hr'] ?? '');
                }
            }
        }

        if ($this->isOverheatedMarketingText($haystack) && $title !== '') {
            return match ($languageCode) {
                'en' => 'A product guide for understanding when ' . $title . ' makes sense in a daily routine.',
                'sl' => 'Vodič za razumevanje, kdaj ima ' . $title . ' smisel v dnevni rutini.',
                default => 'Vodič za razumjeti kada ' . $title . ' ima smisla u svakodnevnoj rutini.',
            };
        }

        return '';
    }

    private function isOverheatedMarketingText(string $text): bool
    {
        return (bool) preg_match('/(?:otkrijte|postignite|savrš|savrs|\bmoć|\bmoc|moćn|mocn|snažn|snazn|vrhunsk|najčiš|najcis|nevjerojat|najnovije\s+tehnologije|ubrzava|ubrzaj|vrača|vraca|detoks|detoksikacij|čudes|cudes|idealn|revolucionar|tajna\s+vitke|vitke\s+linije|sagorite|savladajte|milijun)/iu', $text);
    }

    private function stripPromotionalSummaryFragments(string $text): string
    {
        $text = $this->softenLegacyPurchaseText($text);
        $text = $this->softenLegacyMarketingTone($text);
        $knownProductText = $this->humanKnownProductText($text);
        if ($knownProductText !== '' && ($this->isOverheatedMarketingText($text) || $this->isSummaryPromoSentence($text) || mb_strlen($text) <= 260)) {
            return $knownProductText;
        }

        $sentences = preg_split('/(?<=[.!?])\s+/u', $text) ?: [$text];
        $filtered = array_values(array_filter($sentences, function (string $sentence): bool {
            return !$this->isSummaryPromoSentence($sentence);
        }));

        if ($filtered !== []) {
            $text = implode(' ', $filtered);
        }

        $text = preg_replace('/\b(?:uz našu preporuku|s našom preporukom|putem naše preporuke|with our recommendation|z našim priporočilom)\b/iu', '', $text) ?? $text;

        return trim($this->normalizeWhitespace($text), " \t\n\r\0\x0B,;");
    }

    private function humanizePublicText(string $text): string
    {
        $text = $this->normalizeWhitespace($this->displayText(strip_tags($text)));
        if ($text === '') {
            return '';
        }

        $text = $this->softenLegacyPurchaseText($text);
        $text = $this->softenLegacyMarketingTone($text);
        $knownProductText = $this->humanKnownProductText($text);
        if ($knownProductText !== '' && ($this->isOverheatedMarketingText($text) || $this->isSummaryPromoSentence($text) || mb_strlen($text) <= 260)) {
            return $knownProductText;
        }

        $sentences = preg_split('/(?<=[.!?])\s+/u', $text) ?: [$text];
        $kept = [];

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if ($sentence === '' || $this->isSummaryPromoSentence($sentence)) {
                continue;
            }

            $kept[] = $sentence;
        }

        return $this->normalizeWhitespace(implode(' ', $kept));
    }

    private function polishLegacyBodyCopy(string $html): string
    {
        if (trim($html) === '') {
            return $html;
        }

        $html = preg_replace('/<p\b[^>]*>\s*<em>.*?(?:15\s*%|popust|discount|ušted|ustede|ostvari|iskoristi|putem naših linkova|putem nasih linkova|prilikom naručivanja|prilikom narucivanja).*?<\/em>\s*<\/p>/isu', '', $html) ?? $html;
        $html = preg_replace('/<em>.*?(?:15\s*%|popust|discount|ušted|ustede|ostvari|iskoristi|putem naših linkova|putem nasih linkova|prilikom naručivanja|prilikom narucivanja).*?<\/em>/isu', '', $html) ?? $html;
        $html = preg_replace('/<em>\s*Nakon što uspješno naručite.*?<strong>\s*Ostvari\s+15\s*%\s+popusta.*?<\/div>\s*/isu', '', $html) ?? $html;
        $html = preg_replace('/<em>\s*Nakon sto uspjesno narucite.*?<strong>\s*Ostvari\s+15\s*%\s+popusta.*?<\/div>\s*/isu', '', $html) ?? $html;
        $html = preg_replace('/<div\b[^>]*>\s*<strong>\s*Ostvari\s+15\s*%\s+popusta.*?<\/div>/isu', '', $html) ?? $html;
        $html = preg_replace('/<div\b[^>]*>\s*<strong>\s*AloeVera\s+Centar\s+je\s+nezavisni\s+Forever\s+partner\.\s*<\/strong>\s*<strong>\s*Ostvari\s+15\s*%\s+popusta.*?<\/div>/isu', '', $html) ?? $html;
        $html = preg_replace('/<div\b[^>]*>\s*<strong>\s*Program\s+traje\s+9\s+dana\s*<\/strong>\s*.*?\bdetoksikacij.*?<\/div>/isu', '', $html) ?? $html;
        $html = preg_replace('/\s*<strong>\s*Napomena:\s*<\/strong>.*?(?:15\s*%|popust).*?(?=<h[1-6]\b|<div\b|<section\b|$)/isu', '', $html) ?? $html;
        $html = preg_replace('/<p\b[^>]*>\s*Želiš dodatne savjete\?.*?<\/p>/isu', '', $html) ?? $html;
        $html = preg_replace('/<p\b[^>]*>\s*Zelis dodatne savjete\?.*?<\/p>/isu', '', $html) ?? $html;
        $html = preg_replace('/(?:Želiš|Zelis)\s+dodatne\s+savjete\?.*?personalizirane preporuke za Forever proizvode!?/isu', '', $html) ?? $html;
        $html = preg_replace('/(?:Želiš|Zelis)\s+dodatne\s+savjete\?.*?(?:idealne Forever proizvode za sebe!|personalizirane preporuke.*?!)/isu', '', $html) ?? $html;
        $html = preg_replace('/<a\b[^>]*>\s*kontaktiraj\s+našeg\s*<strong>\s*AI asistenta\s*<\/strong>\s*<\/a>\.\s*Putem AI asistenta.*?<\/div>/isu', '', $html) ?? $html;
        $html = preg_replace('/<a\b[^>]*>\s*kontaktiraj\s+naseg\s*<strong>\s*AI asistenta\s*<\/strong>\s*<\/a>\.\s*Putem AI asistenta.*?<\/div>/isu', '', $html) ?? $html;
        $html = preg_replace('/\s*Klikni na sovicu.*?<\/div>/isu', '', $html) ?? $html;
        $html = preg_replace('/\s*(?:Klikni na sovicu|Kliknite na sovicu|Klikni na sovicu u donjem kutu ekrana|Kliknite na sovicu u donjem kutu|Klikni na sovicu u donjem kutu|Klikni na sovicu\s*<a\b|posjetite\s*<a\b[^>]*>\s*AI asistent\s*<\/a>|posjeti\s*<a\b[^>]*>\s*AI asistent\s*<\/a>|Uz AI savjetnika|Tražite savjete.*?Kliknite na sovicu).*?(?=<h[1-6]\b|<section\b|$)/isu', '', $html) ?? $html;
        $html = preg_replace('/\s*gdje možeš iskoristiti.*?(?=<h[1-6]\b|<section\b|$)/isu', '', $html) ?? $html;
        $html = preg_replace('/\s*gdje moz(e|es) iskoristiti.*?(?=<h[1-6]\b|<section\b|$)/isu', '', $html) ?? $html;
        $html = preg_replace('/<h2>\s*Forever Garcinia Cambogia Plus\s*–\s*Tajna vitke linije i zdravijeg života\s*<\/h2>/iu', '<h2>Forever Garcinia Plus i rutina regulacije težine</h2>', $html) ?? $html;
        $html = preg_replace('/<strong>\s*Forever Garcinia Cambogia Plus\s*<\/strong>.*?(?=<h2>\s*Ostali važni sastojci\s*<\/h2>)/isu', '<p>Forever Garcinia Plus je dodatak prehrani koji se najčešće razmatra kada je cilj bolja kontrola apetita i porcija. Ima smisla promatrati ga kao dio šire rutine prehrane, kretanja i dosljednosti.</p>', $html) ?? $html;
        $html = preg_replace('/<strong>\s*Forever Garcinia Cambogia Plus\s*<\/strong>\s*revolucionaran je dodatak prehrani čija glavna komponenta,/iu', '<strong>Forever Garcinia Cambogia Plus</strong> je dodatak prehrani čija se glavna komponenta,', $html) ?? $html;
        $html = preg_replace('/<p\b[^>]*>\s*Ako biste željeli dodatne pogodnosti,.*?<\/p>/isu', '<p>Ako odlučiš naručiti, koristi gumb za službeni Forever shop kako bi AVC otvorio pravi market link za tvoju lokaciju.</p>', $html) ?? $html;
        $html = preg_replace('/<p\b[^>]*>\s*Ako biste zeljeli dodatne pogodnosti,.*?<\/p>/isu', '<p>Ako odlučiš naručiti, koristi gumb za službeni Forever shop kako bi AVC otvorio pravi market link za tvoju lokaciju.</p>', $html) ?? $html;
        $html = $this->softenLegacyPurchaseText($html);
        $html = $this->softenLegacyMarketingTone($html);

        $html = preg_replace_callback('/<p\b[^>]*>(.*?)<\/p>/isu', function (array $matches): string {
            $original = (string) ($matches[0] ?? '');
            $inner = (string) ($matches[1] ?? '');
            $text = $this->normalizeWhitespace($this->displayText(strip_tags($inner)));
            if ($text === '') {
                return $original;
            }

            if ($this->isSummaryPromoSentence($text)) {
                return '';
            }

            return $original;
        }, $html) ?? $html;

        return trim($html);
    }

    private function softenLegacyMarketingTone(string $html): string
    {
        $replacements = [
            '/Detoks\s+za\s+čišći\s+i\s+zdraviji\s+život/iu' => 'Strukturirani početak rutine u 9 dana',
            '/Detoks\s+za\s+cisci\s+i\s+zdraviji\s+zivot/iu' => 'Strukturirani početak rutine u 9 dana',
            '/cjelovit\w*\s+detoksikacij\w*/iu' => 'jasniji početak rutine',
            '/detoksikacij\w*/iu' => 'početak rutine',
            '/\bdetoks\b/iu' => 'strukturirani početak',
            '/uklanjanj\w*\s+štetnih\s+tvari/iu' => 'uređeniji početak rutine',
            '/uklanjanj\w*\s+stetnih\s+tvari/iu' => 'uređeniji početak rutine',
            '/moćna,\s*ali\s+nježna\s+formula/iu' => 'nježna formula',
            '/mocna,\s*ali\s+njezna\s+formula/iu' => 'nježna formula',
            '/Moćan\s+set/iu' => 'Set',
            '/Mocan\s+set/iu' => 'Set',
            '/klinički\s+dokazanu\s+potporu/iu' => 'opisanu podršku',
            '/klinicki\s+dokazanu\s+potporu/iu' => 'opisanu podršku',
            '/dugotrajnije\s+rezultate\s+i\s+poboljšano\s+zdravlje/iu' => 'lakše održavanje navike',
            '/dugotrajnije\s+rezultate\s+i\s+poboljsano\s+zdravlje/iu' => 'lakše održavanje navike',
            '/Tajna\s+vitke\s+linije\s+i\s+zdravijeg\s+života/iu' => 'Rutina regulacije težine bez velikih obećanja',
            '/Tajna\s+vitke\s+linije\s+i\s+zdravijeg\s+zivota/iu' => 'Rutina regulacije težine bez velikih obećanja',
            '/Postignite\s+savršenu\s+liniju!?/iu' => 'Drži se rutine koja ti je održiva.',
            '/Postignite\s+savrsenu\s+liniju!?/iu' => 'Drži se rutine koja ti je održiva.',
            '/Sagorite\s+masne\s+zalihe.*?!/iu' => 'Usmjeri se na apetit, porcije i dosljednost.',
            '/ubrzava\s+metabolizam/iu' => 'pripada rutini za energiju i kontrolu težine',
            '/Ubrzaj\s+metabolizam\s+i\s+dosegni\s+svoje\s+ciljeve/iu' => 'Podrška energiji u rutini kontrole težine',
            '/revolucionar(?:an|na|ni|no|nog|nim|nih)?\s*/iu' => '',
            '/\bmoćn(?:a|i|o|u|om|ih)?\s+formula\b/iu' => 'formula',
            '/\bmocn(?:a|i|o|u|om|ih)?\s+formula\b/iu' => 'formula',
            '/\bsnažn(?:a|i|o|u|om|ih)?\s+formula\b/iu' => 'formula',
            '/\bsnazn(?:a|i|o|u|om|ih)?\s+formula\b/iu' => 'formula',
            '/savršena\s+kombinacija/iu' => 'kombinacija',
            '/savrsena\s+kombinacija/iu' => 'kombinacija',
            '/najčišći\s+Foreverov\s+proizvod\s+on\s+Aloe\s+Vere/iu' => 'jednostavan Forever proizvod od aloe vere',
            '/najcisci\s+Foreverov\s+proizvod\s+on\s+Aloe\s+Vere/iu' => 'jednostavan Forever proizvod od aloe vere',
            '/nevjerojatno\s+širokom\s+primjenom/iu' => 'vrlo širokom primjenom',
            '/nevjerojatno\s+sirokom\s+primjenom/iu' => 'vrlo širokom primjenom',
            '/milijunima\s+korisnika\s+diljem\s+svijeta/iu' => 'mnogim korisnicima',
            '/Otkrij\s+moć/iu' => 'Upoznaj ulogu',
            '/Otkrijte\s+moć/iu' => 'Upoznajte ulogu',
            '/Otkrijte\s+/iu' => 'Pogledaj ',
            '/Otkrij\s+/iu' => 'Pogledaj ',
            '/Savladajte\s+svakodnevne\s+izazove\s+uz\s+Focus/iu' => 'Uklopi Focus u dane kada želiš više mentalne jasnoće.',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $html = preg_replace($pattern, $replacement, $html) ?? $html;
        }

        return $html;
    }

    private function softenLegacyPurchaseText(string $text): string
    {
        $replacement = 'Ako odlučiš naručiti, koristi gumb na AVC-u kako bi otvorio službeni Forever shop za svoje tržište. Ako nisi siguran je li ovo pravi proizvod, prvo postavi pitanje savjetniku.';

        $text = preg_replace('/Naručite originalan proizvod direktno od kompanije Forever Living Products\.\s*Ukoliko koristite naše poveznice \(preporuke\),.*?dodatnu podršku\./isu', $replacement, $text) ?? $text;
        $text = preg_replace('/Narucite originalan proizvod direktno od kompanije Forever Living Products\.\s*Ukoliko koristite nase poveznice \(preporuke\),.*?dodatnu podrsku\./isu', $replacement, $text) ?? $text;
        $text = preg_replace('/Naručite originalan proizvod direktno od kompanije Forever Living Products\./iu', 'Kada se odlučiš, narudžbu dovršavaš na službenom Forever shopu.', $text) ?? $text;
        $text = preg_replace('/Narucite originalan proizvod direktno od kompanije Forever Living Products\./iu', 'Kada se odlučiš, narudžbu dovršavaš na službenom Forever shopu.', $text) ?? $text;

        return $text;
    }

    private function humanKnownProductText(string $text): string
    {
        $haystack = $this->normalizeSearchText($text);
        $knownSummary = $this->knownProductSummary($haystack, 'hr');
        if ($knownSummary !== '') {
            return $knownSummary;
        }

        if (str_contains($haystack, 'c9') || str_contains($haystack, 'clean 9')) {
            return 'Strukturirani program za početak kada želiš jasniji plan prvih dana rutine.';
        }

        if (str_contains($haystack, 'garcinia')) {
            return 'Podrška uz rutinu regulacije težine kada su tema apetit, porcije i dosljednost.';
        }

        if (str_contains($haystack, 'therm')) {
            return 'Može se razmotriti kada želiš podršku za energiju unutar rutine kontrole težine.';
        }

        if (str_contains($haystack, 'lite ultra') || str_contains($haystack, 'protein') || str_contains($haystack, 'aminotein')) {
            return 'Praktična proteinska podrška kada želiš jednostavniji obrok ili međuobrok.';
        }

        if (str_contains($haystack, 'gelly') || str_contains($haystack, 'aloe first') || str_contains($haystack, 'msm')) {
            return 'Praktičan proizvod za vanjsku njegu kada je cilj ugodnija koža i jednostavnija rutina.';
        }

        if (str_contains($haystack, 'aloe vera gel')) {
            return 'Svakodnevni aloe napitak za korisnike koji žele jednostavnu podršku probavi i rutini.';
        }

        if (str_contains($haystack, 'collagen') || str_contains($haystack, 'kolagen')) {
            return 'Dobar izbor kada je cilj rutina za kožu, kosu, nokte ili podršku zglobovima.';
        }

        if (str_contains($haystack, 'toothgel') || str_contains($haystack, 'zub')) {
            return 'Svakodnevna oralna njega s aloe verom i propolisom, bez nepotrebnog kompliciranja.';
        }

        return '';
    }

    private function normalizeWhitespace(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    private function displayText(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function formatDisplayDate(string $value, string $languageCode): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        try {
            $date = new \DateTimeImmutable($value);
        } catch (\Throwable) {
            return $value;
        }

        $day = (int) $date->format('j');
        $month = (int) $date->format('n');
        $year = $date->format('Y');

        if (strtolower($languageCode) === 'en') {
            $months = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            return ($months[$month] ?? $date->format('M')) . ' ' . $day . ', ' . $year;
        }

        return $day . '. ' . $month . '. ' . $year . '.';
    }

    private function renderSummaryBlock(string $transformedSummary, array $copy, string $contentType): string
    {
        $transformedSummary = $this->polishSummaryHtml($transformedSummary);
        if (trim($transformedSummary) === '') {
            return '';
        }

        $titleKey = $contentType === 'product_guide'
            ? 'summary_title_product'
            : ($contentType === 'article' ? 'summary_title_article' : 'summary_title');

        return '<section class="summary-box"><div class="eyebrow">' . htmlspecialchars($copy['summary_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><div class="content-prose"><h2>' . htmlspecialchars($this->copyValue($copy, $titleKey, $copy['summary_title'] ?? 'Ukratko'), ENT_QUOTES, 'UTF-8') . '</h2>' . $transformedSummary . '</div></section>';
    }

    private function polishSummaryHtml(string $html): string
    {
        if (trim($html) === '') {
            return $html;
        }

        $html = preg_replace_callback('/<li\b[^>]*>(.*?)<\/li>/isu', function (array $matches): string {
            $text = $this->normalizeWhitespace($this->displayText(strip_tags((string) ($matches[1] ?? ''))));
            if ($text === '') {
                return '';
            }

            $knownProductText = $this->humanKnownProductText($text);
            if ($knownProductText !== '') {
                return '<li>' . htmlspecialchars($knownProductText, ENT_QUOTES, 'UTF-8') . '</li>';
            }

            $sentences = preg_split('/(?<=[.!?])\s+/u', $text) ?: [$text];
            $kept = [];
            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                if ($sentence === '' || $this->isSummaryPromoSentence($sentence)) {
                    continue;
                }

                $kept[] = $sentence;
            }

            $cleaned = $this->normalizeWhitespace(implode(' ', $kept));
            if ($cleaned === '') {
                return '';
            }

            return '<li>' . htmlspecialchars($cleaned, ENT_QUOTES, 'UTF-8') . '</li>';
        }, $html) ?? $html;

        $html = preg_replace_callback('/<ul\b[^>]*>(.*?)<\/ul>/isu', function (array $matches): string {
            $itemsHtml = (string) ($matches[1] ?? '');
            preg_match_all('/<li\b[^>]*>(.*?)<\/li>/isu', $itemsHtml, $items);
            $seen = [];
            $cleanItems = [];

            foreach ((array) ($items[1] ?? []) as $itemHtml) {
                $text = $this->normalizeWhitespace($this->displayText(strip_tags((string) $itemHtml)));
                $fingerprint = mb_strtolower($text);
                if ($text === '' || isset($seen[$fingerprint])) {
                    continue;
                }

                $seen[$fingerprint] = true;
                $cleanItems[] = '<li>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</li>';
            }

            return $cleanItems !== [] ? '<ul>' . implode('', $cleanItems) . '</ul>' : '';
        }, $html) ?? $html;

        return trim($html);
    }

    private function isSummaryPromoSentence(string $sentence): bool
    {
        return (bool) preg_match('/(?:15\s*%|popust|discount|iskoristi|ostvari|claim|save|ušted|ustede|uštede|sagorite\s+masne|putem naše preporuke|putem nase preporuke|putem naših linkova|putem nasih linkova|uz našu preporuku|s našom preporukom|with our recommendation|z našim priporočilom|bez potrebe za registracijom|bez registracije|direktno na službenom|directly on the official|neposredno v uradni|thealoeveraco\.shop|forevercard\.club|sovica|ai asistent)/iu', $sentence);
    }

    private function preparePublicFaqItems(array $faqItems, array $contentRecord, string $contentType, array $copy): array
    {
        $prepared = [];

        foreach ($faqItems as $index => $item) {
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            if ($this->isGenericFaqQuestion($question)) {
                $question = $this->friendlyFaqQuestion($contentType, $index, $copy, $question);

                if ($contentType === 'product_guide' && $index === 1) {
                    $answer = $this->copyValue($copy, 'faq_product_answer_check', $answer);
                } elseif ($contentType === 'article' && $index === 1) {
                    $answer = $this->copyValue($copy, 'faq_article_answer_check', $answer);
                }
            }

            $prepared[] = [
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $prepared;
    }

    private function isGenericFaqQuestion(string $question): bool
    {
        $question = mb_strtolower($this->normalizeWhitespace($this->displayText($question)));

        return (bool) preg_match('/(?:što je najvažnije znati o temi|sto je najvaznije znati o temi|na što paziti|na sto paziti|what is most important|what should you watch|kaj je najpomembneje|na kaj paziti)/iu', $question);
    }

    private function friendlyFaqQuestion(string $contentType, int $index, array $copy, string $fallback): string
    {
        if ($contentType === 'product_guide') {
            $keys = ['faq_product_question_1', 'faq_product_question_2', 'faq_product_question_3', 'faq_product_question_4'];
        } elseif ($contentType === 'article') {
            $keys = ['faq_article_question_1', 'faq_article_question_2', 'faq_article_question_3', 'faq_article_question_4'];
        } else {
            $keys = ['faq_page_question_1', 'faq_page_question_2', 'faq_page_question_3', 'faq_page_question_4'];
        }

        $key = $keys[$index] ?? $keys[count($keys) - 1];

        return $this->copyValue($copy, $key, $fallback);
    }

    private function renderFaqBlock(array $faqItems, array $copy): string
    {
        $intro = $this->copyValue($copy, 'faq_intro', '');
        $html = '<section class="faq-list"><div class="eyebrow">' . htmlspecialchars($copy['faq_eyebrow'], ENT_QUOTES, 'UTF-8') . '</div><div class="content-prose"><h2>' . htmlspecialchars($copy['faq_title'], ENT_QUOTES, 'UTF-8') . '</h2></div>';

        if ($intro !== '') {
            $html .= '<p class="faq-intro">' . htmlspecialchars($intro, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        foreach ($faqItems as $index => $item) {
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));
            if ($question === '' || $answer === '') {
                continue;
            }

            $html .= '<details class="faq-item"' . ($index === 0 ? ' open' : '') . '>'
                . '<summary><span class="faq-question-text">' . htmlspecialchars($question, ENT_QUOTES, 'UTF-8') . '</span><span class="faq-toggle" aria-hidden="true"></span></summary>'
                . '<div class="faq-answer">' . $this->renderFaqAnswer($answer) . '</div>'
                . '</details>';
        }

        $html .= '</section>';

        return $html;
    }

    private function renderFaqAnswer(string $answer): string
    {
        $answer = trim($answer);
        if ($answer === '') {
            return '';
        }

        if (str_contains($answer, '<')) {
            return $this->polishLegacyBodyCopy($answer);
        }

        $answer = $this->humanizePublicText($answer);
        if ($answer === '') {
            return '';
        }

        return '<p>' . nl2br(htmlspecialchars($answer, ENT_QUOTES, 'UTF-8')) . '</p>';
    }

    private function isHomeRoute(string $routePath): bool
    {
        return in_array($routePath, ['/', '/en/', '/sl/'], true);
    }

    private function homePathForLanguage(string $languageCode): string
    {
        return match (strtolower(trim($languageCode))) {
            'en' => '/en/',
            'sl' => '/sl/',
            default => '/',
        };
    }

    private function catalogPathForLanguage(string $languageCode): string
    {
        return match (strtolower(trim($languageCode))) {
            'en' => '/en/forever-products/',
            'sl' => '/sl/forever-izdelki/',
            default => '/forever-proizvodi/',
        };
    }

    private function articleCatalogPathForLanguage(string $languageCode): string
    {
        return match (strtolower(trim($languageCode))) {
            'en' => '/en/articles/',
            'sl' => '/sl/clanki/',
            default => '/clanci/',
        };
    }

    private function absoluteUrl(string $path): string
    {
        if ($path === '') {
            return rtrim((string) ($this->config['base_url'] ?? ''), '/') . '/';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim((string) ($this->config['base_url'] ?? ''), '/') . '/' . ltrim($path, '/');
    }

    private function copy(string $languageCode): array
    {
        $copy = [
            'hr' => [
                'brand_tagline' => 'Mirniji odabir Forever proizvoda',
                'nav_home' => 'Početna',
                'nav_guides' => 'Proizvodi',
                'nav_articles' => 'Članci',
                'nav_support' => 'Preporuka',
                'nav_ai' => 'Savjetnik',
                'nav_admin' => 'Admin',
                'home_kicker' => 'Aloe Vera Centar',
                'home_title' => 'Aloe Vera Centar',
                'home_headline' => 'Pronađi Forever Living Products proizvod koji stvarno ima smisla za tvoje potrebe',
                'home_meta_title' => 'Aloe Vera Centar | Pomoć pri odabiru Forever proizvoda',
                'home_meta_description' => 'Aloe Vera Centar pomaže usporediti Forever proizvode, razumjeti za koga su i lakše odlučiti što ima smisla za tvoj cilj.',
                'home_start_eyebrow' => 'Kako krenuti',
                'home_start_title' => 'Odaberi najkraći put',
                'home_subtitle' => 'Ne moraš znati naziv proizvoda unaprijed. Kreni od cilja, navike ili problema koji želiš podržati, a mi ćemo te voditi prema smislenom izboru.',
                'home_primary_cta' => 'Pregledaj proizvode',
                'home_secondary_cta' => 'Zatraži preporuku',
                'home_proof_badge_1' => 'Proizvodi po cilju',
                'home_proof_badge_2' => 'Korisni članci',
                'home_proof_badge_3' => 'Pomoć pri odabiru',
                'home_test_intro' => 'Ako znaš što tražiš, kreni kroz proizvode. Ako još istražuješ, pročitaj članke ili postavi pitanje.',
                'home_feature_1_title' => 'Pregledaj proizvode',
                'home_feature_1_text' => 'Po cilju, rutini i namjeni.',
                'home_feature_2_title' => 'Razjasni nedoumicu',
                'home_feature_2_text' => 'Članci pomažu kada uspoređuješ proizvode ili želiš razumjeti temu prije odluke.',
                'home_feature_3_title' => 'Zatraži preporuku',
                'home_feature_3_text' => 'Kratko objašnjenje što bi imalo smisla za tvoj cilj.',
                'home_showcase_badge' => 'Aloe Vera Centar',
                'home_showcase_title' => 'Kreni od sebe, ne od kataloga',
                'home_showcase_text' => 'Odaberi što želiš podržati i dobit ćeš proizvode i članke koji imaju najviše veze s tom situacijom.',
                'home_showcase_alt' => 'Prikaz proizvoda i sadržaja na Aloe Vera Centru',
                'home_goal_eyebrow' => 'Brzi odabir',
                'home_goal_title' => 'Što želiš podržati?',
                'home_goal_text' => 'Jedan klik vodi te prema najbližem prijedlogu.',
                'goal_digestion_title' => 'Probava',
                'goal_digestion_text' => 'Aloe napitak i mirniji svakodnevni ritam.',
                'goal_skin_title' => 'Koža',
                'goal_skin_text' => 'Kolagen, njega i jednostavnija rutina.',
                'goal_energy_title' => 'Energija',
                'goal_energy_text' => 'Fokus, vitalnost i dnevna podrška.',
                'goal_immunity_title' => 'Imunitet',
                'goal_immunity_text' => 'Vitamini, cink i sezonska otpornost.',
                'goal_care_title' => 'Njega',
                'goal_care_text' => 'Aloe gelovi, kreme i osjetljiva koža.',
                'goal_unsure_title' => 'Nisam siguran',
                'goal_unsure_text' => 'Opiši što želiš i dobit ćeš prvi prijedlog.',
                'goal_product_action' => 'Pogledaj prijedlog',
                'goal_unsure_action' => 'Postavi pitanje',
                'home_quick_1_label' => 'Proizvodi',
                'home_quick_1_title' => 'Pregledaj proizvode',
                'home_quick_1_text' => 'Najbrži put do proizvoda po cilju i rutini.',
                'home_quick_2_label' => 'Blog i savjeti',
                'home_quick_2_title' => 'Pročitaj prije odluke',
                'home_quick_2_text' => 'Korisni članci i kratke usporedbe bez napuhavanja.',
                'home_spotlight_label' => 'Popularan proizvod',
                'intent_guides_eyebrow' => 'Proizvodi',
                'intent_advisor_eyebrow' => 'Preporuka',
                'intent_articles_eyebrow' => 'Članci',
                'market_detected_label' => 'Tvoj lokalni shop',
                'country_detected_label' => 'Zemlja',
                'market_preview_label' => 'Tvoj lokalni shop',
                'market_preview_text' => 'Kad klikneš na kupnju, vodimo te prema službenom Forever Living Products shopu u tvojoj zemlji.',
                'how_it_works_title' => 'Kako do jasnijeg izbora',
                'how_step_1_title' => '1. Prvo razumiješ opcije',
                'how_step_1_text' => 'Članci i vodiči pomažu ti vidjeti za koga proizvod ima smisla, a kada je bolje stati i provjeriti još nešto.',
                'how_step_2_title' => '2. Zatim suziš izbor',
                'how_step_2_text' => 'Preporuke povezuju tvoj cilj s najbližim proizvodima, umjesto da moraš sam prolaziti cijeli katalog.',
                'how_step_3_title' => '3. Kupuješ tek kad si siguran',
                'how_step_3_text' => 'Kad odlučiš, AVC te vodi na službeni Forever shop za tvoje tržište. Ako nisi siguran, možeš prvo pitati.',
                'products_section_title' => 'Popularni proizvodi',
                'products_section_heading' => 'Najtraženiji proizvodi',
                'products_section_text' => 'Proizvodi s kojima ljudi najčešće počinju jer pokrivaju konkretne i svakodnevne potrebe.',
                'articles_section_title' => 'Korisni članci',
                'articles_section_heading' => 'Pročitaj prije odluke',
                'articles_section_text' => 'Objašnjenja za stvarne dvojbe: što ima smisla, što usporediti i kada prvo provjeriti dodatne informacije.',
                'home_about_title' => 'O stranici',
                'summary_eyebrow' => 'Ukratko',
                'summary_title' => 'Što je važno znati',
                'summary_title_product' => 'Što je dobro znati prije odluke',
                'summary_title_article' => 'Najvažnije iz ovog članka',
                'faq_eyebrow' => 'Prije odluke',
                'faq_title' => 'Kratki odgovori koji pomažu',
                'faq_intro' => 'Ako još uspoređuješ opcije, ovdje su stvari koje vrijedi znati prije nego nastaviš dalje.',
                'faq_product_question_1' => 'Kada ovaj proizvod ima najviše smisla?',
                'faq_product_question_2' => 'Što je dobro provjeriti prije narudžbe?',
                'faq_product_question_3' => 'Kako ga uklopiti u svakodnevnu rutinu?',
                'faq_product_question_4' => 'Gdje nastaviti ako ga želiš naručiti?',
                'faq_product_answer_check' => 'Provjeri odgovara li proizvod tvom cilju, sastav i način korištenja. Ako imaš terapiju, dijagnozu, trudnoću ili posebne zdravstvene okolnosti, najbolje je prvo pitati stručnu osobu.',
                'faq_article_question_1' => 'Što je najvažnije zapamtiti?',
                'faq_article_question_2' => 'Na što obratiti pozornost?',
                'faq_article_question_3' => 'Kako ovo primijeniti u praksi?',
                'faq_article_question_4' => 'Koji je sljedeći smislen korak?',
                'faq_article_answer_check' => 'Uzmi tekst kao pomoć pri orijentaciji, ne kao zamjenu za stručan savjet. Ako se tema odnosi na zdravlje, terapiju ili jače simptome, prvo provjeri sa stručnom osobom.',
                'inline_products_title' => 'Preporučeni proizvodi',
                'inline_articles_title' => 'Povezani članci za nastavak čitanja',
                'hero_fallback' => 'Aloe Vera Centar pomaže lakše pregledati proizvode i pronaći jasniji sljedeći korak.',
                'published_label' => 'Objavljeno',
                'empty_body' => 'Sadržaj je uvezen, ali tijelo stranice je prazno.',
                'trust_eyebrow' => 'Kako pomažemo',
                'trust_title' => 'Prvo razumij što ti stvarno treba',
                'trust_text' => 'Na AVC-u možeš usporediti proizvode, pročitati objašnjenja i pitati za preporuku prije nego nastaviš prema shopu.',
                'article_panel_eyebrow' => 'Za lakše čitanje',
                'article_panel_title' => 'Uzmi ono što ti pomaže',
                'article_panel_text' => 'Članak je tu da razjasni temu i pomogne ti prepoznati što ima smisla za tvoju situaciju.',
                'article_panel_point_1_title' => 'Kreni od pitanja',
                'article_panel_point_1_text' => 'Pročitaj dio koji se odnosi na tvoju potrebu i preskoči ono što ti nije važno.',
                'article_panel_point_2_title' => 'Kad želiš sljedeći korak',
                'article_panel_point_2_text' => 'Ispod članka možeš vidjeti povezane proizvode ili pitati za osobniji smjer.',
                'article_sidebar_eyebrow' => 'Dok čitaš',
                'article_sidebar_title' => 'Kreni od svoje situacije',
                'article_sidebar_text' => 'Ne moraš zapamtiti sve. Dovoljno je prepoznati što se odnosi na tvoju rutinu, navike ili pitanje zbog kojeg si otvorio članak.',
                'article_sidebar_next_eyebrow' => 'Ako želiš dalje',
                'article_sidebar_next_title' => 'Poveži temu s praksom',
                'article_sidebar_next_text' => 'Kad ti se tema razjasni, možeš usporediti povezane proizvode ili postaviti pitanje ako još nisi siguran koji korak ima smisla.',
                'sidebar_reassurance_eyebrow' => 'Prije kupnje',
                'sidebar_reassurance_title' => 'Provjeri odgovara li ti proizvod',
                'sidebar_reassurance_text' => 'U vodiču vidiš kome proizvod može odgovarati i kako ga uklopiti u rutinu. Ako se još dvoumiš, pitaj za preporuku prije narudžbe.',
                'support_cta_eyebrow' => 'Još nisi siguran?',
                'support_cta_title' => 'Opiši što želiš postići',
                'support_cta_text' => 'Ako ne znaš koji proizvod odabrati, napiši cilj, navike ili dvojbu i dobit ćeš konkretniji prijedlog.',
                'support_cta_primary' => 'Pitaj za preporuku',
                'support_cta_secondary' => 'Pregledaj proizvode',
                'discount_eyebrow' => 'Popust prije kupnje',
                'discount_title' => 'Aktiviraj 15% popusta za službeni Forever shop',
                'discount_text' => 'Ako želiš naručiti, prvo ti možemo spremiti link s 15% popusta, a zatim te vodimo na službeni Forever Living Products shop u tvojoj zemlji.',
                'price_from' => 'Cijena od',
                'price_help' => 'Cijena je orijentacijska. Konačnu cijenu i dostupnost potvrđuje službeni Forever shop.',
                'market_help' => 'AVC provjerava dostupne market linkove kako bi klik vodio na najbliži službeni shop.',
                'markets_available' => 'Dostupna tržišta',
                'shop_button' => 'Aktiviraj 15% popusta',
                'sidebar_shop_button' => 'Aktiviraj popust',
                'sidebar_related_shop_button' => 'Popust 15%',
                'sidebar_open_guide_button' => 'Detalji',
                'open_guide_button' => 'Pogledaj detalje',
                'open_article_button' => 'Otvori članak',
                'ask_ai_button' => 'Zatraži preporuku',
                'generic_product_name' => 'ovaj proizvod',
                'buy_box_eyebrow' => 'Kad želiš nastaviti',
                'buy_box_title' => 'Je li %s dobar izbor za tebe?',
                'buy_box_text' => 'Ako ti opis proizvoda odgovara, možeš aktivirati 15% popusta i zatim nastaviti na službeni Forever shop. Ako još imaš dvojbu, prvo pitaj za preporuku.',
                'buy_box_repeat_title' => '%s - kad si spreman za narudžbu',
                'buy_box_repeat_text' => 'Narudžbu završavaš na Forever shopu. AVC prije toga može spremiti tvoj discount link i voditi te na pravu zemlju.',
                'buy_box_primary' => 'Aktiviraj popust i nastavi',
                'buy_box_secondary' => 'Pitaj prije kupnje',
                'sticky_shop_prefix' => 'Narudžba',
                'sticky_shop_text' => 'Kad si spreman za narudžbu',
                'sticky_shop_text_product' => '%s - kad si spreman',
                'sticky_shop_primary' => 'Popust 15%',
                'sticky_shop_secondary' => 'Pitaj',
                'article_products_eyebrow' => 'Kad želiš povezati temu s proizvodom',
                'article_products_title' => 'Proizvodi koji se najviše vežu uz ovu temu',
                'article_products_text' => 'Ako nakon čitanja želiš usporediti konkretne opcije, ovo su proizvodi koji su najbliži temi članka.',
                'article_products_primary' => 'Popust 15%',
                'article_products_secondary' => 'Pogledaj vodič',
                'discount_modal_eyebrow' => 'Forever Card popust',
                'discount_modal_title' => 'Želiš 15% popusta za ovaj proizvod?',
                'discount_modal_text' => 'Upiši email ili mobitel i spremit ćemo ti link s popustom. Nakon toga te odmah vodimo na službeni Forever Living Products shop u tvojoj zemlji.',
                'discount_modal_close' => 'Zatvori',
                'discount_field_name' => 'Ime',
                'discount_field_name_placeholder' => 'Kako se zoveš?',
                'discount_field_email' => 'Email',
                'discount_field_email_placeholder' => 'Email za link s popustom',
                'discount_field_phone' => 'Mobitel',
                'discount_field_phone_placeholder' => 'Mobitel ako ti je lakše',
                'discount_consent' => 'Slažem se da me AVC može kontaktirati oko ovog popusta i preporuke proizvoda.',
                'discount_modal_submit' => 'Aktiviraj 15% popusta',
                'discount_modal_skip' => 'Nastavi bez popusta',
                'discount_modal_note' => 'Kupnju završavaš na službenom Forever shopu. AVC samo sprema kontakt i vodi te na pravi link.',
                'discount_modal_contact_required' => 'Upiši email ili mobitel kako bismo ti spremili link s popustom.',
                'discount_modal_consent_required' => 'Potvrdi da te smijemo kontaktirati oko ovog popusta.',
                'discount_modal_loading' => 'Spremamo popust i otvaramo službeni shop...',
                'discount_modal_error' => 'Popust trenutno nije spremljen. Pokušaj ponovno ili nastavi bez popusta.',
                'advisor_eyebrow' => 'Preporuka',
                'advisor_title' => 'Trebaš pomoć pri odabiru?',
                'advisor_text' => 'Ne moraš znati naziv proizvoda. Napiši što želiš postići, što već koristiš ili oko čega se dvoumiš.',
                'advisor_assistant_name' => 'AVC savjetnik',
                'advisor_assistant_status' => 'Tu sam da ti pomognem složiti smislen prvi korak.',
                'advisor_user_label' => 'Ti',
                'advisor_ready' => 'Napiši što te zanima ili odaberi jedan prijedlog.',
                'advisor_typing' => 'AVC savjetnik piše...',
                'advisor_example_1_label' => 'Primjer',
                'advisor_example_1_title' => 'Probava',
                'advisor_example_1_text' => 'Što odabrati za svakodnevnu probavu?',
                'advisor_example_2_label' => 'Primjer',
                'advisor_example_2_title' => 'Koža i njega',
                'advisor_example_2_text' => 'Što je dobar izbor za njegu i blistaviji izgled?',
                'advisor_example_3_label' => 'Primjer',
                'advisor_example_3_title' => 'Imunitet i energija',
                'advisor_example_3_text' => 'Koji proizvod uklopiti u svakodnevnu rutinu za više energije?',
                'advisor_success' => 'Upit je zaprimljen. Dobit ćeš odgovor kroz AVC podršku.',
                'advisor_welcome_hint' => 'Napiši mi svojim riječima što želiš podržati. Ne mora biti savršeno formulirano.',
                'advisor_quick_1_label' => 'Probava',
                'advisor_quick_1_text' => 'Zanima me što bi imalo smisla za svakodnevnu probavu.',
                'advisor_quick_2_label' => 'Koža',
                'advisor_quick_2_text' => 'Želim jednostavniju rutinu za kožu i nisam siguran od čega krenuti.',
                'advisor_quick_3_label' => 'Energija',
                'advisor_quick_3_text' => 'Treba mi prijedlog za više energije i bolji dnevni ritam.',
                'advisor_message_placeholder' => 'Npr. muči me probava i želim nešto za svaki dan...',
                'advisor_send' => 'Pošalji',
                'advisor_personal_followup' => 'Želim da mi se netko javi',
                'advisor_contact_submit' => 'Pošalji kontakt',
                'advisor_loading' => 'Učitavam savjetnika...',
                'advisor_thinking' => 'Čitam što si napisao i slažem prijedlog...',
                'advisor_error' => 'Savjetnik trenutno nije dostupan. Pokušaj ponovno za koji trenutak.',
                'advisor_contact_saving' => 'Spremam kontakt...',
                'advisor_lead_error' => 'Kontakt trenutno nije spremljen. Pokušaj ponovno.',
                'advisor_feedback_prompt' => 'Je li ovo pomoglo?',
                'advisor_feedback_helpful' => 'Da',
                'advisor_feedback_not_helpful' => 'Ne baš',
                'field_name' => 'Ime',
                'field_name_placeholder' => 'Kako se zoveš?',
                'field_email' => 'Email',
                'field_email_placeholder' => 'Na koji email da ti se javimo?',
                'field_phone' => 'Telefon',
                'field_phone_placeholder' => 'Opcionalno',
                'field_question' => 'Pitanje',
                'field_question_placeholder' => 'Koji cilj imaš i koji Forever proizvodi te zanimaju?',
                'advisor_submit' => 'Pošalji upit',
                'footer_title' => 'Aloe Vera Centar',
                'footer_text' => 'Aloe Vera Centar pomaže da lakše razumiješ Forever proizvode, usporediš opcije i napraviš sljedeći korak kad ti ima smisla.',
                'related_products_title' => 'Povezani proizvodi',
                'related_articles_title' => 'Dalje za čitanje',
                'product_badge' => 'Forever proizvod',
                'article_badge' => 'Blog članak',
                'page_badge' => 'Stranica',
                'cards_empty' => 'Još nema dovoljno sadržaja za prikaz kartica.',
            ],
            'en' => [
                'brand_tagline' => 'Calmer Forever product choices',
                'nav_home' => 'Home',
                'nav_guides' => 'Products',
                'nav_articles' => 'Articles',
                'nav_support' => 'Guidance',
                'nav_ai' => 'Advisor',
                'nav_admin' => 'Admin',
                'home_kicker' => 'Aloe Vera Centar',
                'home_title' => 'Aloe Vera Centar',
                'home_headline' => 'Find the Forever product that actually fits your routine',
                'home_meta_title' => 'Aloe Vera Centar | Help choosing Forever products',
                'home_meta_description' => 'Aloe Vera Centar helps you compare Forever products, understand who they suit and decide what makes sense for your goal.',
                'home_start_eyebrow' => 'How to start',
                'home_start_title' => 'Pick the shortest path',
                'home_subtitle' => 'You do not need to know the product name in advance. Start with your goal, habit or concern and move toward a sensible choice.',
                'home_primary_cta' => 'Browse products',
                'home_secondary_cta' => 'Request guidance',
                'home_proof_badge_1' => 'Products by goal',
                'home_proof_badge_2' => 'Useful articles',
                'home_proof_badge_3' => 'Help with choosing',
                'home_test_intro' => 'If you know what you need, start with products. If you are still exploring, read articles or ask a question.',
                'home_feature_1_title' => 'Browse products',
                'home_feature_1_text' => 'By goal, routine and use case.',
                'home_feature_2_title' => 'Clear up the doubt',
                'home_feature_2_text' => 'Articles help when you are comparing products or want to understand the topic first.',
                'home_feature_3_title' => 'Request guidance',
                'home_feature_3_text' => 'A short explanation of what may fit your goal.',
                'home_showcase_badge' => 'Aloe Vera Centar',
                'home_showcase_title' => 'Start with yourself, not the catalogue',
                'home_showcase_text' => 'Choose what you want to support and see the products and articles most connected with that situation.',
                'home_showcase_alt' => 'Preview of products and content on Aloe Vera Centar',
                'home_goal_eyebrow' => 'Quick choice',
                'home_goal_title' => 'What do you want to support?',
                'home_goal_text' => 'One click takes you toward the closest suggestion.',
                'goal_digestion_title' => 'Digestion',
                'goal_digestion_text' => 'Aloe drink and a calmer daily rhythm.',
                'goal_skin_title' => 'Skin',
                'goal_skin_text' => 'Collagen, care and a simpler routine.',
                'goal_energy_title' => 'Energy',
                'goal_energy_text' => 'Focus, vitality and daily support.',
                'goal_immunity_title' => 'Immunity',
                'goal_immunity_text' => 'Vitamins, zinc and seasonal resilience.',
                'goal_care_title' => 'Care',
                'goal_care_text' => 'Aloe gels, creams and sensitive skin.',
                'goal_unsure_title' => 'Not sure',
                'goal_unsure_text' => 'Describe what you want and get a first suggestion.',
                'goal_product_action' => 'View suggestion',
                'goal_unsure_action' => 'Ask a question',
                'home_quick_1_label' => 'Products',
                'home_quick_1_title' => 'Browse products',
                'home_quick_1_text' => 'The quickest path to products by goal and routine.',
                'home_quick_2_label' => 'Journal and advice',
                'home_quick_2_title' => 'Read before you decide',
                'home_quick_2_text' => 'Useful articles and short comparisons without inflated claims.',
                'home_spotlight_label' => 'Popular product',
                'intent_guides_eyebrow' => 'Products',
                'intent_advisor_eyebrow' => 'Guidance',
                'intent_articles_eyebrow' => 'Articles',
                'market_detected_label' => 'Your local shop',
                'country_detected_label' => 'Country',
                'market_preview_label' => 'Your local shop',
                'market_preview_text' => 'When you choose to buy, we route you toward the official Forever shop for your market.',
                'how_it_works_title' => 'How to choose with more clarity',
                'how_step_1_title' => '1. First, understand the options',
                'how_step_1_text' => 'Articles and guides help you see who a product suits and when it is better to pause and check something else.',
                'how_step_2_title' => '2. Then narrow the choice',
                'how_step_2_text' => 'Recommendations connect your goal with the closest products so you do not have to browse the whole catalogue alone.',
                'how_step_3_title' => '3. Buy only when it makes sense',
                'how_step_3_text' => 'When you decide, AVC routes you to the official Forever shop for your market. If you are unsure, ask first.',
                'products_section_title' => 'Popular products',
                'products_section_heading' => 'Most viewed products',
                'products_section_text' => 'Products people often start with because they cover concrete everyday needs.',
                'articles_section_title' => 'Helpful articles',
                'articles_section_heading' => 'Read before you decide',
                'articles_section_text' => 'Explanations for real questions: what makes sense, what to compare and when to check a little more first.',
                'home_about_title' => 'About this page',
                'summary_eyebrow' => 'In short',
                'summary_title' => 'What matters most',
                'summary_title_product' => 'What to know before deciding',
                'summary_title_article' => 'The key points from this article',
                'faq_eyebrow' => 'Before you decide',
                'faq_title' => 'Short answers that help',
                'faq_intro' => 'If you are still comparing options, these are the points worth checking before you continue.',
                'faq_product_question_1' => 'When does this product make the most sense?',
                'faq_product_question_2' => 'What should you check before ordering?',
                'faq_product_question_3' => 'How could it fit into a daily routine?',
                'faq_product_question_4' => 'Where do you continue if you want to order?',
                'faq_product_answer_check' => 'Check whether the product fits your goal, its ingredients and the suggested use. If you have therapy, a diagnosis, pregnancy or specific health circumstances, ask a qualified professional first.',
                'faq_article_question_1' => 'What is worth remembering?',
                'faq_article_question_2' => 'What should you pay attention to?',
                'faq_article_question_3' => 'How can you use this in practice?',
                'faq_article_question_4' => 'What is the next sensible step?',
                'faq_article_answer_check' => 'Use the article as orientation, not as a replacement for professional advice. If the topic involves health, therapy or stronger symptoms, check with a qualified professional first.',
                'inline_products_title' => 'Recommended products',
                'inline_articles_title' => 'Related articles to keep exploring',
                'hero_fallback' => 'Aloe Vera Centar helps people explore products and find a clearer next step.',
                'published_label' => 'Published',
                'empty_body' => 'The content was imported, but the page body is empty.',
                'trust_eyebrow' => 'How we help',
                'trust_title' => 'First understand what you really need',
                'trust_text' => 'On AVC you can compare products, read clear explanations and ask for guidance before continuing to the shop.',
                'article_panel_eyebrow' => 'Easier reading',
                'article_panel_title' => 'Take what helps you',
                'article_panel_text' => 'The article is here to clarify the topic and help you see what makes sense for your situation.',
                'article_panel_point_1_title' => 'Start with your question',
                'article_panel_point_1_text' => 'Read the part that matches your need and skip what is not relevant right now.',
                'article_panel_point_2_title' => 'When you want the next step',
                'article_panel_point_2_text' => 'Below the article you can compare related products or ask for a more personal direction.',
                'article_sidebar_eyebrow' => 'While you read',
                'article_sidebar_title' => 'Start from your situation',
                'article_sidebar_text' => 'You do not need to remember everything. It is enough to notice what connects with your routine, habits or the question that brought you here.',
                'article_sidebar_next_eyebrow' => 'If you want to continue',
                'article_sidebar_next_title' => 'Connect the topic with real life',
                'article_sidebar_next_text' => 'Once the topic is clearer, you can compare related products or ask a question if the next step is still unclear.',
                'support_cta_eyebrow' => 'Still unsure?',
                'support_cta_title' => 'Describe what you want to achieve',
                'support_cta_text' => 'If you do not know which product to choose, write your goal, habits or doubts and get a more concrete suggestion.',
                'support_cta_primary' => 'Request guidance',
                'support_cta_secondary' => 'Browse products',
                'discount_eyebrow' => 'Discount before checkout',
                'discount_title' => 'Activate 15% off for the official Forever shop',
                'discount_text' => 'If you want to order, we can first save your 15% discount link and then send you to the official Forever Living Products shop in your country.',
                'price_from' => 'Price from',
                'price_help' => 'The price is indicative. Final price and availability are confirmed on the official Forever shop.',
                'market_help' => 'AVC checks mapped market links so the click leads to the closest official shop.',
                'markets_available' => 'Markets available',
                'shop_button' => 'Activate 15% off',
                'sidebar_shop_button' => 'Activate discount',
                'sidebar_related_shop_button' => '15% off',
                'sidebar_open_guide_button' => 'Details',
                'open_guide_button' => 'View details',
                'open_article_button' => 'Open article',
                'ask_ai_button' => 'Request guidance',
                'generic_product_name' => 'this product',
                'buy_box_eyebrow' => 'When you want to continue',
                'buy_box_title' => 'Does %s feel like the right fit?',
                'buy_box_text' => 'If the product description fits your situation, you can activate 15% off and then continue to the official Forever shop. If you are still unsure, ask first.',
                'buy_box_repeat_title' => '%s - when you are ready to order',
                'buy_box_repeat_text' => 'You complete the order on the Forever shop. AVC can first save your discount link and route you to the right country.',
                'buy_box_primary' => 'Activate discount and continue',
                'buy_box_secondary' => 'Ask before buying',
                'sticky_shop_prefix' => 'Order',
                'sticky_shop_text' => 'When you are ready to order',
                'sticky_shop_text_product' => '%s - when you are ready',
                'sticky_shop_primary' => '15% off',
                'sticky_shop_secondary' => 'Ask',
                'article_products_eyebrow' => 'When you want to connect the topic with a product',
                'article_products_title' => 'Products most closely related to this topic',
                'article_products_text' => 'If you want to compare concrete options after reading, these products are the closest match to the article topic.',
                'article_products_primary' => '15% off',
                'article_products_secondary' => 'View guide',
                'discount_modal_eyebrow' => 'Forever Card discount',
                'discount_modal_title' => 'Want 15% off this product?',
                'discount_modal_text' => 'Enter your email or phone and we will save your discount link. Then we will take you straight to the official Forever Living Products shop in your country.',
                'discount_modal_close' => 'Close',
                'discount_field_name' => 'Name',
                'discount_field_name_placeholder' => 'What is your name?',
                'discount_field_email' => 'Email',
                'discount_field_email_placeholder' => 'Email for the discount link',
                'discount_field_phone' => 'Phone',
                'discount_field_phone_placeholder' => 'Phone if that is easier',
                'discount_consent' => 'I agree that AVC may contact me about this discount and product recommendation.',
                'discount_modal_submit' => 'Activate 15% off',
                'discount_modal_skip' => 'Continue without discount',
                'discount_modal_note' => 'You complete the purchase on the official Forever shop. AVC only saves your contact and routes you to the right link.',
                'discount_modal_contact_required' => 'Enter your email or phone so we can save your discount link.',
                'discount_modal_consent_required' => 'Please confirm that we may contact you about this discount.',
                'discount_modal_loading' => 'Saving your discount and opening the official shop...',
                'discount_modal_error' => 'The discount was not saved yet. Try again or continue without discount.',
                'advisor_eyebrow' => 'Guidance',
                'advisor_title' => 'Need help choosing?',
                'advisor_text' => 'You do not need to know the product name. Write what you want to achieve, what you already use or what you are unsure about.',
                'advisor_assistant_name' => 'AVC advisor',
                'advisor_assistant_status' => 'Here to help you choose a sensible first step.',
                'advisor_user_label' => 'You',
                'advisor_ready' => 'Write what you need or choose a prompt.',
                'advisor_typing' => 'AVC advisor is typing...',
                'advisor_example_1_label' => 'Example',
                'advisor_example_1_title' => 'Digestion',
                'advisor_example_1_text' => 'What is a good everyday choice for digestion support?',
                'advisor_example_2_label' => 'Example',
                'advisor_example_2_title' => 'Skin and care',
                'advisor_example_2_text' => 'What should I choose for skincare and a healthier glow?',
                'advisor_example_3_label' => 'Example',
                'advisor_example_3_title' => 'Immunity and energy',
                'advisor_example_3_text' => 'Which product fits a daily routine focused on energy?',
                'advisor_success' => 'Your question has been received. AVC support will follow up.',
                'advisor_welcome_hint' => 'Write what you want to support in your own words. It does not need to be perfectly phrased.',
                'advisor_quick_1_label' => 'Digestion',
                'advisor_quick_1_text' => 'I want to know what makes sense for everyday digestion support.',
                'advisor_quick_2_label' => 'Skin',
                'advisor_quick_2_text' => 'I want a simpler skincare routine and I am not sure where to start.',
                'advisor_quick_3_label' => 'Energy',
                'advisor_quick_3_text' => 'I need a suggestion for more energy and a better daily rhythm.',
                'advisor_message_placeholder' => 'For example: I want daily support for digestion...',
                'advisor_send' => 'Send',
                'advisor_personal_followup' => 'I want someone to contact me',
                'advisor_contact_submit' => 'Send contact',
                'advisor_loading' => 'Loading the advisor...',
                'advisor_thinking' => 'Reading your message and preparing a suggestion...',
                'advisor_error' => 'The advisor is currently unavailable. Please try again shortly.',
                'advisor_contact_saving' => 'Saving your contact...',
                'advisor_lead_error' => 'The contact was not saved yet. Please try again.',
                'advisor_feedback_prompt' => 'Was this helpful?',
                'advisor_feedback_helpful' => 'Yes',
                'advisor_feedback_not_helpful' => 'Not really',
                'field_name' => 'Name',
                'field_name_placeholder' => 'What is your name?',
                'field_email' => 'Email',
                'field_email_placeholder' => 'Where should we reply?',
                'field_phone' => 'Phone',
                'field_phone_placeholder' => 'Optional',
                'field_question' => 'Question',
                'field_question_placeholder' => 'What is your goal and which Forever products are you exploring?',
                'advisor_submit' => 'Send request',
                'footer_title' => 'Aloe Vera Centar',
                'footer_text' => 'Aloe Vera Centar helps you understand Forever products, compare options and take the next step when it makes sense.',
                'related_products_title' => 'Related products',
                'related_articles_title' => 'Keep reading',
                'product_badge' => 'Forever product',
                'article_badge' => 'Article',
                'page_badge' => 'Page',
                'cards_empty' => 'There is not enough content yet to populate this section.',
            ],
            'sl' => [
                'brand_tagline' => 'Mirnejša izbira Forever izdelkov',
                'nav_home' => 'Domov',
                'nav_guides' => 'Izdelki',
                'nav_articles' => 'Članki',
                'nav_support' => 'Priporočilo',
                'nav_ai' => 'Svetovalec',
                'nav_admin' => 'Admin',
                'home_kicker' => 'Aloe Vera Centar',
                'home_title' => 'Aloe Vera Centar',
                'home_headline' => 'Najdi Forever izdelek, ki res ustreza tvoji rutini',
                'home_meta_title' => 'Aloe Vera Centar | Pomoč pri izbiri Forever izdelkov',
                'home_meta_description' => 'Aloe Vera Centar pomaga primerjati Forever izdelke, razumeti komu ustrezajo in lažje odločiti, kaj ima smisel za tvoj cilj.',
                'home_start_eyebrow' => 'Kako začeti',
                'home_start_title' => 'Izberi najkrajšo pot',
                'home_subtitle' => 'Ni ti treba vnaprej poznati imena izdelka. Začni s ciljem, navado ali vprašanjem in pojdi proti smiselni izbiri.',
                'home_primary_cta' => 'Preglej izdelke',
                'home_secondary_cta' => 'Zahtevaj priporočilo',
                'home_proof_badge_1' => 'Izdelki po cilju',
                'home_proof_badge_2' => 'Koristni članki',
                'home_proof_badge_3' => 'Pomoč pri izbiri',
                'home_test_intro' => 'Če veš, kaj iščeš, začni pri izdelkih. Če še raziskuješ, preberi članke ali postavi vprašanje.',
                'home_feature_1_title' => 'Preglej izdelke',
                'home_feature_1_text' => 'Po cilju, rutini in namenu.',
                'home_feature_2_title' => 'Razjasni dilemo',
                'home_feature_2_text' => 'Članki pomagajo, ko primerjaš izdelke ali želiš najprej razumeti temo.',
                'home_feature_3_title' => 'Zahtevaj priporočilo',
                'home_feature_3_text' => 'Kratka razlaga, kaj bi lahko ustrezalo tvojemu cilju.',
                'home_showcase_badge' => 'Aloe Vera Centar',
                'home_showcase_title' => 'Začni pri sebi, ne pri katalogu',
                'home_showcase_text' => 'Izberi, kaj želiš podpreti, in videl boš izdelke ter članke, ki so najbližje tej situaciji.',
                'home_showcase_alt' => 'Prikaz izdelkov in vsebine na Aloe Vera Centru',
                'home_goal_eyebrow' => 'Hitra izbira',
                'home_goal_title' => 'Kaj želiš podpreti?',
                'home_goal_text' => 'En klik te vodi do najbližjega predloga.',
                'goal_digestion_title' => 'Prebava',
                'goal_digestion_text' => 'Aloe napitek in mirnejši dnevni ritem.',
                'goal_skin_title' => 'Koža',
                'goal_skin_text' => 'Kolagen, nega in enostavnejša rutina.',
                'goal_energy_title' => 'Energija',
                'goal_energy_text' => 'Fokus, vitalnost in dnevna podpora.',
                'goal_immunity_title' => 'Imunost',
                'goal_immunity_text' => 'Vitamini, cink in sezonska odpornost.',
                'goal_care_title' => 'Nega',
                'goal_care_text' => 'Aloe geli, kreme in občutljiva koža.',
                'goal_unsure_title' => 'Nisem prepričan',
                'goal_unsure_text' => 'Opiši, kaj želiš, in prejmi prvi predlog.',
                'goal_product_action' => 'Poglej predlog',
                'goal_unsure_action' => 'Postavi vprašanje',
                'home_quick_1_label' => 'Izdelki',
                'home_quick_1_title' => 'Preglej izdelke',
                'home_quick_1_text' => 'Najhitrejša pot do izdelkov po cilju in rutini.',
                'home_quick_2_label' => 'Blog in nasveti',
                'home_quick_2_title' => 'Preberi pred odločitvijo',
                'home_quick_2_text' => 'Koristni članki in kratke primerjave brez napihovanja.',
                'home_spotlight_label' => 'Priljubljen izdelek',
                'intent_guides_eyebrow' => 'Izdelki',
                'intent_advisor_eyebrow' => 'Priporočilo',
                'intent_articles_eyebrow' => 'Članki',
                'market_detected_label' => 'Tvoj lokalni shop',
                'country_detected_label' => 'Država',
                'market_preview_label' => 'Tvoj lokalni shop',
                'market_preview_text' => 'Ko se odločiš za nakup, te vodimo proti uradnemu Forever shopu za tvoj trg.',
                'how_it_works_title' => 'Kako do jasnejše izbire',
                'how_step_1_title' => '1. Najprej razumeš možnosti',
                'how_step_1_text' => 'Članki in vodiči pomagajo videti, komu izdelek ustreza in kdaj je bolje še kaj preveriti.',
                'how_step_2_title' => '2. Nato zožiš izbiro',
                'how_step_2_text' => 'Priporočila povežejo tvoj cilj z najbližjimi izdelki, da ti ni treba sam pregledovati celotnega kataloga.',
                'how_step_3_title' => '3. Kupiš šele, ko ima smisel',
                'how_step_3_text' => 'Ko se odločiš, te AVC vodi na uradni Forever shop za tvoj trg. Če nisi prepričan, lahko najprej vprašaš.',
                'products_section_title' => 'Priljubljeni izdelki',
                'products_section_heading' => 'Najbolj iskani izdelki',
                'products_section_text' => 'Izdelki, s katerimi ljudje pogosto začnejo, ker pokrivajo konkretne vsakodnevne potrebe.',
                'articles_section_title' => 'Koristni članki',
                'articles_section_heading' => 'Preberi pred odločitvijo',
                'articles_section_text' => 'Razlage za resnične dileme: kaj ima smisel, kaj primerjati in kdaj najprej preveriti še kaj.',
                'home_about_title' => 'O strani',
                'summary_eyebrow' => 'Na kratko',
                'summary_title' => 'Kaj je najpomembnejše',
                'summary_title_product' => 'Kaj je dobro vedeti pred odločitvijo',
                'summary_title_article' => 'Najpomembnejše iz tega članka',
                'faq_eyebrow' => 'Pred odločitvijo',
                'faq_title' => 'Kratki odgovori za lažjo izbiro',
                'faq_intro' => 'Če še primerjaš možnosti, so tukaj stvari, ki jih je dobro preveriti, preden nadaljuješ.',
                'faq_product_question_1' => 'Kdaj ima ta izdelek največ smisla?',
                'faq_product_question_2' => 'Kaj je dobro preveriti pred naročilom?',
                'faq_product_question_3' => 'Kako ga vključiti v vsakodnevno rutino?',
                'faq_product_question_4' => 'Kje nadaljuješ, če ga želiš naročiti?',
                'faq_product_answer_check' => 'Preveri, ali izdelek ustreza tvojemu cilju, sestavo in način uporabe. Če imaš terapijo, diagnozo, nosečnost ali posebne zdravstvene okoliščine, se najprej posvetuj s strokovno osebo.',
                'faq_article_question_1' => 'Kaj si je dobro zapomniti?',
                'faq_article_question_2' => 'Na kaj biti pozoren?',
                'faq_article_question_3' => 'Kako to uporabiti v praksi?',
                'faq_article_question_4' => 'Kateri je naslednji smiseln korak?',
                'faq_article_answer_check' => 'Članek uporabi kot pomoč pri orientaciji, ne kot zamenjavo za strokovni nasvet. Če gre za zdravje, terapijo ali močnejše simptome, najprej preveri s strokovno osebo.',
                'inline_products_title' => 'Priporočeni izdelki',
                'inline_articles_title' => 'Povezani članki za nadaljnje branje',
                'hero_fallback' => 'Aloe Vera Centar pomaga lažje pregledati izdelke in najti jasnejši naslednji korak.',
                'published_label' => 'Objavljeno',
                'empty_body' => 'Vsebina je uvožena, vendar je telo strani prazno.',
                'trust_eyebrow' => 'Kako pomagamo',
                'trust_title' => 'Najprej razumi, kaj res potrebuješ',
                'trust_text' => 'Na AVC lahko primerjaš izdelke, prebereš jasne razlage in vprašaš za priporočilo, preden nadaljuješ v shop.',
                'article_panel_eyebrow' => 'Za lažje branje',
                'article_panel_title' => 'Vzemi tisto, kar ti pomaga',
                'article_panel_text' => 'Članek je tukaj, da razjasni temo in pomaga prepoznati, kaj ima smisel za tvojo situacijo.',
                'article_panel_point_1_title' => 'Začni z vprašanjem',
                'article_panel_point_1_text' => 'Preberi del, ki se nanaša na tvojo potrebo, in preskoči, kar trenutno ni pomembno.',
                'article_panel_point_2_title' => 'Ko želiš naslednji korak',
                'article_panel_point_2_text' => 'Pod člankom lahko primerjaš povezane izdelke ali vprašaš za bolj oseben smerokaz.',
                'article_sidebar_eyebrow' => 'Med branjem',
                'article_sidebar_title' => 'Začni pri svoji situaciji',
                'article_sidebar_text' => 'Ni ti treba zapomniti vsega. Dovolj je prepoznati, kaj se nanaša na tvojo rutino, navade ali vprašanje, zaradi katerega si odprl članek.',
                'article_sidebar_next_eyebrow' => 'Če želiš naprej',
                'article_sidebar_next_title' => 'Poveži temo s prakso',
                'article_sidebar_next_text' => 'Ko je tema jasnejša, lahko primerjaš povezane izdelke ali postaviš vprašanje, če naslednji korak še ni jasen.',
                'support_cta_eyebrow' => 'Še nisi prepričan?',
                'support_cta_title' => 'Opiši, kaj želiš doseči',
                'support_cta_text' => 'Če ne veš, kateri izdelek izbrati, napiši cilj, navade ali dvom in prejmi bolj konkreten predlog.',
                'support_cta_primary' => 'Vprašaj za priporočilo',
                'support_cta_secondary' => 'Preglej izdelke',
                'discount_eyebrow' => 'Popust pred nakupom',
                'discount_title' => 'Aktiviraj 15% popusta za uradni Forever shop',
                'discount_text' => 'Če želiš naročiti, ti lahko najprej shranimo povezavo s 15% popusta, nato pa te vodimo v uradni Forever Living Products shop v tvoji državi.',
                'price_from' => 'Cena od',
                'price_help' => 'Cena je okvirna. Končno ceno in razpoložljivost potrdi uradni Forever shop.',
                'market_help' => 'AVC preverja povezave po trgih, da klik vodi do najbližjega uradnega shopa.',
                'markets_available' => 'Dostopni trgi',
                'shop_button' => 'Aktiviraj 15% popusta',
                'sidebar_shop_button' => 'Aktiviraj popust',
                'sidebar_related_shop_button' => 'Popust 15%',
                'sidebar_open_guide_button' => 'Podrobnosti',
                'open_guide_button' => 'Poglej podrobnosti',
                'open_article_button' => 'Odpri članek',
                'ask_ai_button' => 'Zahtevaj priporočilo',
                'generic_product_name' => 'ta izdelek',
                'buy_box_eyebrow' => 'Ko želiš nadaljevati',
                'buy_box_title' => 'Ali je %s dobra izbira zate?',
                'buy_box_text' => 'Če opis izdelka ustreza tvoji situaciji, lahko aktiviraš 15% popusta in nato nadaljuješ v uradni Forever shop. Če še nisi prepričan, najprej vprašaj.',
                'buy_box_repeat_title' => '%s - ko si pripravljen na naročilo',
                'buy_box_repeat_text' => 'Naročilo zaključiš v Forever shopu. AVC lahko pred tem shrani tvojo discount povezavo in te vodi v pravo državo.',
                'buy_box_primary' => 'Aktiviraj popust in nadaljuj',
                'buy_box_secondary' => 'Vprašaj pred nakupom',
                'sticky_shop_prefix' => 'Naročilo',
                'sticky_shop_text' => 'Ko si pripravljen na naročilo',
                'sticky_shop_text_product' => '%s - ko si pripravljen',
                'sticky_shop_primary' => 'Popust 15%',
                'sticky_shop_secondary' => 'Vprašaj',
                'article_products_eyebrow' => 'Ko želiš temo povezati z izdelkom',
                'article_products_title' => 'Izdelki, ki so najbližje tej temi',
                'article_products_text' => 'Če želiš po branju primerjati konkretne možnosti, so to izdelki, ki se najbližje povezujejo s temo članka.',
                'article_products_primary' => 'Popust 15%',
                'article_products_secondary' => 'Poglej vodič',
                'discount_modal_eyebrow' => 'Forever Card popust',
                'discount_modal_title' => 'Želiš 15% popusta za ta izdelek?',
                'discount_modal_text' => 'Vpiši email ali telefon in shranili bomo tvojo povezavo s popustom. Nato te takoj vodimo v uradni Forever Living Products shop v tvoji državi.',
                'discount_modal_close' => 'Zapri',
                'discount_field_name' => 'Ime',
                'discount_field_name_placeholder' => 'Kako ti je ime?',
                'discount_field_email' => 'Email',
                'discount_field_email_placeholder' => 'Email za povezavo s popustom',
                'discount_field_phone' => 'Telefon',
                'discount_field_phone_placeholder' => 'Telefon, če ti je lažje',
                'discount_consent' => 'Strinjam se, da me AVC lahko kontaktira glede tega popusta in priporočila izdelka.',
                'discount_modal_submit' => 'Aktiviraj 15% popusta',
                'discount_modal_skip' => 'Nadaljuj brez popusta',
                'discount_modal_note' => 'Nakup zaključiš v uradnem Forever shopu. AVC samo shrani kontakt in te vodi na pravo povezavo.',
                'discount_modal_contact_required' => 'Vpiši email ali telefon, da shranimo povezavo s popustom.',
                'discount_modal_consent_required' => 'Potrdi, da te smemo kontaktirati glede tega popusta.',
                'discount_modal_loading' => 'Shranjujemo popust in odpiramo uradni shop...',
                'discount_modal_error' => 'Popust trenutno ni shranjen. Poskusi znova ali nadaljuj brez popusta.',
                'advisor_eyebrow' => 'Priporočilo',
                'advisor_title' => 'Potrebuješ pomoč pri izbiri?',
                'advisor_text' => 'Ni ti treba poznati imena izdelka. Napiši, kaj želiš doseči, kaj že uporabljaš ali o čem se odločaš.',
                'advisor_assistant_name' => 'AVC svetovalec',
                'advisor_assistant_status' => 'Tukaj sem, da ti pomagam izbrati smiseln prvi korak.',
                'advisor_user_label' => 'Ti',
                'advisor_ready' => 'Napiši, kaj te zanima, ali izberi en predlog.',
                'advisor_typing' => 'AVC svetovalec piše...',
                'advisor_example_1_label' => 'Primer',
                'advisor_example_1_title' => 'Prebava',
                'advisor_example_1_text' => 'Kaj je dobra izbira za vsakodnevno podporo prebavi?',
                'advisor_example_2_label' => 'Primer',
                'advisor_example_2_title' => 'Koža in nega',
                'advisor_example_2_text' => 'Kaj izbrati za nego kože in bolj sijoč videz?',
                'advisor_example_3_label' => 'Primer',
                'advisor_example_3_title' => 'Imunost in energija',
                'advisor_example_3_text' => 'Kateri izdelek sodi v vsakodnevno rutino za več energije?',
                'advisor_success' => 'Vprašanje je prejeto. AVC podpora se bo oglasila.',
                'advisor_welcome_hint' => 'S svojimi besedami napiši, kaj želiš podpreti. Ni treba, da je popolno oblikovano.',
                'advisor_quick_1_label' => 'Prebava',
                'advisor_quick_1_text' => 'Zanima me, kaj ima smisel za vsakodnevno podporo prebavi.',
                'advisor_quick_2_label' => 'Koža',
                'advisor_quick_2_text' => 'Želim enostavnejšo rutino za kožo in nisem prepričan, kje začeti.',
                'advisor_quick_3_label' => 'Energija',
                'advisor_quick_3_text' => 'Potrebujem predlog za več energije in boljši dnevni ritem.',
                'advisor_message_placeholder' => 'Na primer: želim vsakodnevno podporo prebavi...',
                'advisor_send' => 'Pošlji',
                'advisor_personal_followup' => 'Želim, da me nekdo kontaktira',
                'advisor_contact_submit' => 'Pošlji kontakt',
                'advisor_loading' => 'Nalaganje svetovalca...',
                'advisor_thinking' => 'Berem tvoje sporočilo in pripravljam predlog...',
                'advisor_error' => 'Svetovalec trenutno ni na voljo. Poskusi znova čez trenutek.',
                'advisor_contact_saving' => 'Shranjujem kontakt...',
                'advisor_lead_error' => 'Kontakt še ni shranjen. Poskusi znova.',
                'advisor_feedback_prompt' => 'Je to pomagalo?',
                'advisor_feedback_helpful' => 'Da',
                'advisor_feedback_not_helpful' => 'Ne ravno',
                'field_name' => 'Ime',
                'field_name_placeholder' => 'Kako ti je ime?',
                'field_email' => 'Email',
                'field_email_placeholder' => 'Kam ti odgovorimo?',
                'field_phone' => 'Telefon',
                'field_phone_placeholder' => 'Neobvezno',
                'field_question' => 'Vprašanje',
                'field_question_placeholder' => 'Kakšen je tvoj cilj in kateri Forever izdelki te zanimajo?',
                'advisor_submit' => 'Pošlji vprašanje',
                'footer_title' => 'Aloe Vera Centar',
                'footer_text' => 'Aloe Vera Centar pomaga razumeti Forever izdelke, primerjati možnosti in narediti naslednji korak, ko ima smisel.',
                'related_products_title' => 'Povezani izdelki',
                'related_articles_title' => 'Nadaljuj z branjem',
                'product_badge' => 'Forever izdelek',
                'article_badge' => 'Članek',
                'page_badge' => 'Stran',
                'cards_empty' => 'Za ta del še ni dovolj vsebine.',
            ],
        ];

        return $copy[strtolower($languageCode)] ?? $copy['hr'];
    }

    private function brandName(): string
    {
        return 'Aloe Vera Centar';
    }

    private function brandLogoPath(string $variant = 'horizontal'): string
    {
        return $variant === 'stacked'
            ? '/media/branding/aloe-vera-centar-logo-stacked.png'
            : '/media/branding/aloe-vera-centar-logo-horizontal.png';
    }
}
