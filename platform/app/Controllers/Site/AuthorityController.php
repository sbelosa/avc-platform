<?php

declare(strict_types=1);

namespace Avc\Controllers\Site;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Support\PageRenderer;

final class AuthorityController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function about(): never
    {
        $this->render('about');
    }

    public function recommendations(): never
    {
        $this->render('recommendations');
    }

    public function editorial(): never
    {
        $this->render('editorial');
    }

    public function redirectLegacyAbout(): never
    {
        $this->response->redirect((string) ($this->copy($this->languageFromPath(), 'about')['path'] ?? '/o-nama/'), 301);
    }

    private function render(string $page): never
    {
        $languageCode = $this->languageFromPath();
        $copy = $this->copy($languageCode, $page);
        $canonicalPath = (string) ($copy['path'] ?? '/');
        $canonicalUrl = $this->absoluteUrl($canonicalPath);
        $title = (string) ($copy['meta_title'] ?? $copy['title'] ?? 'Aloe Vera Centar');
        $description = (string) ($copy['meta_description'] ?? $copy['intro'] ?? '');

        $body = $this->header($languageCode)
            . '<main class="shell layout layout-single authority-page">'
            . '<article class="content-card content-prose">'
            . '<span class="hero-kicker">' . $this->e((string) ($copy['eyebrow'] ?? 'Aloe Vera Centar')) . '</span>'
            . '<h1>' . $this->e((string) ($copy['title'] ?? 'Aloe Vera Centar')) . '</h1>'
            . '<p class="lead">' . $this->e((string) ($copy['intro'] ?? '')) . '</p>'
            . $this->sections((array) ($copy['sections'] ?? []))
            . '</article>'
            . '</main>'
            . $this->footer($languageCode);

        $this->response->html(PageRenderer::render($title, $body, [
            'lang' => $languageCode,
            'meta_description' => $description,
            'canonical_url' => $canonicalUrl,
            'alternate_links' => $this->alternateLinks($page),
            'body_class' => 'site-authority',
            'open_graph' => [
                'type' => 'website',
                'site_name' => 'Aloe Vera Centar',
                'title' => $title,
                'description' => $description,
                'url' => $canonicalUrl,
                'locale' => $this->locale($languageCode),
            ],
            'schema_json' => json_encode([
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'WebPage',
                        'name' => $title,
                        'description' => $description,
                        'url' => $canonicalUrl,
                        'inLanguage' => $languageCode,
                        'isPartOf' => [
                            '@type' => 'WebSite',
                            'name' => 'Aloe Vera Centar',
                            'url' => $this->absoluteUrl('/'),
                        ],
                    ],
                    [
                        '@type' => 'Organization',
                        'name' => 'Aloe Vera Centar',
                        'url' => $this->absoluteUrl('/'),
                        'description' => 'Aloe Vera Centar pomaže korisnicima razumjeti i odabrati Forever Living Products proizvode.',
                    ],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]));
    }

    private function sections(array $sections): string
    {
        $html = '';

        foreach ($sections as $section) {
            $html .= '<section class="authority-section"><h2>' . $this->e((string) ($section['title'] ?? '')) . '</h2>';
            foreach ((array) ($section['paragraphs'] ?? []) as $paragraph) {
                $html .= '<p>' . $this->e((string) $paragraph) . '</p>';
            }
            if (!empty($section['items'])) {
                $html .= '<ul>';
                foreach ((array) $section['items'] as $item) {
                    $html .= '<li>' . $this->e((string) $item) . '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</section>';
        }

        return $html;
    }

    private function header(string $languageCode): string
    {
        $paths = $this->navigationPaths($languageCode);

        return '<header class="site-header"><div class="header-card">'
            . '<a class="brand" href="' . $this->e($paths['home']) . '"><span class="brand-lockup"><img class="brand-logo" src="/media/branding/aloe-vera-centar-logo-horizontal.png" alt="Aloe Vera Centar" loading="eager" decoding="async"><span class="brand-copy"><strong class="brand-name">Aloe Vera Centar</strong><span class="brand-tagline">' . $this->e($this->navigationCopy($languageCode, 'tagline')) . '</span></span></span></a>'
            . '<nav class="header-links"><a href="' . $this->e($paths['home']) . '">' . $this->e($this->navigationCopy($languageCode, 'home')) . '</a><a href="' . $this->e($paths['products']) . '">' . $this->e($this->navigationCopy($languageCode, 'products')) . '</a><a href="' . $this->e($paths['articles']) . '">' . $this->e($this->navigationCopy($languageCode, 'articles')) . '</a><a href="' . $this->e($paths['support']) . '">' . $this->e($this->navigationCopy($languageCode, 'support')) . '</a></nav>'
            . '</div></header>';
    }

    private function footer(string $languageCode): string
    {
        $links = $this->authorityLinks($languageCode);
        $html = '<footer class="site-footer"><div class="content-card"><strong>Aloe Vera Centar</strong><p class="muted">' . $this->e($this->navigationCopy($languageCode, 'footer')) . '</p><div class="footer-links">';

        foreach ($links as $label => $path) {
            $html .= '<a href="' . $this->e($path) . '">' . $this->e($label) . '</a>';
        }

        return $html . '</div></div></footer>';
    }

    private function languageFromPath(): string
    {
        $path = (string) $this->request->path();
        if (str_starts_with($path, '/en/')) {
            return 'en';
        }
        if (str_starts_with($path, '/sl/')) {
            return 'sl';
        }

        return 'hr';
    }

    private function navigationPaths(string $languageCode): array
    {
        return match ($languageCode) {
            'en' => [
                'home' => '/en/',
                'products' => '/en/forever-products/',
                'articles' => '/en/articles/',
                'support' => '/en/#ai-advisor',
            ],
            'sl' => [
                'home' => '/sl/',
                'products' => '/sl/forever-izdelki/',
                'articles' => '/sl/clanki/',
                'support' => '/sl/#ai-advisor',
            ],
            default => [
                'home' => '/',
                'products' => '/forever-proizvodi/',
                'articles' => '/clanci/',
                'support' => '/#ai-advisor',
            ],
        };
    }

    private function navigationCopy(string $languageCode, string $key): string
    {
        $copy = [
            'hr' => [
                'tagline' => 'Jasniji izbor Forever proizvoda',
                'home' => 'Naslovnica',
                'products' => 'Proizvodi',
                'articles' => 'Članci',
                'support' => 'Preporuka',
                'footer' => 'Sadržaj je edukativan i pomaže u odabiru proizvoda, bez medicinskih dijagnoza ili pritiska na kupnju.',
            ],
            'en' => [
                'tagline' => 'A clearer Forever product choice',
                'home' => 'Home',
                'products' => 'Products',
                'articles' => 'Articles',
                'support' => 'Recommendation',
                'footer' => 'The content is educational and helps with product choice without medical diagnosis or pressure to buy.',
            ],
            'sl' => [
                'tagline' => 'Jasnejša izbira Forever izdelkov',
                'home' => 'Domov',
                'products' => 'Izdelki',
                'articles' => 'Članki',
                'support' => 'Priporočilo',
                'footer' => 'Vsebina je izobraževalna in pomaga pri izbiri izdelkov, brez medicinskih diagnoz ali pritiska k nakupu.',
            ],
        ];

        return (string) ($copy[$languageCode][$key] ?? $copy['hr'][$key] ?? '');
    }

    private function authorityLinks(string $languageCode): array
    {
        return match ($languageCode) {
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

    private function alternateLinks(string $page): array
    {
        $links = [];
        foreach (['hr', 'en', 'sl'] as $languageCode) {
            $copy = $this->copy($languageCode, $page);
            $links[] = [
                'hreflang' => $languageCode,
                'href' => $this->absoluteUrl((string) ($copy['path'] ?? '/')),
            ];
        }

        $links[] = [
            'hreflang' => 'x-default',
            'href' => $this->absoluteUrl((string) ($this->copy('hr', $page)['path'] ?? '/')),
        ];

        return $links;
    }

    private function copy(string $languageCode, string $page): array
    {
        $copies = $this->copies();

        return $copies[$languageCode][$page] ?? $copies['hr'][$page];
    }

    private function copies(): array
    {
        return [
            'hr' => [
                'about' => [
                    'path' => '/o-nama/',
                    'eyebrow' => 'O Aloe Vera Centru',
                    'title' => 'Pomažemo ti razumjeti Forever proizvode prije odluke',
                    'meta_title' => 'O nama | Aloe Vera Centar',
                    'meta_description' => 'Aloe Vera Centar pomaže korisnicima razumjeti Forever Living Products proizvode, usporediti opcije i sigurnije napraviti sljedeći korak.',
                    'intro' => 'Aloe Vera Centar je vodič za ljude koji žele lakše razumjeti Forever Living Products proizvode i odabrati ono što ima smisla za njihove potrebe.',
                    'sections' => [
                        [
                            'title' => 'Zašto postoji AVC',
                            'paragraphs' => [
                                'Mnogi korisnici ne žele krenuti od kataloga, nego od stvarne situacije: probava, koža, energija, imunitet, njega ili nedoumica između više proizvoda.',
                                'Zato AVC spaja članke, product guide stranice, preporuke i siguran nastavak prema službenom Forever Living Products shopu.',
                            ],
                        ],
                        [
                            'title' => 'Kako pomažemo korisniku',
                            'items' => [
                                'objašnjavamo za koga pojedini proizvod ima smisla',
                                'povezujemo proizvode s temama i rutinama',
                                'nudimo preporuku kada korisnik nije siguran',
                                'vodimo prema službenom Forever shopu u odgovarajućoj zemlji',
                            ],
                        ],
                    ],
                ],
                'recommendations' => [
                    'path' => '/kako-rade-preporuke/',
                    'eyebrow' => 'Metodologija',
                    'title' => 'Kako radimo preporuke Forever proizvoda',
                    'meta_title' => 'Kako rade preporuke | Aloe Vera Centar',
                    'meta_description' => 'Saznaj kako Aloe Vera Centar povezuje potrebe korisnika, članke i Forever proizvode u jasnije preporuke bez medicinskih tvrdnji.',
                    'intro' => 'Preporuke na AVC-u nisu zamišljene kao automatsko guranje jednog proizvoda, nego kao pomoć da korisnik prepozna najbliži smisleni sljedeći korak.',
                    'sections' => [
                        [
                            'title' => 'Što uzimamo u obzir',
                            'items' => [
                                'cilj korisnika ili tema koju čita',
                                'jezik, tržište i dostupne službene shop linkove',
                                'slične proizvode koje vrijedi usporediti',
                                'jasnu granicu između edukacije i medicinskog savjeta',
                            ],
                        ],
                        [
                            'title' => 'Što ne radimo',
                            'paragraphs' => [
                                'Ne postavljamo dijagnoze, ne obećavamo terapijski učinak i ne zamjenjujemo liječnika. Ako korisnik opisuje ozbiljan ili dugotrajan problem, sadržaj treba shvatiti kao opću edukaciju i poticaj da potraži stručan savjet.',
                            ],
                        ],
                    ],
                ],
                'editorial' => [
                    'path' => '/urednicka-politika/',
                    'eyebrow' => 'Urednička politika',
                    'title' => 'Kako pišemo i uređujemo sadržaj',
                    'meta_title' => 'Urednička politika | Aloe Vera Centar',
                    'meta_description' => 'Urednička politika Aloe Vera Centra: jasan, koristan i odgovoran sadržaj koji pomaže korisnicima razumjeti Forever proizvode.',
                    'intro' => 'Cilj sadržaja na AVC-u je pomoći korisniku da razumije temu, prepozna opcije i odluči mirnije, bez generičkog SEO teksta i bez pretjeranih tvrdnji.',
                    'sections' => [
                        [
                            'title' => 'Načela sadržaja',
                            'items' => [
                                'pišemo za stvarne korisnike, ne samo za tražilice',
                                'koristimo jasne naslove, sažetke, FAQ blokove i povezane proizvode',
                                'odvajamo edukaciju od kupovnog koraka',
                                'redovito mjerimo koji sadržaji vode prema korisnim akcijama',
                            ],
                        ],
                        [
                            'title' => 'Ažuriranja i odgovornost',
                            'paragraphs' => [
                                'Kada se mijenjaju proizvodi, cijene, dostupnost ili službeni shop linkovi, AVC nastoji ažurirati sadržaj i routing kako bi korisnik završio na ispravnom mjestu.',
                            ],
                        ],
                    ],
                ],
            ],
            'en' => [
                'about' => [
                    'path' => '/en/about/',
                    'eyebrow' => 'About Aloe Vera Centar',
                    'title' => 'We help people understand Forever products before choosing',
                    'meta_title' => 'About | Aloe Vera Centar',
                    'meta_description' => 'Aloe Vera Centar helps visitors understand Forever Living Products, compare options and take a clearer next step.',
                    'intro' => 'Aloe Vera Centar is a guide for people who want to understand Forever Living Products and choose what makes sense for their needs.',
                    'sections' => [
                        ['title' => 'Why AVC exists', 'paragraphs' => ['Many visitors start from a real situation, not a catalogue: digestion, skin, energy, immunity, care or uncertainty between products.', 'AVC connects articles, product guides, recommendations and a safe next step to the official Forever Living Products shop.']],
                        ['title' => 'How we help', 'items' => ['explain when a product may make sense', 'connect products with topics and routines', 'offer guidance when the visitor is unsure', 'route visitors to the official Forever shop for their country']],
                    ],
                ],
                'recommendations' => [
                    'path' => '/en/how-recommendations-work/',
                    'eyebrow' => 'Method',
                    'title' => 'How Forever product recommendations work',
                    'meta_title' => 'How recommendations work | Aloe Vera Centar',
                    'meta_description' => 'How Aloe Vera Centar connects visitor needs, articles and Forever products into clearer recommendations without medical claims.',
                    'intro' => 'AVC recommendations are designed to help visitors recognize the most sensible next step, not to push one product automatically.',
                    'sections' => [
                        ['title' => 'What we consider', 'items' => ['the visitor goal or topic', 'language, market and official shop links', 'similar products worth comparing', 'the boundary between education and medical advice']],
                        ['title' => 'What we do not do', 'paragraphs' => ['We do not diagnose, promise therapeutic effects or replace professional advice. For serious or persistent issues, content should be treated as general education.']],
                    ],
                ],
                'editorial' => [
                    'path' => '/en/editorial-policy/',
                    'eyebrow' => 'Editorial policy',
                    'title' => 'How we write and improve content',
                    'meta_title' => 'Editorial policy | Aloe Vera Centar',
                    'meta_description' => 'Aloe Vera Centar editorial policy: clear, useful and responsible content that helps visitors understand Forever products.',
                    'intro' => 'AVC content should help a visitor understand the topic, compare options and decide calmly.',
                    'sections' => [
                        ['title' => 'Content principles', 'items' => ['write for real people, not only search engines', 'use clear summaries, headings, FAQ blocks and related products', 'separate education from the purchase step', 'measure which pages lead to useful actions']],
                        ['title' => 'Updates', 'paragraphs' => ['When products, availability or official shop links change, AVC aims to update content and routing so visitors land in the right place.']],
                    ],
                ],
            ],
            'sl' => [
                'about' => [
                    'path' => '/sl/o-nas/',
                    'eyebrow' => 'O Aloe Vera Centru',
                    'title' => 'Pomagamo razumeti Forever izdelke pred izbiro',
                    'meta_title' => 'O nas | Aloe Vera Centar',
                    'meta_description' => 'Aloe Vera Centar pomaga obiskovalcem razumeti Forever Living Products izdelke, primerjati možnosti in narediti jasnejši naslednji korak.',
                    'intro' => 'Aloe Vera Centar je vodnik za ljudi, ki želijo lažje razumeti Forever Living Products izdelke in izbrati, kar ima smisel za njihove potrebe.',
                    'sections' => [
                        ['title' => 'Zakaj obstaja AVC', 'paragraphs' => ['Veliko obiskovalcev začne pri resnični situaciji, ne pri katalogu: prebava, koža, energija, imuniteta, nega ali izbira med več izdelki.', 'AVC povezuje članke, product guide strani, priporočila in varen korak do uradnega Forever Living Products shopa.']],
                        ['title' => 'Kako pomagamo', 'items' => ['pojasnimo, kdaj ima izdelek smisel', 'povezujemo izdelke s temami in rutinami', 'ponudimo priporočilo, ko obiskovalec ni prepričan', 'vodimo do uradnega Forever shopa za pravo državo']],
                    ],
                ],
                'recommendations' => [
                    'path' => '/sl/kako-delujejo-priporocila/',
                    'eyebrow' => 'Metoda',
                    'title' => 'Kako delujejo priporočila Forever izdelkov',
                    'meta_title' => 'Kako delujejo priporočila | Aloe Vera Centar',
                    'meta_description' => 'Kako Aloe Vera Centar povezuje potrebe obiskovalcev, članke in Forever izdelke v jasnejša priporočila brez medicinskih trditev.',
                    'intro' => 'Priporočila AVC so zasnovana kot pomoč pri prepoznavanju smiselnega naslednjega koraka.',
                    'sections' => [
                        ['title' => 'Kaj upoštevamo', 'items' => ['cilj obiskovalca ali temo', 'jezik, trg in uradne shop povezave', 'podobne izdelke za primerjavo', 'mejo med izobraževanjem in medicinskim nasvetom']],
                        ['title' => 'Česa ne počnemo', 'paragraphs' => ['Ne postavljamo diagnoz, ne obljubljamo terapevtskih učinkov in ne nadomeščamo strokovnega nasveta.']],
                    ],
                ],
                'editorial' => [
                    'path' => '/sl/uredniska-politika/',
                    'eyebrow' => 'Uredniška politika',
                    'title' => 'Kako pišemo in izboljšujemo vsebino',
                    'meta_title' => 'Uredniška politika | Aloe Vera Centar',
                    'meta_description' => 'Uredniška politika Aloe Vera Centra: jasna, uporabna in odgovorna vsebina za razumevanje Forever izdelkov.',
                    'intro' => 'Vsebina AVC naj obiskovalcu pomaga razumeti temo, primerjati možnosti in se mirneje odločiti.',
                    'sections' => [
                        ['title' => 'Načela vsebine', 'items' => ['pišemo za resnične ljudi, ne samo za iskalnike', 'uporabljamo jasne povzetke, naslove, FAQ bloke in povezane izdelke', 'ločimo izobraževanje od nakupa', 'merimo, katere strani vodijo do koristnih akcij']],
                        ['title' => 'Posodobitve', 'paragraphs' => ['Ko se spremenijo izdelki, razpoložljivost ali uradne shop povezave, AVC poskuša posodobiti vsebino in routing.']],
                    ],
                ],
            ],
        ];
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

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
