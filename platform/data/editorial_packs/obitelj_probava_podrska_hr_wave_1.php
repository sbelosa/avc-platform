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
    'key' => 'obitelj-probava-podrska-hr-wave-1',
    'name' => 'Obitelj, probava i dnevna podrška (HR) - prvi val',
    'notes' => 'Ručni premium pack za dječji stres, probavne teme, dodatke prehrani i svakodnevnu podršku obitelji.',
    'entries' => [
        $entry(
            574,
            'Djeca s ADHD-om: tehnike smirivanja, prehrana i podrška bez brzih etiketa',
            'Kod djece s ADHD-om najviše pomaže kada podrška postane praktična, nježna i ponovljiva, a ne savršena. Ovdje je kako gledati na smirivanje, prehranu i svakodnevnu strukturu bez prejednostavnih rješenja.',
            '<ul><li>Podrška djeci s ADHD-om najbolje radi kad povezuje rutinu, emocije, prehranu i okruženje.</li><li>Najveća pogreška je tražiti jedan trik koji će odmah riješiti sve izazove ponašanja i fokusa.</li><li>Pametniji pristup gradi male ponovljive rituale koji djetetu vraćaju osjećaj sigurnosti i ritma.</li></ul>',
            [
                ['question' => 'Što djeci s ADHD-om najčešće pomaže?', 'answer' => 'Jasna rutina, mirnija komunikacija i podrška koja se ponavlja kroz dan.'],
                ['question' => 'Može li prehrana sama riješiti problem?', 'answer' => 'Ne. Prehrana može biti dio podrške, ali nije jedino rješenje.'],
                ['question' => 'Zašto je smirivanje toliko važno?', 'answer' => 'Zato što dijete lakše surađuje kad se osjeća sigurnije i manje preplavljeno.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Mijenjati previše stvari odjednom i očekivati trenutni preokret.'],
            ],
            'Djeca s ADHD-om: kako graditi podršku kroz rutinu, prehranu i smirivanje',
            'Saznajte kako djeci s ADHD-om pomoći kroz praktične navike, smirivanje i jasniju dnevnu strukturu.',
            'Djeca i ADHD'
        ),
        $entry(
            583,
            'Hipervitaminoza D: kako prepoznati pretjeran unos i ostati na sigurnoj strani',
            'Vitamin D je koristan samo dok ostaje u zoni pametne primjene. Ovdje je kako prepoznati kada dodatak prelazi u pretjerivanje i zašto više nije uvijek bolje.',
            '<ul><li>Vitamin D ima važnu ulogu, ali pretjeran unos može stvoriti nepotrebne rizike.</li><li>Najveća pogreška je dugotrajno uzimati visoke doze bez jasnog razloga i praćenja.</li><li>Pametniji pristup gleda potrebu, kontekst i sigurnije doziranje kroz vrijeme.</li></ul>',
            [
                ['question' => 'Što je hipervitaminoza D?', 'answer' => 'To je stanje povezano s pretjeranim unosom vitamina D kroz dodatke.'],
                ['question' => 'Zašto ljudi pretjeraju?', 'answer' => 'Jer vitamin D ima dobar ugled pa se lako stvori dojam da više znači i bolje.'],
                ['question' => 'Kako ostati sigurniji?', 'answer' => 'Držati se razumnog okvira i ne improvizirati s visokim dozama bez razloga.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Prepisivati tuđe protokole bez vlastitog konteksta i opreza.'],
            ],
            'Hipervitaminoza D: kada koristan dodatak prelazi u nepotrebni rizik',
            'Razumijte kako prepoznati pretjeran unos vitamina D i kako ostati na sigurnijoj strani suplementacije.',
            'Hipervitaminoza D'
        ),
        $entry(
            586,
            'Najbolje vrijeme za uzimanje multivitamina: ujutro, navečer ili uz obrok',
            'Multivitamin ne djeluje bolje zato što ga uzmete u točno savršenoj minuti dana, ali način uzimanja ipak može utjecati na podnošljivost i dosljednost. Ovdje je kako pronaći termin koji ima smisla za stvarni život.',
            '<ul><li>Najbolje vrijeme za multivitamin je ono koje omogućuje redovitost i dobru podnošljivost.</li><li>Najveća pogreška je tražiti savršeni timing, a zanemariti dosljednost i reakciju organizma.</li><li>Pametniji pristup multivitamin uklapa uz obrok i ritam koji možete održati.</li></ul>',
            [
                ['question' => 'Je li jutro uvijek najbolje?', 'answer' => 'Ne nužno. Nekima više odgovara uz prvi veći obrok, a nekima kasnije.'],
                ['question' => 'Zašto je obrok važan?', 'answer' => 'Jer često poboljšava podnošljivost i olakšava redovitost.'],
                ['question' => 'Što je važnije od termina?', 'answer' => 'Dosljednost i to da dodatak ne opterećuje želudac ili rutinu.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Odustati od dodatka jer vrijeme nije savršeno umjesto prilagoditi ga svakodnevici.'],
            ],
            'Multivitamin: kako izabrati termin koji ćete stvarno moći održati',
            'Saznajte kada je najbolje uzimati multivitamin i zašto dosljednost često znači više od savršenog timinga.',
            'Multivitamin i vrijeme uzimanja'
        ),
        $entry(
            587,
            'Prirodna potpora plodnosti: maca, vitex i akupunktura kroz realniju perspektivu',
            'Prirodna potpora plodnosti može imati mjesto u širem planu, ali najviše koristi kad se ne pretvori u potragu za jednim čudesnim rješenjem. Ovdje je kako te opcije gledati smirenije i informiranije.',
            '<ul><li>Dodaci i komplementarne metode imaju najviše smisla kao dio šireg plana, ne kao jedini odgovor.</li><li>Najveća pogreška je prebaciti svu nadu na jedan pripravak ili jednu tehniku.</li><li>Pametniji pristup povezuje informacije, strpljenje i realna očekivanja.</li></ul>',
            [
                ['question' => 'Zašto se spominju maca i vitex?', 'answer' => 'Zato što ih ljudi često povezuju s hormonalnom ravnotežom i podrškom plodnosti.'],
                ['question' => 'Je li akupunktura univerzalno rješenje?', 'answer' => 'Ne. Može imati mjesto podrške, ali nije sama po sebi odgovor na sve.'],
                ['question' => 'Što je najvažnije?', 'answer' => 'Realna očekivanja i sagledavanje plodnosti kroz širu sliku zdravlja i stresa.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Trošiti energiju na mnoštvo metoda bez jasnog prioriteta i okvira.'],
            ],
            'Prirodna potpora plodnosti: kako gledati maca, vitex i akupunkturu bez iluzija',
            'Otkrijte kako prirodnu podršku plodnosti procijeniti mirnije i uklopiti u širu sliku zdravlja i planiranja.',
            'Potpora plodnosti'
        ),
        $entry(
            589,
            'Stres kod djece: kako prepoznati signale i smiriti ih prirodnijim ritmom',
            'Dječji stres rijetko izgleda kao odrasli stres, ali ga se često osjeti kroz ponašanje, san i dnevnu napetost. Ovdje je kako ga prepoznati na vrijeme i kako djetetu vratiti više sigurnosti i mira.',
            '<ul><li>Stres kod djece često se vidi kroz promjene ponašanja, sna i emocionalne osjetljivosti.</li><li>Najveća pogreška je takve signale tumačiti samo kao neposluh ili fazu.</li><li>Pametniji pristup promatra obrasce i djetetu vraća ritam, prisutnost i osjećaj sigurnosti.</li></ul>',
            [
                ['question' => 'Kako se stres kod djece najčešće pokazuje?', 'answer' => 'Kroz razdražljivost, povlačenje, lošiji san ili jaču osjetljivost tijekom dana.'],
                ['question' => 'Zašto ga je lako previdjeti?', 'answer' => 'Zato što se često prikrije iza ponašanja koje odrasli tumače drukčije.'],
                ['question' => 'Što najviše pomaže?', 'answer' => 'Mirniji ritam, sigurnost, više povezanosti i manje preopterećenja.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Reagirati samo na posljedicu, a ne na izvor napetosti koji se ponavlja.'],
            ],
            'Stres kod djece: kako ga prepoznati prije nego što postane svakodnevni teret',
            'Saznajte kako prepoznati stres kod djece i pomoći im kroz sigurniji ritam, prisutnost i smireniju rutinu.',
            'Stres kod djece'
        ),
        $entry(
            592,
            'Žgaravica u trudnoći: prirodnija rješenja koja donose više olakšanja i manje kaosa',
            'Žgaravica u trudnoći iscrpljuje upravo zato što se vraća u periodu kada je organizam i inače osjetljiviji. Ovdje je kako pristupiti olakšanju kroz obroke, ritam dana i jednostavnije navike.',
            '<ul><li>Žgaravica u trudnoći najčešće traži praktične prilagodbe obroka i ritma, ne komplicirane trikove.</li><li>Najveća pogreška je čekati da nelagoda postane jaka pa tek onda pokušavati bilo što promijeniti.</li><li>Pametniji pristup uvodi male promjene koje smanjuju pritisak na probavu kroz dan.</li></ul>',
            [
                ['question' => 'Zašto je žgaravica u trudnoći tako česta?', 'answer' => 'Zbog promjena u tijelu i većeg pritiska na probavni sustav kako trudnoća napreduje.'],
                ['question' => 'Što često pomaže?', 'answer' => 'Manji obroci, mirniji tempo jela i izbjegavanje okidača koji pogoršavaju nelagodu.'],
                ['question' => 'Treba li sve rješavati prirodno pod svaku cijenu?', 'answer' => 'Ne. Važno je zadržati realizam i sigurnost u prvom planu.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Ignorirati obrasce koji jasno pokazuju što simptom pogoršava.'],
            ],
            'Žgaravica u trudnoći: kako si olakšati kroz ritam obroka i manje okidača',
            'Otkrijte kako žgaravicu u trudnoći ublažiti kroz jednostavnije navike, obroke i mirniji dnevni ritam.',
            'Žgaravica u trudnoći'
        ),
        $entry(
            630,
            'Prirodni laksativi: psilij, aloe gel i suhe šljive kroz realan plan za probavu',
            'Kod zatvora i usporene probave ljudi često traže najbrže rješenje, a upravo tu nastaje najviše frustracije. Ovdje je kako prirodne laksative gledati kao dio šireg plana, a ne kao magični prekidač.',
            '<ul><li>Prirodni laksativi mogu pomoći kada se koriste uz dovoljno tekućine, kretanja i bolji ritam obroka.</li><li>Najveća pogreška je tražiti trenutno rješenje bez promjene onoga što problem stalno vraća.</li><li>Pametniji pristup kombinira vlakna, tekućinu i nježnu podršku kroz nekoliko dana ili tjedana.</li></ul>',
            [
                ['question' => 'Zašto su psilij i suhe šljive popularni?', 'answer' => 'Zato što ih ljudi povezuju s vlaknima, omekšavanjem stolice i prirodnijim olakšanjem.'],
                ['question' => 'Može li aloe gel pomoći?', 'answer' => 'Može imati smisla u nekim rutinama, ali nije jedini faktor koji određuje probavu.'],
                ['question' => 'Što je ključno uz laksative?', 'answer' => 'Tekućina, kretanje i svakodnevne navike koje probavu drže aktivnijom.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Uzeti proizvod, a zaboraviti na širi uzrok usporene probave.'],
            ],
            'Prirodni laksativi: kako ih koristiti kao podršku, a ne kao jedini plan za probavu',
            'Saznajte kako psilij, aloe gel i suhe šljive mogu pomoći probavi kad ih koristite unutar šireg plana.',
            'Prirodni laksativi'
        ),
        $entry(
            642,
            'Sirup od luka i meda za kašalj: kada ima smisla i kako ga koristiti realno',
            'Sirup od luka i meda preživio je generacije upravo zato što može donijeti osjećaj olakšanja i njege grla. Ovdje je kako ga koristiti razumno, bez romantiziranja svakog kućnog recepta.',
            '<ul><li>Kućni sirup ima najviše smisla kao pomoćni ritual za grlo i oporavak, ne kao odgovor na sve vrste kašlja.</li><li>Najveća pogreška je pretvoriti narodni recept u jedini plan kad simptom traje dugo ili se pogoršava.</li><li>Pametniji pristup koristi sirup za ugodu i promatra širu sliku kašlja kroz vrijeme.</li></ul>',
            [
                ['question' => 'Zašto je ovaj sirup toliko popularan?', 'answer' => 'Zato što spaja toplinu, tradiciju i osjećaj umirenja grla.'],
                ['question' => 'Može li pomoći svakome?', 'answer' => 'Ne jednako, ali nekima donosi osjećaj olakšanja i više ugode.'],
                ['question' => 'Kada ima smisla?', 'answer' => 'Kad se koristi kao podrška uz odmor, tekućinu i promatranje simptoma.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Osloniti se samo na sirup kada simptom traži širu procjenu i oprez.'],
            ],
            'Sirup od luka i meda: gdje stvarno pomaže grlu, a gdje nije dovoljan sam',
            'Otkrijte kada sirup od luka i meda za kašalj ima smisla i kako ga koristiti kao razumnu kućnu podršku.',
            'Sirup od luka i meda'
        ),
        $entry(
            653,
            'Alkohol i kava: kako zajedno utječu na probavu, san i osjećaj imuniteta',
            'Alkohol i kava često se gledaju odvojeno, iako u stvarnom životu često djeluju unutar istog ritma stresa, sna i probave. Ovdje je kako razumjeti njihov zajednički učinak bez dramatiziranja, ali i bez ignoriranja signala tijela.',
            '<ul><li>Kava i alkohol mogu pojačati probavnu osjetljivost, poremetiti san i utjecati na osjećaj oporavka.</li><li>Najveća pogreška je promatrati svaki od tih faktora zasebno, a zanemariti njihov zajednički učinak kroz dan.</li><li>Pametniji pristup prati obrasce, količinu i trenutke kada tijelo pokazuje da mu kombinacija ne odgovara.</li></ul>',
            [
                ['question' => 'Zašto ova kombinacija često smeta probavi?', 'answer' => 'Jer oba faktora mogu opteretiti želudac i poremetiti ritam probavnog sustava.'],
                ['question' => 'Kako utječu na san?', 'answer' => 'Često smanjuju kvalitetu oporavka, čak i kada osoba ima dojam da spava dovoljno.'],
                ['question' => 'Je li problem samo količina?', 'answer' => 'Ne. Važan je i ukupni obrazac navika, stresa i vremena konzumacije.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Naviknuti se na nelagodu i prestati primjećivati što tijelo pokušava pokazati.'],
            ],
            'Alkohol i kava: kako njihova kombinacija utječe na probavu i oporavak',
            'Saznajte kako alkohol i kava zajedno utječu na probavu, san i opći osjećaj otpornosti organizma.',
            'Alkohol i kava'
        ),
        $entry(
            658,
            'Stres kod adolescenata: kako roditelji mogu pomoći bez još većeg pritiska',
            'Adolescentski stres često izgleda kao povlačenje, razdražljivost ili otpor, ali ispod toga se često krije preopterećenje koje traži više razumijevanja nego kontrole. Ovdje je kako roditelji mogu pomoći kroz odnos i ritam, a ne samo kroz zabrane.',
            '<ul><li>Adolescentima najviše pomaže podrška koja poštuje njihovu autonomiju, ali i dalje daje stabilan okvir.</li><li>Najveća pogreška je na stres odgovarati samo većim pritiskom, kritikama ili predavanjima.</li><li>Pametniji pristup gradi razgovor, povjerenje i male promjene koje smanjuju opterećenje.</li></ul>',
            [
                ['question' => 'Kako se stres kod adolescenata često vidi?', 'answer' => 'Kroz povlačenje, promjene raspoloženja, slabiji san ili jaču razdražljivost.'],
                ['question' => 'Što roditelji najčešće pogrešno naprave?', 'answer' => 'Povećaju kontrolu baš onda kada dijete najviše treba smireniji odnos i prisutnost.'],
                ['question' => 'Kako pomoći bez gušenja?', 'answer' => 'Kroz slušanje, jasne ali mirne granice i manje svakodnevnog kaosa.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Miješati znakove stresa sa samom neposlušnošću i tako propustiti širu sliku.'],
            ],
            'Stres kod adolescenata: kako pomoći kroz povjerenje, a ne samo kontrolu',
            'Otkrijte kako roditelji mogu pomoći adolescentima pod stresom kroz odnos, ritam i manje pritiska.',
            'Stres kod adolescenata'
        ),
    ],
];
