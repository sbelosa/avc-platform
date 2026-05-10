<?php

declare(strict_types=1);

$entry = static fn (
    int $id,
    string $title,
    string $excerpt,
    string $summaryHtml,
    array $faqItems,
    string $metaTitle,
    string $metaDescription,
    string $breadcrumbTitle
): array => [
    'content_translation_id' => $id,
    'title' => $title,
    'excerpt' => $excerpt,
    'summary_html' => $summaryHtml,
    'faq_items' => $faqItems,
    'meta_title' => $metaTitle,
    'meta_description' => $metaDescription,
    'breadcrumb_title' => $breadcrumbTitle,
];

return [
    'key' => 'zavrsni-cleanup-hr-wave-1',
    'name' => 'Završni cleanup (HR) - prvi val',
    'notes' => 'Završni ručni premium pack za posljednja četiri HR članka bez SL para.',
    'entries' => [
        $entry(
            629,
            'Vitamin D: snažne kosti, bolji imunitet i vedrije raspoloženje kroz realniji pristup',
            'Vitamin D se često spominje kao rješenje za sve, ali njegova prava vrijednost dolazi tek kada ga gledamo kroz kosti, imunitet, raspoloženje i stvarni životni kontekst. Ovdje je kako ga razumjeti bez pretjerivanja.',
            '<ul><li>Vitamin D može imati važnu ulogu za kosti, imunitet i opći osjećaj vitalnosti.</li><li>Najveća pogreška je očekivati da jedan nutrijent nadoknadi loš san, slabu prehranu i premalo kretanja.</li><li>Pametniji pristup gleda sunce, prehranu i razumnu suplementaciju kroz širi zdravstveni okvir.</li></ul>',
            [
                ['question' => 'Zašto se vitamin D toliko često spominje?', 'answer' => 'Zato što sudjeluje u zdravlju kostiju, imunitetu i širem osjećaju vitalnosti.'],
                ['question' => 'Može li pomoći raspoloženju?', 'answer' => 'Može biti jedan od faktora, ali nikako nije jedini razlog za dobro ili loše raspoloženje.'],
                ['question' => 'Što najviše određuje smisao suplementacije?', 'answer' => 'Sezona, sunce, prehrana i stvarna potreba osobe.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Pretvoriti vitamin D u univerzalno objašnjenje za svaki problem energije i imuniteta.'],
            ],
            'Vitamin D: kako ga gledati za kosti, imunitet i raspoloženje bez pretjerivanja',
            'Saznajte kako vitamin D može podržati kosti, imunitet i raspoloženje kad ga promatrate realnije i šire.',
            'Vitamin D i vitalnost'
        ),
        $entry(
            827,
            'Biljni čajevi: kada ih piti i kako aloe gel može imati smisla kao dodatna podrška',
            'Biljni čajevi imaju dugu tradiciju i praktičnu vrijednost, ali najviše pomažu kada ih koristimo ciljano i umjereno. Ovdje je kako odabrati pravi trenutak za čaj i gdje aloe gel može logično ući u širu rutinu.',
            '<ul><li>Biljni čajevi imaju najviše smisla kada odgovaraju trenutku dana, simptomu i cilju zbog kojeg ih birate.</li><li>Najveća pogreška je očekivati da jedna biljna mješavina riješi sve tegobe i zamijeni ostale navike.</li><li>Pametniji pristup promatra čajeve kao dio smirene rutine, a ne kao jedino rješenje.</li></ul>',
            [
                ['question' => 'Kada je najbolje piti biljne čajeve?', 'answer' => 'To ovisi o vrsti čaja, cilju i dijelu dana kada želite više smirenja, topline ili podrške.'],
                ['question' => 'Mogu li čajevi pomoći probavi i grlu?', 'answer' => 'Često mogu donijeti osjećaj olakšanja i udobnosti kao dio šire rutine.'],
                ['question' => 'Gdje aloe gel može ući u priču?', 'answer' => 'Kao dodatna podrška uz rutinu, ne kao zamjena za sve ostalo što tijelu treba.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Pretvoriti čajeve u jedini plan podrške umjesto u jednostavan dio svakodnevice.'],
            ],
            'Biljni čajevi: kada ih piti i gdje aloe gel ima logike kao podrška',
            'Otkrijte kako biljne čajeve koristiti pametnije i kada aloe gel može imati smisla kao dodatna podrška rutini.',
            'Biljni čajevi'
        ),
        $entry(
            837,
            'Zumba, pilates ili crossfit: kako odabrati grupni trening koji stvarno odgovara vama',
            'Pravi grupni trening nije onaj koji izgleda najuzbudljivije nego onaj kojem se možete vraćati bez osjećaja kazne. Ovdje je kako između zumbe, pilatesa i crossfita birati prema cilju, tijelu i osobnom temperamentu.',
            '<ul><li>Najbolji grupni trening je onaj koji se uklapa u vaše tijelo, ritam i stvarni motivacijski stil.</li><li>Najveća pogreška je birati trening po dojmu ili trendu bez procjene što možete održati.</li><li>Pametniji pristup gleda cilj: energija, snaga, mobilnost, zabava ili osjećaj zajedništva.</li></ul>',
            [
                ['question' => 'Je li jedan grupni trening objektivno najbolji?', 'answer' => 'Ne. Najbolji je onaj koji odgovara vašem cilju, tijelu i načinu na koji ostajete motivirani.'],
                ['question' => 'Za koga zumba često ima smisla?', 'answer' => 'Za osobe kojima treba više zabave, ritma i lakšeg ulaska u redovitije kretanje.'],
                ['question' => 'Kome pilates više odgovara?', 'answer' => 'Onima koji žele rad na kontroli pokreta, mobilnosti, disanju i stabilnosti.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Krenuti u trening koji vas impresionira, ali vam ne odgovara dovoljno da u njemu ostanete.'],
            ],
            'Zumba, pilates ili crossfit: kako odabrati grupni trening koji ćete stvarno održati',
            'Saznajte kako birati između zumbe, pilatesa i crossfita prema cilju, tijelu i stilu motivacije.',
            'Grupni trening'
        ),
        $entry(
            857,
            'Kuhanje bez otpada: kako bolje iskoristiti korijene, listove i stabljike bez kompliciranja',
            'Kuhanje bez otpada nije samo ekološka ideja nego i praktičan način da iz iste kupnje izvučete više okusa i vrijednosti. Ovdje je kako ostatke povrća i biljaka pretvoriti u nešto korisno, a da to ne postane još jedan zamoran projekt.',
            '<ul><li>Kuhanje bez otpada najbolje funkcionira kad ostane jednostavno, praktično i uklopljeno u stvaran ritam kuhinje.</li><li>Najveća pogreška je pretvoriti zero-waste pristup u perfekcionistički zadatak koji nitko ne želi održavati.</li><li>Pametniji pristup koristi male navike: temeljce, pesto, pečenje i pametnije spremanje dijelova biljke.</li></ul>',
            [
                ['question' => 'Što se sve može iskoristiti osim glavnog dijela povrća?', 'answer' => 'Često i listovi, stabljike, korijenje ili kore mogu imati kulinarsku vrijednost.'],
                ['question' => 'Mora li kuhanje bez otpada biti komplicirano?', 'answer' => 'Ne. Najviše smisla ima kada se oslanja na nekoliko malih i lakih navika.'],
                ['question' => 'Zašto je ovaj pristup koristan osim zbog ekologije?', 'answer' => 'Jer može donijeti više okusa, manje bacanja hrane i bolji osjećaj iskorištenosti kupnje.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Htjeti iskoristiti baš sve i tako si nepotrebno zakomplicirati kuhanje.'],
            ],
            'Kuhanje bez otpada: kako iskoristiti više biljke bez dodatnog kuhinjskog stresa',
            'Otkrijte kako kuhanje bez otpada može biti jednostavnije, ukusnije i realnije za svakodnevnu kuhinju.',
            'Kuhanje bez otpada'
        ),
    ],
];
