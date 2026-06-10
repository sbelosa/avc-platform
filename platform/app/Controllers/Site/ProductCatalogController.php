<?php

declare(strict_types=1);

namespace Avc\Controllers\Site;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\ContentRepository;
use Avc\Services\Geo\CountryDetector;
use Avc\Services\Geo\MarketResolver;
use Avc\Support\PageRenderer;

final class ProductCatalogController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $languageCode = $this->languageFromPath($this->request->path());
        $canonicalPath = $this->catalogPathForLanguage($languageCode);
        $normalizedCanonicalPath = rtrim($canonicalPath, '/');

        if ($this->request->path() !== $normalizedCanonicalPath) {
            $this->response->redirect($canonicalPath, 301);
        }

        $copy = $this->copy($languageCode);
        $products = (new ContentRepository($this->config))->findProductCatalogCards($languageCode, 160);
        $products = $this->decorateProducts($products, $languageCode);
        $featuredProducts = $this->selectFeaturedProducts($products);
        $marketContext = $this->buildMarketContext($products);
        $alternateLinks = $this->buildAlternateLinks();
        $canonicalUrl = $this->absoluteUrl($canonicalPath);
        $title = (string) $copy['meta_title'];
        $description = (string) $copy['meta_description'];

        $body = '<div class="shell catalog-page" data-catalog>'
            . $this->renderSiteHeader($copy, $languageCode)
            . '<section class="hero catalog-hero"><div class="hero-panel catalog-hero-panel">'
            . '<div class="catalog-hero-copy"><span class="hero-kicker">' . $this->e($copy['kicker']) . '</span>'
            . '<div class="content-prose"><h1>' . $this->e($copy['headline']) . '</h1></div>'
            . '<p class="muted hero-intro">' . $this->e($copy['intro']) . '</p>'
            . '<div class="hero-note"><span class="badge">' . $this->e($copy['stat_products']) . ': ' . count($products) . '</span><span class="badge">' . $this->e($copy['stat_ready']) . ': ' . $this->countReadyProducts($products) . '</span><span class="badge">' . $this->e($copy['market_label']) . ': ' . $this->e((string) ($marketContext['market_label'] ?? 'AUTO')) . '</span></div>'
            . '<div class="hero-actions"><a class="button button-primary" href="#catalog-grid">' . $this->e($copy['primary_cta']) . '</a><a class="button button-secondary" href="' . $this->e($this->homePathForLanguage($languageCode) . '#ai-advisor') . '">' . $this->e($copy['advisor_cta']) . '</a></div>'
            . $this->renderLanguageSwitcher($alternateLinks, $languageCode)
            . '</div>'
            . $this->renderHeroShowcase($featuredProducts, $copy, $canonicalPath, $languageCode)
            . '</div></section>'
            . '<section class="catalog-shop-section">'
            . '<div class="section-heading"><div class="eyebrow">' . $this->e($copy['catalog_eyebrow']) . '</div><div class="content-prose"><h2>' . $this->e($copy['catalog_title']) . '</h2></div><p>' . $this->e($copy['catalog_text']) . '</p></div>'
            . $this->renderToolbar($copy, $products)
            . $this->renderProductGrid($products, $copy, $canonicalPath, $languageCode)
            . '</section>'
            . $this->renderAdvisorBand($copy, $languageCode)
            . $this->renderSiteFooter($copy, $languageCode)
            . '</div>'
            . $this->renderDiscountLeadModal($copy)
            . $this->renderCatalogScript($copy);

        $schemaJson = json_encode($this->buildSchema($products, $copy, $canonicalUrl, $description, $languageCode), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->response->html(PageRenderer::render($title, $body, [
            'lang' => $languageCode,
            'meta_description' => $description,
            'canonical_url' => $canonicalUrl,
            'robots' => 'index,follow',
            'alternate_links' => $alternateLinks,
            'open_graph' => [
                'type' => 'website',
                'site_name' => $this->brandName(),
                'title' => $title,
                'description' => $description,
                'url' => $canonicalUrl,
                'image' => $this->absoluteUrl('/media/branding/avc-home-premium-og-wide.png'),
                'locale' => $this->localeForLanguage($languageCode),
            ],
            'schema_json' => $schemaJson,
            'body_class' => 'site-public site-catalog',
        ]));
    }

    public function redirectToHr(): never
    {
        $this->response->redirect($this->catalogPathForLanguage('hr'), 301);
    }

    public function redirectLegacyCommerce(): never
    {
        $this->response->redirect($this->catalogPathForLanguage($this->languageFromPath($this->request->path())), 301);
    }

    private function decorateProducts(array $products, string $languageCode): array
    {
        foreach ($products as &$product) {
            $product['catalog_category'] = $this->categoryForProduct($product);
            $product['catalog_summary'] = $this->summaryForProduct($product, $languageCode);
            $product['catalog_ready'] = count((array) ($product['market_urls'] ?? [])) > 0;
        }
        unset($product);

        return $products;
    }

    private function selectFeaturedProducts(array $products): array
    {
        $preferredNeedles = [
            'aloe vera gel',
            'c9',
            'forever marine collagen',
            'forever therm',
            'active pro-b',
            'aloe vera gelly',
        ];
        $featured = [];
        $used = [];

        foreach ($preferredNeedles as $needle) {
            foreach ($products as $index => $product) {
                if (isset($used[$index]) || !$this->isReadyProduct($product)) {
                    continue;
                }

                if (str_contains($this->searchText($product), $needle)) {
                    $featured[] = $product;
                    $used[$index] = true;
                    break;
                }
            }
        }

        foreach ($products as $index => $product) {
            if (count($featured) >= 3) {
                break;
            }

            if (isset($used[$index]) || !$this->isReadyProduct($product)) {
                continue;
            }

            $featured[] = $product;
        }

        return array_slice($featured, 0, 3);
    }

    private function buildMarketContext(array $products): array
    {
        $availableMarketCodes = [];
        foreach ($products as $product) {
            foreach (array_keys((array) ($product['market_urls'] ?? [])) as $marketCode) {
                $availableMarketCodes[] = (string) $marketCode;
            }
        }

        $countryCode = (new CountryDetector())->detect($this->request);
        $marketCode = (new MarketResolver())->resolvePreferredMarket(
            $countryCode,
            (string) $this->request->header('Accept-Language', ''),
            $availableMarketCodes
        );

        return [
            'country_label' => $countryCode !== null ? strtoupper($countryCode) : 'AUTO',
            'market_label' => strtoupper($marketCode),
        ];
    }

    private function renderSiteHeader(array $copy, string $languageCode): string
    {
        $homePath = $this->homePathForLanguage($languageCode);
        $catalogPath = $this->catalogPathForLanguage($languageCode);
        $articlePath = $this->articleCatalogPathForLanguage($languageCode);
        $contactPath = $this->contactPathForLanguage($languageCode);
        $brandName = $this->brandName();

        return '<header class="site-header"><div class="header-card">'
            . '<a class="brand" href="' . $this->e($homePath) . '"><span class="brand-lockup"><img class="brand-logo" src="/media/branding/aloe-vera-centar-logo-horizontal.png" alt="' . $this->e($brandName) . '" loading="eager" decoding="async"><span class="brand-copy"><strong class="brand-name">' . $this->e($brandName) . '</strong><span class="brand-tagline">' . $this->e($copy['brand_tagline']) . '</span></span></span></a>'
            . '<nav class="header-links"><a href="' . $this->e($homePath) . '">' . $this->e($copy['nav_home']) . '</a><a href="' . $this->e($catalogPath) . '">' . $this->e($copy['nav_products']) . '</a><a href="' . $this->e($articlePath) . '">' . $this->e($copy['nav_articles']) . '</a><a href="' . $this->e($homePath . '#ai-advisor') . '">' . $this->e($copy['nav_support']) . '</a><a href="' . $this->e($contactPath) . '">' . $this->e($copy['nav_contact']) . '</a></nav>'
            . '</div></header>';
    }

    private function renderHeroShowcase(array $featuredProducts, array $copy, string $sourcePath, string $languageCode): string
    {
        $html = '<div class="catalog-showcase"><div class="catalog-showcase-head"><div><div class="eyebrow">' . $this->e($copy['showcase_eyebrow']) . '</div><strong>' . $this->e($copy['showcase_title']) . '</strong><p>' . $this->e($copy['showcase_text']) . '</p></div></div>';

        foreach ($featuredProducts as $index => $product) {
            $title = $this->displayText((string) ($product['title'] ?? ''));
            $image = trim((string) ($product['featured_image_path'] ?? ''));
            $detailPath = (string) ($product['route_path'] ?? '#');
            $price = $this->formatPrice($product);
            $shopUrl = $this->buildProductCtaUrl((int) ($product['content_translation_id'] ?? 0), $languageCode, $sourcePath, 'catalog_featured_shop', (string) $copy['shop_button']);

            $html .= '<article class="catalog-featured-product catalog-featured-' . ($index + 1) . '">'
                . ($image !== '' ? '<a class="catalog-featured-media" href="' . $this->e($detailPath) . '"><img src="' . $this->e($image) . '" alt="' . $this->e($title) . '" loading="' . ($index === 0 ? 'eager' : 'lazy') . '" decoding="async"></a>' : '')
                . '<div class="catalog-featured-copy"><span class="badge">' . $this->e($copy['featured_badge']) . '</span><strong>' . $this->e($title) . '</strong><p>' . $this->e((string) ($product['catalog_summary'] ?? '')) . '</p>'
                . ($price !== '' ? '<span class="catalog-price">' . $this->e($price) . '</span>' : '')
                . '<div class="card-actions"><a class="button button-primary" href="' . $this->e($shopUrl) . '">' . $this->e($copy['shop_button']) . '</a><a class="button button-secondary" href="' . $this->e($detailPath) . '">' . $this->e($copy['details_button']) . '</a></div></div>'
                . '</article>';
        }

        return $html . '</div>';
    }

    private function renderToolbar(array $copy, array $products): string
    {
        $categories = $this->categoryLabels($copy);
        $counts = ['all' => count($products)];
        foreach ($products as $product) {
            $category = (string) ($product['catalog_category'] ?? 'other');
            $counts[$category] = (int) ($counts[$category] ?? 0) + 1;
        }

        $buttons = '<button class="catalog-chip is-active" type="button" data-category-filter="all">' . $this->e($copy['filter_all']) . ' <span>' . count($products) . '</span></button>';
        foreach ($categories as $key => $label) {
            $count = (int) ($counts[$key] ?? 0);
            if ($count <= 0) {
                continue;
            }

            $buttons .= '<button class="catalog-chip" type="button" data-category-filter="' . $this->e($key) . '">' . $this->e($label) . ' <span>' . $count . '</span></button>';
        }

        return '<div class="catalog-toolbar">'
            . '<label class="catalog-search"><span>' . $this->e($copy['search_label']) . '</span><input type="search" data-catalog-search placeholder="' . $this->e($copy['search_placeholder']) . '"></label>'
            . '<label class="catalog-sort"><span>' . $this->e($copy['sort_label']) . '</span><select data-catalog-sort><option value="featured">' . $this->e($copy['sort_featured']) . '</option><option value="name">' . $this->e($copy['sort_name']) . '</option><option value="price_asc">' . $this->e($copy['sort_price_asc']) . '</option><option value="price_desc">' . $this->e($copy['sort_price_desc']) . '</option></select></label>'
            . '<div class="catalog-chip-row" aria-label="' . $this->e($copy['category_filter_label']) . '">' . $buttons . '</div>'
            . '</div>';
    }

    private function renderProductGrid(array $products, array $copy, string $sourcePath, string $languageCode): string
    {
        if ($products === []) {
            return '<p class="muted">' . $this->e($copy['empty']) . '</p>';
        }

        $html = '<div class="catalog-result-line"><strong data-catalog-count>' . count($products) . '</strong><span>' . $this->e($copy['result_label']) . '</span></div><div class="catalog-grid-shop" id="catalog-grid">';

        foreach ($products as $index => $product) {
            $html .= $this->renderProductCard($product, $copy, $sourcePath, $languageCode, $index);
        }

        return $html . '</div><p class="catalog-empty-state" data-catalog-empty hidden>' . $this->e($copy['no_results']) . '</p>';
    }

    private function renderProductCard(array $product, array $copy, string $sourcePath, string $languageCode, int $index): string
    {
        $title = $this->displayText((string) ($product['title'] ?? ''));
        $detailPath = (string) ($product['route_path'] ?? '#');
        $image = trim((string) ($product['featured_image_path'] ?? ''));
        $category = (string) ($product['catalog_category'] ?? 'other');
        $categoryLabel = (string) ($this->categoryLabels($copy)[$category] ?? $copy['category_other']);
        $summary = (string) ($product['catalog_summary'] ?? '');
        $priceValue = $this->numericPrice($product);
        $price = $this->formatPrice($product);
        $isReady = $this->isReadyProduct($product);
        $shopUrl = $this->buildProductCtaUrl((int) ($product['content_translation_id'] ?? 0), $languageCode, $sourcePath, 'catalog_card_shop', (string) $copy['shop_button']);
        $searchText = $this->normalizeSearchText(implode(' ', [
            $title,
            (string) ($product['slug'] ?? ''),
            $categoryLabel,
            $summary,
        ]));

        $primaryAction = $isReady
            ? '<a class="button button-primary" href="' . $this->e($shopUrl) . '">' . $this->e($copy['shop_button_short']) . '</a>'
            : '<a class="button button-primary" href="' . $this->e($detailPath) . '">' . $this->e($copy['details_button']) . '</a>';

        return '<article class="catalog-product-card" data-catalog-card data-category="' . $this->e($category) . '" data-title="' . $this->e($this->normalizeSearchText($title)) . '" data-search="' . $this->e($searchText) . '" data-price="' . $this->e((string) $priceValue) . '" data-ready="' . ($isReady ? '1' : '0') . '" data-order="' . $index . '">'
            . '<a class="catalog-product-media" href="' . $this->e($detailPath) . '">' . ($image !== '' ? '<img src="' . $this->e($image) . '" alt="' . $this->e($title) . '" loading="lazy" decoding="async">' : '<span>' . $this->e($copy['image_fallback']) . '</span>') . '</a>'
            . '<div class="catalog-product-body"><div class="badge-row"><span class="badge">' . $this->e($categoryLabel) . '</span>' . ($isReady ? '<span class="badge badge-ready">' . $this->e($copy['ready_badge']) . '</span>' : '<span class="badge badge-muted">' . $this->e($copy['not_ready_badge']) . '</span>') . '</div>'
            . '<h3><a href="' . $this->e($detailPath) . '">' . $this->e($title) . '</a></h3>'
            . '<p>' . $this->e($summary) . '</p>'
            . '<div class="catalog-product-meta">' . ($price !== '' ? '<span class="catalog-price">' . $this->e($price) . '</span>' : '<span class="muted">' . $this->e($copy['price_on_shop']) . '</span>') . '<span>' . $this->e($copy['markets_count']) . ': ' . count((array) ($product['market_urls'] ?? [])) . '</span></div>'
            . '<div class="card-actions">' . $primaryAction . '<a class="button button-secondary" href="' . $this->e($detailPath) . '">' . $this->e($copy['details_button']) . '</a></div></div>'
            . '</article>';
    }

    private function renderAdvisorBand(array $copy, string $languageCode): string
    {
        $homePath = $this->homePathForLanguage($languageCode);

        return '<section class="catalog-advisor-band"><div><div class="eyebrow">' . $this->e($copy['advisor_eyebrow']) . '</div><div class="content-prose"><h2>' . $this->e($copy['advisor_title']) . '</h2><p>' . $this->e($copy['advisor_text']) . '</p></div></div><div class="card-actions"><a class="button button-primary" href="' . $this->e($homePath . '#ai-advisor') . '">' . $this->e($copy['advisor_cta']) . '</a><a class="button button-secondary" href="#catalog-grid">' . $this->e($copy['back_to_products']) . '</a></div></section>';
    }

    private function renderSiteFooter(array $copy, string $languageCode = 'hr'): string
    {
        $html = '<footer class="site-footer"><div class="content-card"><strong>' . $this->e($this->brandName()) . '</strong><p class="muted">' . $this->e($copy['footer_text']) . '</p><p class="muted">' . $this->e($copy['footer_about']) . '</p><div class="footer-links">';

        foreach ($this->authorityFooterLinks($languageCode) as $label => $path) {
            $html .= '<a href="' . $this->e($path) . '">' . $this->e($label) . '</a>';
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
                'Contact' => '/en/contact/',
            ],
            'sl' => [
                'O nas' => '/sl/o-nas/',
                'Kako delujejo priporočila' => '/sl/kako-delujejo-priporocila/',
                'Uredniška politika' => '/sl/uredniska-politika/',
                'Kontakt' => '/sl/kontakt/',
            ],
            default => [
                'O nama' => '/o-nama/',
                'Kako radimo preporuke' => '/kako-rade-preporuke/',
                'Urednička politika' => '/urednicka-politika/',
                'Kontakt' => '/kontakt/',
            ],
        };
    }

    private function renderDiscountLeadModal(array $copy): string
    {
        return '<div class="discount-modal js-discount-modal" hidden aria-hidden="true">'
            . '<div class="discount-modal-backdrop js-discount-close" aria-hidden="true"></div>'
            . '<section class="discount-modal-card" role="dialog" aria-modal="true" aria-labelledby="discount-modal-title">'
            . '<button class="discount-modal-close js-discount-close" type="button" aria-label="' . $this->e((string) ($copy['discount_modal_close'] ?? 'Zatvori')) . '">×</button>'
            . '<div class="discount-modal-head"><div class="eyebrow">' . $this->e((string) ($copy['discount_modal_eyebrow'] ?? 'Forever Card popust')) . '</div>'
            . '<h2 id="discount-modal-title">' . $this->e((string) ($copy['discount_modal_title'] ?? 'Želiš 15% popusta za ovaj proizvod?')) . '</h2>'
            . '<p class="muted js-discount-modal-text">' . $this->e((string) ($copy['discount_modal_text'] ?? 'Upiši email ili mobitel i spremit ćemo ti link s popustom. Nakon toga te odmah vodimo na službeni Forever Living Products shop.')) . '</p></div>'
            . '<form class="discount-form js-discount-form">'
            . '<label>' . $this->e((string) ($copy['discount_field_name'] ?? 'Ime')) . '<input type="text" name="name" autocomplete="name" placeholder="' . $this->e((string) ($copy['discount_field_name_placeholder'] ?? 'Kako se zoveš?')) . '"></label>'
            . '<div class="discount-contact-grid">'
            . '<label class="discount-field-email">' . $this->e((string) ($copy['discount_field_email'] ?? 'Email')) . '<input type="email" name="email" autocomplete="email" placeholder="' . $this->e((string) ($copy['discount_field_email_placeholder'] ?? 'Email za link s popustom')) . '"></label>'
            . '<label class="discount-field-phone">' . $this->e((string) ($copy['discount_field_phone'] ?? 'Mobitel')) . '<input type="tel" name="phone" autocomplete="tel" placeholder="' . $this->e((string) ($copy['discount_field_phone_placeholder'] ?? 'Mobitel ako ti je lakše')) . '"></label>'
            . '</div>'
            . '<div class="discount-status js-discount-status" role="status"></div>'
            . '<div class="discount-actions"><button class="button button-primary js-discount-submit" type="submit">' . $this->e((string) ($copy['discount_modal_submit'] ?? 'Aktiviraj 15% popusta')) . '</button>'
            . '<button class="button button-secondary js-discount-skip" type="button">' . $this->e((string) ($copy['discount_modal_skip'] ?? 'Nastavi bez popusta')) . '</button></div>'
            . '<p class="discount-note">' . $this->e((string) ($copy['discount_modal_note'] ?? 'Kupnju završavaš na službenom Forever shopu. AVC samo sprema kontakt i vodi te na pravi link.')) . '</p>'
            . '</form></section></div>';
    }

    private function renderLanguageSwitcher(array $alternateLinks, string $activeLanguageCode): string
    {
        $labels = ['hr' => 'HR', 'en' => 'EN', 'sl' => 'SL'];
        $html = '<div class="locale-switcher">';

        foreach ($alternateLinks as $alternateLink) {
            $languageCode = strtolower((string) ($alternateLink['hreflang'] ?? ''));
            if (!isset($labels[$languageCode])) {
                continue;
            }

            $path = (string) parse_url((string) ($alternateLink['href'] ?? ''), PHP_URL_PATH);
            $html .= '<a' . ($languageCode === $activeLanguageCode ? ' aria-current="page"' : '') . ' href="' . $this->e($path) . '">' . $this->e($labels[$languageCode]) . '</a>';
        }

        return $html . '</div>';
    }

    private function renderCatalogScript(array $copy): string
    {
        $emptyText = json_encode((string) $copy['result_label'], JSON_UNESCAPED_UNICODE);

        return '<script>
function avcJson(url, payload) {
  return fetch(url, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Content-Type": "application/json"
    },
    body: JSON.stringify(payload || {})
  }).then((response) => response.json().then((data) => ({ ok: response.ok, data: data || {} })));
}

