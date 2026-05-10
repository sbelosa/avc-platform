<?php

declare(strict_types=1);

$entry = static fn (
    int $sourceId,
    string $title,
    string $slug,
    string $excerpt,
    string $summaryHtml,
    array $faqItems,
    string $metaTitle,
    string $metaDescription,
    string $breadcrumbTitle,
    array $sections
): array => [
    'source_translation_id' => $sourceId,
    'language_code' => 'sl',
    'title' => $title,
    'slug' => $slug,
    'excerpt' => $excerpt,
    'summary_html' => $summaryHtml,
    'faq_items' => $faqItems,
    'meta_title' => $metaTitle,
    'meta_description' => $metaDescription,
    'breadcrumb_title' => $breadcrumbTitle,
    'sections' => $sections,
];

return [
    'key' => 'zakljucni-cleanup-sl-wave-1',
    'name' => 'Zaključni cleanup (SL) - prvi val',
    'notes' => 'Ročno lokaliziran zaključni premium val za zadnje štiri preostale teme brez SL para.',
    'entries' => [
        $entry(
            629,
            'Vitamin D: močne kosti, boljša imunost in boljše razpoloženje skozi bolj realen pogled',
            'vitamin-d-mocne-kosti-boljsa-imunost-in-razpolozenje',
            'Vitamin D se pogosto omenja kot rešitev za skoraj vse, njegova prava vrednost pa se pokaže šele, ko ga gledamo skozi kosti, imunost, razpoloženje in resničen življenjski kontekst. Tukaj je, kako ga razumeti brez pretiravanja.',
            '<ul><li>Vitamin D je lahko pomemben za kosti, imunost in širši občutek vitalnosti.</li><li>Največja napaka je pričakovati, da bo eno hranilo nadomestilo slab spanec, slabo prehrano in premalo gibanja.</li><li>Pametnejši pristop gleda sonce, hrano in razumno dopolnjevanje v širšem zdravstvenem okviru.</li></ul>',
            [
                ['question' => 'Zakaj se o vitaminu D toliko govori?', 'answer' => 'Ker je povezan z zdravjem kosti, imunostjo in širšo vitalnostjo.'],
                ['question' => 'Ali lahko vpliva na razpoloženje?', 'answer' => 'Lahko je eden od dejavnikov, nikakor pa ni edina razlaga za razpoloženje.'],
                ['question' => 'Kaj določa smiselnost dopolnjevanja?', 'answer' => 'Letni čas, izpostavljenost soncu, prehrana in dejanska potreba posameznika.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Vitamin D spremeniti v univerzalno razlago za vsako težavo z energijo ali imunostjo.'],
            ],
            'Vitamin D: kako ga gledati za kosti, imunost in razpoloženje brez pretiravanja',
            'Spoznajte, kako lahko vitamin D podpira kosti, imunost in razpoloženje, ko ga razumemo bolj realistično.',
            'Vitamin D in vitalnost',
            [
                ['heading' => 'Zakaj vitamin D hitro zveni večji, kot bi moral', 'html' => '<p>Ker je povezan z več pomembnimi sistemi v telesu, vitamin D hitro začne zveneti kot popoln odgovor. V praksi pa je veliko bolj uporaben takrat, ko ga razumemo kot en smiseln del širše zdravstvene slike.</p>'],
                ['heading' => 'Zakaj je kontekst pomembnejši od ugleda', 'html' => '<p>Hranilo je lahko pomembno in hkrati zelo lahko napačno razumljeno. Najbolj uporaben pristop je skoraj vedno tisti, ki vitamin D poveže z življenjskim ritmom, sezono in dejansko potrebo.</p>'],
            ]
        ),
        $entry(
            827,
            'Zeliščni čaji: kdaj jih piti in kako aloe gel smiselno vključiti kot dodatno podporo',
            'zeliscni-caji-kdaj-jih-piti-in-kako-jih-povezati-z-aloe-gelom',
            'Zeliščni čaji imajo dolgo tradicijo in resnično praktično vrednost, največ pa pomagajo, ko jih uporabljamo ciljno in zmerno. Tukaj je, kako izbrati pravi trenutek za čaj in kje lahko aloe gel smiselno dopolni širšo rutino.',
            '<ul><li>Zeliščni čaji imajo največ smisla, ko ustrezajo času dneva, simptomu in razlogu, zaradi katerega jih izberemo.</li><li>Največja napaka je pričakovati, da bo ena mešanica rešila vse težave in nadomestila vse druge navade.</li><li>Pametnejši pristop čaj razume kot del mirnejše rutine, ne kot celoten načrt podpore.</li></ul>',
            [
                ['question' => 'Kdaj je najboljši čas za zeliščni čaj?', 'answer' => 'To je odvisno od vrste čaja, cilja in dela dneva, ko želite več topline, miru ali podpore.'],
                ['question' => 'Ali lahko zeliščni čaji pomagajo prebavi in grlu?', 'answer' => 'Pogosto lahko prinesejo občutek olajšanja in več udobja kot del širše rutine.'],
                ['question' => 'Kje lahko pride aloe gel v poštev?', 'answer' => 'Kot dodatna plast podpore v rutini, ne pa kot zamenjava za vse ostalo.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Iz čaja narediti edini načrt podpore namesto preprostega dela vsakdanje nege.'],
            ],
            'Zeliščni čaji: kdaj imajo smisel in kje aloe gel dopolni rutino',
            'Odkrijte, kako zeliščne čaje uporabljati bolj premišljeno in kje aloe gel lahko smiselno dopolni rutino.',
            'Zeliščni čaji',
            [
                ['heading' => 'Zakaj čas dneva spremeni vrednost čaja', 'html' => '<p>Isti čaj lahko pomeni nekaj povsem drugega, če ga pijemo zjutraj, po obroku ali zvečer za umirjanje. Prav zato je trenutek uporabe pogosto pomembnejši od same mešanice.</p>'],
                ['heading' => 'Zakaj podpora najbolje deluje kot mirna rutina', 'html' => '<p>Zeliščni čaji imajo največ smisla, ko postanejo del širšega, nežnega rituala. Takrat lahko aloe gel ali druga preprosta navada dopolni občutek podpore brez pretiranih obljub.</p>'],
            ]
        ),
        $entry(
            837,
            'Zumba, pilates ali crossfit: kako izbrati skupinski trening, ki vam res ustreza',
            'zumba-pilates-ali-crossfit-kako-izbrati-skupinski-trening',
            'Pravi skupinski trening navadno ni tisti, ki na začetku izgleda najbolj vznemirljivo, temveč tisti, h kateremu se lahko vračate brez občutka kazni. Tukaj je, kako med zumbo, pilatesom in crossfitom izbirati glede na cilj, telo in vrsto motivacije.',
            '<ul><li>Najboljši skupinski trening je tisti, ki se ujema z vašim telesom, ritmom in resničnim slogom motivacije.</li><li>Največja napaka je izbrati trend namesto tega, kar lahko dejansko vzdržujete.</li><li>Pametnejši pristop gleda cilj: zabava, moč, mobilnost, energija ali občutek skupnosti.</li></ul>',
            [
                ['question' => 'Ali obstaja objektivno najboljši skupinski trening?', 'answer' => 'Ne. Najboljša izbira je odvisna od cilja, telesa in načina, kako ostajate motivirani.'],
                ['question' => 'Komu zumba pogosto najbolj ustreza?', 'answer' => 'Ljudem, ki želijo več ritma, zabave in lažji vstop v redno gibanje.'],
                ['question' => 'Komu lahko bolj ustreza pilates?', 'answer' => 'Tistim, ki si želijo več nadzora gibanja, stabilnosti, mobilnosti in diha.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Izbrati trening, ki vas navduši, ne pa tudi dovolj ustreza, da bi v njem ostali.'],
            ],
            'Zumba, pilates ali crossfit: kako izbrati trening, ki ga boste res vzdrževali',
            'Spoznajte, kako med zumbo, pilatesom in crossfitom izbrati glede na cilj, telo in tip motivacije.',
            'Skupinski trening',
            [
                ['heading' => 'Zakaj začetno navdušenje ni isto kot prava ujemanje', 'html' => '<p>Vadba lahko na prvi pogled deluje izjemno motivacijsko, a to še ne pomeni, da bo dolgoročno prava za vas. Večinoma ostanemo z vadbo, ki jo lahko ponavljamo brez notranjega odpora.</p>'],
                ['heading' => 'Zakaj mora trening slediti človeku in ne obratno', 'html' => '<p>Nekdo potrebuje več ritma in družbe, drugi več strukture in nadzora gibanja. Ko slog treninga bolje sledi osebi, je doslednost precej lažja.</p>'],
            ]
        ),
        $entry(
            857,
            'Kuhanje brez odpadkov: kako bolje uporabiti korenine, liste in stebla brez zapletanja',
            'kuhanje-brez-odpadkov-kako-uporabiti-korenine-liste-in-stebla',
            'Kuhanje brez odpadkov ni samo ekološka ideja, ampak tudi praktičen način, da iz istega nakupa dobite več okusa in vrednosti. Tukaj je, kako korenine, liste in stebla uporabiti brez občutka, da kuhinja postaja še en naporen projekt.',
            '<ul><li>Kuhanje brez odpadkov najbolje deluje, ko ostane preprosto, praktično in primerno za resničen ritem kuhinje.</li><li>Največja napaka je pristop spremeniti v perfekcionističen izziv, ki ga nihče ne želi vzdrževati.</li><li>Pametnejši pristop uporablja majhne navade: osnove za juhe, pesto, pečenje in boljše shranjevanje delov rastline.</li></ul>',
            [
                ['question' => 'Kaj vse lahko uporabimo poleg glavnega dela zelenjave?', 'answer' => 'Listi, stebla, korenine in celo nekateri olupki imajo pogosto pravo kulinarično vrednost.'],
                ['question' => 'Ali mora biti kuhanje brez odpadkov zapleteno?', 'answer' => 'Ne. Najbolje deluje z nekaj preprostimi navadami, ne z velikim projektom.'],
                ['question' => 'Zakaj je to koristno poleg ekologije?', 'answer' => 'Ker lahko prinese več okusa, manj zavržene hrane in boljšo vrednost istega nakupa.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Poskušati rešiti prav vse in si po nepotrebnem otežiti kuhanje.'],
            ],
            'Kuhanje brez odpadkov: kako uporabiti več rastline brez dodatnega kuhinjskega stresa',
            'Odkrijte, kako je lahko kuhanje brez odpadkov preprostejše, okusnejše in bolj uporabno za vsakdanjo kuhinjo.',
            'Kuhanje brez odpadkov',
            [
                ['heading' => 'Zakaj majhne navade pomenijo več kot popolna trajnost', 'html' => '<p>Večina ljudi ne potrebuje popolne preobrazbe življenjskega sloga, da bi zavrgla manj hrane. Nekaj ponovljivih kuhinjskih navad običajno naredi veliko več kot popolna ideja o “nič odpadka”.</p>'],
                ['heading' => 'Zakaj okus in prihranek pomagata navadi ostati', 'html' => '<p>Ljudje navado veliko lažje ohranimo, ko je hkrati okusna in uporabna. Kuhanje brez odpadkov je najlažje vzdrževati takrat, ko izboljša tako jed kot občutek izkoriščenosti nakupa.</p>'],
            ]
        ),
    ],
];
