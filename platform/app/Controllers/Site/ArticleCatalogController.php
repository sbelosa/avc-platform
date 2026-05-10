<?php

declare(strict_types=1);

namespace Avc\Controllers\Site;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\ContentRepository;
use Avc\Support\PageRenderer;

final class ArticleCatalogController
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
        $canonicalPath = $this->articleCatalogPathForLanguage($languageCode);
        $normalizedCanonicalPath = rtrim($canonicalPath, '/');

        if ($this->request->path() !== $normalizedCanonicalPath) {
            $this->response->redirect($canonicalPath, 301);
        }

        $copy = $this->copy($languageCode);
        $articles = (new ContentRepository($this->config))->findArticleCatalogCards($languageCode, 160);
        $articles = $this->decorateArticles($articles, $languageCode);
        $featuredArticles = $this->selectFeaturedArticles($articles);
        $alternateLinks = $this->buildAlternateLinks();
        $canonicalUrl = $this->absoluteUrl($canonicalPath);
        $title = (string) $copy['meta_title'];
        $description = (string) $copy['meta_description'];

        $body = '<div class="shell catalog-page article-catalog-page" data-catalog>'
            . $this->renderSiteHeader($copy, $languageCode)
            . '<section class="hero catalog-hero article-catalog-hero"><div class="hero-panel catalog-hero-panel article-catalog-hero-panel">'
            . '<div class="catalog-hero-copy"><span class="hero-kicker">' . $this->e($copy['kicker']) . '</span>'
            . '<div class="content-prose"><h1>' . $this->e($copy['headline']) . '</h1></div>'
            . '<p class="muted hero-intro">' . $this->e($copy['intro']) . '</p>'
            . '<div class="hero-note"><span class="badge">' . $this->e($copy['stat_articles']) . ': ' . count($articles) . '</span><span class="badge">' . $this->e($copy['stat_topics']) . ': ' . $this->countActiveCategories($articles) . '</span></div>'
            . '<div class="hero-actions"><a class="button button-primary" href="#article-grid">' . $this->e($copy['primary_cta']) . '</a><a class="button button-secondary" href="' . $this->e($this->homePathForLanguage($languageCode) . '#ai-advisor') . '">' . $this->e($copy['advisor_cta']) . '</a></div>'
            . $this->renderLanguageSwitcher($alternateLinks, $languageCode)
            . $this->renderHeroTopics($copy)
            . '</div>'
            . $this->renderHeroShowcase($featuredArticles, $copy)
            . '</div></section>'
            . '<section class="catalog-shop-section article-catalog-section">'
            . '<div class="section-heading"><div class="eyebrow">' . $this->e($copy['catalog_eyebrow']) . '</div><div class="content-prose"><h2>' . $this->e($copy['catalog_title']) . '</h2></div><p>' . $this->e($copy['catalog_text']) . '</p></div>'
            . $this->renderToolbar($copy, $articles)
            . $this->renderArticleGrid($articles, $copy)
            . '</section>'
            . $this->renderAdvisorBand($copy, $languageCode)
            . $this->renderSiteFooter($copy)
            . '</div>'
            . $this->renderCatalogScript($copy);

        $schemaJson = json_encode($this->buildSchema($articles, $copy, $canonicalUrl, $description, $languageCode), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

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
            'body_class' => 'site-public site-catalog site-articles',
        ]));
    }

    public function redirectToHr(): never
    {
        $this->response->redirect($this->articleCatalogPathForLanguage('hr'), 301);
    }

    private function decorateArticles(array $articles, string $languageCode): array
    {
        foreach ($articles as &$article) {
            $article['catalog_category'] = $this->categoryForArticle($article);
            $article['catalog_summary'] = $this->summaryForArticle($article, $languageCode);
            $article['catalog_date_label'] = $this->formatDisplayDate((string) ($article['published_at'] ?? ''), $languageCode);
            $article['catalog_timestamp'] = $this->timestampForArticle($article);
        }
        unset($article);

        return $articles;
    }

    private function selectFeaturedArticles(array $articles): array
    {
        $preferredNeedles = [
            'probava',
            'koza',
            'aloe',
            'imunitet',
            'energija',
            'rutina',
        ];
        $featured = [];
        $used = [];

        foreach ($preferredNeedles as $needle) {
            foreach ($articles as $index => $article) {
                if (isset($used[$index])) {
                    continue;
                }

                if (str_contains($this->searchText($article), $needle)) {
                    $featured[] = $article;
                    $used[$index] = true;
                    break;
                }
            }
        }

        foreach ($articles as $index => $article) {
            if (count($featured) >= 3) {
                break;
            }

            if (isset($used[$index])) {
                continue;
            }

            $featured[] = $article;
        }

        return array_slice($featured, 0, 3);
    }

    private function renderSiteHeader(array $copy, string $languageCode): string
    {
        $homePath = $this->homePathForLanguage($languageCode);
        $catalogPath = $this->catalogPathForLanguage($languageCode);
        $articlePath = $this->articleCatalogPathForLanguage($languageCode);
        $brandName = $this->brandName();

        return '<header class="site-header"><div class="header-card">'
            . '<a class="brand" href="' . $this->e($homePath) . '"><span class="brand-lockup"><img class="brand-logo" src="/media/branding/aloe-vera-centar-logo-horizontal.png" alt="' . $this->e($brandName) . '" loading="eager" decoding="async"><span class="brand-copy"><strong class="brand-name">' . $this->e($brandName) . '</strong><span class="brand-tagline">' . $this->e($copy['brand_tagline']) . '</span></span></span></a>'
            . '<nav class="header-links"><a href="' . $this->e($homePath) . '">' . $this->e($copy['nav_home']) . '</a><a href="' . $this->e($catalogPath) . '">' . $this->e($copy['nav_products']) . '</a><a href="' . $this->e($articlePath) . '" aria-current="page">' . $this->e($copy['nav_articles']) . '</a><a href="' . $this->e($homePath . '#ai-advisor') . '">' . $this->e($copy['nav_support']) . '</a></nav>'
            . '</div></header>';
    }

    private function renderHeroShowcase(array $featuredArticles, array $copy): string
    {
        $html = '<div class="catalog-showcase article-catalog-showcase"><div class="catalog-showcase-head"><div><div class="eyebrow">' . $this->e($copy['showcase_eyebrow']) . '</div><strong>' . $this->e($copy['showcase_title']) . '</strong><p>' . $this->e($copy['showcase_text']) . '</p></div></div><div class="article-featured-stack">';

        foreach ($featuredArticles as $index => $article) {
            $title = $this->displayText((string) ($article['title'] ?? ''));
            $image = trim((string) ($article['featured_image_path'] ?? ''));
            $detailPath = (string) ($article['route_path'] ?? '#');
            $summary = (string) ($article['catalog_summary'] ?? '');
            $dateLabel = (string) ($article['catalog_date_label'] ?? '');
            $category = (string) ($article['catalog_category'] ?? 'other');
            $categoryLabel = (string) ($this->categoryLabels($copy)[$category] ?? $copy['category_other']);
            $featuredClass = $index === 0 ? ' is-main' : ' is-secondary';
            $actions = $index === 0
                ? '<div class="card-actions"><a class="button button-secondary" href="' . $this->e($detailPath) . '">' . $this->e($copy['open_article_button']) . '</a></div>'
                : '<a class="article-featured-link" href="' . $this->e($detailPath) . '">' . $this->e($copy['open_article_short']) . '</a>';

            $html .= '<article class="catalog-featured-product catalog-featured-article' . $featuredClass . '">'
                . ($image !== '' ? '<a class="catalog-featured-media catalog-featured-article-media" href="' . $this->e($detailPath) . '"><img src="' . $this->e($image) . '" alt="' . $this->e($title) . '" loading="' . ($index === 0 ? 'eager' : 'lazy') . '" decoding="async"></a>' : '')
                . '<div class="catalog-featured-copy"><span class="badge">' . $this->e($categoryLabel) . '</span><strong>' . $this->e($title) . '</strong><p>' . $this->e($summary) . '</p>'
                . ($dateLabel !== '' ? '<span class="card-meta">' . $this->e($dateLabel) . '</span>' : '')
                . $actions . '</div>'
                . '</article>';
        }

        return $html . '</div></div>';
    }

    private function renderHeroTopics(array $copy): string
    {
        $topics = [
            ['key' => 'nutrition', 'label' => (string) $copy['category_nutrition'], 'text' => (string) $copy['topic_nutrition_text']],
            ['key' => 'skin', 'label' => (string) $copy['category_skin'], 'text' => (string) $copy['topic_skin_text']],
            ['key' => 'choice', 'label' => (string) $copy['category_choice'], 'text' => (string) $copy['topic_choice_text']],
            ['key' => 'routine', 'label' => (string) $copy['category_routine'], 'text' => (string) $copy['topic_routine_text']],
        ];
        $html = '<div class="article-hero-topics"><div class="eyebrow">' . $this->e($copy['topic_eyebrow']) . '</div><div class="article-topic-grid">';

        foreach ($topics as $topic) {
            $html .= '<button class="article-topic-card" type="button" data-hero-category="' . $this->e((string) $topic['key']) . '"><strong>' . $this->e((string) $topic['label']) . '</strong><span>' . $this->e((string) $topic['text']) . '</span></button>';
        }

        return $html . '</div></div>';
    }

    private function renderToolbar(array $copy, array $articles): string
    {
        $categories = $this->categoryLabels($copy);
        $counts = ['all' => count($articles)];
        foreach ($articles as $article) {
            $category = (string) ($article['catalog_category'] ?? 'other');
            $counts[$category] = (int) ($counts[$category] ?? 0) + 1;
        }

        $buttons = '<button class="catalog-chip is-active" type="button" data-category-filter="all">' . $this->e($copy['filter_all']) . ' <span>' . count($articles) . '</span></button>';
        foreach ($categories as $key => $label) {
            $count = (int) ($counts[$key] ?? 0);
            if ($count <= 0) {
                continue;
            }

            $buttons .= '<button class="catalog-chip" type="button" data-category-filter="' . $this->e($key) . '">' . $this->e($label) . ' <span>' . $count . '</span></button>';
        }

        return '<div class="catalog-toolbar article-catalog-toolbar">'
            . '<label class="catalog-search"><span>' . $this->e($copy['search_label']) . '</span><input type="search" data-catalog-search placeholder="' . $this->e($copy['search_placeholder']) . '"></label>'
            . '<label class="catalog-sort"><span>' . $this->e($copy['sort_label']) . '</span><select data-catalog-sort><option value="featured">' . $this->e($copy['sort_featured']) . '</option><option value="newest">' . $this->e($copy['sort_newest']) . '</option><option value="oldest">' . $this->e($copy['sort_oldest']) . '</option><option value="title">' . $this->e($copy['sort_title']) . '</option></select></label>'
            . '<div class="catalog-chip-row" aria-label="' . $this->e($copy['category_filter_label']) . '">' . $buttons . '</div>'
            . '</div>';
    }

    private function renderArticleGrid(array $articles, array $copy): string
    {
        if ($articles === []) {
            return '<p class="muted">' . $this->e($copy['empty']) . '</p>';
        }

        $html = '<div class="catalog-result-line"><strong data-catalog-count>' . count($articles) . '</strong><span>' . $this->e($copy['result_label']) . '</span></div><div class="article-catalog-grid" id="article-grid">';

        foreach ($articles as $index => $article) {
            $html .= $this->renderArticleCard($article, $copy, $index);
        }

        return $html . '</div><p class="catalog-empty-state" data-catalog-empty hidden>' . $this->e($copy['no_results']) . '</p>';
    }

    private function renderArticleCard(array $article, array $copy, int $index): string
    {
        $title = $this->displayText((string) ($article['title'] ?? ''));
        $detailPath = (string) ($article['route_path'] ?? '#');
        $image = trim((string) ($article['featured_image_path'] ?? ''));
        $category = (string) ($article['catalog_category'] ?? 'other');
        $categoryLabel = (string) ($this->categoryLabels($copy)[$category] ?? $copy['category_other']);
        $summary = (string) ($article['catalog_summary'] ?? '');
        $dateLabel = (string) ($article['catalog_date_label'] ?? '');
        $timestamp = (int) ($article['catalog_timestamp'] ?? 0);
        $searchText = $this->normalizeSearchText(implode(' ', [
            $title,
            (string) ($article['slug'] ?? ''),
            $categoryLabel,
            $summary,
        ]));

        return '<article class="catalog-article-card" data-catalog-card data-category="' . $this->e($category) . '" data-title="' . $this->e($this->normalizeSearchText($title)) . '" data-search="' . $this->e($searchText) . '" data-date="' . $this->e((string) $timestamp) . '" data-order="' . $index . '">'
            . '<a class="catalog-article-media" href="' . $this->e($detailPath) . '">' . ($image !== '' ? '<img src="' . $this->e($image) . '" alt="' . $this->e($title) . '" loading="lazy" decoding="async">' : '<span>' . $this->e($copy['image_fallback']) . '</span>') . '<span class="badge">' . $this->e($categoryLabel) . '</span></a>'
            . '<div class="catalog-article-body">'
            . ($dateLabel !== '' ? '<span class="card-meta">' . $this->e($dateLabel) . '</span>' : '')
            . '<h3><a href="' . $this->e($detailPath) . '">' . $this->e($title) . '</a></h3>'
            . '<p>' . $this->e($summary) . '</p>'
            . '<div class="card-actions"><a class="button button-secondary" href="' . $this->e($detailPath) . '">' . $this->e($copy['open_article_button']) . '</a></div></div>'
            . '</article>';
    }

    private function renderAdvisorBand(array $copy, string $languageCode): string
    {
        $homePath = $this->homePathForLanguage($languageCode);

        return '<section class="catalog-advisor-band"><div><div class="eyebrow">' . $this->e($copy['advisor_eyebrow']) . '</div><div class="content-prose"><h2>' . $this->e($copy['advisor_title']) . '</h2><p>' . $this->e($copy['advisor_text']) . '</p></div></div><div class="card-actions"><a class="button button-primary" href="' . $this->e($homePath . '#ai-advisor') . '">' . $this->e($copy['advisor_cta']) . '</a><a class="button button-secondary" href="#article-grid">' . $this->e($copy['back_to_articles']) . '</a></div></section>';
    }

    private function renderSiteFooter(array $copy): string
    {
        return '<footer class="site-footer"><div class="content-card"><strong>' . $this->e($this->brandName()) . '</strong><p class="muted">' . $this->e($copy['footer_text']) . '</p></div></footer>';
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
        $resultLabel = json_encode((string) $copy['result_label'], JSON_UNESCAPED_UNICODE);

        return '<script>
(() => {
  const root = document.querySelector("[data-catalog]");
  if (!root) return;
  const grid = document.querySelector("#article-grid");
  const cards = Array.from(document.querySelectorAll("[data-catalog-card]"));
  const search = document.querySelector("[data-catalog-search]");
  const sort = document.querySelector("[data-catalog-sort]");
  const count = document.querySelector("[data-catalog-count]");
  const empty = document.querySelector("[data-catalog-empty]");
  const chips = Array.from(document.querySelectorAll("[data-category-filter]"));
  const heroTopics = Array.from(document.querySelectorAll("[data-hero-category]"));
  let activeCategory = "all";
  const resultLabel = ' . ($resultLabel ?: '""') . ';

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
      if (sortMode === "title") return (left.dataset.title || "").localeCompare(right.dataset.title || "", "hr");
      if (sortMode === "newest") return Number(right.dataset.date || 0) - Number(left.dataset.date || 0);
      if (sortMode === "oldest") return Number(left.dataset.date || 0) - Number(right.dataset.date || 0);
      return Number(left.dataset.order || 0) - Number(right.dataset.order || 0);
    });

    visibleCards.forEach((card) => grid.appendChild(card));
    if (count) count.textContent = String(visibleCards.length);
    if (empty) empty.hidden = visibleCards.length !== 0;
    if (resultLabel) root.dataset.resultLabel = resultLabel;
  }

  function setCategory(category) {
    activeCategory = category || "all";
    chips.forEach((item) => item.classList.toggle("is-active", (item.dataset.categoryFilter || "all") === activeCategory));
    heroTopics.forEach((item) => item.classList.toggle("is-active", item.dataset.heroCategory === activeCategory));
    applyCatalogState();
  }

  chips.forEach((chip) => {
    chip.addEventListener("click", () => {
      setCategory(chip.dataset.categoryFilter || "all");
    });
  });

  heroTopics.forEach((topic) => {
    topic.addEventListener("click", () => {
      setCategory(topic.dataset.heroCategory || "all");
      const target = document.querySelector("#article-grid");
      if (target) target.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  });

  if (search) search.addEventListener("input", applyCatalogState);
  if (sort) sort.addEventListener("change", applyCatalogState);
  applyCatalogState();
})();
</script>';
    }

    private function buildSchema(array $articles, array $copy, string $canonicalUrl, string $description, string $languageCode): array
    {
        $itemListElements = [];

        foreach (array_slice($articles, 0, 80) as $index => $article) {
            $title = $this->displayText((string) ($article['title'] ?? ''));
            $detailUrl = $this->absoluteUrl((string) ($article['route_path'] ?? '/'));

            $articleSchema = array_filter([
                '@type' => 'Article',
                'headline' => $title,
                'url' => $detailUrl,
                'image' => trim((string) ($article['featured_image_path'] ?? '')) !== '' ? $this->absoluteUrl((string) $article['featured_image_path']) : null,
                'description' => (string) ($article['catalog_summary'] ?? ''),
                'datePublished' => $this->schemaDate((string) ($article['published_at'] ?? '')),
                'inLanguage' => $languageCode,
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $this->brandName(),
                ],
            ], static fn (mixed $value): bool => $value !== null && $value !== []);

            $itemListElements[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => $detailUrl,
                'item' => $articleSchema,
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
                    'url' => $canonicalUrl . '#article-grid',
                    'numberOfItems' => count($articles),
                    'itemListElement' => $itemListElements,
                ],
            ],
        ];
    }

    private function countActiveCategories(array $articles): int
    {
        $categories = [];
        foreach ($articles as $article) {
            $category = (string) ($article['catalog_category'] ?? '');
            if ($category !== '') {
                $categories[$category] = true;
            }
        }

        return count($categories);
    }

    private function categoryForArticle(array $article): string
    {
        $text = $this->searchText($article);

        if (preg_match('/(koza|njega|krema|gel|beauty|lice|osjetljiva|sensitive|skin|care|higijena|ruke|hand)/', $text) === 1) {
            return 'skin';
        }

        if (preg_match('/(probava|vlakna|mikrobiom|prehrana|zeludac|crijev|hrana|med|alerg|digest|fiber|nutrition|prebava)/', $text) === 1) {
            return 'nutrition';
        }

        if (preg_match('/(imunitet|energija|fokus|umor|vitamin|immune|energy|focus|odpornost)/', $text) === 1) {
            return 'energy';
        }

        if (preg_match('/(rutina|svakodnev|navika|plan|jutro|vecer|daily|routine|navade)/', $text) === 1) {
            return 'routine';
        }

        if (preg_match('/(uspored|odabir|kupnj|preporuk|koji je bolji|choice|compare|buy|izbira|nakup)/', $text) === 1) {
            return 'choice';
        }

        return 'other';
    }

    private function summaryForArticle(array $article, string $languageCode): string
    {
        $fallback = trim((string) ($article['meta_description'] ?? ''));
        if ($fallback === '') {
            $fallback = trim(strip_tags((string) ($article['excerpt'] ?? '')));
        }

        $fallback = $this->humanizeText($fallback);

        if ($fallback === '' || mb_strlen($fallback) < 42) {
            $fallback = $this->categoryFallbackSummary($article, $languageCode);
        }

        return mb_strimwidth($fallback, 0, 142, '...');
    }

    private function categoryFallbackSummary(array $article, string $languageCode): string
    {
        $category = (string) ($article['catalog_category'] ?? $this->categoryForArticle($article));

        return match ($category) {
            'skin' => match ($languageCode) {
                'en' => 'A practical guide for care routines, skin comfort and easier product comparison.',
                'sl' => 'Praktičen vodnik za nego, udobje kože in lažjo primerjavo izdelkov.',
                default => 'Praktičan vodič za njegu, ugodniji osjećaj kože i lakšu usporedbu proizvoda.',
            },
            'nutrition' => match ($languageCode) {
                'en' => 'A clear read for digestion, nutrition and everyday decisions around aloe routines.',
                'sl' => 'Jasen članek o prebavi, prehrani in vsakodnevnih aloe rutinah.',
                default => 'Jasan članak za probavu, prehranu i svakodnevne odluke oko aloe rutine.',
            },
            'energy' => match ($languageCode) {
                'en' => 'Helpful context for energy, immunity and choosing a routine that fits your day.',
                'sl' => 'Koristen kontekst za energijo, odpornost in izbiro rutine, ki ustreza tvojemu dnevu.',
                default => 'Korisno objašnjenje za energiju, imunitet i odabir rutine koja se uklapa u dan.',
            },
            'choice' => match ($languageCode) {
                'en' => 'A comparison-focused article when you want a clearer choice before ordering.',
                'sl' => 'Članek za primerjavo, ko želiš jasnejšo izbiro pred naročilom.',
                default => 'Članak za usporedbu kada želiš jasniji izbor prije narudžbe.',
            },
            default => match ($languageCode) {
                'en' => 'A useful Aloe Vera Centar article that helps you understand the topic before choosing a product.',
                'sl' => 'Uporaben članek Aloe Vera Centra, ki pomaga razumeti temo pred izbiro izdelka.',
                default => 'Korisni članak Aloe Vera Centra koji pomaže razumjeti temu prije odabira proizvoda.',
            },
        };
    }

    private function timestampForArticle(array $article): int
    {
        $candidate = trim((string) ($article['published_at'] ?? ''));
        if ($candidate === '') {
            $candidate = trim((string) ($article['updated_at'] ?? ''));
        }

        $timestamp = strtotime($candidate);

        return $timestamp !== false ? $timestamp : 0;
    }

    private function formatDisplayDate(string $value, string $languageCode): string
    {
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return '';
        }

        return match (strtolower($languageCode)) {
            'en' => date('M j, Y', $timestamp),
            default => date('j. n. Y.', $timestamp),
        };
    }

    private function schemaDate(string $value): ?string
    {
        $timestamp = strtotime($value);

        return $timestamp !== false ? date(DATE_ATOM, $timestamp) : null;
    }

    private function searchText(array $article): string
    {
        return $this->normalizeSearchText(implode(' ', [
            (string) ($article['title'] ?? ''),
            (string) ($article['slug'] ?? ''),
            (string) ($article['route_path'] ?? ''),
            (string) ($article['meta_description'] ?? ''),
            (string) ($article['excerpt'] ?? ''),
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
                'href' => $this->absoluteUrl($this->articleCatalogPathForLanguage($languageCode)),
            ];
        }

        $links[] = [
            'hreflang' => 'x-default',
            'href' => $this->absoluteUrl($this->articleCatalogPathForLanguage('hr')),
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

    private function articleCatalogPathForLanguage(string $languageCode): string
    {
        return match (strtolower($languageCode)) {
            'en' => '/en/articles/',
            'sl' => '/sl/clanki/',
            default => '/clanci/',
        };
    }

    private function catalogPathForLanguage(string $languageCode): string
    {
        return match (strtolower($languageCode)) {
            'en' => '/en/forever-products/',
            'sl' => '/sl/forever-izdelki/',
            default => '/forever-proizvodi/',
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
            'nutrition' => (string) $copy['category_nutrition'],
            'skin' => (string) $copy['category_skin'],
            'energy' => (string) $copy['category_energy'],
            'routine' => (string) $copy['category_routine'],
            'choice' => (string) $copy['category_choice'],
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
                'meta_title' => 'Članci | Aloe Vera Centar vodiči za Forever proizvode',
                'meta_description' => 'Pregled korisnih članaka Aloe Vera Centra: probava, koža, rutina, usporedbe i objašnjenja koja pomažu odabrati Forever proizvod s više sigurnosti.',
                'kicker' => 'Korisni članci',
                'headline' => 'Pročitaj prije odluke',
                'intro' => 'Kratka, jasna objašnjenja za trenutke kada želiš razumjeti temu, usporediti opcije ili lakše odlučiti koji Forever Living Products proizvod ima smisla za tebe.',
                'stat_articles' => 'Članaka',
                'stat_topics' => 'Tema',
                'primary_cta' => 'Pregledaj članke',
                'advisor_cta' => 'Pitaj za preporuku',
                'showcase_eyebrow' => 'Za brzi početak',
                'showcase_title' => 'Najkorisnije za čitanje prvo',
                'showcase_text' => 'Ako tek istražuješ, kreni od članaka koji najčešće pomažu razjasniti potrebu prije odabira proizvoda.',
                'catalog_eyebrow' => 'Biblioteka',
                'catalog_title' => 'Svi članci na jednom mjestu',
                'catalog_text' => 'Pretraži po temi, filtriraj po potrebi i otvori članak koji ti najviše odgovara. Cilj je da odluka bude jednostavnija, ne da čitaš više nego što treba.',
                'filter_all' => 'Svi',
                'search_label' => 'Pretraga',
                'search_placeholder' => 'Upiši temu, pitanje ili proizvod...',
                'sort_label' => 'Sortiranje',
                'sort_featured' => 'Preporučeno prvo',
                'sort_newest' => 'Najnovije prvo',
                'sort_oldest' => 'Najstarije prvo',
                'sort_title' => 'Naziv A-Z',
                'category_filter_label' => 'Filter tema',
                'category_nutrition' => 'Probava i prehrana',
                'category_skin' => 'Koža i njega',
                'category_energy' => 'Imunitet i energija',
                'category_routine' => 'Rutina',
                'category_choice' => 'Prije kupnje',
                'category_other' => 'Ostalo',
                'open_article_button' => 'Otvori članak',
                'open_article_short' => 'Otvori',
                'topic_eyebrow' => 'Kreni po temi',
                'topic_nutrition_text' => 'Kad te zanima probava, prehrana ili svakodnevna ravnoteža.',
                'topic_skin_text' => 'Za njegu, osjetljivu kožu i jednostavnije rutine.',
                'topic_choice_text' => 'Za usporedbe i jasniju odluku prije kupnje.',
                'topic_routine_text' => 'Kad želiš proizvod uklopiti u stvarni dan.',
                'image_fallback' => 'Aloe Vera Centar članak',
                'result_label' => 'članaka prikazano',
                'no_results' => 'Nema članaka za ovaj filter. Probaj kraći pojam ili drugu temu.',
                'empty' => 'Biblioteka članaka trenutno nema stavki.',
                'advisor_eyebrow' => 'Nisi siguran što čitati?',
                'advisor_title' => 'Možeš krenuti od pitanja, ne od članka',
                'advisor_text' => 'Ako ne znaš odakle početi, napiši što želiš riješiti i dobit ćeš smjer prema proizvodu ili korisnom članku.',
                'back_to_articles' => 'Natrag na članke',
                'footer_text' => 'Članci pomažu povezati stvarne potrebe, rutinu i Forever proizvode u jasniji sljedeći korak.',
            ],
            'en' => [
                'brand_tagline' => 'Calmer Forever product choices',
                'nav_home' => 'Home',
                'nav_products' => 'Products',
                'nav_articles' => 'Articles',
                'nav_support' => 'Guidance',
                'meta_title' => 'Articles | Aloe Vera Centar Forever product guides',
                'meta_description' => 'Browse useful Aloe Vera Centar articles about digestion, skin, routines, comparisons and explanations that help you choose a Forever product with more confidence.',
                'kicker' => 'Useful articles',
                'headline' => 'Read before choosing',
                'intro' => 'Clear, practical explanations for moments when you want to understand the topic, compare options or choose the Forever Living Products item that fits your needs.',
                'stat_articles' => 'Articles',
                'stat_topics' => 'Topics',
                'primary_cta' => 'Browse articles',
                'advisor_cta' => 'Ask for guidance',
                'showcase_eyebrow' => 'Quick start',
                'showcase_title' => 'Most useful reads first',
                'showcase_text' => 'If you are just exploring, start with articles that help clarify the need before choosing a product.',
                'catalog_eyebrow' => 'Library',
                'catalog_title' => 'All articles in one place',
                'catalog_text' => 'Search by topic, filter by need and open the article that fits your situation. The goal is an easier decision, not more reading than needed.',
                'filter_all' => 'All',
                'search_label' => 'Search',
                'search_placeholder' => 'Search by topic, question or product...',
                'sort_label' => 'Sort',
                'sort_featured' => 'Recommended first',
                'sort_newest' => 'Newest first',
                'sort_oldest' => 'Oldest first',
                'sort_title' => 'Title A-Z',
                'category_filter_label' => 'Topic filter',
                'category_nutrition' => 'Digestion and nutrition',
                'category_skin' => 'Skin and care',
                'category_energy' => 'Immunity and energy',
                'category_routine' => 'Routine',
                'category_choice' => 'Before buying',
                'category_other' => 'Other',
                'open_article_button' => 'Open article',
                'open_article_short' => 'Open',
                'topic_eyebrow' => 'Start by topic',
                'topic_nutrition_text' => 'For digestion, nutrition and everyday balance.',
                'topic_skin_text' => 'For care, sensitive skin and simpler routines.',
                'topic_choice_text' => 'For comparisons and clearer choices before buying.',
                'topic_routine_text' => 'When you want a product to fit real daily life.',
                'image_fallback' => 'Aloe Vera Centar article',
                'result_label' => 'articles shown',
                'no_results' => 'No articles match this filter. Try a shorter search or another topic.',
                'empty' => 'The article library is currently empty.',
                'advisor_eyebrow' => 'Not sure what to read?',
                'advisor_title' => 'Start with a question, not an article',
                'advisor_text' => 'If you do not know where to begin, describe what you want to solve and get a direction toward a product or useful article.',
                'back_to_articles' => 'Back to articles',
                'footer_text' => 'Articles help connect real needs, routines and Forever products into a clearer next step.',
            ],
            'sl' => [
                'brand_tagline' => 'Mirnejša izbira Forever izdelkov',
                'nav_home' => 'Domov',
                'nav_products' => 'Izdelki',
                'nav_articles' => 'Članki',
                'nav_support' => 'Priporočilo',
                'meta_title' => 'Članki | Aloe Vera Centar vodniki za Forever izdelke',
                'meta_description' => 'Preglej uporabne članke Aloe Vera Centra o prebavi, koži, rutini, primerjavah in razlagah, ki pomagajo lažje izbrati Forever izdelek.',
                'kicker' => 'Uporabni članki',
                'headline' => 'Preberi pred izbiro',
                'intro' => 'Jasne in praktične razlage za trenutke, ko želiš razumeti temo, primerjati možnosti ali izbrati Forever Living Products izdelek za svoje potrebe.',
                'stat_articles' => 'Člankov',
                'stat_topics' => 'Tem',
                'primary_cta' => 'Preglej članke',
                'advisor_cta' => 'Vprašaj za priporočilo',
                'showcase_eyebrow' => 'Hiter začetek',
                'showcase_title' => 'Najkoristnejše najprej',
                'showcase_text' => 'Če šele raziskuješ, začni s članki, ki pomagajo razjasniti potrebo pred izbiro izdelka.',
                'catalog_eyebrow' => 'Knjižnica',
                'catalog_title' => 'Vsi članki na enem mestu',
                'catalog_text' => 'Išči po temi, filtriraj po potrebi in odpri članek, ki ustreza tvoji situaciji. Cilj je lažja odločitev, ne več branja kot je potrebno.',
                'filter_all' => 'Vsi',
                'search_label' => 'Iskanje',
                'search_placeholder' => 'Vpiši temo, vprašanje ali izdelek...',
                'sort_label' => 'Razvrsti',
                'sort_featured' => 'Priporočeno najprej',
                'sort_newest' => 'Najnovejše najprej',
                'sort_oldest' => 'Najstarejše najprej',
                'sort_title' => 'Naslov A-Z',
                'category_filter_label' => 'Filter tem',
                'category_nutrition' => 'Prebava in prehrana',
                'category_skin' => 'Koža in nega',
                'category_energy' => 'Odpornost in energija',
                'category_routine' => 'Rutina',
                'category_choice' => 'Pred nakupom',
                'category_other' => 'Ostalo',
                'open_article_button' => 'Odpri članek',
                'open_article_short' => 'Odpri',
                'topic_eyebrow' => 'Začni po temi',
                'topic_nutrition_text' => 'Za prebavo, prehrano in vsakodnevno ravnovesje.',
                'topic_skin_text' => 'Za nego, občutljivo kožo in preprostejše rutine.',
                'topic_choice_text' => 'Za primerjave in jasnejšo izbiro pred nakupom.',
                'topic_routine_text' => 'Ko želiš izdelek vključiti v resničen dan.',
                'image_fallback' => 'Aloe Vera Centar članek',
                'result_label' => 'člankov prikazano',
                'no_results' => 'Za ta filter ni člankov. Poskusi krajši pojem ali drugo temo.',
                'empty' => 'Knjižnica člankov trenutno nima vnosov.',
                'advisor_eyebrow' => 'Nisi prepričan, kaj brati?',
                'advisor_title' => 'Začni z vprašanjem, ne s člankom',
                'advisor_text' => 'Če ne veš, kje začeti, napiši, kaj želiš rešiti, in dobiš smer do izdelka ali uporabnega članka.',
                'back_to_articles' => 'Nazaj na članke',
                'footer_text' => 'Članki pomagajo povezati resnične potrebe, rutino in Forever izdelke v jasnejši naslednji korak.',
            ],
        ];

        return $copy[$languageCode] ?? $copy['hr'];
    }
}