(() => {
  const modal = document.querySelector(".js-discount-modal");
  if (!modal || !window.fetch) return;

  const form = modal.querySelector(".js-discount-form");
  const status = modal.querySelector(".js-discount-status");
  const submitButton = modal.querySelector(".js-discount-submit");
  const skipButton = modal.querySelector(".js-discount-skip");
  let pendingHref = "";
  let pendingPayload = null;
  const contactRequiredMessage = ' . json_encode((string) ($copy['discount_modal_contact_required'] ?? 'Upiši email ili mobitel kako bismo ti spremili link s popustom.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
  const loadingMessage = ' . json_encode((string) ($copy['discount_modal_loading'] ?? 'Spremamo popust i otvaramo službeni shop...'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
  const genericErrorMessage = ' . json_encode((string) ($copy['discount_modal_error'] ?? 'Popust trenutno nije spremljen. Pokušaj ponovno ili nastavi bez popusta.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
  const abTestKey = "discount_modal_contact";
  const abCookieName = "avc_ab_" + abTestKey;
  const abVariantCopy = {
    hr: {
      emailText: "Upiši email i spremit ćemo ti link s popustom. Nakon toga te odmah vodimo na službeni Forever Living Products shop u tvojoj zemlji.",
      phoneText: "Upiši broj mobitela i odmah ćemo ti otvoriti proizvod s popustom u službenom Forever Living Products shopu.",
      emailRequired: "Upiši email kako bismo ti spremili link s popustom.",
      phoneRequired: "Upiši broj mobitela kako bismo ti aktivirali popust."
    },
    en: {
      emailText: "Enter your email and we will save your discount link. Then we will take you straight to the official Forever Living Products shop in your country.",
      phoneText: "Enter your phone number and we will open the discounted product in the official Forever Living Products shop.",
      emailRequired: "Enter your email so we can save your discount link.",
      phoneRequired: "Enter your phone number so we can activate the discount."
    },
    sl: {
      emailText: "Vpiši email in shranili bomo tvojo povezavo s popustom. Nato te takoj vodimo v uradni Forever Living Products shop v tvoji državi.",
      phoneText: "Vpiši telefon in takoj bomo odprli izdelek s popustom v uradni Forever Living Products shop.",
      emailRequired: "Vpiši email, da shranimo povezavo s popustom.",
      phoneRequired: "Vpiši telefon, da aktiviramo popust."
    }
  };
  const abLanguage = String(document.documentElement.lang || "hr").slice(0, 2).toLowerCase();
  const abCopy = abVariantCopy[abLanguage] || abVariantCopy.hr;

  function setStatus(message, isError) {
    if (!status) return;
    status.textContent = message || "";
    status.classList.toggle("is-error", !!isError);
  }

  function readCookie(name) {
    const prefix = name + "=";
    return document.cookie.split("; ").reduce((value, part) => {
      if (value || !part.startsWith(prefix)) return value;
      try {
        return decodeURIComponent(part.substring(prefix.length));
      } catch (error) {
        return "";
      }
    }, "");
  }

  function writeCookie(name, value) {
    document.cookie = name + "=" + encodeURIComponent(value) + "; Max-Age=7776000; Path=/; SameSite=Lax";
  }

  function normalizeAbVariant(value) {
    value = String(value || "").trim().toLowerCase();
    if (value === "email" || value === "email_only") return "email_only";
    if (value === "phone" || value === "mobitel" || value === "telefon" || value === "phone_only") return "phone_only";
    return "";
  }

  function resolveAbVariant() {
    const params = new URLSearchParams(window.location.search || "");
    const forcedVariant = normalizeAbVariant(params.get("ab_discount_modal_contact"));
    if (forcedVariant) {
      writeCookie(abCookieName, forcedVariant);
      return forcedVariant;
    }

    const storedVariant = normalizeAbVariant(readCookie(abCookieName));
    if (storedVariant) return storedVariant;

    const assignedVariant = Math.random() < 0.5 ? "email_only" : "phone_only";
    writeCookie(abCookieName, assignedVariant);
    return assignedVariant;
  }

  function applyAbVariant() {
    const variant = resolveAbVariant();
    modal.dataset.abTest = abTestKey;
    modal.dataset.abVariant = variant;

    const modalText = modal.querySelector(".js-discount-modal-text");
    const emailInput = form ? form.querySelector("input[name=\"email\"]") : null;
    const phoneInput = form ? form.querySelector("input[name=\"phone\"]") : null;

    if (modalText) modalText.textContent = variant === "phone_only" ? abCopy.phoneText : abCopy.emailText;
    if (emailInput) {
      emailInput.required = variant === "email_only";
      emailInput.disabled = variant !== "email_only";
    }
    if (phoneInput) {
      phoneInput.required = variant === "phone_only";
      phoneInput.disabled = variant !== "phone_only";
    }

    return variant;
  }

  function abContext() {
    return {
      ab_test_key: abTestKey,
      ab_variant_key: applyAbVariant()
    };
  }

  function recordAbEvent(eventType, extra) {
    const variant = applyAbVariant();
    const payload = Object.assign({}, pendingPayload || {}, extra || {}, {
      test_key: abTestKey,
      variant_key: variant,
      event_type: eventType
    });

    fetch("/api/ab-test/event", {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json"
      },
      body: JSON.stringify(payload),
      keepalive: true
    }).catch(() => {});
  }

  function parseProductLink(href) {
    let url;
    try {
      url = new URL(href, window.location.origin);
    } catch (error) {
      return null;
    }

    if (url.pathname !== "/go/product") return null;
    const contentTranslationId = Number(url.searchParams.get("id") || 0);
    if (!contentTranslationId) return null;

    return {
      content_translation_id: contentTranslationId,
      language_code: url.searchParams.get("lang") || document.documentElement.lang || "hr",
      source_path: url.searchParams.get("source_path") || window.location.pathname || "/",
      source: url.searchParams.get("source") || "catalog_card_shop",
      cta_position: url.searchParams.get("cta_position") || url.searchParams.get("source") || "catalog_card_shop",
      cta_variant: url.searchParams.get("cta_variant") || "discount_15_modal",
      cta_label: url.searchParams.get("cta_label") || ""
    };
  }

  function openModal(href, payload) {
    pendingHref = href;
    pendingPayload = payload;
    const variant = applyAbVariant();
    const analyticsPayload = Object.assign({}, payload, {
      ab_test_key: abTestKey,
      ab_variant_key: variant
    });
    recordAbEvent("impression", { event_source: "product_catalog" });
    if (window.avcTrackEvent) {
      window.avcTrackEvent("discount_modal_open", Object.assign({}, analyticsPayload, { event_source: "product_catalog" }));
    }
    modal.hidden = false;
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("discount-modal-open");
    setStatus("", false);
    if (submitButton) submitButton.disabled = false;
    if (form) {
      form.reset();
      window.setTimeout(() => {
        const firstInput = variant === "phone_only"
          ? form.querySelector("input[name=\"phone\"]")
          : form.querySelector("input[name=\"email\"]");
        if (firstInput) firstInput.focus();
      }, 80);
    }
  }

  function closeModal() {
    modal.hidden = true;
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("discount-modal-open");
    pendingHref = "";
    pendingPayload = null;
    setStatus("", false);
  }

  function hasPhone(value) {
    return String(value || "").replace(/\\D+/g, "").length >= 6;
  }

  document.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;

    if (target.closest(".js-discount-close")) {
      event.preventDefault();
      closeModal();
      return;
    }

    const link = target.closest("a[href]");
    if (!link || link.getAttribute("target") || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

    const payload = parseProductLink(link.getAttribute("href") || "");
    if (!payload || link.getAttribute("data-discount-bypass") === "1") return;

    event.preventDefault();
    openModal(link.href || link.getAttribute("href") || "", payload);
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && !modal.hidden) closeModal();
  });

  if (skipButton) {
    skipButton.addEventListener("click", () => {
      if (pendingHref) {
        const redirect = pendingHref;
        const context = abContext();
        recordAbEvent("skip", { event_source: "product_catalog" });
        if (window.avcTrackEvent && pendingPayload) {
          window.avcTrackEvent("forever_outbound_click", Object.assign({}, pendingPayload, context, {
            event_source: "discount_skip",
            click_type: "continue_without_discount"
          }), () => { window.location.href = redirect; });
        } else {
          window.location.href = redirect;
        }
      } else {
        closeModal();
      }
    });
  }

  if (form) {
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      if (!pendingPayload) return;

      const formData = new FormData(form);
      const variant = applyAbVariant();
      const email = variant === "email_only" ? String(formData.get("email") || "").trim() : "";
      const phone = variant === "phone_only" ? String(formData.get("phone") || "").trim() : "";

      if (variant === "email_only" && !email) {
        setStatus(abCopy.emailRequired || contactRequiredMessage, true);
        return;
      }

      if (variant === "phone_only" && !hasPhone(phone)) {
        setStatus(abCopy.phoneRequired || contactRequiredMessage, true);
        return;
      }

      if (submitButton) submitButton.disabled = true;
      setStatus(loadingMessage, false);
      const context = abContext();

      avcJson("/api/discount-leads", Object.assign({}, pendingPayload, context, {
        name: formData.get("name") || "",
        email,
        phone,
        consent_contact: true
      })).then((result) => {
        if (!result.ok || result.data.status !== "ok") {
          throw new Error(result.data.message || genericErrorMessage);
        }

        setStatus(result.data.message || loadingMessage, false);
        const redirect = result.data.redirect_url || pendingHref;
        if (window.avcTrackEvent) {
          window.avcTrackEvent("discount_lead_submit", Object.assign({}, pendingPayload, context, {
            event_source: "product_catalog",
            discount_lead_id: result.data.discount_lead_id || "",
            customer_notified: !!result.data.customer_notified
          }));
          window.avcTrackEvent("forever_outbound_click", Object.assign({}, pendingPayload, context, {
            event_source: "discount_lead",
            click_type: "discount_submit"
          }), () => { window.location.href = redirect; });
        } else {
          window.location.href = redirect;
        }
      }).catch((error) => {
        setStatus(error.message || genericErrorMessage, true);
        if (submitButton) submitButton.disabled = false;
      });
    });
  }
})();

