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
    'key' => 'vitalnost-prehrana-forever-hr-wave-1',
    'name' => 'Vitalnost, prehrana i Forever vodiči (HR) - prvi val',
    'notes' => 'Veći ručni premium editorial pack za svakodnevnu vitalnost, prehranu, migrene, sezonsku podršku i odabrane Forever vodiče.',
    'entries' => [
        $entry(
            141,
            'Aloe vera i organizam: gdje hidrolat i nježna njega stvarno imaju smisla',
            'Aloe vera se često spominje i iznutra i izvana, ali najviše koristi daje kad znamo što točno očekivati od proizvoda i načina primjene. Ovdje je kako o aloe veri razmišljati kroz rutinu njege, osjećaj kože i praktičnu svakodnevnu upotrebu.',
            '<ul><li>Aloe vera ima najviše smisla kad je uključena u jasnu rutinu, a ne kad se od nje očekuje univerzalno rješenje.</li><li>Najveća pogreška je miješati različite oblike i namjene proizvoda bez razumijevanja čemu služe.</li><li>Pametniji pristup bira formulu prema koži, potrebi i stvarnom iskustvu korištenja.</li></ul>',
            [
                ['question' => 'Je li svaki aloe proizvod isti?', 'answer' => 'Nije. Razlike u sastavu, obradi i namjeni mogu potpuno promijeniti iskustvo i rezultat.'],
                ['question' => 'Kada aloe vera najčešće ima smisla?', 'answer' => 'Kad koži treba nježnija podrška, osjećaj svježine ili rutina koja je jednostavna i ponovljiva.'],
                ['question' => 'Što je česta greška?', 'answer' => 'Očekivati od aloe vere učinak koji traži širi plan njege, prehrane ili oporavka.'],
                ['question' => 'Kako pametnije birati?', 'answer' => 'Po namjeni proizvoda, sastavu i tome kako se uklapa u vašu stvarnu rutinu.'],
            ],
            'Aloe vera i organizam: gdje stvarno pomaže, a gdje marketing pretjeruje',
            'Saznajte gdje aloe vera ima smisla za njegu i osjećaj kože te kako je koristiti realno i bez pretjerivanja.',
            'Aloe vera i organizam'
        ),
        $entry(
            150,
            'Kako unijeti 5 porcija voća i povrća dnevno bez osjećaja da ste na dijeti',
            'Pet porcija voća i povrća dnevno zvuči jednostavno dok ne krene stvarni radni ritam, kupnja i priprema hrane. Ovdje je kako tu naviku učiniti laganijom, jeftinijom i stvarno održivom za svaki dan u tjednu.',
            '<ul><li>Više voća i povrća najlakše ulazi u prehranu kroz male rutinske promjene, a ne kroz savršene planove.</li><li>Najveća pogreška je pokušati sve riješiti odjednom i onda odustati čim raspored postane naporan.</li><li>Pametniji pristup gradi nekoliko jednostavnih mjesta u danu gdje voće i povrće dolaze prirodno.</li></ul>',
            [
                ['question' => 'Što se računa kao porcija?', 'answer' => 'Jedna porcija može biti šaka povrća, komad voća ili slična praktična količina koju lako pratite kroz dan.'],
                ['question' => 'Mora li sve biti svježe?', 'answer' => 'Ne. Smrznute i jednostavno pripremljene opcije često pomažu da navika bude održivija.'],
                ['question' => 'Kako to olakšati djeci i obitelji?', 'answer' => 'Kroz ponavljajuće jednostavne kombinacije koje se lako poslužuju i ne stvaraju dodatni stres.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Kupiti puno namirnica bez plana pa ih kasnije baciti jer nisu iskorištene na vrijeme.'],
            ],
            '5 porcija voća i povrća dnevno: kako ih unijeti lakše i bez stresa',
            'Otkrijte kako bez dijetne rigidnosti unijeti 5 porcija voća i povrća dnevno kroz praktične male navike.',
            '5 porcija dnevno'
        ),
        $entry(
            151,
            'Napuni baterije: kako se boriti protiv kroničnog umora bez još jedne instant formule',
            'Kronični umor rijetko nastaje iz samo jednog razloga, a još rjeđe nestaje uz jedno brzo rješenje. Ovdje je kako prepoznati glavne obrasce iscrpljenosti i gdje prehrana, ritam dana i podrška organizmu stvarno mogu pomoći.',
            '<ul><li>Kronični umor najčešće je posljedica sloja više problema, a ne jednog manjka koji sve objašnjava.</li><li>Najveća pogreška je gasiti umor samo kofeinom ili kratkim naletima motivacije.</li><li>Pametniji pristup traži obrasce u snu, stresu, prehrani i dnevnom opterećenju.</li></ul>',
            [
                ['question' => 'Zašto se umor stalno vraća?', 'answer' => 'Jer uzrok često nije jedan nego kombinacija sna, stresa, prehrane, manjka oporavka i životnog ritma.'],
                ['question' => 'Pomažu li dodaci?', 'answer' => 'Ponekad, ali tek kad su osnovni uzroci i navike barem djelomično prepoznati.'],
                ['question' => 'Što prvo promatrati?', 'answer' => 'San, energiju kroz dan, obroke, kretanje i znakove da tijelo nema dovoljno vremena za oporavak.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Zamijeniti iscrpljenost s manjkom discipline i ignorirati signale tijela mjesecima.'],
            ],
            'Kronični umor: kako vratiti energiju bez oslanjanja na brze trikove',
            'Saznajte kako prepoznati glavne uzroke kroničnog umora i vratiti više energije kroz održive promjene.',
            'Kronični umor'
        ),
        $entry(
            153,
            'Kuhanje s aloe verom: 3 neobična recepta koji imaju više smisla nego što zvuče',
            'Kuhanje s aloe verom mnogima zvuči egzotično ili nepotrebno komplicirano, ali može biti zanimljivo kad se koristi umjereno i s jasnom idejom. Ovdje su tri receptna smjera koja spajaju znatiželju, okus i praktičnost.',
            '<ul><li>Aloe vera u kuhinji ima smisla kad nadopunjuje recept, a ne kad pokušava biti glavni spektakl na tanjuru.</li><li>Najveća pogreška je pretjerati s količinom ili je ubacivati u jela bez osjećaja za okus i teksturu.</li><li>Pametniji pristup bira jednostavne recepte koji aloe veri daju pomoćnu, a ne agresivnu ulogu.</li></ul>',
            [
                ['question' => 'Je li aloe vera stvarno za kuhanje?', 'answer' => 'Može biti, ako se koristi u prikladnom obliku i u receptima gdje ima kulinarskog smisla.'],
                ['question' => 'Kakva jela najbolje funkcioniraju?', 'answer' => 'Lakše kombinacije, osvježavajući recepti i jela gdje tekstura može ostati ugodna.'],
                ['question' => 'Što je najčešća greška?', 'answer' => 'Pretvoriti zanimljiv sastojak u prejak ili čudan element koji dominira cijelim jelom.'],
                ['question' => 'Trebaju li recepti biti komplicirani?', 'answer' => 'Ne. Najbolji su oni koji ostaju jednostavni i izvedivi i u običnom tjednu.'],
            ],
            'Kuhanje s aloe verom: 3 recepta koja su zanimljiva, ali i stvarno izvediva',
            'Isprobajte tri jednostavnija smjera kuhanja s aloe verom bez pretjerivanja i bez izgubljenog okusa.',
            'Kuhanje s aloe verom'
        ),
        $entry(
            161,
            'Najbolji Forever paketi za početnike: kako odabrati onaj koji stvarno odgovara',
            'Početnički paket ima smisla samo ako odgovara vašem cilju, ritmu i onome što ćete zaista koristiti. Ovdje je kako odabrati Forever paket bez pretrpavanja proizvoda i bez osjećaja da morate kupiti “sve odjednom”.',
            '<ul><li>Najbolji početnički paket nije najveći, nego onaj koji ćete stvarno koristiti dovoljno dugo.</li><li>Najveća pogreška je uzeti paket po tuđem oduševljenju bez jasnog osobnog razloga i plana.</li><li>Pametniji pristup bira prema cilju: energija, rutina, probava, njega ili opći početak.</li></ul>',
            [
                ['question' => 'Po čemu birati početnički paket?', 'answer' => 'Po stvarnom cilju, budžetu i navikama koje možete održati, a ne po veličini paketa.'],
                ['question' => 'Trebam li odmah više proizvoda?', 'answer' => 'Ne. Za početak obično više vrijedi manji i jasniji izbor nego preopterećenje.'],
                ['question' => 'Je li paket dobar za sve?', 'answer' => 'Nije. Koristan paket ovisi o osobi, navikama i očekivanjima od proizvoda.'],
                ['question' => 'Koja je česta greška početnika?', 'answer' => 'Kupiti previše, pa onda polovicu ne koristiti dovoljno redovito.'],
            ],
            'Forever paketi za početnike: kako izabrati pametnije i bez viška proizvoda',
            'Saznajte kako odabrati Forever paket za početak prema cilju, navikama i onome što ćete stvarno koristiti.',
            'Forever paketi'
        ),
        $entry(
            162,
            'Kolagen i Forever Marine Collagen: gdje su koristi realne, a gdje počinje pretjerivanje',
            'Kolagen je jedan od najpopularnijih dodataka za kožu i zglobove, ali očekivanja često prerastu ono što proizvod realno može podržati. Ovdje je kako gledati na Marine Collagen kroz rutinu, prehranu i dosljednost, a ne kroz hype.',
            '<ul><li>Kolagen može imati smisla kao dio šireg plana za kožu, zglobove i oporavak vezivnih tkiva.</li><li>Najveća pogreška je očekivati dramatičnu promjenu bez vremena, dosljednosti i osnovne brige o tijelu.</li><li>Pametniji pristup kombinira kolagen s hidratacijom, proteinima i navikama koje stvarno štite kožu i zglobove.</li></ul>',
            [
                ['question' => 'Može li kolagen pomoći koži?', 'answer' => 'Može biti podrška, ali najbolji učinak dolazi kad je dio šire rutine i dovoljno redovite uporabe.'],
                ['question' => 'Ima li smisla za zglobove?', 'answer' => 'Može imati, posebno kao dio općeg plana oporavka, kretanja i prehrane.'],
                ['question' => 'Kada očekivati rezultat?', 'answer' => 'Ne odmah. Kod takvih dodataka važni su vrijeme, dosljednost i realna očekivanja.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Tražiti učinak od kolagena, a ignorirati san, protein, hidraciju i zaštitu kože.'],
            ],
            'Kolagen i Marine Collagen: što realno možete očekivati za kožu i zglobove',
            'Razumijte gdje kolagen ima smisla za kožu i zglobove te kako ga uklopiti bez nerealnih očekivanja.',
            'Kolagen'
        ),
        $entry(
            163,
            'Forever i sportaši: mogu li profesionalci koristiti FLP suplemente pametno i sigurno',
            'Sportaši trebaju više jasnoće i sigurnosti nego prosječni korisnici dodataka, jer svaki proizvod mora imati smisla u stvarnom trening okruženju. Ovdje je kako gledati na Forever suplemente kroz oporavak, rutinu i odgovorniji izbor.',
            '<ul><li>Dodaci za sportaše imaju smisla samo ako rješavaju stvarnu potrebu i uklapaju se u već dobru osnovu treninga i prehrane.</li><li>Najveća pogreška je pretvoriti suplementaciju u glavni oslonac umjesto u pomoćni alat.</li><li>Pametniji pristup bira manje proizvoda, jasnu svrhu i sigurnu rutinu korištenja.</li></ul>',
            [
                ['question' => 'Trebaju li sportaši posebne dodatke?', 'answer' => 'Ponekad da, ali tek kad postoji jasna potreba i kad su prehrana i oporavak već na dobrom nivou.'],
                ['question' => 'Jesu li svi proizvodi korisni svima?', 'answer' => 'Ne. Potrebe ovise o sportu, intenzitetu treninga i cilju sportaša.'],
                ['question' => 'Što je važnije od dodataka?', 'answer' => 'Kvalitetan trening, san, oporavak i osnovna prehrana.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Kopirati rutinu drugih sportaša bez razumijevanja vlastitih potreba.'],
            ],
            'Forever i sportaši: kako birati FLP suplemente smisleno i bez viška',
            'Saznajte kako sportaši mogu pametnije koristiti Forever suplemente kroz jasnu potrebu i dobru osnovu.',
            'Forever i sport'
        ),
        $entry(
            165,
            'Prirodna pomoć kod migrena: aloe, magnezij i riboflavin u realnom planu podrške',
            'Kod migrena najviše pomaže razumijevanje okidača i dosljedan plan, a ne nasumično isprobavanje svega što “zvuči prirodno”. Ovdje je kako magnezij, riboflavin i ritam dana mogu biti dio razumnije potpore.',
            '<ul><li>Prirodna podrška migrenama najviše vrijedi kad se poveže s okidačima, ritmom sna i razinom stresa.</li><li>Najveća pogreška je čekati samo napad, a ne raditi na obrascima koji mu prethode.</li><li>Pametniji pristup kombinira prevenciju, praćenje simptoma i ciljanu podršku koja ima smisla za pojedinca.</li></ul>',
            [
                ['question' => 'Mogu li magnezij i riboflavin pomoći?', 'answer' => 'Kod nekih ljudi mogu biti korisna podrška, osobito kad se koriste dosljedno i ciljano.'],
                ['question' => 'Zašto je praćenje migrena važno?', 'answer' => 'Jer tek kad prepoznate okidače i obrasce, podrška postaje stvarno korisna.'],
                ['question' => 'Je li dovoljno reagirati samo kad krene migrena?', 'answer' => 'Najčešće nije. Prevencija i stabilniji ritam često su jednako važni kao akutna pomoć.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Mijenjati previše stvari odjednom pa kasnije ne znati što zapravo pomaže.'],
            ],
            'Migrena: prirodna podrška kroz magnezij, riboflavin i pametniji plan',
            'Otkrijte kako migrenama pristupiti prirodnije kroz magnezij, riboflavin i bolji ritam okidača i oporavka.',
            'Migrena'
        ),
        $entry(
            166,
            'Vitamin D i K2 zimi: kako podržati kosti i izbjeći sezonske greške',
            'Zima često otvori pitanje vitamina D, ali bez konteksta se lako sklizne u pretjerivanje ili potpuno nasumičnu suplementaciju. Ovdje je kako D i K2 gledati kroz sezonu, kosti i realnu potrebu organizma.',
            '<ul><li>Vitamin D zimi mnogima postaje važniji, ali smisao dodatka ovisi o stvarnoj potrebi i kontekstu.</li><li>Najveća pogreška je uzimati visoke doze bez plana, praćenja i razumijevanja šire slike.</li><li>Pametniji pristup povezuje vitamin D, K2, prehranu i stanje organizma, a ne samo trend sezone.</li></ul>',
            [
                ['question' => 'Zašto je vitamin D posebno važan zimi?', 'answer' => 'Zbog manjeg izlaganja suncu i veće šanse da razina padne ako nema dovoljno unosa ili zaliha.'],
                ['question' => 'Zašto se spominje i K2?', 'answer' => 'Jer se često promatra kao dio šire priče o kostima i ravnoteži povezanih nutrijenata.'],
                ['question' => 'Treba li svatko uzimati isto?', 'answer' => 'Ne. Potrebe i doze ovise o osobi, prehrani, izloženosti suncu i ponekad laboratorijskoj slici.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Preuzeti tuđu dozu ili zimski ritual bez provjere ima li to smisla i za vas.'],
            ],
            'Vitamin D i K2 zimi: kako ih koristiti pametnije za kosti i ravnotežu',
            'Saznajte gdje vitamin D i K2 imaju smisla zimi i kako izbjeći najčešće greške u sezonskoj suplementaciji.',
            'Vitamin D i K2'
        ),
        $entry(
            170,
            'Hormoni sreće: kako ih poduprijeti prehranom, kretanjem i boljim dnevnim ritmom',
            '“Hormoni sreće” zvuče jednostavno dok ne shvatite koliko su povezani sa snom, kretanjem, hranom i osjećajem povezanosti. Ovdje je kako poduprijeti serotonin, dopamin i slične mehanizme bez lažnih brzih obećanja.',
            '<ul><li>Raspoloženje i unutarnja energija ovise o više sustava, a ne samo o jednom “hormonu sreće”.</li><li>Najveća pogreška je tražiti brzo podizanje raspoloženja bez brige o osnovnom ritmu života.</li><li>Pametniji pristup gradi male izvore zadovoljstva kroz san, svjetlo, hranu, kretanje i odnose.</li></ul>',
            [
                ['question' => 'Može li prehrana utjecati na raspoloženje?', 'answer' => 'Može, posebno kao dio šireg ritma koji uključuje stabilnu energiju, manje oscilacije i bolju probavu.'],
                ['question' => 'Koliko je kretanje važno?', 'answer' => 'Vrlo, jer redovito kretanje često pomaže i živčanom sustavu i osjećaju mentalne svježine.'],
                ['question' => 'Postoji li jedan dodatak za “sreću”?', 'answer' => 'Ne. Raspoloženje je rezultat više povezanih faktora, a ne jedne formule.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Zanemariti osnovni životni ritam i očekivati da raspoloženje riješi samo motivacija.'],
            ],
            'Hormoni sreće: što stvarno pomaže raspoloženju kroz hranu, san i kretanje',
            'Razumijte kako podržati raspoloženje kroz prehranu, san, kretanje i održive dnevne navike.',
            'Hormoni sreće'
        ),
    ],
];
