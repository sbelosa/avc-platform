<?php

declare(strict_types=1);

namespace Avc\Controllers\Site;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\ContentRepository;
use Avc\Support\PageRenderer;

final class GoalLandingController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function show(): never
    {
        $match = $this->currentDefinition();
        if ($match === null) {
            $this->response->html('<h1>404</h1><p>Route not found.</p>', 404);
        }

        [$languageCode, $goalKey, $copy] = $match;
        $repository = new ContentRepository($this->config);
        $products = $repository->findCardsByContentItemIds((array) ($copy['product_ids'] ?? []), $languageCode, true);
        $curatedArticles = $repository->findCardsByContentItemIds((array) ($copy['article_ids'] ?? []), $languageCode);
        $articles = $this->mergeCards($curatedArticles, $this->selectArticles(
            $repository->findArticleCatalogCards($languageCode, 160),
            (array) ($copy['article_terms'] ?? []),
            3
        ), 3);
        $canonicalPath = (string) ($copy['path'] ?? '/');
        $canonicalUrl = $this->absoluteUrl($canonicalPath);
        $title = (string) ($copy['meta_title'] ?? $copy['title'] ?? 'Aloe Vera Centar');
        $description = (string) ($copy['meta_description'] ?? $copy['intro'] ?? '');
        $catalogPath = $this->navigationPaths($languageCode)['products'];
        $advisorPath = $this->navigationPaths($languageCode)['support'];

        $body = $this->header($languageCode)
            . '<main class="shell goal-page goal-page-' . $this->e($goalKey) . '">'
            . '<section class="goal-hero-panel">'
            . '<div class="goal-hero-copy content-prose">'
            . '<span class="hero-kicker">' . $this->e((string) ($copy['eyebrow'] ?? 'Forever vodič')) . '</span>'
            . '<h1>' . $this->e((string) ($copy['title'] ?? '')) . '</h1>'
            . '<p class="lead">' . $this->e((string) ($copy['intro'] ?? '')) . '</p>'
            . '<div class="trust-actions"><a class="button button-primary" href="#preporuceni-proizvodi">' . $this->e((string) ($copy['primary_cta'] ?? 'Pogledaj proizvode')) . '</a><a class="button button-secondary" href="' . $this->e($advisorPath) . '">' . $this->e((string) ($copy['secondary_cta'] ?? 'Pitaj za preporuku')) . '</a></div>'
            . '</div>'
            . '<aside class="goal-path-card">'
            . '<span>' . $this->e((string) ($copy['path_label'] ?? 'Kako krenuti')) . '</span>'
            . '<strong>' . $this->e((string) ($copy['path_title'] ?? '')) . '</strong>'
            . '<ol>' . $this->listItems((array) ($copy['steps'] ?? []), true) . '</ol>'
            . '</aside>'
            . '</section>'
            . '<section class="goal-insight-grid">' . $this->insightCards((array) ($copy['insights'] ?? [])) . '</section>'
            . '<section id="preporuceni-proizvodi" class="goal-section">'
            . '<div class="section-heading"><div class="eyebrow">' . $this->e((string) ($copy['products_eyebrow'] ?? 'Proizvodi')) . '</div><h2>' . $this->e((string) ($copy['products_title'] ?? 'Proizvodi koje vrijedi usporediti')) . '</h2><p>' . $this->e((string) ($copy['products_text'] ?? '')) . '</p></div>'
            . $this->productCards($products, $languageCode)
            . '</section>'
            . '<section class="goal-advisor-band">'
            . '<div><span class="eyebrow">' . $this->e((string) ($copy['advisor_label'] ?? 'Preporuka')) . '</span><strong>' . $this->e((string) ($copy['advisor_title'] ?? 'Nisi siguran što odabrati?')) . '</strong><p>' . $this->e((string) ($copy['advisor_text'] ?? '')) . '</p></div>'
            . '<div class="trust-actions"><a class="button button-primary" href="' . $this->e($advisorPath) . '">' . $this->e((string) ($copy['advisor_button'] ?? 'Pitaj za preporuku')) . '</a><a class="button button-secondary" href="' . $this->e($catalogPath) . '">' . $this->e((string) ($copy['catalog_button'] ?? 'Svi proizvodi')) . '</a></div>'
            . '</section>'
            . '<section class="goal-section">'
            . '<div class="section-heading"><div class="eyebrow">' . $this->e((string) ($copy['articles_eyebrow'] ?? 'Prije odluke')) . '</div><h2>' . $this->e((string) ($copy['articles_title'] ?? 'Korisno za pročitati')) . '</h2><p>' . $this->e((string) ($copy['articles_text'] ?? '')) . '</p></div>'
            . $this->articleCards($articles, $languageCode)
            . '</section>'
            . '</main>'
            . $this->footer($languageCode);

        $this->response->html(PageRenderer::render($title, $body, [
            'lang' => $languageCode,
            'meta_description' => $description,
            'canonical_url' => $canonicalUrl,
            'alternate_links' => $this->alternateLinks($goalKey),
            'body_class' => 'site-goal',
            'open_graph' => [
                'type' => 'website',
                'site_name' => 'Aloe Vera Centar',
                'title' => $title,
                'description' => $description,
                'url' => $canonicalUrl,
                'locale' => $this->locale($languageCode),
            ],
            'schema_json' => json_encode($this->schema($title, $description, $canonicalUrl, $languageCode, $products, $articles), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]));
    }

    private function productCards(array $products, string $languageCode): string
    {
        if ($products === []) {
            return '';
        }

        $copy = $this->cardCopy($languageCode);
        $html = '<div class="goal-product-grid">';

        foreach ($products as $product) {
            $href = (string) ($product['route_path'] ?? '#');
            $title = $this->displayText((string) ($product['title'] ?? ''));
            $html .= '<article class="goal-product-card">'
                . $this->image((string) ($product['featured_image_path'] ?? ''), $title)
                . '<div class="goal-card-copy"><h3><a href="' . $this->e($href) . '">' . $this->e($title) . '</a></h3>'
                . '<p>' . $this->e($this->productSummary($product)) . '</p>'
                . '<div class="card-actions"><a class="button button-secondary" href="' . $this->e($href) . '">' . $this->e($copy['details']) . '</a><a class="button button-primary" href="' . $this->e($href) . '">' . $this->e($copy['shop']) . '</a></div></div>'
                . '</article>';
        }

        return $html . '</div>';
    }

    private function articleCards(array $articles, string $languageCode): string
    {
        if ($articles === []) {
            return '';
        }

        $copy = $this->cardCopy($languageCode);
        $html = '<div class="goal-article-grid">';

        foreach ($articles as $article) {
            $href = (string) ($article['route_path'] ?? '#');
            $title = $this->displayText((string) ($article['title'] ?? ''));
            $html .= '<article class="goal-article-card">'
                . $this->image((string) ($article['featured_image_path'] ?? ''), $title)
                . '<div class="goal-card-copy"><h3><a href="' . $this->e($href) . '">' . $this->e($title) . '</a></h3>'
                . '<p>' . $this->e($this->summary($article, 120)) . '</p>'
                . '<a class="card-link" href="' . $this->e($href) . '">' . $this->e($copy['article']) . '</a></div>'
                . '</article>';
        }

        return $html . '</div>';
    }

    private function insightCards(array $cards): string
    {
        $html = '';

        foreach ($cards as $card) {
            $html .= '<article class="goal-insight-card"><span>' . $this->e((string) ($card['label'] ?? '')) . '</span><strong>' . $this->e((string) ($card['title'] ?? '')) . '</strong><p>' . $this->e((string) ($card['text'] ?? '')) . '</p></article>';
        }

        return $html;
    }

    private function listItems(array $items, bool $numbered = false): string
    {
        $html = '';

        foreach ($items as $item) {
            $html .= '<li>' . $this->e((string) $item) . '</li>';
        }

        return $html;
    }

    private function selectArticles(array $articles, array $terms, int $limit): array
    {
        $terms = array_values(array_filter(array_map(static fn (mixed $term): string => mb_strtolower(trim((string) $term)), $terms)));
        $scored = [];

        foreach ($articles as $index => $article) {
            $haystack = mb_strtolower($this->plain(implode(' ', [
                (string) ($article['title'] ?? ''),
                (string) ($article['excerpt'] ?? ''),
                (string) ($article['meta_description'] ?? ''),
                (string) ($article['route_path'] ?? ''),
            ])));
            $score = 0;

            foreach ($terms as $term) {
                if ($term !== '' && str_contains($haystack, $term)) {
                    $score++;
                }
            }

            if ($score > 0) {
                $scored[] = ['score' => $score, 'index' => $index, 'article' => $article];
            }
        }

        usort($scored, static fn (array $a, array $b): int => ($b['score'] <=> $a['score']) ?: ($a['index'] <=> $b['index']));
        $selected = array_map(static fn (array $row): array => $row['article'], array_slice($scored, 0, $limit));

        if (count($selected) >= $limit) {
            return $selected;
        }

        foreach ($articles as $article) {
            if (count($selected) >= $limit) {
                break;
            }
            $selected[] = $article;
        }

        return $selected;
    }

    private function mergeCards(array $preferred, array $fallback, int $limit): array
    {
        $items = [];
        $seen = [];

        foreach (array_merge($preferred, $fallback) as $item) {
            $key = (string) ($item['content_item_id'] ?? $item['route_path'] ?? '');
            if ($key === '' || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $items[] = $item;

            if (count($items) >= $limit) {
                break;
            }
        }

        return $items;
    }

    private function image(string $path, string $alt): string
    {
        $path = trim($path);
        if ($path === '') {
            return '<div class="goal-card-image goal-card-image-empty"><span>Aloe Vera Centar</span></div>';
        }

        return '<div class="goal-card-image"><img src="' . $this->e($path) . '" alt="' . $this->e($alt) . '" loading="lazy" decoding="async"></div>';
    }

    private function summary(array $row, int $limit): string
    {
        $candidate = trim((string) ($row['meta_description'] ?? ''));
        if ($candidate === '') {
            $candidate = trim((string) ($row['excerpt'] ?? ''));
        }
        if ($candidate === '') {
            $candidate = trim((string) ($row['title'] ?? ''));
        }

        return mb_strimwidth($this->plain($candidate), 0, $limit, '…');
    }

    private function productSummary(array $row): string
    {
        $languageCode = strtolower((string) ($row['language_code'] ?? 'hr'));
        $title = $this->displayText((string) ($row['title'] ?? ''));
        $haystack = mb_strtolower($this->plain(implode(' ', [
            $title,
            (string) ($row['slug'] ?? ''),
            (string) ($row['route_path'] ?? ''),
            (string) ($row['meta_description'] ?? ''),
            (string) ($row['excerpt'] ?? ''),
        ])));

        $text = '';

        if (str_contains($haystack, 'aloe vera gel') && !str_contains($haystack, 'gelly')) {
            $text = match ($languageCode) {
                'en' => 'A daily aloe drink when you want a simple foundation for digestion and routine.',
                'sl' => 'Vsakodnevni aloe napitek, ko želiš preprosto osnovo za prebavo in rutino.',
                default => 'Svakodnevni aloe napitak kada želiš jednostavnu osnovu za probavu i rutinu.',
            };
        } elseif (str_contains($haystack, 'berry')) {
            $text = match ($languageCode) {
                'en' => 'A fruitier aloe drink option with cranberry and apple for a daily routine.',
                'sl' => 'Sadnejša aloe možnost z brusnico in jabolkom za dnevno rutino.',
                default => 'Voćnija aloe opcija s brusnicom i jabukom za svakodnevnu rutinu.',
            };
        } elseif (str_contains($haystack, 'active pro b')) {
            $text = match ($languageCode) {
                'en' => 'A probiotic option when the topic is microbiome balance and everyday digestion.',
                'sl' => 'Probiotična možnost, ko sta tema mikrobiom in vsakodnevna prebava.',
                default => 'Probiotička opcija kada su tema mikrobiom i svakodnevna probava.',
            };
        } elseif (str_contains($haystack, 'fiber') || str_contains($haystack, 'vlakna')) {
            $text = match ($languageCode) {
                'en' => 'Simple fiber support when meals, satiety and digestion are part of the goal.',
                'sl' => 'Preprosta podpora z vlakninami, ko so tema obroki, sitost in prebava.',
                default => 'Jednostavna podrška vlaknima kada su tema obroci, sitost i probava.',
            };
        } elseif (str_contains($haystack, 'collagen') || str_contains($haystack, 'kolagen')) {
            $text = match ($languageCode) {
                'en' => 'Collagen support for skin, hair, nails and beauty routines.',
                'sl' => 'Kolagenska podpora za kožo, lase, nohte in beauty rutinu.',
                default => 'Kolagenska podrška za kožu, kosu, nokte i beauty rutinu.',
            };
        } elseif (str_contains($haystack, 'bakuchiol') || str_contains($haystack, 'vitamin c')) {
            $text = match ($languageCode) {
                'en' => 'A focused skincare routine when the goal is a cared-for, fresher-looking complexion.',
                'sl' => 'Ciljana rutina za obraz, ko želiš negovaniji in svežji videz kože.',
                default => 'Ciljana rutina za lice kada želiš njegovaniji i svježiji izgled kože.',
            };
        } elseif (str_contains($haystack, 'gelly')) {
            $text = match ($languageCode) {
                'en' => 'Aloe gel for simple outer care when skin comfort is the goal.',
                'sl' => 'Aloe gel za preprosto zunanjo nego, ko želiš udobje kože.',
                default => 'Aloe gel za jednostavnu vanjsku njegu kada želiš ugodniji osjećaj kože.',
            };
        } elseif (str_contains($haystack, 'propolis')) {
            $text = match ($languageCode) {
                'en' => 'A richer care cream when dry or demanding skin needs a simple routine.',
                'sl' => 'Bogatejša krema za nego, ko suha ali zahtevnejša koža potrebuje rutino.',
                default => 'Bogata krema za njegu kada suha ili zahtjevnija koža treba jednostavnu rutinu.',
            };
        } elseif (str_contains($haystack, 'argi')) {
            $text = match ($languageCode) {
                'en' => 'A practical option for active days, vitality and a more consistent routine.',
                'sl' => 'Praktična možnost za aktivne dni, vitalnost in bolj dosledno rutino.',
                default => 'Praktična opcija za aktivnije dane, vitalnost i dosljedniju rutinu.',
            };
        } elseif (str_contains($haystack, 'focus')) {
            $text = match ($languageCode) {
                'en' => 'Worth comparing when focus and mental clarity are the main topic.',
                'sl' => 'Vredno primerjave, ko sta glavna tema fokus in mentalna jasnost.',
                default => 'Vrijedi ga usporediti kada su fokus i mentalna jasnoća glavna tema.',
            };
        } elseif (str_contains($haystack, 'daily')) {
            $text = match ($languageCode) {
                'en' => 'Daily vitamin and mineral support when you want a simpler foundation.',
                'sl' => 'Dnevna vitaminsko-mineralna podpora, ko želiš preprostejšo osnovo.',
                default => 'Dnevna vitaminsko-mineralna podrška kada želiš jednostavniju osnovu.',
            };
        } elseif (str_contains($haystack, 'b12')) {
            $text = match ($languageCode) {
                'en' => 'A B12-focused product to compare when energy and daily rhythm are the topic.',
                'sl' => 'Izdelek z B12 za primerjavo, ko sta tema energija in dnevni ritem.',
                default => 'B12 proizvod za usporedbu kada su tema energija i dnevni ritam.',
            };
        } elseif (str_contains($haystack, 'immublend')) {
            $text = match ($languageCode) {
                'en' => 'A seasonal immune-support option for a simple daily routine.',
                'sl' => 'Sezonska možnost za podporo imunosti v preprosti dnevni rutini.',
                default => 'Sezonska opcija za podršku imunitetu u jednostavnoj dnevnoj rutini.',
            };
        } elseif (str_contains($haystack, 'absorbent c') || str_contains($haystack, 'absorbent d')) {
            $text = match ($languageCode) {
                'en' => 'Vitamin support to compare when the goal is seasonal resilience.',
                'sl' => 'Vitaminska podpora za primerjavo, ko je cilj sezonska odpornost.',
                default => 'Vitaminska podrška za usporedbu kada je cilj sezonska otpornost.',
            };
        } elseif (str_contains($haystack, 'garlic')) {
            $text = match ($languageCode) {
                'en' => 'A herbal supplement to compare when you want plant-based daily support.',
                'sl' => 'Zeliščni dodatek za primerjavo, ko želiš rastlinsko dnevno podporo.',
                default => 'Biljni dodatak za usporedbu kada želiš dnevnu podršku iz biljne linije.',
            };
        } elseif (str_contains($haystack, 'aloe first')) {
            $text = match ($languageCode) {
                'en' => 'A practical aloe spray for everyday outer care and quick use.',
                'sl' => 'Praktičen aloe sprej za vsakodnevno zunanjo nego in hitro uporabo.',
                default => 'Praktičan aloe sprej za svakodnevnu vanjsku njegu i brzu upotrebu.',
            };
        } elseif (str_contains($haystack, 'msm')) {
            $text = match ($languageCode) {
                'en' => 'A gel option for outer care when comfort and massage-like use are the goal.',
                'sl' => 'Gel za zunanjo nego, ko sta cilj udobje in masažna uporaba.',
                default => 'Gel za vanjsku njegu kada su cilj ugoda i korištenje poput masaže.',
            };
        } elseif (str_contains($haystack, 'toothgel') || str_contains($haystack, 'zobni')) {
            $text = match ($languageCode) {
                'en' => 'Everyday oral care with aloe and propolis, without complicating the routine.',
                'sl' => 'Vsakodnevna ustna nega z aloe vero in propolisom, brez zapletanja rutine.',
                default => 'Svakodnevna oralna njega s aloe verom i propolisom, bez kompliciranja rutine.',
            };
        }

        return $text !== '' ? $text : $this->summary($row, 118);
    }

    private function header(string $languageCode): string
    {
        $paths = $this->navigationPaths($languageCode);
        $copy = $this->navigationCopy($languageCode);

        return '<header class="site-header"><div class="header-card">'
            . '<a class="brand" href="' . $this->e($paths['home']) . '"><span class="brand-lockup"><img class="brand-logo" src="/media/branding/aloe-vera-centar-logo-horizontal.png" alt="Aloe Vera Centar" loading="eager" decoding="async"><span class="brand-copy"><strong class="brand-name">Aloe Vera Centar</strong><span class="brand-tagline">' . $this->e($copy['tagline']) . '</span></span></span></a>'
            . '<nav class="header-links"><a href="' . $this->e($paths['home']) . '">' . $this->e($copy['home']) . '</a><a href="' . $this->e($paths['products']) . '">' . $this->e($copy['products']) . '</a><a href="' . $this->e($paths['articles']) . '">' . $this->e($copy['articles']) . '</a><a href="' . $this->e($paths['support']) . '">' . $this->e($copy['support']) . '</a><a href="' . $this->e($paths['contact']) . '">' . $this->e($copy['contact']) . '</a></nav>'
            . '</div></header>';
    }

    private function footer(string $languageCode): string
    {
        $copy = $this->navigationCopy($languageCode);
        $links = $this->authorityLinks($languageCode);
        $html = '<footer class="site-footer"><div class="shell"><div class="content-card"><strong>Aloe Vera Centar</strong><p class="muted">' . $this->e($copy['footer']) . '</p><p class="muted">' . $this->e($copy['footer_about']) . '</p><div class="footer-links">';

        foreach ($links as $label => $path) {
            $html .= '<a href="' . $this->e($path) . '">' . $this->e($label) . '</a>';
        }

        return $html . '</div></div></div></footer>';
    }

    private function navigationPaths(string $languageCode): array
    {
        return match ($languageCode) {
            'en' => ['home' => '/en/', 'products' => '/en/forever-products/', 'articles' => '/en/articles/', 'support' => '/en/#ai-advisor', 'contact' => '/en/contact/'],
            'sl' => ['home' => '/sl/', 'products' => '/sl/forever-izdelki/', 'articles' => '/sl/clanki/', 'support' => '/sl/#ai-advisor', 'contact' => '/sl/kontakt/'],
            default => ['home' => '/', 'products' => '/forever-proizvodi/', 'articles' => '/clanci/', 'support' => '/#ai-advisor', 'contact' => '/kontakt/'],
        };
    }

    private function navigationCopy(string $languageCode): array
    {
        return match ($languageCode) {
            'en' => ['tagline' => 'A clearer Forever product choice', 'home' => 'Home', 'products' => 'Products', 'articles' => 'Articles', 'support' => 'Guidance', 'contact' => 'Contact', 'footer' => 'Educational guidance for easier Forever product choices.', 'footer_about' => 'Aloe Vera Centar presents, recommends and explains Forever products. The website is owned and operated by BS International.'],
            'sl' => ['tagline' => 'Jasnejša izbira Forever izdelkov', 'home' => 'Domov', 'products' => 'Izdelki', 'articles' => 'Članki', 'support' => 'Priporočilo', 'contact' => 'Kontakt', 'footer' => 'Izobraževalni vodiči za lažjo izbiro Forever izdelkov.', 'footer_about' => 'Aloe Vera Centar predstavlja, priporoča in pojasnjuje Forever izdelke. Stran je v lasti in upravljanju podjetja BS International.'],
            default => ['tagline' => 'Jasniji izbor Forever proizvoda', 'home' => 'Naslovnica', 'products' => 'Proizvodi', 'articles' => 'Članci', 'support' => 'Preporuka', 'contact' => 'Kontakt', 'footer' => 'Edukativni vodiči za lakši odabir Forever proizvoda.', 'footer_about' => 'Aloe Vera Centar je web stranica namijenjena predstavljanju, preporuci i informiranju o Forever proizvodima. Stranica je u vlasništvu i pod upravljanjem tvrtke BS International.'],
        };
    }

    private function authorityLinks(string $languageCode): array
    {
        return match ($languageCode) {
            'en' => ['About AVC' => '/en/about/', 'How recommendations work' => '/en/how-recommendations-work/', 'Editorial policy' => '/en/editorial-policy/', 'Contact' => '/en/contact/'],
            'sl' => ['O nas' => '/sl/o-nas/', 'Kako delujejo priporočila' => '/sl/kako-delujejo-priporocila/', 'Uredniška politika' => '/sl/uredniska-politika/', 'Kontakt' => '/sl/kontakt/'],
            default => ['O nama' => '/o-nama/', 'Kako radimo preporuke' => '/kako-rade-preporuke/', 'Urednička politika' => '/urednicka-politika/', 'Kontakt' => '/kontakt/'],
        };
    }

    private function cardCopy(string $languageCode): array
    {
        return match ($languageCode) {
            'en' => ['details' => 'View guide', 'shop' => 'View and buy', 'article' => 'Open article'],
            'sl' => ['details' => 'Poglej vodič', 'shop' => 'Poglej in kupi', 'article' => 'Odpri članek'],
            default => ['details' => 'Pogledaj vodič', 'shop' => 'Pogledaj i kupi', 'article' => 'Otvori članak'],
        };
    }

    private function currentDefinition(): ?array
    {
        $path = $this->request->path();

        foreach ($this->definitions() as $languageCode => $goals) {
            foreach ($goals as $goalKey => $definition) {
                if (rtrim((string) ($definition['path'] ?? ''), '/') === $path) {
                    return [$languageCode, $goalKey, $definition];
                }
            }
        }

        return null;
    }

    private function alternateLinks(string $goalKey): array
    {
        $links = [];
        $definitions = $this->definitions();

        foreach (['hr', 'en', 'sl'] as $languageCode) {
            $path = (string) ($definitions[$languageCode][$goalKey]['path'] ?? '/');
            $links[] = ['hreflang' => $languageCode, 'href' => $this->absoluteUrl($path)];
        }

        $links[] = ['hreflang' => 'x-default', 'href' => $this->absoluteUrl((string) ($definitions['hr'][$goalKey]['path'] ?? '/'))];

        return $links;
    }

    private function schema(string $title, string $description, string $canonicalUrl, string $languageCode, array $products, array $articles): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebPage',
                    'name' => $title,
                    'description' => $description,
                    'url' => $canonicalUrl,
                    'inLanguage' => $languageCode,
                ],
                [
                    '@type' => 'ItemList',
                    'name' => $title,
                    'itemListElement' => array_map(function (array $product, int $index): array {
                        return [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'url' => $this->absoluteUrl((string) ($product['route_path'] ?? '/')),
                            'name' => $this->displayText((string) ($product['title'] ?? '')),
                        ];
                    }, array_slice($products, 0, 6), array_keys(array_slice($products, 0, 6))),
                ],
                [
                    '@type' => 'Organization',
                    'name' => 'Aloe Vera Centar',
                    'url' => $this->absoluteUrl('/'),
                ],
            ],
        ];
    }

    private function definitions(): array
    {
        $shared = [
            'digestion' => [
                'product_ids' => [456, 457, 480, 483],
                'article_ids' => [447, 26, 271],
                'article_terms' => ['probava', 'prebava', 'digestion', 'mikrobiom', 'probiotik', 'vlakna', 'refluks', 'želudac', 'zeludac'],
            ],
            'skin' => [
                'product_ids' => [505, 512, 502, 504],
                'article_ids' => [421, 350, 139],
                'article_terms' => ['koža', 'koza', 'skin', 'kolagen', 'dermatitis', 'ekcem', 'osjetljiva', 'sensitive'],
            ],
            'energy' => [
                'product_ids' => [479, 470, 477, 466],
                'article_ids' => [359, 396, 432],
                'article_terms' => ['energija', 'energy', 'umor', 'fokus', 'san', 'magnezij', 'b12'],
            ],
            'immunity' => [
                'product_ids' => [471, 464, 508, 509],
                'article_ids' => [432, 422, 298],
                'article_terms' => ['imunitet', 'imunost', 'immunity', 'vitamin', 'cink', 'alergije', 'prehlada'],
            ],
            'care' => [
                'product_ids' => [502, 500, 503, 511],
                'article_ids' => [288, 132, 233],
                'article_terms' => ['njega', 'nega', 'care', 'ruke', 'koža', 'gel', 'krema', 'nokti', 'tetovaže'],
            ],
            'unsure' => [
                'product_ids' => [456, 505, 471, 479, 502],
                'article_ids' => [421, 447, 432],
                'article_terms' => ['rutina', 'odabir', 'uspored', 'navika', 'preporuka', 'routine', 'choice'],
            ],
        ];

        return [
            'hr' => $this->mergeShared($shared, [
                'digestion' => [
                    'path' => '/cilj/probava/',
                    'eyebrow' => 'Forever proizvodi za probavu',
                    'title' => 'Kreni prema mirnijoj probavi bez nasumičnog odabira',
                    'meta_title' => 'Forever proizvodi za probavu | Aloe Vera Centar',
                    'meta_description' => 'Vodič kroz Forever proizvode za probavu: aloe napitci, probiotik, vlakna i jednostavniji prvi korak prema rutini.',
                    'intro' => 'Ako želiš podržati svakodnevnu probavu, najvažnije je krenuti jednostavno: razumjeti razliku između aloe napitka, probiotika i vlakana te odabrati ono što se uklapa u tvoju rutinu.',
                    'path_title' => 'Najčešće je dobro krenuti od osnove',
                    'steps' => ['prepoznaj je li tema ritam, nadutost, vlakna ili svakodnevna rutina', 'usporedi aloe napitak, probiotik i vlakna bez žurbe', 'ako nisi siguran, pošalji pitanje prije kupnje'],
                    'insights' => [
                        ['label' => 'Prvi izbor', 'title' => 'Aloe napitak za rutinu', 'text' => 'Za mnoge korisnike aloe napitak je najjednostavniji početak jer se lako uklapa u dan.'],
                        ['label' => 'Kad treba dodatno', 'title' => 'Probiotik ili vlakna', 'text' => 'Ako je tema mikrobiom, prehrana ili osjećaj sitosti, vrijedi pogledati ciljane dodatke.'],
                        ['label' => 'Bez pretjerivanja', 'title' => 'Jedan jasan početak', 'text' => 'Bolje je krenuti s jasnom rutinom nego odjednom otvoriti previše opcija.'],
                    ],
                ],
                'skin' => [
                    'path' => '/cilj/koza/',
                    'eyebrow' => 'Forever proizvodi za kožu',
                    'title' => 'Jednostavnija rutina za kožu, sjaj i svakodnevnu njegu',
                    'meta_title' => 'Forever proizvodi za kožu | Aloe Vera Centar',
                    'meta_description' => 'Vodič kroz Forever proizvode za kožu: kolagen, aloe njega, hidratacija i proizvodi koje vrijedi usporediti.',
                    'intro' => 'Kod kože nije poanta kupiti sve, nego odabrati rutinu koja ima smisla za tvoj cilj: hidratacija, elastičnost, osjetljivost, ruke, lice ili jednostavnija njega.',
                    'path_title' => 'Prvo odredi što koži najviše treba',
                    'steps' => ['razlikuj unutarnju podršku od vanjske njege', 'odaberi jedan glavni cilj: sjaj, hidratacija, ugoda ili rutina', 'usporedi kolagen, aloe gel i kremu prije kupnje'],
                    'insights' => [
                        ['label' => 'Izvana', 'title' => 'Aloe njega za kožu', 'text' => 'Gelly, kreme i gelovi imaju smisla kada želiš jednostavnu vanjsku njegu.'],
                        ['label' => 'Iznutra', 'title' => 'Kolagen kao dio rutine', 'text' => 'Kolagen se najčešće gleda kada je cilj koža, kosa, nokti i svakodnevna beauty rutina.'],
                        ['label' => 'Osjetljiva koža', 'title' => 'Manje proizvoda, jasniji izbor', 'text' => 'Ako je koža osjetljiva, korisno je krenuti pažljivo i pratiti kako rutina odgovara.'],
                    ],
                ],
                'energy' => [
                    'path' => '/cilj/energija/',
                    'eyebrow' => 'Forever proizvodi za energiju',
                    'title' => 'Podrška energiji kada želiš bolji dnevni ritam',
                    'meta_title' => 'Forever proizvodi za energiju | Aloe Vera Centar',
                    'meta_description' => 'Forever proizvodi za energiju, fokus i dnevnu rutinu: Argi+, Focus, Daily i B12 Plus uz jasan vodič za odabir.',
                    'intro' => 'Kada je tema energija, važno je razlikovati kratki osjećaj poticaja od rutine koja podržava fokus, vitalnost i dosljednost kroz dan.',
                    'path_title' => 'Energija nije uvijek ista potreba',
                    'steps' => ['prepoznaj trebaš li fokus, vitalnost ili dnevnu osnovu', 'usporedi proizvode prema rutini, ne samo prema nazivu', 'ako uzimaš terapiju ili imaš zdravstvene okolnosti, provjeri sa stručnom osobom'],
                    'insights' => [
                        ['label' => 'Dnevni ritam', 'title' => 'Podrška za aktivan dan', 'text' => 'Argi+ i slični proizvodi imaju smisla kada želiš podržati aktivniju rutinu.'],
                        ['label' => 'Fokus', 'title' => 'Kad je tema koncentracija', 'text' => 'Ako želiš jasniji fokus, vrijedi usporediti proizvode koji nisu samo “za energiju”.'],
                        ['label' => 'Osnova', 'title' => 'Vitamini i dnevna podrška', 'text' => 'Nekad je smislenije krenuti od dnevne osnove nego od najjačeg dojma.'],
                    ],
                ],
                'immunity' => [
                    'path' => '/cilj/imunitet/',
                    'eyebrow' => 'Forever proizvodi za imunitet',
                    'title' => 'Sezonska podrška imunitetu bez kompliciranja',
                    'meta_title' => 'Forever proizvodi za imunitet | Aloe Vera Centar',
                    'meta_description' => 'Vodič za Forever proizvode za imunitet: Immublend, vitamin C, vitamin D i biljni dodaci uz odgovoran odabir.',
                    'intro' => 'Kod imuniteta je najbolje krenuti od svakodnevne podrške i sezone u kojoj se nalaziš, bez obećanja i bez pretjeranih tvrdnji.',
                    'path_title' => 'Odaberi prema sezoni i rutini',
                    'steps' => ['pogledaj trebaš li osnovnu ili sezonsku podršku', 'usporedi vitamine, minerale i biljne formule', 'kod terapije, trudnoće ili dijagnoze prvo pitaj stručnu osobu'],
                    'insights' => [
                        ['label' => 'Sezona', 'title' => 'Kad želiš dodatnu podršku', 'text' => 'Immublend i vitamin C često se gledaju kada dolazi zahtjevnije razdoblje.'],
                        ['label' => 'Dnevno', 'title' => 'Ne mora biti komplicirano', 'text' => 'Bolja je jednostavna rutina koju možeš pratiti nego previše proizvoda odjednom.'],
                        ['label' => 'Odgovorno', 'title' => 'Bez medicinskih obećanja', 'text' => 'Dodaci prehrani nisu zamjena za liječenje, ali mogu biti dio urednije rutine.'],
                    ],
                ],
                'care' => [
                    'path' => '/cilj/njega/',
                    'eyebrow' => 'Forever proizvodi za njegu',
                    'title' => 'Aloe njega za kožu, ruke, usnu higijenu i svakodnevicu',
                    'meta_title' => 'Forever proizvodi za njegu | Aloe Vera Centar',
                    'meta_description' => 'Forever proizvodi za njegu: Aloe Vera Gelly, Aloe First, Aloe MSM Gel i Bright Toothgel kroz jednostavan vodič za rutinu.',
                    'intro' => 'Njega je često najlakši ulaz u Forever proizvode jer se rezultat rutine vidi kroz svakodnevnu upotrebu: koža, ruke, osvježenje, usna higijena i praktični proizvodi za dom.',
                    'path_title' => 'Odaberi proizvod prema načinu korištenja',
                    'steps' => ['odredi trebaš li gel, kremu, sprej ili oralnu njegu', 'pogledaj proizvod koji najlakše ulazi u tvoju rutinu', 'ako biraš za osjetljivu kožu, kreni jednostavno'],
                    'insights' => [
                        ['label' => 'Praktično', 'title' => 'Proizvodi koji se brzo uklope', 'text' => 'Njega najbolje radi kada proizvod stvarno koristiš, ne kada stoji u ormariću.'],
                        ['label' => 'Aloe baza', 'title' => 'Gelly, sprej i gelovi', 'text' => 'Aloe proizvodi su čest izbor za jednostavnu vanjsku rutinu.'],
                        ['label' => 'Svakodnevno', 'title' => 'Usna higijena i ruke', 'text' => 'Toothgel i kreme mogu biti najlakši način da osoba isproba Forever rutinu.'],
                    ],
                ],
                'unsure' => [
                    'path' => '/cilj/nisam-siguran/',
                    'eyebrow' => 'Kada nisi siguran',
                    'title' => 'Ne moraš znati proizvod. Dovoljno je opisati cilj.',
                    'meta_title' => 'Koji Forever proizvod odabrati? | Aloe Vera Centar',
                    'meta_description' => 'Ako nisi siguran koji Forever proizvod odabrati, kreni od cilja, navike ili pitanja i dobij jasniji prijedlog.',
                    'intro' => 'Najčešća pogreška je krenuti od previše proizvoda odjednom. Bolje je opisati što želiš postići, što već koristiš i što ti nije jasno.',
                    'path_title' => 'Najjednostavniji način za početak',
                    'steps' => ['napiši jednu rečenicu o tome što želiš podržati', 'dodaj ako već koristiš neki proizvod ili dodatak', 'dobit ćeš prijedlog vodiča, proizvoda ili sljedećeg pitanja'],
                    'insights' => [
                        ['label' => 'Bez pritiska', 'title' => 'Prvo razjasni potrebu', 'text' => 'Ako izbor nije jasan, preporuka treba prvo pomoći razumjeti smjer.'],
                        ['label' => 'Pametnije', 'title' => 'Manje proizvoda, bolji početak', 'text' => 'Jedan dobar prvi korak često vrijedi više od velike košarice.'],
                        ['label' => 'Osobnije', 'title' => 'Tvoja rutina je važna', 'text' => 'Isti proizvod nema isti smisao za svaku osobu i svaku naviku.'],
                    ],
                ],
            ]),
            'en' => $this->mergeShared($shared, [
                'digestion' => ['path' => '/en/goal/digestion/', 'eyebrow' => 'Forever products for digestion', 'title' => 'Start toward calmer digestion without random choices', 'meta_title' => 'Forever products for digestion | Aloe Vera Centar', 'meta_description' => 'A simple guide to Forever products for digestion: aloe drinks, probiotic, fiber and a clearer first step.', 'intro' => 'When digestion is the goal, start with the difference between an aloe drink, probiotic and fiber so the routine stays simple.', 'path_title' => 'Start with the everyday foundation', 'steps' => ['recognize whether the topic is rhythm, bloating, fiber or daily routine', 'compare aloe drink, probiotic and fiber calmly', 'ask before buying if the choice is not clear'], 'insights' => [['label' => 'First step', 'title' => 'Aloe drink routine', 'text' => 'For many visitors, an aloe drink is the simplest daily starting point.'], ['label' => 'Targeted support', 'title' => 'Probiotic or fiber', 'text' => 'When microbiome, meals or satiety are the topic, compare targeted options.'], ['label' => 'Simple', 'title' => 'One clear start', 'text' => 'A clear routine beats too many choices at once.']]],
                'skin' => ['path' => '/en/goal/skin/', 'eyebrow' => 'Forever products for skin', 'title' => 'A simpler routine for skin, glow and daily care', 'meta_title' => 'Forever products for skin | Aloe Vera Centar', 'meta_description' => 'Forever products for skin: collagen, aloe care, hydration and products worth comparing.', 'intro' => 'For skin, the point is not to buy everything. Choose a routine that fits hydration, elasticity, sensitivity, hands, face or simple care.', 'path_title' => 'Define what your skin needs most', 'steps' => ['separate inner support from outer care', 'choose one main goal', 'compare collagen, aloe gel and cream before buying'], 'insights' => [['label' => 'Outside', 'title' => 'Aloe care for skin', 'text' => 'Gelly, creams and gels make sense for simple outer care.'], ['label' => 'Inside', 'title' => 'Collagen routine', 'text' => 'Collagen is usually explored for skin, hair, nails and beauty routines.'], ['label' => 'Sensitive skin', 'title' => 'Fewer products, clearer start', 'text' => 'If skin is sensitive, start carefully and observe the routine.']]],
                'energy' => ['path' => '/en/goal/energy/', 'eyebrow' => 'Forever products for energy', 'title' => 'Energy support for a better daily rhythm', 'meta_title' => 'Forever products for energy | Aloe Vera Centar', 'meta_description' => 'Forever products for energy, focus and daily rhythm: Argi+, Focus, Daily and B12 Plus.', 'intro' => 'When energy is the topic, distinguish a quick boost from a routine that supports focus, vitality and consistency.', 'path_title' => 'Energy can mean different needs', 'steps' => ['recognize whether you need focus, vitality or daily basics', 'compare products by routine, not only by name', 'check with a professional for therapy or health circumstances'], 'insights' => [['label' => 'Active day', 'title' => 'Support for daily rhythm', 'text' => 'Argi+ and similar options fit active routines.'], ['label' => 'Focus', 'title' => 'When concentration is the topic', 'text' => 'Compare products beyond the general “energy” label.'], ['label' => 'Foundation', 'title' => 'Vitamins and daily support', 'text' => 'Sometimes the basics are the smarter first step.']]],
                'immunity' => ['path' => '/en/goal/immunity/', 'eyebrow' => 'Forever products for immunity', 'title' => 'Seasonal immune support without overcomplicating', 'meta_title' => 'Forever products for immunity | Aloe Vera Centar', 'meta_description' => 'Forever immunity products: Immublend, vitamin C, vitamin D and herbal support with responsible guidance.', 'intro' => 'For immunity, start with everyday support and the season you are in, without exaggerated claims.', 'path_title' => 'Choose by season and routine', 'steps' => ['see whether you need basic or seasonal support', 'compare vitamins, minerals and herbal formulas', 'ask a professional for therapy, pregnancy or diagnosis'], 'insights' => [['label' => 'Seasonal', 'title' => 'When you want extra support', 'text' => 'Immublend and vitamin C are often explored before demanding seasons.'], ['label' => 'Daily', 'title' => 'It does not need to be complicated', 'text' => 'A simple routine is easier to keep.'], ['label' => 'Responsible', 'title' => 'No medical promises', 'text' => 'Supplements are not treatment, but can be part of a better routine.']]],
                'care' => ['path' => '/en/goal/care/', 'eyebrow' => 'Forever products for care', 'title' => 'Aloe care for skin, hands, oral hygiene and daily use', 'meta_title' => 'Forever care products | Aloe Vera Centar', 'meta_description' => 'Forever care products: Aloe Vera Gelly, Aloe First, Aloe MSM Gel and Bright Toothgel.', 'intro' => 'Care is often the easiest way to start because the product fits visible everyday use: skin, hands, freshness, oral hygiene and home routines.', 'path_title' => 'Choose by how you will use it', 'steps' => ['decide whether you need gel, cream, spray or oral care', 'open the product that fits your routine most easily', 'start simply with sensitive skin'], 'insights' => [['label' => 'Practical', 'title' => 'Products that fit quickly', 'text' => 'Care works best when the product is actually used.'], ['label' => 'Aloe base', 'title' => 'Gelly, spray and gels', 'text' => 'Aloe products are common choices for simple outer care.'], ['label' => 'Daily', 'title' => 'Oral care and hands', 'text' => 'Toothgel and creams can be an easy Forever first step.']]],
                'unsure' => ['path' => '/en/goal/not-sure/', 'eyebrow' => 'When you are not sure', 'title' => 'You do not need the product name. Describe the goal.', 'meta_title' => 'Which Forever product should I choose? | Aloe Vera Centar', 'meta_description' => 'If you are not sure which Forever product to choose, start with your goal and get a clearer suggestion.', 'intro' => 'The common mistake is starting with too many products. It is better to describe your goal, current routine and doubt.', 'path_title' => 'The simplest way to start', 'steps' => ['write one sentence about what you want to support', 'add what you already use if relevant', 'get a product, guide or next-question suggestion'], 'insights' => [['label' => 'Clearer', 'title' => 'Start with the need', 'text' => 'If the choice is not clear, guidance should clarify the direction first.'], ['label' => 'Smarter', 'title' => 'Fewer products, better start', 'text' => 'One good first step is often better than a large basket.'], ['label' => 'Personal', 'title' => 'Your routine matters', 'text' => 'The same product does not mean the same thing for every person.']]],
            ]),
            'sl' => $this->mergeShared($shared, [
                'digestion' => ['path' => '/sl/cilj/prebava/', 'eyebrow' => 'Forever izdelki za prebavo', 'title' => 'Začni k mirnejši prebavi brez naključnega izbora', 'meta_title' => 'Forever izdelki za prebavo | Aloe Vera Centar', 'meta_description' => 'Vodnik po Forever izdelkih za prebavo: aloe napitki, probiotik, vlaknine in jasnejši prvi korak.', 'intro' => 'Če želiš podpreti vsakodnevno prebavo, začni preprosto: razumi razliko med aloe napitkom, probiotikom in vlakninami.', 'path_title' => 'Najpogosteje je dobro začeti z osnovo', 'steps' => ['prepoznaj, ali je tema ritem, napihnjenost, vlaknine ali dnevna rutina', 'mirno primerjaj aloe napitek, probiotik in vlaknine', 'vprašaj pred nakupom, če nisi prepričan'], 'insights' => [['label' => 'Prvi izbor', 'title' => 'Aloe napitek za rutino', 'text' => 'Za mnoge je aloe napitek najpreprostejši začetek.'], ['label' => 'Dodatno', 'title' => 'Probiotik ali vlaknine', 'text' => 'Ko so tema mikrobiom, prehrana ali sitost, primerjaj ciljne dodatke.'], ['label' => 'Preprosto', 'title' => 'En jasen začetek', 'text' => 'Jasna rutina je boljša kot preveč možnosti naenkrat.']]],
                'skin' => ['path' => '/sl/cilj/koza/', 'eyebrow' => 'Forever izdelki za kožo', 'title' => 'Preprostejša rutina za kožo, sijaj in vsakodnevno nego', 'meta_title' => 'Forever izdelki za kožo | Aloe Vera Centar', 'meta_description' => 'Vodnik po Forever izdelkih za kožo: kolagen, aloe nega, hidracija in izdelki za primerjavo.', 'intro' => 'Pri koži ni cilj kupiti vsega, ampak izbrati rutino za hidratacijo, elastičnost, občutljivost, roke, obraz ali preprosto nego.', 'path_title' => 'Najprej določi, kaj koža potrebuje', 'steps' => ['ločuj notranjo podporo od zunanje nege', 'izberi en glavni cilj', 'primerjaj kolagen, aloe gel in kremo'], 'insights' => [['label' => 'Zunaj', 'title' => 'Aloe nega za kožo', 'text' => 'Gelly, kreme in geli so smiselni za preprosto zunanjo nego.'], ['label' => 'Znotraj', 'title' => 'Kolagen kot rutina', 'text' => 'Kolagen se pogosto izbira za kožo, lase, nohte in beauty rutino.'], ['label' => 'Občutljiva koža', 'title' => 'Manj izdelkov, jasnejši začetek', 'text' => 'Pri občutljivi koži začni previdno.']]],
                'energy' => ['path' => '/sl/cilj/energija/', 'eyebrow' => 'Forever izdelki za energijo', 'title' => 'Podpora energiji za boljši dnevni ritem', 'meta_title' => 'Forever izdelki za energijo | Aloe Vera Centar', 'meta_description' => 'Forever izdelki za energijo, fokus in dnevno rutino: Argi+, Focus, Daily in B12 Plus.', 'intro' => 'Ko je tema energija, loči kratek občutek spodbude od rutine za fokus, vitalnost in doslednost.', 'path_title' => 'Energija ni vedno ista potreba', 'steps' => ['prepoznaj, ali potrebuješ fokus, vitalnost ali dnevno osnovo', 'primerjaj izdelke po rutini, ne samo po imenu', 'pri terapiji ali zdravstvenih okoliščinah vprašaj strokovnjaka'], 'insights' => [['label' => 'Aktiven dan', 'title' => 'Podpora dnevnemu ritmu', 'text' => 'Argi+ in podobne možnosti se ujemajo z aktivno rutino.'], ['label' => 'Fokus', 'title' => 'Ko je tema koncentracija', 'text' => 'Primerjaj izdelke širše od oznake energija.'], ['label' => 'Osnova', 'title' => 'Vitamini in dnevna podpora', 'text' => 'Včasih je osnova boljši prvi korak.']]],
                'immunity' => ['path' => '/sl/cilj/imunost/', 'eyebrow' => 'Forever izdelki za imunost', 'title' => 'Sezonska podpora imunosti brez zapletanja', 'meta_title' => 'Forever izdelki za imunost | Aloe Vera Centar', 'meta_description' => 'Vodnik po Forever izdelkih za imunost: Immublend, vitamin C, vitamin D in odgovorna izbira.', 'intro' => 'Pri imunosti začni s vsakodnevno podporo in sezono, brez pretiranih obljub.', 'path_title' => 'Izberi po sezoni in rutini', 'steps' => ['poglej, ali potrebuješ osnovno ali sezonsko podporo', 'primerjaj vitamine, minerale in zeliščne formule', 'pri terapiji, nosečnosti ali diagnozi najprej vprašaj strokovnjaka'], 'insights' => [['label' => 'Sezona', 'title' => 'Ko želiš dodatno podporo', 'text' => 'Immublend in vitamin C se pogosto izbirata pred zahtevnejšim obdobjem.'], ['label' => 'Dnevno', 'title' => 'Ni treba zapletati', 'text' => 'Preprosta rutina je lažja za spremljanje.'], ['label' => 'Odgovorno', 'title' => 'Brez medicinskih obljub', 'text' => 'Dodatki niso zdravljenje, lahko pa so del bolj urejene rutine.']]],
                'care' => ['path' => '/sl/cilj/nega/', 'eyebrow' => 'Forever izdelki za nego', 'title' => 'Aloe nega za kožo, roke, ustno higieno in vsakdan', 'meta_title' => 'Forever izdelki za nego | Aloe Vera Centar', 'meta_description' => 'Forever izdelki za nego: Aloe Vera Gelly, Aloe First, Aloe MSM Gel in Bright Toothgel.', 'intro' => 'Nega je pogosto najlažji vstop v Forever izdelke, ker se rutina vidi v vsakodnevni uporabi: koža, roke, svežina, ustna higiena in dom.', 'path_title' => 'Izberi po načinu uporabe', 'steps' => ['določi, ali potrebuješ gel, kremo, sprej ali ustno nego', 'poglej izdelek, ki najlažje vstopi v rutino', 'pri občutljivi koži začni preprosto'], 'insights' => [['label' => 'Praktično', 'title' => 'Izdelki, ki se hitro vključijo', 'text' => 'Nega deluje, ko izdelek res uporabljaš.'], ['label' => 'Aloe osnova', 'title' => 'Gelly, sprej in geli', 'text' => 'Aloe izdelki so pogosta izbira za zunanjo nego.'], ['label' => 'Vsakodnevno', 'title' => 'Ustna higiena in roke', 'text' => 'Toothgel in kreme so lahek prvi Forever korak.']]],
                'unsure' => ['path' => '/sl/cilj/nisem-preprican/', 'eyebrow' => 'Ko nisi prepričan', 'title' => 'Ni ti treba poznati izdelka. Dovolj je opisati cilj.', 'meta_title' => 'Kateri Forever izdelek izbrati? | Aloe Vera Centar', 'meta_description' => 'Če nisi prepričan, kateri Forever izdelek izbrati, začni s ciljem in prejmi jasnejši predlog.', 'intro' => 'Najpogostejša napaka je začeti s preveč izdelki. Bolje je opisati cilj, rutino in dvom.', 'path_title' => 'Najpreprostejši začetek', 'steps' => ['napiši en stavek o tem, kaj želiš podpreti', 'dodaj, kaj že uporabljaš, če je pomembno', 'prejmi predlog izdelka, vodiča ali naslednjega vprašanja'], 'insights' => [['label' => 'Jasneje', 'title' => 'Najprej razjasni potrebo', 'text' => 'Če izbira ni jasna, naj priporočilo najprej razjasni smer.'], ['label' => 'Pametneje', 'title' => 'Manj izdelkov, boljši začetek', 'text' => 'En dober prvi korak je pogosto boljši od velike košarice.'], ['label' => 'Osebno', 'title' => 'Tvoja rutina je pomembna', 'text' => 'Isti izdelek nima istega pomena za vsako osebo.']]],
            ]),
        ];
    }

    private function mergeShared(array $shared, array $localized): array
    {
        foreach ($localized as $key => $definition) {
            $definition += [
                'primary_cta' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'View products',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Poglej izdelke',
                    default => 'Pogledaj proizvode',
                },
                'secondary_cta' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Ask for guidance',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Vprašaj za priporočilo',
                    default => 'Pitaj za preporuku',
                },
                'path_label' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'How to start',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Kako začeti',
                    default => 'Kako krenuti',
                },
                'products_eyebrow' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Suggested products',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Predlagani izdelki',
                    default => 'Preporučeni proizvodi',
                },
                'products_title' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Products worth comparing',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Izdelki, ki jih je vredno primerjati',
                    default => 'Proizvodi koje vrijedi usporediti',
                },
                'products_text' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Open the guide first, then continue to the official shop when the choice feels clear.',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Najprej odpri vodič, nato nadaljuj v uradni shop, ko je izbira jasna.',
                    default => 'Prvo otvori vodič, a zatim nastavi prema službenom shopu kada ti izbor ima smisla.',
                },
                'advisor_label' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Personal guidance',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Osebno priporočilo',
                    default => 'Osobnija preporuka',
                },
                'advisor_title' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Still choosing between products?',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Še izbiraš med izdelki?',
                    default => 'Još biraš između više proizvoda?',
                },
                'advisor_text' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Write one sentence about your goal and get a clearer next step before you order.',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Napiši en stavek o cilju in dobi jasnejši naslednji korak pred naročilom.',
                    default => 'Napiši jednu rečenicu o cilju i dobit ćeš jasniji sljedeći korak prije narudžbe.',
                },
                'advisor_button' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Ask for guidance',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Vprašaj za priporočilo',
                    default => 'Zatraži preporuku',
                },
                'catalog_button' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'All products',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Vsi izdelki',
                    default => 'Svi proizvodi',
                },
                'articles_eyebrow' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Before deciding',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Pred odločitvijo',
                    default => 'Prije odluke',
                },
                'articles_title' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Useful reading for this goal',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Koristno branje za ta cilj',
                    default => 'Korisno za pročitati uz ovaj cilj',
                },
                'articles_text' => match (true) {
                    str_starts_with((string) ($definition['path'] ?? ''), '/en/') => 'Short explanations help you compare products with more confidence.',
                    str_starts_with((string) ($definition['path'] ?? ''), '/sl/') => 'Kratke razlage pomagajo primerjati izdelke z več zaupanja.',
                    default => 'Kratka objašnjenja pomažu da proizvod usporediš s više sigurnosti.',
                },
            ];

            $localized[$key] = array_merge($shared[$key] ?? [], $definition);
        }

        return $localized;
    }

    private function absoluteUrl(string $path): string
    {
        return rtrim((string) ($this->config['base_url'] ?? ''), '/') . ($path !== '' ? $path : '/');
    }

    private function locale(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'en_US',
            'sl' => 'sl_SI',
            default => 'hr_HR',
        };
    }

    private function displayText(string $value): string
    {
        return trim(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function plain(string $value): string
    {
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';

        return trim($value);
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