(() => {
  const root = document.querySelector("[data-catalog]");
  if (!root) return;
  const grid = document.querySelector("#catalog-grid");
  const cards = Array.from(document.querySelectorAll("[data-catalog-card]"));
  const search = document.querySelector("[data-catalog-search]");
  const sort = document.querySelector("[data-catalog-sort]");
  const count = document.querySelector("[data-catalog-count]");
  const empty = document.querySelector("[data-catalog-empty]");
  const chips = Array.from(document.querySelectorAll("[data-category-filter]"));
  let activeCategory = "all";
  const resultLabel = ' . ($emptyText ?: '""') . ';

  const normalize = (value) => value.toString().toLocaleLowerCase("hr-HR").normalize("NFD").replace(/[\\u0300-\\u036f]/g, "");

  function applyCatalogState() {
    const term = normalize(search ? search.value : "");
    let visibleCards = cards.filter((card) => {
      const categoryMatch = activeCategory === "all" || card.dataset.category === activeCategory;
      const termMatch = term === "" || normalize(card.dataset.search || "").includes(term);
      const isVisible = categoryMatch && termMatch;
      card.hidden = !isVisible;
      return isVisible;
    });

    const sortMode = sort ? sort.value : "featured";
    visibleCards.sort((left, right) => {
      if (sortMode === "name") return (left.dataset.title || "").localeCompare(right.dataset.title || "", "hr");
      if (sortMode === "price_asc") return Number(left.dataset.price || 999999) - Number(right.dataset.price || 999999);
      if (sortMode === "price_desc") return Number(right.dataset.price || 0) - Number(left.dataset.price || 0);
      return Number(right.dataset.ready || 0) - Number(left.dataset.ready || 0) || Number(left.dataset.order || 0) - Number(right.dataset.order || 0);
    });

    visibleCards.forEach((card) => grid.appendChild(card));
    if (count) count.textContent = String(visibleCards.length);
    if (empty) empty.hidden = visibleCards.length !== 0;
    if (resultLabel) root.dataset.resultLabel = resultLabel;
  }

  chips.forEach((chip) => {
    chip.addEventListener("click", () => {
      activeCategory = chip.dataset.categoryFilter || "all";
      chips.forEach((item) => item.classList.toggle("is-active", item === chip));
      applyCatalogState();
    });
  });

  if (search) search.addEventListener("input", applyCatalogState);
  if (sort) sort.addEventListener("change", applyCatalogState);
  applyCatalogState();
})();
</script>';
    }

    private function buildSchema(array $products, array $copy, string $canonicalUrl, string $description, string $languageCode): array
    {
        $itemListElements = [];

        foreach (array_slice($products, 0, 80) as $index => $product) {
            $title = $this->displayText((string) ($product['title'] ?? ''));
            $detailUrl = $this->absoluteUrl((string) ($product['route_path'] ?? '/'));
            $price = $this->numericPrice($product);
            $currency = trim((string) ($product['currency_code'] ?? 'EUR')) ?: 'EUR';

            $productSchema = array_filter([
                '@type' => 'Product',
                'name' => $title,
                'url' => $detailUrl,
                'image' => trim((string) ($product['featured_image_path'] ?? '')) !== '' ? $this->absoluteUrl((string) $product['featured_image_path']) : null,
                'description' => (string) ($product['catalog_summary'] ?? ''),
                'brand' => [
                    '@type' => 'Brand',
                    'name' => 'Forever Living',
                ],
                'sku' => trim((string) ($product['sku'] ?? '')) !== '' ? trim((string) $product['sku']) : null,
                'offers' => $price > 0 ? [
                    '@type' => 'Offer',
                    'url' => $detailUrl,
                    'priceCurrency' => $currency,
                    'price' => number_format($price, 2, '.', ''),
                    'availability' => $this->isReadyProduct($product) ? 'https://schema.org/InStock' : 'https://schema.org/LimitedAvailability',
                    'seller' => [
                        '@type' => 'Organization',
                        'name' => 'Forever Living Products',
                    ],
                ] : null,
            ], static fn (mixed $value): bool => $value !== null && $value !== []);

            $itemListElements[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => $detailUrl,
                'item' => $productSchema,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => (string) $copy['meta_title'],
                    'headline' => (string) $copy['headline'],
                    'description' => $description,
                    'url' => $canonicalUrl,
                    'inLanguage' => $languageCode,
                    'isPartOf' => [
                        '@type' => 'WebSite',
                        'name' => $this->brandName(),
                        'url' => $this->absoluteUrl('/'),
                    ],
                ],
                [
                    '@type' => 'ItemList',
                    'name' => (string) $copy['catalog_title'],
                    'url' => $canonicalUrl . '#catalog-grid',
                    'numberOfItems' => count($products),
                    'itemListElement' => $itemListElements,
                ],
            ],
        ];
    }

    private function buildProductCtaUrl(int $contentTranslationId, string $languageCode, string $sourcePath, string $source, string $ctaLabel): string
    {
        if ($contentTranslationId <= 0) {
            return $sourcePath;
        }

        $normalizedSource = $this->normalizeTrackingKey($source, 'catalog_card_shop', 50);
        $params = [
            'id' => $contentTranslationId,
            'lang' => strtolower(trim($languageCode)) ?: 'hr',
            'source_path' => $sourcePath,
            'source' => $normalizedSource,
            'cta_position' => $normalizedSource,
            'cta_variant' => $this->normalizeTrackingKey($ctaLabel, $normalizedSource, 80),
            'cta_label' => mb_substr($this->normalizeWhitespace($this->displayText($ctaLabel)), 0, 180),
        ];

        return '/go/product?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    private function categoryForProduct(array $product): string
    {
        $text = $this->searchText($product);

        if (preg_match('/(c9|clean 9|garcinia|lean|therm|lite|weight|fast break|program|pack|paket)/', $text) === 1) {
            return 'weight';
        }

        if (preg_match('/(jojoba|shampoo|conditioner|liquid soap|toothgel|bright|deodorant|avocado|sonya)/', $text) === 1) {
            return 'personal';
        }

        if (preg_match('/(activator|first|propolis creme|msm gel|heat lotion|gelly|lotion|scrub|mask|serum|collagen|bakuchiol|skin|creme)/', $text) === 1) {
            return 'skin';
        }

        if (preg_match('/(aloe vera gel|berry nectar|freedom|peaches|bits|drink|napit|nectar)/', $text) === 1) {
            return 'drinks';
        }

        if (preg_match('/(bee|pollen|royal jelly|honey)/', $text) === 1) {
            return 'bee';
        }

        if (preg_match('/(absorbent|arctic|fields|garlic|lycium|nature|min|maca|vitolize|vitalize|q10|immublend|active pro|argi|vitamin|supplement)/', $text) === 1) {
            return 'supplements';
        }

        return 'other';
    }

    private function summaryForProduct(array $product, string $languageCode): string
    {
        $title = $this->displayText((string) ($product['title'] ?? ''));
        $identityText = $this->normalizeSearchText(implode(' ', [
            $title,
            (string) ($product['slug'] ?? ''),
            (string) ($product['route_path'] ?? ''),
        ]));

        $known = [
            'aloe vera gelly' => [
                'hr' => 'Aloe gel za vanjsku njegu kada želiš umiriti, osvježiti ili jednostavno imati aloe bazu pri ruci.',
                'en' => 'An external aloe gel when you want to soothe, refresh or keep a simple aloe-care base nearby.',
                'sl' => 'Aloe gel za zunanjo nego, ko želiš pomiriti, osvežiti ali imeti preprosto aloe osnovo pri roki.',
            ],
            'aloe vera gel' => [
                'hr' => 'Aloe napitak za svakodnevnu rutinu kada želiš jednostavnu bazu za probavu, hidrataciju i opći osjećaj ravnoteže.',
                'en' => 'A daily aloe drink when you want a simple base for digestion, hydration and an easier wellness rhythm.',
                'sl' => 'Aloe napitek za vsakodnevno rutino, ko želiš preprosto osnovo za prebavo, hidracijo in občutek ravnovesja.',
            ],
            'c9' => [
                'hr' => 'Strukturirani program za početak kada želiš jasan plan prvih dana i lakše se držati rutine.',
                'en' => 'A structured starter program when you want a clear first-days plan and an easier routine to follow.',
                'sl' => 'Strukturiran začetni program, ko želiš jasen načrt prvih dni in lažje slediti rutini.',
            ],
            'forever therm' => [
                'hr' => 'Podrška uz aktivniji dan i rutinu regulacije težine, posebno kada želiš više fokusa i dosljednosti.',
                'en' => 'Support for an active day and weight-management routine when focus and consistency matter.',
                'sl' => 'Podpora aktivnejšemu dnevu in rutini uravnavanja teže, ko sta pomembna fokus in doslednost.',
            ],
            'forever marine collagen' => [
                'hr' => 'Kolagenska podrška za kožu i beauty rutinu kada želiš ciljano raditi na elastičnosti i njegovanom izgledu.',
                'en' => 'Collagen support for skin and beauty routines when elasticity and a cared-for look are the goal.',
                'sl' => 'Kolagenska podpora za kožo in beauty rutino, ko želiš podpreti elastičnost in negovan videz.',
            ],
            'active pro-b' => [
                'hr' => 'Probiotička podrška za dane kada želiš mirnije voditi brigu o probavi i svakodnevnoj prehrambenoj rutini.',
                'en' => 'Probiotic support for days when you want a calmer digestion and nutrition routine.',
                'sl' => 'Probiotična podpora za dneve, ko želiš mirneje skrbeti za prebavo in prehransko rutino.',
            ],
            'aloe activator' => [
                'hr' => 'Lagani aloe proizvod za vanjsku njegu kada želiš jednostavno osvježenje kože ili dodatak kućnoj beauty rutini.',
                'en' => 'A light aloe product for external care when you want simple skin refreshment or a home beauty-routine step.',
                'sl' => 'Lahek aloe izdelek za zunanjo nego, ko želiš preprosto osvežitev kože ali korak v domači beauty rutini.',
            ],
        ];

        foreach ($known as $needle => $translations) {
            if (str_contains($identityText, $needle)) {
                return $translations[$languageCode] ?? $translations['hr'];
            }
        }

        $fallback = trim((string) ($product['meta_description'] ?? ''));
        if ($fallback === '') {
            $fallback = trim(strip_tags((string) ($product['excerpt'] ?? '')));
        }

        $fallback = $this->stripCatalogPromoText($this->humanizeText($fallback));

        if ($fallback === '' || mb_strlen($fallback) < 48) {
            $fallback = $this->categoryFallbackSummary($product, $title, $languageCode);
        }

        return mb_strimwidth($fallback, 0, 132, '...');
    }

    private function stripCatalogPromoText(string $value): string
    {
        $sentences = preg_split('/(?<=[.!?])\s+/u', $value) ?: [$value];
        $clean = [];

        foreach ($sentences as $sentence) {
            $sentence = $this->normalizeWhitespace($sentence);
            if ($sentence === '') {
                continue;
            }

            if (preg_match('/(?:15\s*%|popust|discount|naruči|naruci|online|preporuk|službeni web shop|sluzbeni web shop|thealoeveraco|forevercard|uštedi|ustedi|iskoristi)/iu', $sentence) === 1) {
                continue;
            }

            $clean[] = $sentence;
        }

        return $this->normalizeWhitespace(implode(' ', $clean));
    }

    private function categoryFallbackSummary(array $product, string $title, string $languageCode): string
    {
        $category = (string) ($product['catalog_category'] ?? $this->categoryForProduct($product));

        return match ($category) {
            'drinks' => match ($languageCode) {
                'en' => $title . ' is a Forever drink to compare when you want a simple daily aloe routine.',
                'sl' => $title . ' je Forever napitek za primerjavo, ko želiš preprosto dnevno aloe rutino.',
                default => $title . ' je Forever napitak koji vrijedi usporediti kada želiš jednostavnu dnevnu aloe rutinu.',
            },
            'skin' => match ($languageCode) {
                'en' => $title . ' fits a care routine when the goal is skin comfort, freshness or a more polished daily ritual.',
                'sl' => $title . ' sodi v rutino nege, ko je cilj udobje kože, svežina ali bolj urejen dnevni ritual.',
                default => $title . ' se uklapa u rutinu njege kada je cilj ugodnija koža, svježina ili uredniji dnevni ritual.',
            },
            'personal' => match ($languageCode) {
                'en' => $title . ' is a practical personal-care option for a cleaner, simpler everyday routine.',
                'sl' => $title . ' je praktična izbira osebne nege za čistejšo in preprostejšo vsakodnevno rutino.',
                default => $title . ' je praktična opcija osobne njege za čišću i jednostavniju svakodnevnu rutinu.',
            },
            'weight' => match ($languageCode) {
                'en' => $title . ' is worth comparing when you are building a more structured nutrition or weight-management routine.',
                'sl' => $title . ' je smiseln za primerjavo, ko gradiš bolj strukturirano prehransko rutino ali rutino teže.',
                default => $title . ' vrijedi usporediti kada gradiš strukturiraniju prehrambenu rutinu ili rutinu regulacije težine.',
            },
            'bee' => match ($languageCode) {
                'en' => $title . ' belongs to the bee-product line and is useful to compare for natural daily support.',
                'sl' => $title . ' spada v linijo čebeljih izdelkov in je uporaben za primerjavo pri naravni dnevni podpori.',
                default => $title . ' pripada liniji pčelinjih proizvoda i koristan je za usporedbu uz prirodnu dnevnu podršku.',
            },
            default => match ($languageCode) {
                'en' => $title . ' guide with details, routine context and the next step toward the official Forever shop.',
                'sl' => 'Vodnik za ' . $title . ' z razlago, kontekstom rutine in naslednjim korakom do uradnega Forever shopa.',
                default => 'Vodič za ' . $title . ' s objašnjenjem, kontekstom rutine i sljedećim korakom prema službenom Forever shopu.',
            },
        };
    }

    private function countReadyProducts(array $products): int
    {
        return count(array_filter($products, fn (array $product): bool => $this->isReadyProduct($product)));
    }

    private function isReadyProduct(array $product): bool
    {
        return !empty($product['catalog_ready']) || count((array) ($product['market_urls'] ?? [])) > 0;
    }

    private function numericPrice(array $product): float
    {
        $price = (float) ($product['sale_price'] ?: $product['price'] ?: 0);

        return $price > 0 ? $price : 0.0;
    }

    private function formatPrice(array $product): string
    {
        $price = $this->numericPrice($product);
        if ($price <= 0) {
            return '';
        }

        $currency = trim((string) ($product['currency_code'] ?? 'EUR')) ?: 'EUR';

        return number_format($price, 2, ',', '.') . ' ' . $currency;
    }

    private function searchText(array $product): string
    {
        return $this->normalizeSearchText(implode(' ', [
            (string) ($product['title'] ?? ''),
            (string) ($product['slug'] ?? ''),
            (string) ($product['route_path'] ?? ''),
            (string) ($product['meta_description'] ?? ''),
            (string) ($product['excerpt'] ?? ''),
        ]));
    }

    private function normalizeSearchText(string $value): string
    {
        $value = mb_strtolower($this->displayText($value));
        $value = strtr($value, [
            'č' => 'c',
            'ć' => 'c',
            'đ' => 'd',
            'š' => 's',
            'ž' => 'z',
        ]);

        return $this->normalizeWhitespace((string) preg_replace('/[^a-z0-9]+/u', ' ', $value));
    }

    private function normalizeTrackingKey(string $value, string $fallback, int $maxLength): string
    {
        $value = $this->normalizeSearchText($value);
        $value = str_replace(' ', '_', $value);

        return $value !== '' ? mb_substr($value, 0, $maxLength) : mb_substr($fallback, 0, $maxLength);
    }

    private function humanizeText(string $value): string
    {
        $value = $this->displayText(strip_tags($value));
        $value = preg_replace('/\s*\|\s*Aloe Vera Centar\s*/iu', '', $value) ?? $value;

        return $this->normalizeWhitespace($value);
    }

    private function displayText(string $value): string
    {
        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function normalizeWhitespace(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    private function buildAlternateLinks(): array
    {
        $links = [];
        foreach (['hr', 'en', 'sl'] as $languageCode) {
            $links[] = [
                'hreflang' => $languageCode,
                'href' => $this->absoluteUrl($this->catalogPathForLanguage($languageCode)),
            ];
        }

        $links[] = [
            'hreflang' => 'x-default',
            'href' => $this->absoluteUrl($this->catalogPathForLanguage('hr')),
        ];

        return $links;
    }

    private function languageFromPath(string $path): string
    {
        if (str_starts_with($path, '/en/')) {
            return 'en';
        }

        if (str_starts_with($path, '/sl/')) {
            return 'sl';
        }

        return 'hr';
    }

    private function catalogPathForLanguage(string $languageCode): string
    {
        return match (strtolower($languageCode)) {
            'en' => '/en/forever-products/',
            'sl' => '/sl/forever-izdelki/',
            default => '/forever-proizvodi/',
        };
    }

    private function articleCatalogPathForLanguage(string $languageCode): string
    {
        return match (strtolower($languageCode)) {
            'en' => '/en/articles/',
            'sl' => '/sl/clanki/',
            default => '/clanci/',
        };
    }

    private function homePathForLanguage(string $languageCode): string
    {
        return match (strtolower($languageCode)) {
            'en' => '/en/',
            'sl' => '/sl/',
            default => '/',
        };
    }

    private function contactPathForLanguage(string $languageCode): string
    {
        return match (strtolower($languageCode)) {
            'en' => '/en/contact/',
            'sl' => '/sl/kontakt/',
            default => '/kontakt/',
        };
    }

    private function absoluteUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim((string) ($this->config['base_url'] ?? ''), '/') . '/' . ltrim($path, '/');
    }

    private function localeForLanguage(string $languageCode): string
    {
        return match (strtolower($languageCode)) {
            'en' => 'en_GB',
            'sl' => 'sl_SI',
            default => 'hr_HR',
        };
    }

    private function categoryLabels(array $copy): array
    {
        return [
            'drinks' => (string) $copy['category_drinks'],
            'supplements' => (string) $copy['category_supplements'],
            'skin' => (string) $copy['category_skin'],
            'personal' => (string) $copy['category_personal'],
            'weight' => (string) $copy['category_weight'],
            'bee' => (string) $copy['category_bee'],
            'other' => (string) $copy['category_other'],
        ];
    }

    private function brandName(): string
    {
        return 'Aloe Vera Centar';
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function copy(string $languageCode): array
    {
        $copy = [
            'hr' => [
                'brand_tagline' => 'Mirniji odabir Forever proizvoda',
                'nav_home' => 'Početna',
                'nav_products' => 'Proizvodi',
                'nav_articles' => 'Članci',
                'nav_support' => 'Preporuka',
                'nav_contact' => 'Kontakt',
                'meta_title' => 'Forever proizvodi | Aloe Vera Centar web shop vodič',
                'meta_description' => 'Pregledaj Forever proizvode na Aloe Vera Centru kao web shop katalog: cijene, vodiči, kategorije i siguran nastavak prema službenom Forever shopu.',
                'kicker' => 'Forever web shop vodič',
                'headline' => 'Svi Forever proizvodi na jednom mjestu',
                'intro' => 'Pregledaj proizvode kao katalog, filtriraj po cilju i nastavi na službeni Forever shop tek kada znaš što želiš naručiti.',
                'stat_products' => 'Proizvodi',
                'stat_ready' => 'Spremno za shop',
                'market_label' => 'Tvoj market',
                'primary_cta' => 'Pregledaj katalog',
                'advisor_cta' => 'Pitaj za preporuku',
                'showcase_eyebrow' => 'Brzi izbor',
                'showcase_title' => 'Najbolje krenuti od rutine',
                'showcase_text' => 'Ovo su proizvodi koje ljudi najčešće gledaju kada žele jednostavan prvi korak.',
                'featured_badge' => 'Istaknuto',
                'catalog_eyebrow' => 'Katalog',
                'catalog_title' => 'Forever proizvodi kao pregledan shop',
                'catalog_text' => 'Koristi pretragu, kategorije i sortiranje. Svaka kartica vodi na detaljan vodič ili direktno prema službenom shopu.',
                'filter_all' => 'Svi',
                'search_label' => 'Pretraga',
                'search_placeholder' => 'Upiši naziv, cilj ili sastojak...',
                'sort_label' => 'Sortiranje',
                'sort_featured' => 'Preporučeno prvo',
                'sort_name' => 'Naziv A-Z',
                'sort_price_asc' => 'Cijena niža prvo',
                'sort_price_desc' => 'Cijena viša prvo',
                'category_filter_label' => 'Filter kategorija',
                'category_drinks' => 'Aloe napici',
                'category_supplements' => 'Dodaci prehrani',
                'category_skin' => 'Koža i njega',
                'category_personal' => 'Osobna njega',
                'category_weight' => 'Regulacija težine',
                'category_bee' => 'Pčelinji proizvodi',
                'category_other' => 'Ostalo',
                'ready_badge' => 'Shop link',
                'not_ready_badge' => 'Detalji',
                'shop_button' => 'Aktiviraj 15% popusta',
                'shop_button_short' => 'Popust 15%',
                'details_button' => 'Detalji',
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
                'price_on_shop' => 'Cijena na shopu',
                'markets_count' => 'Tržišta',
                'image_fallback' => 'Forever proizvod',
                'result_label' => 'proizvoda prikazano',
                'no_results' => 'Nema proizvoda za ovaj filter. Probaj kraći pojam ili drugu kategoriju.',
                'empty' => 'Katalog proizvoda trenutno nema stavki.',
                'advisor_eyebrow' => 'Nisi siguran?',
                'advisor_title' => 'Kreni od cilja, ne od naziva proizvoda',
                'advisor_text' => 'Ako se dvoumiš između nekoliko proizvoda, napiši što želiš postići i dobit ćeš konkretniji smjer prije kupnje.',
                'back_to_products' => 'Natrag na katalog',
                'footer_text' => 'Katalog pomaže pregledati Forever proizvode, razumjeti razliku između opcija i nastaviti prema službenom shopu bez traženja lokalnih linkova.',
                'footer_about' => 'Aloe Vera Centar je web stranica namijenjena predstavljanju, preporuci i informiranju o Forever proizvodima. Stranica je u vlasništvu i pod upravljanjem tvrtke BS International.',
            ],
            'en' => [
                'brand_tagline' => 'Calmer Forever product choices',
                'nav_home' => 'Home',
                'nav_products' => 'Products',
                'nav_articles' => 'Articles',
                'nav_support' => 'Guidance',
                'nav_contact' => 'Contact',
                'meta_title' => 'Forever products | Aloe Vera Centar shop guide',
                'meta_description' => 'Browse Forever products on Aloe Vera Centar like a shop catalogue: prices, guides, categories and a safe next step to the official Forever shop.',
                'kicker' => 'Forever shop guide',
                'headline' => 'All Forever products in one place',
                'intro' => 'Browse products like a catalogue, filter by goal and continue to the official Forever shop only when the choice feels clear.',
                'stat_products' => 'Products',
                'stat_ready' => 'Shop-ready',
                'market_label' => 'Your market',
                'primary_cta' => 'Browse catalogue',
                'advisor_cta' => 'Ask for guidance',
                'showcase_eyebrow' => 'Quick choice',
                'showcase_title' => 'Start with the routine',
                'showcase_text' => 'These are the products people often compare when they want a simple first step.',
                'featured_badge' => 'Featured',
                'catalog_eyebrow' => 'Catalogue',
                'catalog_title' => 'Forever products in a clean shop view',
                'catalog_text' => 'Use search, categories and sorting. Each card opens a detailed guide or continues toward the official shop.',
                'filter_all' => 'All',
                'search_label' => 'Search',
                'search_placeholder' => 'Search by product, goal or ingredient...',
                'sort_label' => 'Sort',
                'sort_featured' => 'Recommended first',
                'sort_name' => 'Name A-Z',
                'sort_price_asc' => 'Lowest price first',
                'sort_price_desc' => 'Highest price first',
                'category_filter_label' => 'Category filter',
                'category_drinks' => 'Aloe drinks',
                'category_supplements' => 'Supplements',
                'category_skin' => 'Skin and care',
                'category_personal' => 'Personal care',
                'category_weight' => 'Weight management',
                'category_bee' => 'Bee products',
                'category_other' => 'Other',
                'ready_badge' => 'Shop link',
                'not_ready_badge' => 'Details',
                'shop_button' => 'Activate 15% off',
                'shop_button_short' => '15% off',
                'details_button' => 'Details',
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
                'price_on_shop' => 'Price on shop',
                'markets_count' => 'Markets',
                'image_fallback' => 'Forever product',
                'result_label' => 'products shown',
                'no_results' => 'No products match this filter. Try a shorter search or another category.',
                'empty' => 'The product catalogue is currently empty.',
                'advisor_eyebrow' => 'Not sure?',
                'advisor_title' => 'Start with the goal, not the product name',
                'advisor_text' => 'If you are comparing several products, describe what you want to achieve and get a clearer direction before buying.',
                'back_to_products' => 'Back to catalogue',
                'footer_text' => 'The catalogue helps you explore Forever products, understand the difference between options and continue to the official shop without hunting for local links.',
                'footer_about' => 'Aloe Vera Centar presents, recommends and explains Forever products. The website is owned and operated by BS International.',
            ],
            'sl' => [
                'brand_tagline' => 'Mirnejša izbira Forever izdelkov',
                'nav_home' => 'Domov',
                'nav_products' => 'Izdelki',
                'nav_articles' => 'Članki',
                'nav_support' => 'Priporočilo',
                'nav_contact' => 'Kontakt',
                'meta_title' => 'Forever izdelki | Aloe Vera Centar shop vodnik',
                'meta_description' => 'Preglej Forever izdelke na Aloe Vera Centru kot shop katalog: cene, vodniki, kategorije in varen korak do uradnega Forever shopa.',
                'kicker' => 'Forever shop vodnik',
                'headline' => 'Vsi Forever izdelki na enem mestu',
                'intro' => 'Preglej izdelke kot katalog, filtriraj po cilju in nadaljuj v uradni Forever shop šele, ko je izbira jasna.',
                'stat_products' => 'Izdelki',
                'stat_ready' => 'Pripravljeno za shop',
                'market_label' => 'Tvoj trg',
                'primary_cta' => 'Preglej katalog',
                'advisor_cta' => 'Vprašaj za priporočilo',
                'showcase_eyebrow' => 'Hitra izbira',
                'showcase_title' => 'Začni pri rutini',
                'showcase_text' => 'To so izdelki, ki jih ljudje pogosto primerjajo, ko želijo preprost prvi korak.',
                'featured_badge' => 'Izpostavljeno',
                'catalog_eyebrow' => 'Katalog',
                'catalog_title' => 'Forever izdelki v preglednem shop prikazu',
                'catalog_text' => 'Uporabi iskanje, kategorije in razvrščanje. Vsaka kartica odpre vodnik ali vodi naprej proti uradnemu shopu.',
                'filter_all' => 'Vsi',
                'search_label' => 'Iskanje',
                'search_placeholder' => 'Vpiši izdelek, cilj ali sestavino...',
                'sort_label' => 'Razvrsti',
                'sort_featured' => 'Priporočeno najprej',
                'sort_name' => 'Naziv A-Z',
                'sort_price_asc' => 'Nižja cena najprej',
                'sort_price_desc' => 'Višja cena najprej',
                'category_filter_label' => 'Filter kategorij',
                'category_drinks' => 'Aloe napitki',
                'category_supplements' => 'Prehranska dopolnila',
                'category_skin' => 'Koža in nega',
                'category_personal' => 'Osebna nega',
                'category_weight' => 'Uravnavanje teže',
                'category_bee' => 'Čebelji izdelki',
                'category_other' => 'Ostalo',
                'ready_badge' => 'Shop povezava',
                'not_ready_badge' => 'Podrobnosti',
                'shop_button' => 'Aktiviraj 15% popusta',
                'shop_button_short' => 'Popust 15%',
                'details_button' => 'Podrobnosti',
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
                'price_on_shop' => 'Cena v shopu',
                'markets_count' => 'Trgi',
                'image_fallback' => 'Forever izdelek',
                'result_label' => 'izdelkov prikazano',
                'no_results' => 'Za ta filter ni izdelkov. Poskusi krajši pojem ali drugo kategorijo.',
                'empty' => 'Katalog izdelkov trenutno nima postavk.',
                'advisor_eyebrow' => 'Nisi prepričan?',
                'advisor_title' => 'Začni s ciljem, ne z imenom izdelka',
                'advisor_text' => 'Če izbiraš med več izdelki, opiši, kaj želiš doseči, in prejmi jasnejšo smer pred nakupom.',
                'back_to_products' => 'Nazaj na katalog',
                'footer_text' => 'Katalog pomaga pregledati Forever izdelke, razumeti razlike med možnostmi in nadaljevati v uradni shop brez iskanja lokalnih povezav.',
                'footer_about' => 'Aloe Vera Centar predstavlja, priporoča in pojasnjuje Forever izdelke. Stran je v lasti in upravljanju podjetja BS International.',
            ],
        ];

        return $copy[strtolower($languageCode)] ?? $copy['hr'];
    }
}
