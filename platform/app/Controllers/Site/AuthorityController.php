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

    public function contact(): never
    {
        $this->render('contact');
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
        $webPageType = $page === 'contact' ? 'ContactPage' : 'WebPage';

        $body = $this->header($languageCode)
            . '<main class="shell trust-page trust-page-' . $this->e($page) . '">'
            . '<section class="trust-hero-panel">'
            . '<div class="trust-hero-copy content-prose">'
            . '<span class="hero-kicker">' . $this->e((string) ($copy['eyebrow'] ?? 'Aloe Vera Centar')) . '</span>'
            . '<h1>' . $this->e((string) ($copy['title'] ?? 'Aloe Vera Centar')) . '</h1>'
            . '<p class="lead">' . $this->e((string) ($copy['intro'] ?? '')) . '</p>'
            . $this->actions((array) ($copy['actions'] ?? []))
            . '</div>'
            . '<aside class="trust-hero-note">'
            . '<span>' . $this->e((string) ($copy['note_label'] ?? 'Dobro je znati')) . '</span>'
            . '<strong>' . $this->e((string) ($copy['note_title'] ?? '')) . '</strong>'
            . '<p>' . $this->e((string) ($copy['note_text'] ?? '')) . '</p>'
            . '</aside>'
            . '</section>'
            . $this->quickCards((array) ($copy['cards'] ?? []))
            . $this->sections((array) ($copy['sections'] ?? []))
            . $this->cta((array) ($copy['cta'] ?? []))
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
                        '@type' => $webPageType,
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
                        'legalName' => 'BS International',
                        'url' => $this->absoluteUrl('/'),
                        'email' => 'info@aloevera-centar.com',
                        'taxID' => '15435222026',
                        'vatID' => '15435222026',
                        'address' => [
                            '@type' => 'PostalAddress',
                            'streetAddress' => 'Ivana Gorana Kovačića 15',
                            'postalCode' => '10408',
                            'addressLocality' => 'Velika Mlaka',
                            'addressCountry' => 'HR',
                        ],
                        'contactPoint' => [
                            '@type' => 'ContactPoint',
                            'email' => 'info@aloevera-centar.com',
                            'contactType' => 'customer support',
                            'availableLanguage' => ['Croatian', 'English', 'Slovenian'],
                        ],
                        'description' => 'Aloe Vera Centar pomaže korisnicima razumjeti i odabrati Forever Living Products proizvode.',
                    ],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]));
    }

    private function sections(array $sections): string
    {
        if ($sections === []) {
            return '';
        }

        $html = '<section class="trust-section-grid">';

        foreach ($sections as $section) {
            $html .= '<article class="trust-section-card content-prose"><h2>' . $this->e((string) ($section['title'] ?? '')) . '</h2>';
            foreach ((array) ($section['paragraphs'] ?? []) as $paragraph) {
                $html .= '<p>' . $this->e((string) $paragraph) . '</p>';
            }
            if (!empty($section['items'])) {
                $html .= '<ul class="trust-list">';
                foreach ((array) $section['items'] as $item) {
                    $html .= '<li>' . $this->e((string) $item) . '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</article>';
        }

        return $html . '</section>';
    }

    private function quickCards(array $cards): string
    {
        if ($cards === []) {
            return '';
        }

        $html = '<section class="trust-card-grid">';

        foreach ($cards as $card) {
            $html .= '<article class="trust-card">'
                . '<span>' . $this->e((string) ($card['label'] ?? '')) . '</span>'
                . '<strong>' . $this->e((string) ($card['title'] ?? '')) . '</strong>'
                . '<p>' . $this->e((string) ($card['text'] ?? '')) . '</p>'
                . '</article>';
        }

        return $html . '</section>';
    }

    private function cta(array $cta): string
    {
        if ($cta === []) {
            return '';
        }

        return '<section class="trust-cta-band">'
            . '<div><span class="eyebrow">' . $this->e((string) ($cta['label'] ?? '')) . '</span>'
            . '<strong>' . $this->e((string) ($cta['title'] ?? '')) . '</strong>'
            . '<p>' . $this->e((string) ($cta['text'] ?? '')) . '</p></div>'
            . $this->actions((array) ($cta['actions'] ?? []))
            . '</section>';
    }

    private function actions(array $actions): string
    {
        if ($actions === []) {
            return '';
        }

        $html = '<div class="trust-actions">';

        foreach ($actions as $index => $action) {
            $class = $index === 0 ? 'button button-primary' : 'button button-secondary';
            $html .= '<a class="' . $class . '" href="' . $this->e((string) ($action['href'] ?? '#')) . '">' . $this->e((string) ($action['label'] ?? '')) . '</a>';
        }

        return $html . '</div>';
    }

    private function header(string $languageCode): string
    {
        $paths = $this->navigationPaths($languageCode);

        return '<header class="site-header"><div class="header-card">'
            . '<a class="brand" href="' . $this->e($paths['home']) . '"><span class="brand-lockup"><img class="brand-logo" src="/media/branding/aloe-vera-centar-logo-horizontal.png" alt="Aloe Vera Centar" loading="eager" decoding="async"><span class="brand-copy"><strong class="brand-name">Aloe Vera Centar</strong><span class="brand-tagline">' . $this->e($this->navigationCopy($languageCode, 'tagline')) . '</span></span></span></a>'
            . '<nav class="header-links"><a href="' . $this->e($paths['home']) . '">' . $this->e($this->navigationCopy($languageCode, 'home')) . '</a><a href="' . $this->e($paths['products']) . '">' . $this->e($this->navigationCopy($languageCode, 'products')) . '</a><a href="' . $this->e($paths['articles']) . '">' . $this->e($this->navigationCopy($languageCode, 'articles')) . '</a><a href="' . $this->e($paths['support']) . '">' . $this->e($this->navigationCopy($languageCode, 'support')) . '</a><a href="' . $this->e($paths['contact']) . '">' . $this->e($this->navigationCopy($languageCode, 'contact')) . '</a></nav>'
            . '</div></header>';
    }

    private function footer(string $languageCode): string
    {
        $links = $this->authorityLinks($languageCode);
        $html = '<footer class="site-footer"><div class="shell"><div class="content-card"><strong>Aloe Vera Centar</strong><p class="muted">' . $this->e($this->navigationCopy($languageCode, 'footer')) . '</p><div class="footer-links">';

        foreach ($links as $label => $path) {
            $html .= '<a href="' . $this->e($path) . '">' . $this->e($label) . '</a>';
        }

        return $html . '</div></div></div></footer>';
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
                'contact' => '/en/contact/',
            ],
            'sl' => [
                'home' => '/sl/',
                'products' => '/sl/forever-izdelki/',
                'articles' => '/sl/clanki/',
                'support' => '/sl/#ai-advisor',
                'contact' => '/sl/kontakt/',
            ],
            default => [
                'home' => '/',
                'products' => '/forever-proizvodi/',
                'articles' => '/clanci/',
                'support' => '/#ai-advisor',
                'contact' => '/kontakt/',
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
                'contact' => 'Kontakt',
                'footer' => 'Aloe Vera Centar je web stranica za predstavljanje, preporuku i informiranje o Forever proizvodima. Stranica je u vlasništvu i pod upravljanjem tvrtke BS International.',
            ],
            'en' => [
                'tagline' => 'A clearer Forever product choice',
                'home' => 'Home',
                'products' => 'Products',
                'articles' => 'Articles',
                'support' => 'Recommendation',
                'contact' => 'Contact',
                'footer' => 'Aloe Vera Centar presents, recommends and explains Forever products. The website is owned and operated by BS International.',
            ],
            'sl' => [
                'tagline' => 'Jasnejša izbira Forever izdelkov',
                'home' => 'Domov',
                'products' => 'Izdelki',
                'articles' => 'Članki',
                'support' => 'Priporočilo',
                'contact' => 'Kontakt',
                'footer' => 'Aloe Vera Centar predstavlja, priporoča in pojasnjuje Forever izdelke. Stran je v lasti in upravljanju podjetja BS International.',
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
                    'title' => 'Jasniji izbor Forever Living Products proizvoda, bez lutanja',
                    'meta_title' => 'O nama | Aloe Vera Centar',
                    'meta_description' => 'Aloe Vera Centar pomaže korisnicima razumjeti Forever Living Products proizvode, usporediti opcije i sigurnije odabrati sljedeći korak.',
                    'intro' => 'Ako znaš što želiš podržati, ali ne znaš od kojeg proizvoda krenuti, AVC ti pomaže prevesti katalog u stvarnu svakodnevnu rutinu.',
                    'note_label' => 'Naš pristup',
                    'note_title' => 'Prvo razumijevanje, zatim odluka',
                    'note_text' => 'Ne moraš napamet znati naziv proizvoda. Dovoljno je znati što želiš poboljšati, što već koristiš i oko čega nisi siguran.',
                    'actions' => [
                        ['label' => 'Pregledaj proizvode', 'href' => '/forever-proizvodi/'],
                        ['label' => 'Pitaj za preporuku', 'href' => '/#ai-advisor'],
                    ],
                    'cards' => [
                        ['label' => 'Kreni od potrebe', 'title' => 'Probava, koža, energija, imunitet ili njega', 'text' => 'Stranica je složena oko stvarnih pitanja koja ljudi imaju prije narudžbe, ne samo oko naziva iz kataloga.'],
                        ['label' => 'Usporedi mirnije', 'title' => 'Vidi što se uklapa u tvoju rutinu', 'text' => 'Kod proizvoda naglašavamo kada ima smisla, za koga nije prvi izbor i što vrijedi usporediti.'],
                        ['label' => 'Nastavi sigurno', 'title' => 'Kupnja ide prema službenom shopu', 'text' => 'Kada odlučiš, vodimo te prema službenom Forever Living Products shopu u tvojoj zemlji.'],
                    ],
                    'sections' => [
                        [
                            'title' => 'Zašto postoji Aloe Vera Centar',
                            'paragraphs' => [
                                'Većina ljudi ne traži “još jedan katalog”. Traže jednostavan odgovor: što bi imalo smisla za moju situaciju, što nije pretjerano i gdje mogu nastaviti ako želim naručiti.',
                                'Zato na jednom mjestu povezujemo članke, vodiče za proizvode, preporuke i službene shop linkove. Cilj nije da kupiš bilo što, nego da lakše prepoznaš što se uklapa u tvoju potrebu.',
                            ],
                        ],
                        [
                            'title' => 'Kako pomažemo u odluci',
                            'items' => [
                                'objašnjavamo jednostavnim riječima za koga proizvod može imati smisla',
                                'povezujemo proizvode s navikama, rutinom i temom koju čitaš',
                                'nudimo preporuku kada nisi siguran između više opcija',
                                'usmjeravamo te prema službenom Forever Living Products shopu u tvojoj zemlji',
                            ],
                        ],
                    ],
                    'cta' => [
                        'label' => 'Sljedeći korak',
                        'title' => 'Nisi siguran od čega krenuti?',
                        'text' => 'Opiši što želiš podržati i dobit ćeš konkretniji prijedlog proizvoda ili članaka koje vrijedi prvo otvoriti.',
                        'actions' => [
                            ['label' => 'Zatraži preporuku', 'href' => '/#ai-advisor'],
                            ['label' => 'Otvori proizvode', 'href' => '/forever-proizvodi/'],
                        ],
                    ],
                ],
                'recommendations' => [
                    'path' => '/kako-rade-preporuke/',
                    'eyebrow' => 'Metodologija',
                    'title' => 'Kako nastaje preporuka koja ima smisla za tebe',
                    'meta_title' => 'Kako rade preporuke | Aloe Vera Centar',
                    'meta_description' => 'Saznaj kako Aloe Vera Centar povezuje potrebe korisnika, članke i Forever proizvode u jasnije preporuke bez medicinskih tvrdnji.',
                    'intro' => 'Dobra preporuka ne počinje nazivom proizvoda. Počinje pitanjem što želiš podržati, kakvu rutinu već imaš i koliko jednostavno želiš krenuti.',
                    'note_label' => 'Važno',
                    'note_title' => 'Preporuka nije dijagnoza',
                    'note_text' => 'AVC pomaže u izboru proizvoda i rutine. Za zdravstvene tegobe, terapiju, trudnoću ili dugotrajan problem uvijek je najbolje uključiti stručnu osobu.',
                    'actions' => [
                        ['label' => 'Isprobaj preporuku', 'href' => '/#ai-advisor'],
                        ['label' => 'Pogledaj proizvode po cilju', 'href' => '/forever-proizvodi/'],
                    ],
                    'cards' => [
                        ['label' => '1. Potreba', 'title' => 'Što želiš podržati?', 'text' => 'Probava, koža, energija, imunitet, njega ili situacija u kojoj nisi siguran odakle krenuti.'],
                        ['label' => '2. Kontekst', 'title' => 'Što već koristiš i što ti smeta?', 'text' => 'Različiti korisnici mogu trebati različit početak, čak i kada gledaju istu kategoriju proizvoda.'],
                        ['label' => '3. Sljedeći korak', 'title' => 'Vodič, proizvod ili pitanje', 'text' => 'Nekad je najbolji korak proizvod, a nekad kratak članak ili dodatno pitanje prije kupnje.'],
                    ],
                    'sections' => [
                        [
                            'title' => 'Što gledamo prije preporuke',
                            'items' => [
                                'cilj korisnika i temu zbog koje je otvorio stranicu',
                                'najbliže proizvode, slične opcije i razliku među njima',
                                'jezik, zemlju i službeni shop link koji vodi na pravo mjesto',
                                'granice edukacije, bez medicinskih obećanja i pretjeranih tvrdnji',
                            ],
                        ],
                        [
                            'title' => 'Što svjesno ne radimo',
                            'paragraphs' => [
                                'Ne guramo isti proizvod svima, ne postavljamo dijagnoze i ne obećavamo terapijski učinak. Ako pitanje zvuči ozbiljno ili dugotrajno, sadržaj treba shvatiti kao opću edukaciju i poticaj da se potraži stručan savjet.',
                                'Kupovni korak dolazi tek kada postoji dovoljno konteksta da korisnik razumije zašto baš taj proizvod ili ta rutina imaju smisla.',
                            ],
                        ],
                    ],
                    'cta' => [
                        'label' => 'Praktično',
                        'title' => 'Najbrže je krenuti od jedne rečenice',
                        'text' => 'Napiši što želiš poboljšati, što već koristiš ili oko čega se dvoumiš. Preporuka će te usmjeriti prema smislenijem izboru.',
                        'actions' => [
                            ['label' => 'Postavi pitanje', 'href' => '/#ai-advisor'],
                            ['label' => 'Pogledaj članke', 'href' => '/clanci/'],
                        ],
                    ],
                ],
                'editorial' => [
                    'path' => '/urednicka-politika/',
                    'eyebrow' => 'Urednička politika',
                    'title' => 'Kako brinemo da sadržaj bude koristan i pouzdan',
                    'meta_title' => 'Urednička politika | Aloe Vera Centar',
                    'meta_description' => 'Kako Aloe Vera Centar piše korisne i odgovorne vodiče koji pomažu posjetiteljima razumjeti temu, usporediti opcije i lakše odabrati Forever proizvod.',
                    'intro' => 'Želimo da nakon čitanja znaš što ti je jasnije: koja je tema, koje opcije imaš, što vrijedi usporediti i kada je pametno pitati za dodatni savjet.',
                    'note_label' => 'Za tvoju sigurnost',
                    'note_title' => 'Bez velikih obećanja',
                    'note_text' => 'Ne pišemo da proizvod liječi bolest i ne guramo kupnju pod svaku cijenu. Ako imaš dijagnozu, terapiju, trudnoću ili dugotrajan problem, najbolje je uključiti stručnu osobu.',
                    'actions' => [
                        ['label' => 'Pregledaj članke', 'href' => '/clanci/'],
                        ['label' => 'Pitaj za preporuku', 'href' => '/#ai-advisor'],
                    ],
                    'cards' => [
                        ['label' => 'Jasno objašnjeno', 'title' => 'Prvo razumiješ temu', 'text' => 'Članak treba pomoći da brzo prepoznaš što se odnosi na tvoju situaciju, bez nepotrebnog kompliciranja.'],
                        ['label' => 'Lakša odluka', 'title' => 'Vidiš što vrijedi usporediti', 'text' => 'Kada postoji više sličnih opcija, izdvajamo razliku kako bi lakše odabrao prvi smislen korak.'],
                        ['label' => 'Sigurniji korak', 'title' => 'Znaš kada pitati dalje', 'text' => 'Ako informacija nije dovoljna za odluku, bolje je zatražiti preporuku nego naručiti napamet.'],
                    ],
                    'sections' => [
                        [
                            'title' => 'Što možeš očekivati od naših tekstova',
                            'items' => [
                                'kratko objašnjenje teme bez praznih fraza i kompliciranja',
                                'jasno istaknute proizvode koji mogu imati smisla za tu situaciju',
                                'usporedbe kada postoji više sličnih opcija',
                                'podsjetnik da se kod terapije, trudnoće, dijagnoze ili dugotrajnih tegoba savjetuješ sa stručnom osobom',
                            ],
                        ],
                        [
                            'title' => 'Kada preporučimo proizvod',
                            'paragraphs' => [
                                'Proizvod povezujemo s temom tek kada može pomoći razumjeti sljedeći korak. Ne želimo da kupuješ napamet, nego da znaš zašto bi baš ta opcija mogla imati smisla.',
                                'Kada se promijene proizvodi, cijene, dostupnost ili službeni shop linkovi, nastojimo sadržaj ažurirati kako bi te klik vodio na ispravno mjesto.',
                            ],
                        ],
                    ],
                    'cta' => [
                        'label' => 'Nastavi pametno',
                        'title' => 'Ako nakon čitanja još nisi siguran, pitaj prije kupnje',
                        'text' => 'Napiši što želiš podržati, što već koristiš ili između čega se dvoumiš. Dobit ćeš jasniji prijedlog sljedećeg koraka.',
                        'actions' => [
                            ['label' => 'Zatraži preporuku', 'href' => '/#ai-advisor'],
                            ['label' => 'Pregledaj proizvode', 'href' => '/forever-proizvodi/'],
                        ],
                    ],
                ],
                'contact' => [
                    'path' => '/kontakt/',
                    'eyebrow' => 'Kontakt',
                    'title' => 'Kontakt i podaci o stranici',
                    'meta_title' => 'Kontakt | Aloe Vera Centar',
                    'meta_description' => 'Kontakt podaci za Aloe Vera Centar, web stranicu u vlasništvu tvrtke BS International koja informira i preporučuje Forever proizvode.',
                    'intro' => 'Ako imaš pitanje o stranici, sadržaju ili preporuci Forever proizvoda, ovdje su svi osnovni podaci i najbrži način da nam se javiš.',
                    'note_label' => 'Tko stoji iza stranice',
                    'note_title' => 'BS International',
                    'note_text' => 'Aloe Vera Centar je u vlasništvu i pod upravljanjem tvrtke BS International, s ciljem da posjetiteljima olakša razumijevanje i odabir Forever proizvoda.',
                    'actions' => [
                        ['label' => 'Pošalji email', 'href' => 'mailto:info@aloevera-centar.com'],
                        ['label' => 'Pitaj za preporuku', 'href' => '/#ai-advisor'],
                    ],
                    'cards' => [
                        ['label' => 'Naziv stranice', 'title' => 'Aloe Vera Centar', 'text' => 'Web vodič za predstavljanje, preporuku i informiranje o Forever Living Products proizvodima.'],
                        ['label' => 'Vlasnik', 'title' => 'BS International', 'text' => 'Tvrtka koja upravlja stranicom, sadržajem i kontaktima zaprimljenima putem AVC-a.'],
                        ['label' => 'Adresa', 'title' => 'Ivana Gorana Kovačića 15, 10408 Velika Mlaka', 'text' => 'Službena adresa vlasnika stranice.'],
                        ['label' => 'E-mail', 'title' => 'info@aloevera-centar.com', 'text' => 'Za pitanja o stranici, preporukama ili informacijama objavljenima na AVC-u.'],
                        ['label' => 'Web', 'title' => 'https://aloevera-centar.com', 'text' => 'Službena web adresa Aloe Vera Centra.'],
                        ['label' => 'OIB / VAT ID', 'title' => '15435222026', 'text' => 'Identifikacijski broj vlasnika stranice.'],
                        ['label' => 'Odgovorna osoba', 'title' => 'Stjepan Beloša', 'text' => 'Odgovorna osoba za upravljanje stranicom.'],
                    ],
                    'sections' => [
                        [
                            'title' => 'O stranici Aloe Vera Centar',
                            'paragraphs' => [
                                'Aloe Vera Centar je web stranica namijenjena predstavljanju, preporuci i informiranju o Forever proizvodima. Stranica je u vlasništvu i pod upravljanjem tvrtke BS International.',
                                'Sadržaj je namijenjen posjetiteljima koji žele lakše razumjeti proizvode, usporediti opcije i pronaći smislen sljedeći korak prije odluke o narudžbi.',
                            ],
                        ],
                        [
                            'title' => 'Kako ti možemo pomoći',
                            'items' => [
                                'ako želiš razumjeti koji Forever proizvod odgovara tvojoj potrebi',
                                'ako trebaš pomoć oko preporuke, popusta ili nastavka prema službenom shopu',
                                'ako želiš prijaviti netočan podatak, neispravan link ili sadržaj koji treba ažurirati',
                            ],
                        ],
                    ],
                    'cta' => [
                        'label' => 'Najbrži kontakt',
                        'title' => 'Pošalji pitanje ili kreni kroz preporuku',
                        'text' => 'Za opća pitanja možeš poslati email. Ako želiš pomoć pri odabiru proizvoda, najbrže je opisati cilj kroz AVC preporuku.',
                        'actions' => [
                            ['label' => 'Pošalji email', 'href' => 'mailto:info@aloevera-centar.com'],
                            ['label' => 'Zatraži preporuku', 'href' => '/#ai-advisor'],
                        ],
                    ],
                ],
            ],
            'en' => [
                'about' => [
                    'path' => '/en/about/',
                    'eyebrow' => 'About Aloe Vera Centar',
                    'title' => 'A clearer Forever Living Products choice, without wandering',
                    'meta_title' => 'About | Aloe Vera Centar',
                    'meta_description' => 'Aloe Vera Centar helps visitors understand Forever Living Products, compare options and take a clearer next step.',
                    'intro' => 'If you know what you want to support but do not know which product to start with, AVC helps translate the catalogue into a practical daily routine.',
                    'note_label' => 'Our approach',
                    'note_title' => 'Understand first, choose second',
                    'note_text' => 'You do not need to know product names by heart. Start with your goal, current routine and the question you are unsure about.',
                    'actions' => [
                        ['label' => 'Browse products', 'href' => '/en/forever-products/'],
                        ['label' => 'Ask for guidance', 'href' => '/en/#ai-advisor'],
                    ],
                    'cards' => [
                        ['label' => 'Start with need', 'title' => 'Digestion, skin, energy, immunity or care', 'text' => 'The site is organized around real questions people ask before ordering, not only around catalogue names.'],
                        ['label' => 'Compare calmly', 'title' => 'See what fits your routine', 'text' => 'Product guides explain when a product may make sense, when to compare it and what to consider first.'],
                        ['label' => 'Continue safely', 'title' => 'Purchase on the official shop', 'text' => 'When you decide, AVC routes you to the official Forever Living Products shop for your country.'],
                    ],
                    'sections' => [
                        ['title' => 'Why AVC exists', 'paragraphs' => ['Most visitors are not looking for another catalogue. They want a simple answer: what makes sense for my situation, what is not exaggerated and where can I continue if I want to order.', 'AVC connects articles, product guides, recommendations and official shop links so the next step feels easier.']],
                        ['title' => 'How we help', 'items' => ['explain in plain language when a product may fit', 'connect products with routines and topics', 'offer guidance when the visitor is unsure', 'route visitors to the official Forever Living Products shop for their country']],
                    ],
                    'cta' => [
                        'label' => 'Next step',
                        'title' => 'Not sure where to start?',
                        'text' => 'Describe your goal and get a clearer product or article suggestion before opening the shop.',
                        'actions' => [
                            ['label' => 'Ask for guidance', 'href' => '/en/#ai-advisor'],
                            ['label' => 'Open products', 'href' => '/en/forever-products/'],
                        ],
                    ],
                ],
                'recommendations' => [
                    'path' => '/en/how-recommendations-work/',
                    'eyebrow' => 'Method',
                    'title' => 'How a recommendation becomes useful for you',
                    'meta_title' => 'How recommendations work | Aloe Vera Centar',
                    'meta_description' => 'How Aloe Vera Centar connects visitor needs, articles and Forever products into clearer recommendations without medical claims.',
                    'intro' => 'A useful recommendation does not begin with a product name. It begins with what you want to support, what your routine looks like and how simple you want the first step to be.',
                    'note_label' => 'Important',
                    'note_title' => 'Guidance is not diagnosis',
                    'note_text' => 'AVC helps with product choice and routine ideas. For medical conditions, therapy, pregnancy or persistent issues, include a qualified professional.',
                    'actions' => [
                        ['label' => 'Try guidance', 'href' => '/en/#ai-advisor'],
                        ['label' => 'View products by goal', 'href' => '/en/forever-products/'],
                    ],
                    'cards' => [
                        ['label' => '1. Need', 'title' => 'What do you want to support?', 'text' => 'Digestion, skin, energy, immunity, care or uncertainty between a few possible products.'],
                        ['label' => '2. Context', 'title' => 'What do you already use?', 'text' => 'Different people may need different starting points even when they explore the same category.'],
                        ['label' => '3. Next step', 'title' => 'Guide, product or question', 'text' => 'Sometimes the right next step is a product; sometimes it is a short article or one more question.'],
                    ],
                    'sections' => [
                        ['title' => 'What we consider', 'items' => ['the visitor goal and the page they are reading', 'nearby products and similar options worth comparing', 'language, country and official shop routing', 'clear boundaries between education and medical advice']],
                        ['title' => 'What we do not do', 'paragraphs' => ['We do not push the same product to everyone, diagnose, promise therapeutic effects or replace professional advice.', 'The purchase step comes only after there is enough context for the visitor to understand why a product or routine may make sense.']],
                    ],
                    'cta' => [
                        'label' => 'Practical',
                        'title' => 'Start with one sentence',
                        'text' => 'Write what you want to improve, what you already use or what you are unsure about.',
                        'actions' => [
                            ['label' => 'Ask a question', 'href' => '/en/#ai-advisor'],
                            ['label' => 'Read articles', 'href' => '/en/articles/'],
                        ],
                    ],
                ],
                'editorial' => [
                    'path' => '/en/editorial-policy/',
                    'eyebrow' => 'Editorial policy',
                    'title' => 'How we write content visitors can trust',
                    'meta_title' => 'Editorial policy | Aloe Vera Centar',
                    'meta_description' => 'Aloe Vera Centar editorial policy: clear, useful and responsible content that helps visitors understand Forever products.',
                    'intro' => 'Every AVC article should help a real person understand a topic, compare options and take the next step without feeling pressured.',
                    'note_label' => 'Editorial goal',
                    'note_title' => 'Clear, useful and responsible',
                    'note_text' => 'Content should be easy to scan, but concrete enough to help a visitor decide what to read or compare next.',
                    'actions' => [
                        ['label' => 'Read articles', 'href' => '/en/articles/'],
                        ['label' => 'Ask for guidance', 'href' => '/en/#ai-advisor'],
                    ],
                    'cards' => [
                        ['label' => 'Human tone', 'title' => 'We write like a guide, not a catalogue', 'text' => 'We avoid empty phrases and focus on what a visitor needs to know before deciding.'],
                        ['label' => 'Clear structure', 'title' => 'Summaries, headings, FAQ and related products', 'text' => 'Pages should be easy to scan when someone is comparing several options.'],
                        ['label' => 'Measurement', 'title' => 'We watch what actually helps', 'text' => 'We track which pages lead to guidance, contacts and official Forever shop clicks.'],
                    ],
                    'sections' => [
                        ['title' => 'Content principles', 'items' => ['explain the visitor situation before the product', 'avoid dramatic promises and medical claims that do not belong there', 'connect articles with useful products without interrupting reading aggressively', 'improve pages that sound generic even when they are technically SEO-friendly']],
                        ['title' => 'Updates', 'paragraphs' => ['When products, availability or official shop links change, AVC aims to update content and routing so visitors land in the right place.', 'If a page does not give the visitor clear value, it is a page that needs more work.']],
                    ],
                    'cta' => [
                        'label' => 'For visitors',
                        'title' => 'Content should make decisions easier, not just attract clicks',
                        'text' => 'That is why AVC connects articles, products, guidance and analytics into one recommendation system.',
                        'actions' => [
                            ['label' => 'Open articles', 'href' => '/en/articles/'],
                            ['label' => 'Open products', 'href' => '/en/forever-products/'],
                        ],
                    ],
                ],
                'contact' => [
                    'path' => '/en/contact/',
                    'eyebrow' => 'Contact',
                    'title' => 'Contact and website details',
                    'meta_title' => 'Contact | Aloe Vera Centar',
                    'meta_description' => 'Contact details for Aloe Vera Centar, a BS International website that explains and recommends Forever products.',
                    'intro' => 'If you have a question about the website, content or Forever product guidance, this page gives you the essential contact and ownership details.',
                    'note_label' => 'Website owner',
                    'note_title' => 'BS International',
                    'note_text' => 'Aloe Vera Centar is owned and operated by BS International to help visitors understand Forever products and choose a clearer next step.',
                    'actions' => [
                        ['label' => 'Send email', 'href' => 'mailto:info@aloevera-centar.com'],
                        ['label' => 'Ask for guidance', 'href' => '/en/#ai-advisor'],
                    ],
                    'cards' => [
                        ['label' => 'Website name', 'title' => 'Aloe Vera Centar', 'text' => 'A guide for presenting, recommending and explaining Forever Living Products.'],
                        ['label' => 'Owner', 'title' => 'BS International', 'text' => 'The company operating the website, content and contacts received through AVC.'],
                        ['label' => 'Address', 'title' => 'Ivana Gorana Kovačića 15, 10408 Velika Mlaka, Croatia', 'text' => 'Official owner address.'],
                        ['label' => 'Email', 'title' => 'info@aloevera-centar.com', 'text' => 'For questions about the website, recommendations or published information.'],
                        ['label' => 'Website', 'title' => 'https://aloevera-centar.com', 'text' => 'Official Aloe Vera Centar web address.'],
                        ['label' => 'VAT ID', 'title' => '15435222026', 'text' => 'Owner identification number.'],
                        ['label' => 'Responsible person', 'title' => 'Stjepan Beloša', 'text' => 'Responsible person for website management.'],
                    ],
                    'sections' => [
                        [
                            'title' => 'About Aloe Vera Centar',
                            'paragraphs' => [
                                'Aloe Vera Centar is a website for presenting, recommending and explaining Forever products. The website is owned and operated by BS International.',
                                'The content is intended for visitors who want to understand products, compare options and find a clearer next step before ordering.',
                            ],
                        ],
                        [
                            'title' => 'How we can help',
                            'items' => [
                                'when you want to understand which Forever product fits your need',
                                'when you need help with guidance, discount links or continuing to the official shop',
                                'when you want to report incorrect information, a broken link or content that should be updated',
                            ],
                        ],
                    ],
                    'cta' => [
                        'label' => 'Fastest contact',
                        'title' => 'Send a question or start with guidance',
                        'text' => 'For general questions, send an email. For product choice, describe your goal through AVC guidance.',
                        'actions' => [
                            ['label' => 'Send email', 'href' => 'mailto:info@aloevera-centar.com'],
                            ['label' => 'Ask for guidance', 'href' => '/en/#ai-advisor'],
                        ],
                    ],
                ],
            ],
            'sl' => [
                'about' => [
                    'path' => '/sl/o-nas/',
                    'eyebrow' => 'O Aloe Vera Centru',
                    'title' => 'Jasnejša izbira Forever Living Products izdelkov, brez tavanja',
                    'meta_title' => 'O nas | Aloe Vera Centar',
                    'meta_description' => 'Aloe Vera Centar pomaga obiskovalcem razumeti Forever Living Products izdelke, primerjati možnosti in narediti jasnejši naslednji korak.',
                    'intro' => 'Če veš, kaj želiš podpreti, vendar ne veš, s katerim izdelkom začeti, AVC pomaga prevesti katalog v praktično dnevno rutino.',
                    'note_label' => 'Naš pristop',
                    'note_title' => 'Najprej razumevanje, nato odločitev',
                    'note_text' => 'Ni ti treba poznati imen izdelkov na pamet. Začni s ciljem, obstoječo rutino in vprašanjem, pri katerem nisi prepričan.',
                    'actions' => [
                        ['label' => 'Preglej izdelke', 'href' => '/sl/forever-izdelki/'],
                        ['label' => 'Vprašaj za priporočilo', 'href' => '/sl/#ai-advisor'],
                    ],
                    'cards' => [
                        ['label' => 'Začni pri potrebi', 'title' => 'Prebava, koža, energija, imunost ali nega', 'text' => 'Stran je urejena okoli resničnih vprašanj pred naročilom, ne samo okoli imen iz kataloga.'],
                        ['label' => 'Primerjaj mirneje', 'title' => 'Poglej, kaj se ujema s tvojo rutino', 'text' => 'Vodiči pojasnijo, kdaj ima izdelek smisel in kaj je dobro primerjati.'],
                        ['label' => 'Nadaljuj varno', 'title' => 'Nakup gre v uradno trgovino', 'text' => 'Ko se odločiš, te AVC usmeri v uradno Forever Living Products trgovino za tvojo državo.'],
                    ],
                    'sections' => [
                        ['title' => 'Zakaj obstaja AVC', 'paragraphs' => ['Večina obiskovalcev ne išče še enega kataloga. Želijo preprost odgovor: kaj ima smisel za mojo situacijo, kaj ni pretirano in kje lahko nadaljujem, če želim naročiti.', 'AVC povezuje članke, vodiče za izdelke, priporočila in uradne shop povezave, da je naslednji korak lažji.']],
                        ['title' => 'Kako pomagamo', 'items' => ['preprosto pojasnimo, kdaj ima izdelek lahko smisel', 'povezujemo izdelke z rutino in temami', 'ponudimo priporočilo, ko obiskovalec ni prepričan', 'vodimo do uradne Forever Living Products trgovine za pravo državo']],
                    ],
                    'cta' => [
                        'label' => 'Naslednji korak',
                        'title' => 'Nisi prepričan, kje začeti?',
                        'text' => 'Opiši svoj cilj in prejmi jasnejši predlog izdelka ali članka, ki ga je dobro najprej odpreti.',
                        'actions' => [
                            ['label' => 'Vprašaj za priporočilo', 'href' => '/sl/#ai-advisor'],
                            ['label' => 'Odpri izdelke', 'href' => '/sl/forever-izdelki/'],
                        ],
                    ],
                ],
                'recommendations' => [
                    'path' => '/sl/kako-delujejo-priporocila/',
                    'eyebrow' => 'Metoda',
                    'title' => 'Kako nastane priporočilo, ki ima smisel zate',
                    'meta_title' => 'Kako delujejo priporočila | Aloe Vera Centar',
                    'meta_description' => 'Kako Aloe Vera Centar povezuje potrebe obiskovalcev, članke in Forever izdelke v jasnejša priporočila brez medicinskih trditev.',
                    'intro' => 'Koristno priporočilo se ne začne z imenom izdelka. Začne se s tem, kaj želiš podpreti, kakšna je tvoja rutina in kako preprost naj bo prvi korak.',
                    'note_label' => 'Pomembno',
                    'note_title' => 'Priporočilo ni diagnoza',
                    'note_text' => 'AVC pomaga pri izbiri izdelka in rutine. Pri zdravstvenih težavah, terapiji, nosečnosti ali dolgotrajnih težavah je dobro vključiti strokovnjaka.',
                    'actions' => [
                        ['label' => 'Preizkusi priporočilo', 'href' => '/sl/#ai-advisor'],
                        ['label' => 'Poglej izdelke po cilju', 'href' => '/sl/forever-izdelki/'],
                    ],
                    'cards' => [
                        ['label' => '1. Potreba', 'title' => 'Kaj želiš podpreti?', 'text' => 'Prebava, koža, energija, imunost, nega ali negotovost med več izdelki.'],
                        ['label' => '2. Kontekst', 'title' => 'Kaj že uporabljaš?', 'text' => 'Različni ljudje lahko potrebujejo različen začetek, tudi ko raziskujejo isto kategorijo.'],
                        ['label' => '3. Naslednji korak', 'title' => 'Vodič, izdelek ali vprašanje', 'text' => 'Včasih je pravi korak izdelek, včasih kratek članek ali dodatno vprašanje.'],
                    ],
                    'sections' => [
                        ['title' => 'Kaj upoštevamo', 'items' => ['cilj obiskovalca in stran, ki jo bere', 'najbližje izdelke in podobne možnosti za primerjavo', 'jezik, državo in uradno shop usmerjanje', 'jasno mejo med izobraževanjem in medicinskim nasvetom']],
                        ['title' => 'Česa ne počnemo', 'paragraphs' => ['Ne priporočamo istega izdelka vsem, ne postavljamo diagnoz, ne obljubljamo terapevtskih učinkov in ne nadomeščamo strokovnega nasveta.', 'Nakup pride na vrsto šele, ko ima obiskovalec dovolj konteksta, da razume, zakaj ima izdelek ali rutina smisel.']],
                    ],
                    'cta' => [
                        'label' => 'Praktično',
                        'title' => 'Začni z enim stavkom',
                        'text' => 'Napiši, kaj želiš izboljšati, kaj že uporabljaš ali pri čem nisi prepričan.',
                        'actions' => [
                            ['label' => 'Postavi vprašanje', 'href' => '/sl/#ai-advisor'],
                            ['label' => 'Preberi članke', 'href' => '/sl/clanki/'],
                        ],
                    ],
                ],
                'editorial' => [
                    'path' => '/sl/uredniska-politika/',
                    'eyebrow' => 'Uredniška politika',
                    'title' => 'Kako pišemo vsebino, ki ji obiskovalec lahko zaupa',
                    'meta_title' => 'Uredniška politika | Aloe Vera Centar',
                    'meta_description' => 'Uredniška politika Aloe Vera Centra: jasna, uporabna in odgovorna vsebina za razumevanje Forever izdelkov.',
                    'intro' => 'Vsak tekst na AVC naj pomaga resnični osebi razumeti temo, primerjati možnosti in narediti naslednji korak brez občutka pritiska.',
                    'note_label' => 'Uredniški cilj',
                    'note_title' => 'Jasno, uporabno in odgovorno',
                    'note_text' => 'Vsebina mora biti hitro berljiva, a dovolj konkretna, da pomaga pri izbiri naslednjega branja ali primerjave.',
                    'actions' => [
                        ['label' => 'Preberi članke', 'href' => '/sl/clanki/'],
                        ['label' => 'Vprašaj za priporočilo', 'href' => '/sl/#ai-advisor'],
                    ],
                    'cards' => [
                        ['label' => 'Človeški ton', 'title' => 'Pišemo kot vodič, ne kot katalog', 'text' => 'Izogibamo se praznim frazam in se osredotočamo na to, kar obiskovalec potrebuje pred odločitvijo.'],
                        ['label' => 'Jasna struktura', 'title' => 'Povzetki, naslovi, FAQ in povezani izdelki', 'text' => 'Strani morajo biti enostavne za pregled, posebej pri primerjavi več možnosti.'],
                        ['label' => 'Merjenje', 'title' => 'Spremljamo, kaj res pomaga', 'text' => 'Merimo, katere strani vodijo do priporočila, kontakta ali klika v uradni Forever shop.'],
                    ],
                    'sections' => [
                        ['title' => 'Načela vsebine', 'items' => ['najprej pojasnimo situacijo obiskovalca, nato izdelek', 'izogibamo se dramatičnim obljubam in medicinskim trditvam', 'članke povezujemo z uporabnimi izdelki brez agresivnega prekinjanja branja', 'izboljšujemo strani, ki zvenijo generično, tudi če so tehnično SEO pravilne']],
                        ['title' => 'Posodobitve', 'paragraphs' => ['Ko se spremenijo izdelki, razpoložljivost ali uradne shop povezave, AVC poskuša posodobiti vsebino in routing.', 'Če stran obiskovalcu ne daje jasne vrednosti, je to stran, ki jo je treba še izboljšati.']],
                    ],
                    'cta' => [
                        'label' => 'Za obiskovalce',
                        'title' => 'Vsebina mora olajšati odločitev, ne samo dobiti klik',
                        'text' => 'Zato AVC povezuje članke, izdelke, priporočila in analitiko v en sistem priporočanja.',
                        'actions' => [
                            ['label' => 'Odpri članke', 'href' => '/sl/clanki/'],
                            ['label' => 'Odpri izdelke', 'href' => '/sl/forever-izdelki/'],
                        ],
                    ],
                ],
                'contact' => [
                    'path' => '/sl/kontakt/',
                    'eyebrow' => 'Kontakt',
                    'title' => 'Kontakt in podatki o strani',
                    'meta_title' => 'Kontakt | Aloe Vera Centar',
                    'meta_description' => 'Kontaktni podatki za Aloe Vera Centar, stran podjetja BS International za informacije in priporočila Forever izdelkov.',
                    'intro' => 'Če imaš vprašanje o strani, vsebini ali priporočilih Forever izdelkov, so tukaj osnovni podatki in najhitrejši način za stik.',
                    'note_label' => 'Lastnik strani',
                    'note_title' => 'BS International',
                    'note_text' => 'Aloe Vera Centar je v lasti in upravljanju podjetja BS International, da obiskovalcem olajša razumevanje in izbiro Forever izdelkov.',
                    'actions' => [
                        ['label' => 'Pošlji email', 'href' => 'mailto:info@aloevera-centar.com'],
                        ['label' => 'Vprašaj za priporočilo', 'href' => '/sl/#ai-advisor'],
                    ],
                    'cards' => [
                        ['label' => 'Naziv strani', 'title' => 'Aloe Vera Centar', 'text' => 'Spletni vodič za predstavitev, priporočila in informacije o Forever Living Products izdelkih.'],
                        ['label' => 'Lastnik', 'title' => 'BS International', 'text' => 'Podjetje, ki upravlja stran, vsebino in kontakte, prejete prek AVC.'],
                        ['label' => 'Naslov', 'title' => 'Ivana Gorana Kovačića 15, 10408 Velika Mlaka, Hrvaška', 'text' => 'Uradni naslov lastnika strani.'],
                        ['label' => 'E-mail', 'title' => 'info@aloevera-centar.com', 'text' => 'Za vprašanja o strani, priporočilih ali objavljenih informacijah.'],
                        ['label' => 'Web', 'title' => 'https://aloevera-centar.com', 'text' => 'Uradni spletni naslov Aloe Vera Centra.'],
                        ['label' => 'VAT ID', 'title' => '15435222026', 'text' => 'Identifikacijska številka lastnika strani.'],
                        ['label' => 'Odgovorna oseba', 'title' => 'Stjepan Beloša', 'text' => 'Odgovorna oseba za upravljanje strani.'],
                    ],
                    'sections' => [
                        [
                            'title' => 'O strani Aloe Vera Centar',
                            'paragraphs' => [
                                'Aloe Vera Centar je spletna stran za predstavitev, priporočila in informacije o Forever izdelkih. Stran je v lasti in upravljanju podjetja BS International.',
                                'Vsebina je namenjena obiskovalcem, ki želijo lažje razumeti izdelke, primerjati možnosti in najti smiseln naslednji korak pred naročilom.',
                            ],
                        ],
                        [
                            'title' => 'Kako lahko pomagamo',
                            'items' => [
                                'če želiš razumeti, kateri Forever izdelek ustreza tvoji potrebi',
                                'če potrebuješ pomoč pri priporočilu, popustu ali nadaljevanju v uradni shop',
                                'če želiš prijaviti napačen podatek, nedelujočo povezavo ali vsebino, ki jo je treba posodobiti',
                            ],
                        ],
                    ],
                    'cta' => [
                        'label' => 'Najhitrejši kontakt',
                        'title' => 'Pošlji vprašanje ali začni s priporočilom',
                        'text' => 'Za splošna vprašanja pošlji email. Za izbiro izdelka najhitreje opiši cilj skozi AVC priporočilo.',
                        'actions' => [
                            ['label' => 'Pošlji email', 'href' => 'mailto:info@aloevera-centar.com'],
                            ['label' => 'Vprašaj za priporočilo', 'href' => '/sl/#ai-advisor'],
                        ],
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
