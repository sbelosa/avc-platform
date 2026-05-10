<?php

declare(strict_types=1);

$faq = static fn (
    string $q1,
    string $a1,
    string $q2,
    string $a2,
    string $q3,
    string $a3,
    string $q4,
    string $a4
): array => [
    ['question' => $q1, 'answer' => $a1],
    ['question' => $q2, 'answer' => $a2],
    ['question' => $q3, 'answer' => $a3],
    ['question' => $q4, 'answer' => $a4],
];

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
    'key' => 'legacy-storefront-skincare-hr-wave-1',
    'name' => 'Legacy storefront i skincare (HR) - prvi val',
    'notes' => 'Ručni premium pack za legacy storefront URL-ove, aloe foundation stranice i starije skincare/product tekstove.',
    'entries' => [
        $entry(
            74,
            'Forever Fiber: kada dnevna vlakna stvarno pomažu apetitu, probavi i ritmu prehrane',
            'Forever Fiber najviše smisla ima kada ga gledamo kao podršku prehrani, ne kao zamjenu za povrće, vodu i uredniji ritam obroka. Ovdje je kako vlakna procijeniti realno i koristiti ih pametnije.',
            '<ul><li>Dodaci vlaknima imaju najviše smisla kada prehrana stalno zaostaje za dnevnim unosom vlakana.</li><li>Najveća pogreška je očekivati da vlakna sama riješe apetit, zatvor i težinu bez rada na navikama.</li><li>Pametniji pristup gleda vodu, ritam obroka i ukupni unos biljne hrane zajedno s proizvodom.</li></ul>',
            $faq(
                'Kada Forever Fiber ima više smisla?',
                'Kada prehrana redovito ima premalo vlakana i kada želite jednostavniju dnevnu podršku probavi.',
                'Može li vlaknasti dodatak zamijeniti zdravu prehranu?',
                'Ne. Najviše vrijedi kao dodatak, a ne kao zamjena za osnovu prehrane.',
                'Zašto su vlakna važna i za apetit?',
                'Zato što bolja raspodjela vlakana često pomaže osjećaju sitosti i stabilnijem ritmu prehrane.',
                'Koja je česta pogreška?',
                'Uvesti vlakna bez dovoljno vode i bez promjene ostatka prehrane.'
            ),
            'Forever Fiber: kada dnevna vlakna imaju smisla za probavu i sitost',
            'Saznajte kada Forever Fiber može biti koristan i kako ga uklopiti u prehranu bez nerealnih očekivanja.',
            'Forever Fiber'
        ),
        $entry(
            75,
            'Gentleman’s Pride: kada aftershave bez alkohola stvarno pomaže koži nakon brijanja',
            'Kod njege nakon brijanja najvažniji su udobnost kože, manje peckanja i bolja podnošljivost rutine. Ovdje je gdje Gentleman’s Pride ima smisla i kome takav aloe losion može bolje odgovarati.',
            '<ul><li>Aftershave bez alkohola ima više smisla za osjetljiviju kožu i korisnike kojima smeta pečenje nakon brijanja.</li><li>Najveća pogreška je misliti da je svaki losion poslije brijanja samo pitanje mirisa i dojma.</li><li>Pametniji pristup gleda osjećaj kože, crvenilo i koliko proizvod stvarno smiruje rutinu brijanja.</li></ul>',
            $faq(
                'Kome Gentleman’s Pride može više odgovarati?',
                'Muškarcima koji žele blaži osjećaj nakon brijanja i manje peckanja na koži.',
                'Zašto je formula bez alkohola nekima važna?',
                'Zato što može biti ugodnija za kožu koja se lako zacrveni ili isuši nakon brijanja.',
                'Može li dobar aftershave popraviti cijelu rutinu brijanja?',
                'Može pomoći, ali i tehnika brijanja i priprema kože ostaju važni.',
                'Koja je česta pogreška?',
                'Birati proizvod samo po mirisu, bez razmišljanja kako koža reagira nakon britvice.'
            ),
            'Gentleman’s Pride: kada aftershave bez alkohola ima više smisla',
            'Otkrijte kome Gentleman’s Pride može odgovarati i zašto aloe aftershave bez alkohola nekim tipovima kože više godi.',
            'Gentleman’s Pride'
        ),
        $entry(
            76,
            'Forever Activator: kada dubinska hidratacija i priprema kože imaju više smisla od agresivnih aktivnih formula',
            'Activator ima vrijednost kad koži treba više vode, manje iritacije i bolja podloga za daljnju njegu. Ovdje je kako ga procijeniti bez prenaglašavanja i kome najviše odgovara.',
            '<ul><li>Forever Activator najviše smisla ima kada koža traži hidrataciju, smirenje i bolju podlogu za rutinu njege.</li><li>Najveća pogreška je očekivati od hidratantnog proizvoda rezultate koje daju sasvim druge kategorije aktivnih formula.</li><li>Pametniji pristup gleda barijeru kože, podnošljivost i dosljednost korištenja.</li></ul>',
            $faq(
                'Za što se Forever Activator najčešće koristi?',
                'Najčešće kao hidratantna i umirujuća baza unutar njege lica i osjetljivije kože.',
                'Kome može više odgovarati?',
                'Koži koja traži ugodu, vlagu i manje agresivan pristup njezi.',
                'Može li zamijeniti cijelu anti-age rutinu?',
                'Ne. Najviše smisla ima kao jedan koristan hidratantni korak.',
                'Koja je česta pogreška?',
                'Očekivati dramatičnu transformaciju umjesto stabilne i ugodne podrške koži.'
            ),
            'Forever Activator: kada dubinska hidratacija kože stvarno ima smisla',
            'Saznajte kada Forever Activator može pomoći koži i zašto dobar hidratantni korak često vrijedi više od agresivnije rutine.',
            'Forever Activator'
        ),
        $entry(
            77,
            'Forever Awakening Eye Cream: kada krema za područje oko očiju stvarno donosi više udobnosti i svježine',
            'Područje oko očiju ne traži čuda nego nježnost, hidraciju i rutinu koja se može održati. Ovdje je kako Awakening Eye Cream procijeniti kroz stvarne potrebe kože i svakodnevni ritam.',
            '<ul><li>Krema za područje oko očiju ima smisla kada donosi više ugode, manje isušivanja i uredniju rutinu njege.</li><li>Najveća pogreška je očekivati da će sama krema izbrisati umor, san i životni stil.</li><li>Pametniji pristup gleda teksturu, podnošljivost i realna očekivanja od njege oko očiju.</li></ul>',
            $faq(
                'Kada krema za područje oko očiju ima više smisla?',
                'Kada je koža tanka, suha i traži nježniji hidratantni korak.',
                'Može li Eye Cream sama riješiti podočnjake i natečenost?',
                'Ne u potpunosti. Najviše smisla ima kao dio šire rutine i boljeg oporavka.',
                'Zašto ljudi vole zasebnu kremu za oči?',
                'Zbog blažeg osjećaja i lakše, ciljanije njege osjetljivog područja.',
                'Koja je česta pogreška?',
                'Tražiti u kremi ono što zapravo više ovisi o snu, stresu i navikama.'
            ),
            'Awakening Eye Cream: kada njega oko očiju stvarno ima smisla',
            'Otkrijte kada Forever Awakening Eye Cream može pomoći području oko očiju i kako je procijeniti bez prenapuhanih očekivanja.',
            'Awakening Eye Cream'
        ),
        $entry(
            78,
            'Aloe Moisturising Lotion: zašto ova klasična Forever krema i dalje ima svoje mjesto u osnovnoj njezi kože',
            'Neke kreme ostaju popularne zato što su jednostavne, podnošljive i lako uklopive u svakodnevnu rutinu. Ovdje je zašto Aloe Moisturising Lotion i dalje ima smisla i kome najviše odgovara.',
            '<ul><li>Klasična hidratantna krema ima najviše smisla kada korisniku treba stabilna, ugodna i jednostavna njega.</li><li>Najveća pogreška je podcijeniti vrijednost osnovne hidratacije i stalno tražiti složenije proizvode.</li><li>Pametniji pristup gleda dosljednost, osjećaj na koži i koliko proizvod stvarno olakšava rutinu.</li></ul>',
            $faq(
                'Kome Aloe Moisturising Lotion može više odgovarati?',
                'Korisnicima koji vole jednostavnu, klasičnu i ugodnu hidratantnu njegu.',
                'Zašto neke osnovne kreme ostaju tako popularne?',
                'Zato što su praktične, podnošljive i lako se koriste svaki dan.',
                'Može li osnovna krema biti dovoljna za puno ljudi?',
                'Da, osobito kada koža ne traži kompleksnu aktivnu rutinu.',
                'Koja je česta pogreška?',
                'Preskakati osnovnu hidrataciju i očekivati da će aktivni proizvodi sami riješiti sve.'
            ),
            'Aloe Moisturising Lotion: zašto jednostavna krema često ima najviše smisla',
            'Saznajte kome Aloe Moisturising Lotion može najviše odgovarati i zašto osnovna hidratacija kože često donosi najveću razliku.',
            'Aloe Moisturising Lotion'
        ),
        $entry(
            79,
            'Forever Alpha E Factor: kada bogatija i luksuznija tekstura kože zaista donosi vrijednost',
            'Alpha E Factor ima smisla kada koža traži više udobnosti, hranjivosti i osjećaj kvalitetnije njege, ali bez nerealnog očekivanja da jedna formula mijenja sve. Ovdje je kako je procijeniti zrelije.',
            '<ul><li>Bogatija luksuznija njega ima više smisla kad koža traži više hranjivosti i zaštitnog osjećaja.</li><li>Najveća pogreška je misliti da skuplja ili bogatija formula automatski znači i bolji rezultat za svakoga.</li><li>Pametniji pristup gleda tip kože, teksturu i dugoročnu održivost rutine.</li></ul>',
            $faq(
                'Kome Alpha E Factor može više odgovarati?',
                'Koži koja voli bogatiji osjećaj, više udobnosti i hranjiviju teksturu.',
                'Je li bogatija krema uvijek bolji izbor?',
                'Ne. Ovisi o tipu kože, sezoni i tome koliko korisniku odgovara takva formula.',
                'Zašto ljudi vole ovakve proizvode?',
                'Zbog dojma luksuza, ugode i osjećaja dublje njege.',
                'Koja je česta pogreška?',
                'Birati teksturu prema dojmu, a ne prema stvarnim potrebama kože.'
            ),
            'Forever Alpha E Factor: kada bogatija njega kože stvarno vrijedi',
            'Otkrijte kada Forever Alpha E Factor ima više smisla i zašto bogatija njega kože nije automatski najbolja za svakoga.',
            'Alpha E Factor'
        ),
        $entry(
            80,
            'Forever MSM Gel: kada spoj sumpora i aloe gela ima više smisla za lokalnu njegu i osjećaj olakšanja',
            'MSM Gel je proizvod koji ljudi često vežu uz lokalnu primjenu, osjećaj hlađenja i ugodniju podršku koži ili području koje traži pažnju. Ovdje je kako ga gledati korisno i bez pretjerivanja.',
            '<ul><li>MSM Gel ima najviše smisla kao lokalni praktični proizvod za njegu i osjećaj ugode na ciljanim područjima.</li><li>Najveća pogreška je tretirati ga kao univerzalno rješenje za svaku lokalnu nelagodu.</li><li>Pametniji pristup gleda situaciju primjene, redovitost i realna očekivanja od topikalnog proizvoda.</li></ul>',
            $faq(
                'Za što ljudi najčešće koriste MSM Gel?',
                'Najčešće za lokalnu njegu i osjećaj ugode na određenim područjima kože ili tijela.',
                'Zašto je kombinacija MSM-a i aloe zanimljiva?',
                'Zbog osjećaja hlađenja, lakoće i praktične primjene na koži.',
                'Može li gel riješiti sve lokalne tegobe?',
                'Ne. Najviše smisla ima kao podrška i praktičan dodatak rutini.',
                'Koja je česta pogreška?',
                'Očekivati od gela više nego što topikalni proizvod može realno pružiti.'
            ),
            'Forever MSM Gel: kada lokalna njega s aloe i sumporom ima smisla',
            'Saznajte kada Forever MSM Gel može biti koristan i kako ga uklopiti u realnu rutinu lokalne njege.',
            'Forever MSM Gel'
        ),
        $entry(
            81,
            'Forever R3 Factor: kada anti-age krema ima više smisla kao podrška barijeri nego kao veliko obećanje',
            'R3 Factor ima smisla kada se promatra kroz suhoću, osjećaj zrelije kože i želju za bogatijom rutinom, a ne kroz priču o čudesnom zaokretu. Ovdje je kako ga procijeniti pametnije.',
            '<ul><li>R3 Factor najviše smisla ima kada koži treba više ugode, elastičnosti i hranjivijeg anti-age dojma.</li><li>Najveća pogreška je jednu kremu pretvoriti u glavni odgovor na sve znakove starenja.</li><li>Pametniji pristup gleda barijeru kože, zaštitu od sunca i ukupnu rutinu njege zajedno s proizvodom.</li></ul>',
            $faq(
                'Kome R3 Factor može više odgovarati?',
                'Sušoj i zrelijoj koži koja traži bogatiji osjećaj njege i zaštite.',
                'Može li jedna anti-age krema sama napraviti veliku razliku?',
                'Najčešće ne. Najveći učinak dolazi iz cijele rutine i dosljednosti.',
                'Zašto ovakve kreme ostaju popularne?',
                'Zbog osjećaja luksuza, udobnosti i dojma obnovljene njege kože.',
                'Koja je česta pogreška?',
                'Zanemariti osnovne korake poput zaštite od sunca i od jedne kreme očekivati sve.'
            ),
            'Forever R3 Factor: kada anti-age krema ima stvarnu ulogu u njezi',
            'Otkrijte kada Forever R3 Factor može imati smisla i zašto anti-age krema najbolje djeluje kao dio šire rutine.',
            'Forever R3 Factor'
        ),
        $entry(
            82,
            'Infinite by Forever: kada anti-age set ima smisla, a kada je korisnija jednostavnija rutina',
            'Setovi za njegu privlačni su jer obećavaju kompletno rješenje, ali njihova vrijednost ovisi o tome odgovara li takav sustav stvarno koži i korisniku. Ovdje je kako Infinite by Forever gledati realno.',
            '<ul><li>Anti-age set ima smisla kada korisnik želi uređen sustav i može ga dosljedno pratiti.</li><li>Najveća pogreška je kupiti cijeli set bez provjere odgovara li koži i tempu svakodnevice.</li><li>Pametniji pristup gleda podnošljivost, realnu upotrebu i koliko rutina ostaje održiva kroz vrijeme.</li></ul>',
            $faq(
                'Kome kompletan skincare set može više odgovarati?',
                'Korisnicima koji vole jasno složenu rutinu i žele manje nagađanja oko koraka njege.',
                'Je li set uvijek bolji od jednostavne rutine?',
                'Ne. Nekima više odgovara nekoliko pažljivo odabranih proizvoda nego cijeli sustav.',
                'Zašto je dosljednost važna kod setova?',
                'Zato što vrijednost seta dolazi tek kada ga korisnik zaista koristi kroz vrijeme.',
                'Koja je česta pogreška?',
                'Kupiti set zbog dojma ekskluzivnosti, a zatim ga ne koristiti redovito.'
            ),
            'Infinite by Forever: kada kompletan anti-age set ima smisla',
            'Saznajte kada Infinite by Forever može biti dobar izbor i zašto skincare set nije uvijek bolji od jednostavnije rutine.',
            'Infinite by Forever'
        ),
        $entry(
            83,
            'Sonya Deep Moisturizing Cream: kada dubinska hidratacija koži daje više vrijednosti od aktivnih trendova',
            'Dubinska hidratacija često je podcijenjena upravo zato što ne zvuči spektakularno, a mnogim tipovima kože donosi najveće olakšanje. Ovdje je kako Sonya kremu gledati kroz stvarne potrebe kože.',
            '<ul><li>Dubinski hidratantna krema ima smisla kada koža traži više vode, ugode i stabilnosti barijere.</li><li>Najveća pogreška je preskakati hidrataciju i tražiti rješenje samo u aktivnim sastojcima.</li><li>Pametniji pristup gleda kako koža reagira na vlagu, teksturu i kontinuiranu njegu.</li></ul>',
            $faq(
                'Kome Sonya Deep Moisturizing Cream može više odgovarati?',
                'Koži koja često djeluje zategnuto, dehidrirano ili umorno te traži više ugode.',
                'Zašto je dubinska hidratacija toliko važna?',
                'Zato što bez nje koža često teže podnosi ostatak rutine i brže gubi osjećaj ravnoteže.',
                'Je li dubinska hidratacija dovoljna sama za sve?',
                'Ne, ali je vrlo često važna baza za stabilniju njegu.',
                'Koja je česta pogreška?',
                'Podcijeniti koliko dobra hidratacija može promijeniti cijeli osjećaj kože.'
            ),
            'Sonya Deep Moisturizing Cream: kada dubinska hidratacija koži najviše treba',
            'Otkrijte kada Sonya Deep Moisturizing Cream ima više smisla i zašto dubinska hidratacija često vrijedi više od trendova.',
            'Sonya Deep Moisturizing Cream'
        ),
        $entry(
            84,
            'Forever Balancing Toner: kada tonik stvarno pomaže rutini njege, a kada je samo dekoracija u kupaonici',
            'Tonik ima smisla kada doprinosi ugodi, ravnoteži i boljoj pripremi kože, a ne kada je samo još jedan korak bez jasne funkcije. Ovdje je kako Forever Balancing Toner procijeniti realnije.',
            '<ul><li>Tonik ima najviše smisla kada kožu čini ugodnijom, mirnijom i spremnijom za ostatak rutine.</li><li>Najveća pogreška je koristiti tonik bez razloga samo zato što “tako ide” u skincareu.</li><li>Pametniji pristup gleda kako koža reagira na tonik i doprinosi li zaista osjećaju ravnoteže.</li></ul>',
            $faq(
                'Kada tonik ima više smisla u rutini?',
                'Kada pomaže osjećaju svježine, udobnosti i pripremi kože za sljedeće korake.',
                'Treba li svatko koristiti tonik?',
                'Ne nužno. Nekim kožama tonik puno znači, drugima je manje važan.',
                'Zašto ljudi vole ovakve proizvode?',
                'Zbog osjećaja čistoće, ravnoteže i urednije rutine njege.',
                'Koja je česta pogreška?',
                'Dodavati tonik automatski, bez provjere donosi li koži išta korisno.'
            ),
            'Forever Balancing Toner: kada tonik stvarno ima ulogu u njezi',
            'Saznajte kada Forever Balancing Toner može biti koristan i zašto tonik ima smisla samo kad koži stvarno pomaže.',
            'Forever Balancing Toner'
        ),
        $entry(
            85,
            'Aloe vera u stanu: kako uzgojiti korisnu biljku bez previše zalijevanja, panike i improvizacije',
            'Aloe vera u stanu uspijeva najbolje kada joj damo dovoljno svjetla, dobru drenažu i manje previše brižne njege. Ovdje je kako ju uzgajati jednostavno i iskoristiti ono što biljka realno može ponuditi.',
            '<ul><li>Aloe vera u stanu najbolje raste uz više svjetla, manje vode i dobar supstrat.</li><li>Najveća pogreška je prema aloe veri odnositi se kao prema biljci koja stalno traži zalijevanje i intervencije.</li><li>Pametniji pristup gleda stabilnost uvjeta i realnu kućnu upotrebu biljke.</li></ul>',
            $faq(
                'Što aloe veri u stanu najviše treba?',
                'Puno svjetla, prozračan supstrat i umjereno zalijevanje.',
                'Zašto aloe kod kuće često propadne?',
                'Najčešće zbog previše vode, slabe drenaže ili premalo svjetla.',
                'Može li kućna aloe biti praktična?',
                'Može, ako je biljka zdrava i ako imate realna očekivanja od kućne upotrebe.',
                'Koja je česta pogreška?',
                'Previše pomagati biljci koja zapravo traži mirnije uvjete.'
            ),
            'Aloe vera u stanu: kako je uzgajati jednostavno i pametno',
            'Otkrijte kako aloe veru u stanu uzgajati s manje grešaka i kako joj dati uvjete u kojima zaista uspijeva.',
            'Aloe vera u stanu'
        ),
        $entry(
            86,
            'Smoothing Exfoliator: kada piling za lice pomaže svježini tena, a kada ga je bolje koristiti rjeđe',
            'Piling ima smisla kad koži vraća svježinu i glatkoću bez narušavanja ugode, ali samo ako je dobro usklađen s tipom kože i učestalošću. Ovdje je kako procijeniti Smoothing Exfoliator bez pretjerivanja.',
            '<ul><li>Piling za lice ima više smisla kada doprinosi glatkijem i svježijem tenu bez dodatne iritacije.</li><li>Najveća pogreška je pretjerivati s pilingom i misliti da će više trenja dati bolji rezultat.</li><li>Pametniji pristup gleda osjetljivost kože, učestalost i ostatak skincare rutine.</li></ul>',
            $faq(
                'Kada piling za lice ima više smisla?',
                'Kada koža treba osjećaj svježine i glađe površine, ali bez pretjerivanja.',
                'Može li prečest piling škoditi?',
                'Može, osobito kod osjetljivije kože koja loše podnosi previše mehaničkog ili aktivnog djelovanja.',
                'Zašto ljudi vole ovakve proizvode?',
                'Zbog trenutnog osjećaja glatkoće, čistoće i svježijeg izgleda tena.',
                'Koja je česta pogreška?',
                'Koristiti piling prečesto i zamijeniti svježinu s agresivnošću.'
            ),
            'Smoothing Exfoliator: kada piling za lice ima najviše smisla',
            'Saznajte kada Smoothing Exfoliator može pomoći svježijem tenu i zašto piling za lice treba koristiti promišljenije.',
            'Smoothing Exfoliator'
        ),
        $entry(
            87,
            'Forever Living Products proizvodi: kako se snaći u katalogu i odabrati ono što stvarno odgovara vašem cilju',
            'Velik katalog nije prednost ako ne znate što tražite. Ovdje je kako se snaći u Forever ponudi, povezati cilj s pravom kategorijom proizvoda i izbjeći impulzivnu kupnju bez plana.',
            '<ul><li>Najbolji izbor proizvoda dolazi kada prvo definirate cilj, a tek onda gledate katalog.</li><li>Najveća pogreška je kupovati prema dojmu, popularnosti ili samo jednoj preporuci bez konteksta.</li><li>Pametniji pristup razdvaja proizvode za probavu, skincare, vitalnost i specifične rutine podrške.</li></ul>',
            $faq(
                'Kako se najbolje snaći u velikom Forever katalogu?',
                'Tako da prvo odredite svoj cilj, a zatim suzite izbor na relevantnu kategoriju proizvoda.',
                'Zašto ljudi često kupe pogrešan proizvod?',
                'Zato što kreću od hypea ili dojma, a ne od stvarne potrebe.',
                'Je li bolje uzeti više proizvoda odjednom?',
                'Ne nužno. Često je pametnije krenuti s manjim i jasnijim izborom.',
                'Koja je česta pogreška?',
                'Kupovati cijelu grupu proizvoda bez razumijevanja što zapravo želite postići.'
            ),
            'Forever Living Products proizvodi: kako birati pametnije kroz katalog',
            'Otkrijte kako se snaći u Forever katalogu i odabrati proizvode koji stvarno odgovaraju vašem cilju i rutini.',
            'Forever proizvodi katalog'
        ),
        $entry(
            89,
            'Aloe vera sok: kada ima smisla za dnevnu rutinu i zašto ga nije dobro svesti samo na imunitet',
            'Aloe vera sok ljudi često uzimaju zbog imuniteta, ali njegova vrijednost najčešće je šira i povezana s rutinom, probavom i osjećajem vitalnosti. Ovdje je kako ga gledati realnije i pametnije.',
            '<ul><li>Aloe vera sok ima najviše smisla kada je dio dnevne rutine, a ne jednokratni odgovor na sezonski strah.</li><li>Najveća pogreška je cijelu priču o soku svesti samo na imunitet bez gledanja ostatka životnog stila.</li><li>Pametniji pristup gleda probavu, navike i dosljednost korištenja zajedno s općom brigom o zdravlju.</li></ul>',
            $faq(
                'Zašto ljudi piju aloe vera sok?',
                'Najčešće zbog interesa za probavu, imunitet i opći osjećaj svakodnevne vitalnosti.',
                'Je li aloe sok samo proizvod za imunitet?',
                'Ne. Ljudi ga često koriste i kao dio šire wellness i probavne rutine.',
                'Kada ima više smisla?',
                'Kada ga uvodite dosljedno i u sklopu urednije dnevne rutine.',
                'Koja je česta pogreška?',
                'Očekivati da će sok sam nadoknaditi loš san, prehranu i stres.'
            ),
            'Aloe vera sok: kada ima smisla za rutinu, probavu i vitalnost',
            'Saznajte kada aloe vera sok može biti koristan i zašto ga vrijedi gledati šire od same priče o imunitetu.',
            'Aloe vera sok'
        ),
        $entry(
            90,
            'Aloe vera kroz povijest: što i danas vrijedi znati o biljci koja je preživjela stoljeća interesa',
            'Povijest aloe vere važna je zato što pokazuje koliko je duboko ova biljka ušla u kulturu njege i podrške organizmu. Ovdje je što iz te priče i danas vrijedi razumjeti bez pretjerivanja.',
            '<ul><li>Povijest aloe vere pomaže razumjeti zašto je biljka i danas toliko prisutna u njezi i wellnessu.</li><li>Najveća pogreška je povijesnu fascinaciju pretvoriti u nekritičnu potvrdu svih modernih tvrdnji.</li><li>Pametniji pristup povezuje tradiciju s procjenom konkretnih suvremenih proizvoda.</li></ul>',
            $faq(
                'Zašto je aloe vera toliko prisutna kroz povijest?',
                'Zato što je stoljećima bila cijenjena u različitim kulturama za njegu i podršku svakodnevici.',
                'Što nam povijest aloe danas govori?',
                'Da je riječ o biljci s trajnim ugledom, ali i da suvremene proizvode ipak treba procjenjivati zasebno.',
                'Zašto ljudi vole ovakve priče o biljkama?',
                'Jer daju širi smisao i kontekst modernoj upotrebi proizvoda.',
                'Koja je česta pogreška?',
                'Zamijeniti povijesnu vrijednost s automatskom kvalitetom svakog proizvoda na tržištu.'
            ),
            'Aloe vera kroz povijest: što i danas stvarno vrijedi znati',
            'Otkrijte što priča o aloe veri kroz povijest i danas može naučiti korisnika koji želi birati proizvode informiranije.',
            'Aloe vera kroz povijest'
        ),
        $entry(
            91,
            'Vital 5: kako razumjeti ovaj Forever koncept zdravlja i vitalnosti bez pojednostavljivanja',
            'Vital 5 je koristan tek kada ga gledamo kao okvir za navike i rutinu, a ne kao marketinški slogan. Ovdje je kako razumjeti ovaj koncept i gdje on stvarno može pomoći korisniku.',
            '<ul><li>Vital 5 ima više smisla kao okvir za razmišljanje o zdravlju nego kao popis koji čudesno rješava sve.</li><li>Najveća pogreška je koncept zdravlja pretvoriti u slogan bez stvarne primjene u svakodnevici.</li><li>Pametniji pristup gleda kako se osnovni stupovi rutine doista žive iz dana u dan.</li></ul>',
            $faq(
                'Što je zapravo Vital 5 koncept?',
                'To je okvir koji pokušava pojednostaviti ključne stupove zdravijeg i vitalnijeg života.',
                'Može li koncept sam po sebi promijeniti zdravlje?',
                'Ne. Vrijednost dolazi tek kada se doista primjenjuje kroz rutinu i navike.',
                'Zašto je ovakav okvir ljudima koristan?',
                'Zato što zdravlje čini preglednijim i lakšim za organizirati u svakodnevici.',
                'Koja je česta pogreška?',
                'Zadržati se na ideji bez pretvaranja ideje u konkretno ponašanje.'
            ),
            'Vital 5: kako ga pretvoriti u stvaran okvir zdravlja i vitalnosti',
            'Saznajte kako razumjeti Vital 5 koncept i gdje on korisniku može stvarno pomoći u organizaciji zdravijih navika.',
            'Vital 5'
        ),
        $entry(
            98,
            'Forever cijene proizvoda i katalog: kako gledati vrijednost, a ne samo broj uz svaki proizvod',
            'Katalog i cijene pomažu tek kada ih znate čitati kroz vrijednost, učestalost korištenja i realnu potrebu. Ovdje je kako pristupiti cijenama Forever proizvoda bez impulzivnosti i bez pogrešnog dojma skupoće ili “super ponude”.',
            '<ul><li>Cijenu proizvoda treba gledati zajedno s namjenom, količinom i stvarnom učestalošću korištenja.</li><li>Najveća pogreška je uspoređivati cijene bez razumijevanja kategorije i razloga za kupnju.</li><li>Pametniji pristup gleda vrijednost po rutini i dugoročnu korisnost, a ne samo istaknutu cifru.</li></ul>',
            $faq(
                'Kako je najbolje čitati katalog i cijene?',
                'Kroz odnos između namjene proizvoda, količine i učestalosti korištenja.',
                'Zašto niža cijena nije uvijek bolja kupnja?',
                'Jer proizvod mora odgovarati cilju i rutini, inače ostaje neiskorišten.',
                'Što pomaže pri pametnijem odabiru?',
                'Jasan cilj, manji fokus i usporedba vrijednosti, a ne samo cijene.',
                'Koja je česta pogreška?',
                'Kupovati prema osjećaju “akcije” umjesto prema stvarnoj potrebi.'
            ),
            'Forever cijene proizvoda: kako katalog čitati kroz stvarnu vrijednost',
            'Otkrijte kako gledati Forever katalog i cijene proizvoda pametnije, s više fokusa na vrijednost i manje na impulzivni dojam.',
            'Forever cijene i katalog'
        ),
        $entry(
            101,
            'Forever Aloe Vera Gel: zašto ovaj klasik mnogima postaje baza vitalnosti, a ne samo povremeni proizvod',
            'Forever Aloe Vera Gel ima smisla kada ga promatrate kao dio rutine i osjećaja vitalnosti, a ne kao proizvod koji se uzima samo “kad nešto zatreba”. Ovdje je kako ga uključiti smislenije i realnije.',
            '<ul><li>Forever Aloe Vera Gel ima više smisla kada je dio dosljednije wellness i probavne rutine.</li><li>Najveća pogreška je koristiti ga povremeno i od tog povremenog korištenja očekivati puni učinak.</li><li>Pametniji pristup gleda gel kao bazni proizvod unutar šireg obrasca njege organizma i navika.</li></ul>',
            $faq(
                'Zašto ljudi Forever Aloe Vera Gel doživljavaju kao bazni proizvod?',
                'Zato što ga mnogi vežu uz svakodnevnu rutinu, probavu i opći osjećaj vitalnosti.',
                'Je li gel bolji kad se koristi dosljedno?',
                'Najčešće da, jer rutina često daje smisao ovakvim proizvodima.',
                'Može li sam gel biti dovoljan za osjećaj vitalnosti?',
                'Ne u potpunosti. Najviše vrijedi kao dio šireg zdravijeg ritma života.',
                'Koja je česta pogreška?',
                'Gledati ga kao sezonsku pomoć umjesto kao stabilan svakodnevni korak.'
            ),
            'Forever Aloe Vera Gel: kada postaje baza rutine i vitalnosti',
            'Saznajte zašto Forever Aloe Vera Gel mnogima ima najviše smisla kao dio dnevne rutine, a ne samo povremeni proizvod.',
            'Forever Aloe Vera Gel'
        ),
        $entry(
            102,
            'Proteinski suplementi: što stvarno djeluje za jačanje mišića, a što je samo skupa konfuzija',
            'Proteinski suplementi imaju smisla samo kada su dio treninga, prehrane i oporavka, a ne čarobni prečac do mišića. Ovdje je kako ih procijeniti ozbiljnije i bez fitness mitova.',
            '<ul><li>Proteinski suplementi imaju najviše smisla kada prehranom teško dosežete potreban unos proteina.</li><li>Najveća pogreška je očekivati da će protein sam graditi mišiće bez treninga i oporavka.</li><li>Pametniji pristup gleda ukupni unos proteina, trening opterećenja i praktičnost dodatka.</li></ul>',
            $faq(
                'Kada proteinski suplement ima više smisla?',
                'Kada redovitom prehranom teško postižete unos proteina koji vam je potreban.',
                'Može li protein sam graditi mišiće?',
                'Ne. Mišići rastu kroz trening, oporavak i dovoljno ukupnog unosa proteina.',
                'Zašto ljudi pretjeruju s proteinskim dodacima?',
                'Jer zvuče jednostavno i brzo, pa se često precijeni njihova samostalna uloga.',
                'Koja je česta pogreška?',
                'Kupiti protein prije nego što je riješen trening i osnovna prehrana.'
            ),
            'Proteinski suplementi: kada stvarno djeluju za mišiće i oporavak',
            'Otkrijte kada proteinski suplementi imaju smisla i kako ih procijeniti bez fitness mitova i marketinške konfuzije.',
            'Proteinski suplementi'
        ),
    ],
];
