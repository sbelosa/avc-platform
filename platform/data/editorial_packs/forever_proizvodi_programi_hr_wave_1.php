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
    'key' => 'forever-proizvodi-programi-hr-wave-1',
    'name' => 'Forever proizvodi i programi (HR) - prvi val',
    'notes' => 'Veći ručni premium editorial pack za Forever proizvode, FIT programe i odabir početnih rješenja prema cilju.',
    'entries' => [
        $entry(
            489,
            'Forever Arctic Sea: omega-3 podrška za srce, mozak i svakodnevnu ravnotežu',
            'Omega-3 proizvodi imaju smisla samo kad ih gledamo kao dio šireg plana za prehranu, upalne procese i dugoročnu rutinu. Ovdje je kako Forever Arctic Sea procijeniti realno, bez pretvaranja kapsule u čudo za sve.',
            '<ul><li>Omega-3 podrška najviše vrijedi kao dio šireg plana prehrane i oporavka, a ne kao usamljeni “zdravi dodatak”.</li><li>Najveća pogreška je očekivati da će jedan proizvod ispraviti prehranu siromašnu kvalitetnim mastima i loš dnevni ritam.</li><li>Pametniji pristup gleda dosljednost, ukupni unos masnih kiselina i stvarni cilj zbog kojeg proizvod birate.</li></ul>',
            [
                ['question' => 'Za što se omega-3 najčešće koristi?', 'answer' => 'Najčešće kao podrška kardiovaskularnom zdravlju, mozgu i općoj ravnoteži upalnih procesa.'],
                ['question' => 'Može li Arctic Sea zamijeniti prehranu?', 'answer' => 'Ne. Proizvod ima najviše smisla kao dopuna, a ne kao zamjena za kvalitetan unos masti kroz hranu.'],
                ['question' => 'Kada ima najviše smisla?', 'answer' => 'Kad želite podržati rutinu koja već ide u dobrom smjeru i traži dodatnu dosljednost.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Kupiti omega-3 bez jasne ideje zašto je uvodite i kako ćete je zapravo redovito koristiti.'],
            ],
            'Forever Arctic Sea: kada omega-3 stvarno ima smisla za srce i mozak',
            'Saznajte gdje Forever Arctic Sea ima smisla kao omega-3 podrška i kako ga uklopiti bez nerealnih očekivanja.',
            'Forever Arctic Sea'
        ),
        $entry(
            500,
            'C9 vs. F15: koji Forever FIT program ima više smisla za vaš stvarni cilj',
            'C9 i F15 zvuče kao logičan nastavak jedan drugome, ali nisu isti alat niti služe istoj početnoj poziciji. Ovdje je kako odabrati program prema energiji, navikama i realnom kapacitetu, a ne samo prema želji za brzim rezultatom.',
            '<ul><li>C9 i F15 imaju različitu ulogu, tempo i razinu zahtjevnosti, pa izbor ovisi o početnoj točki i cilju.</li><li>Najveća pogreška je izabrati program po dojmu ili tuđem iskustvu bez procjene vlastitog ritma i spremnosti.</li><li>Pametniji pristup gleda koliko strukture, podrške i trajanja vam zaista odgovara u ovom trenutku.</li></ul>',
            [
                ['question' => 'Je li C9 isto što i F15?', 'answer' => 'Ne. Iako su povezani unutar istog sustava, imaju drugačiji fokus i drugačiji osjećaj intenziteta.'],
                ['question' => 'Za koga C9 obično ima više smisla?', 'answer' => 'Za osobe koje žele kraći, jasniji start i strukturiraniji prvi reset navika.'],
                ['question' => 'Kada F15 ima više smisla?', 'answer' => 'Kad želite dulji okvir, više rada na održivosti i osjećaj postupnijeg nastavka.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Ući u program bez pripreme rasporeda, hrane i realne procjene koliko ga možete pratiti.'],
            ],
            'C9 ili F15: kako izabrati Forever FIT program prema stvarnom cilju',
            'Usporedite C9 i F15 prema tempu, cilju i održivosti kako biste izabrali program koji vam stvarno odgovara.',
            'C9 vs F15'
        ),
        $entry(
            535,
            'Clean 9 program: prvi korak prema boljoj vitalnosti ili preambiciozan start',
            'Clean 9 može biti koristan početni okvir, ali samo ako ga ne pretvorite u kratki ekstrem nakon kojeg se sve vraća na staro. Ovdje je kako na program gledati kao na strukturirani početak, a ne kao na čudesno rješenje bez nastavka.',
            '<ul><li>Clean 9 ima smisla kad služi kao ulaz u jasniju rutinu, a ne kao izolirani sprint bez nastavka.</li><li>Najveća pogreška je očekivati da devet dana riješi dugogodišnje navike bez daljnjeg plana.</li><li>Pametniji pristup koristi program kao početni okvir za energiju, fokus i jednostavniji prehrambeni reset.</li></ul>',
            [
                ['question' => 'Zašto ljudi kreću s Clean 9?', 'answer' => 'Najčešće zbog želje za strukturiranim početkom i osjećajem jasnijeg plana.'],
                ['question' => 'Je li program dovoljan sam po sebi?', 'answer' => 'Ne dugoročno. Najviše vrijedi ako iza njega slijedi održiv nastavak navika.'],
                ['question' => 'Kome može biti prezahtjevan?', 'answer' => 'Osobama koje ulaze bez pripreme, lošeg sna ili nerealnog očekivanja brzog čuda.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Završiti program pa se odmah vratiti starom obrascu hrane i ritma dana.'],
            ],
            'Clean 9 program: kako ga koristiti kao pametan start, a ne kratki ekstrem',
            'Razumijte kada Clean 9 ima smisla i kako ga pretvoriti u početak održivije rutine, a ne samo kratki reset.',
            'Clean 9'
        ),
        $entry(
            536,
            'Forever Alpha E Factor: dubinska hidratacija ili još jedan skupi beauty dojam',
            'Kod proizvoda za njegu kože najviše vrijedi razumjeti komu formula odgovara i u kojoj rutini zaista ima ulogu. Ovdje je kako gledati na Alpha E Factor kroz suhu kožu, barijeru i realna očekivanja od luksuznijeg proizvoda.',
            '<ul><li>Hidratantni i hranjivi proizvodi imaju najviše smisla kad odgovaraju tipu kože i ostatku rutine.</li><li>Najveća pogreška je procjenjivati ih samo po dojmu luksuza, a ne po stvarnoj podnošljivosti i ulozi u njezi.</li><li>Pametniji pristup gleda kako formula pomaže suhoći, barijeri i osjećaju kože kroz vrijeme.</li></ul>',
            [
                ['question' => 'Kome Alpha E Factor najviše odgovara?', 'answer' => 'Najčešće sušoj, umornijoj ili dehidriranoj koži koja traži bogatiji osjećaj njege.'],
                ['question' => 'Može li zamijeniti cijelu rutinu?', 'answer' => 'Ne. Ima najviše smisla kao dio rutine, a ne kao jedini proizvod od kojeg sve očekujete.'],
                ['question' => 'Je li luksuzna tekstura dovoljan razlog za kupnju?', 'answer' => 'Ne sama po sebi. Važno je kako koža reagira i treba li joj takav tip proizvoda.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Kupiti bogatu njegu bez obzira na to odgovara li stvarno vašem tipu kože.'],
            ],
            'Forever Alpha E Factor: kada dubinska hidratacija ima smisla za vašu kožu',
            'Saznajte kome Forever Alpha E Factor najviše odgovara i gdje bogatija njega stvarno donosi korist.',
            'Alpha E Factor'
        ),
        $entry(
            537,
            'Forever Lean: regulacija tjelesne težine uz realna očekivanja i pametniji plan',
            'Proizvodi za regulaciju težine vrlo lako završe kao zamjena za plan umjesto kao podrška dobrom planu. Ovdje je gdje Forever Lean može imati smisla i kako ga gledati bez fantazije da će sam nositi cijelu priču o mršavljenju.',
            '<ul><li>Forever Lean može imati smisla kao pomoćni alat uz već složen prehrambeni i životni ritam.</li><li>Najveća pogreška je očekivati od proizvoda učinak koji ovisi o obrocima, apetitu, snu i kretanju.</li><li>Pametniji pristup gleda Lean kao mali dio većeg plana, a ne kao glavni motor promjene.</li></ul>',
            [
                ['question' => 'Za što Forever Lean najviše ima smisla?', 'answer' => 'Kao podrška osobama koje već rade na prehrani i žele dodatni osjećaj strukture unutar plana.'],
                ['question' => 'Može li sam skinuti kilograme?', 'answer' => 'Ne. Bez promjene navika i dosljednog plana učinak neće nositi cijelu priču.'],
                ['question' => 'Kada ga nema smisla uvoditi?', 'answer' => 'Kad još nema osnovnog reda u obrocima, apetitu i dnevnom ritmu.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Koristiti Lean kao zamjenu za plan prehrane umjesto kao dopunu postojećem planu.'],
            ],
            'Forever Lean: gdje pomaže, a gdje ipak morate riješiti veći plan mršavljenja',
            'Otkrijte kada Forever Lean ima smisla kao podrška regulaciji težine i kako ga uklopiti realnije.',
            'Forever Lean'
        ),
        $entry(
            538,
            'C9 Forever Detox: reset organizma ili samo dobar okvir za novi početak',
            'Riječ “detox” često stvara veća očekivanja nego što program realno treba nositi. Ovdje je kako na C9 Forever Detox gledati kao na strukturirani novi početak za navike, a ne kao na magično čišćenje svega nakupljenog.',
            '<ul><li>Detox programi imaju više smisla kad služe kao okvir za novi ritam, a ne kao mitološko “čišćenje”.</li><li>Najveća pogreška je ući u program s očekivanjem da će nekoliko dana ispraviti cijelu životnu strukturu.</li><li>Pametniji pristup koristi reset kao alat za fokus, jednostavnije obroke i jasniji nastavak.</li></ul>',
            [
                ['question' => 'Što C9 Detox stvarno može dati?', 'answer' => 'Jasnu strukturu, osjećaj početka i lakši ulaz u discipliniraniji ritam hrane i rutine.'],
                ['question' => 'Je li detox doslovno “čišćenje tijela”?', 'answer' => 'Ne na način kako marketing često sugerira. Više je riječ o resetu navika i prehrambenog okvira.'],
                ['question' => 'Kada program ima više smisla?', 'answer' => 'Kad ga gledate kao početak procesa, a ne kao završeno rješenje nakon nekoliko dana.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Završiti detox bez ikakvog nastavka i vratiti se istom kaosu u obrocima i ritmu.'],
            ],
            'C9 Forever Detox: kako ga koristiti kao reset navika, a ne kao marketinški mit',
            'Saznajte gdje C9 Forever Detox ima smisla kao novi početak i kako izbjeći najčešće zablude o reset programima.',
            'C9 Detox'
        ),
        $entry(
            540,
            'Active Pro-B probiotik: kada stvarno pomaže probavi, a kada je samo još jedna kapsula',
            'Probiotici imaju smisla tek kad znamo zašto ih uvodimo i što zapravo želimo podržati u probavnom sustavu. Ovdje je kako gledati na Active Pro-B kroz svakodnevnu probavu, ritam prehrane i realno mjesto jednog probiotika u širem planu.',
            '<ul><li>Probiotik ima najviše smisla kad postoji jasan probavni cilj ili potreba za podrškom ravnoteži crijeva.</li><li>Najveća pogreška je uzimati probiotik naslijepo bez promjene hrane, ritma obroka i okidača probavnih tegoba.</li><li>Pametniji pristup kombinira probiotik s boljim prehrambenim navikama i razumijevanjem simptoma.</li></ul>',
            [
                ['question' => 'Kada probiotički proizvod ima smisla?', 'answer' => 'Kad postoji jasan cilj poput podrške probavi, oporavka nakon poremećaja ili boljeg ritma crijeva.'],
                ['question' => 'Može li probiotik sam riješiti probavne smetnje?', 'answer' => 'Ne uvijek. Hrana, stres i obrasci obroka često jednako snažno utječu na stanje.'],
                ['question' => 'Kako znati pomaže li?', 'answer' => 'Praćenjem simptoma kroz vrijeme i promatranjem kako se probava ponaša uz istodobne navike.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Mijenjati više stvari odjednom pa kasnije ne znati što je zapravo imalo učinak.'],
            ],
            'Active Pro-B: kada probiotik ima smisla za probavu i kako ga ne precijeniti',
            'Razumijte kada Active Pro-B probiotik može pomoći probavi i kako ga uklopiti u širi plan za crijeva.',
            'Active Pro-B'
        ),
        $entry(
            542,
            'Forever Nutra Q10: podrška energiji i srcu ili samo popularna anti-age priča',
            'Q10 je jedan od dodataka oko kojih se često spajaju priče o energiji, starenju i srcu. Ovdje je kako Forever Nutra Q10 gledati trezveno, kroz stvarnu potrebu organizma i okvir u kojem jedan takav proizvod ima smisla.',
            '<ul><li>Q10 može imati korisno mjesto kao podrška energiji i širem osjećaju vitalnosti u određenim kontekstima.</li><li>Najveća pogreška je od njega očekivati univerzalni “boost” bez obzira na san, stres i prehranu.</li><li>Pametniji pristup promatra Q10 kao ciljanu podršku, a ne kao zamjenu za osnovni energetski balans.</li></ul>',
            [
                ['question' => 'Zašto ljudi uzimaju Q10?', 'answer' => 'Najčešće zbog osjećaja energije, vitalnosti i interesa za podršku srcu ili starenju.'],
                ['question' => 'Može li Q10 riješiti umor sam po sebi?', 'answer' => 'Ne. Ako su glavni uzroci umora drugdje, jedan dodatak neće nositi cijelo rješenje.'],
                ['question' => 'Kada ima više smisla?', 'answer' => 'Kad postoji jasan razlog i kad je dio šireg plana podrške organizmu.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Tražiti od Q10 osjećaj energije koji zapravo traži bolji san, ritam i oporavak.'],
            ],
            'Forever Nutra Q10: gdje ima smisla za energiju i srce, a gdje hype pretjeruje',
            'Saznajte kada Forever Nutra Q10 može imati smisla i kako ga gledati kroz realnu potrebu, a ne samo popularnost.',
            'Nutra Q10'
        ),
        $entry(
            545,
            'Aloe First sprej: višenamjenska zaštita i njega za kožu i kosu bez preuveličavanja',
            'Višenamjenski proizvodi lako završe kao obećanje za sve i svakoga, ali stvarna vrijednost dolazi iz toga gdje ih ljudi zaista koriste i vole. Ovdje je kako Aloe First procijeniti kroz kožu, tjeme i male svakodnevne situacije gdje sprej format stvarno pomaže.',
            '<ul><li>Aloe First sprej ima najviše smisla ondje gdje treba brz, praktičan i nježan sloj podrške za kožu ili kosu.</li><li>Najveća pogreška je očekivati da će višenamjenski proizvod biti idealno rješenje za baš svaki tip problema.</li><li>Pametniji pristup gleda gdje sprej stvarno olakšava rutinu i koje su mu granice u odnosu na specifičnije proizvode.</li></ul>',
            [
                ['question' => 'Za što Aloe First najčešće ljudi koriste?', 'answer' => 'Za njegu kože, osjećaj svježine, praktičnu pomoć nakon izlaganja suncu ili kao laganu podršku kosi i tjemenu.'],
                ['question' => 'Je li dobar za sve situacije?', 'answer' => 'Ne. Ima smisla u mnogim malim rutinama, ali nije zamjena za ciljanu njegu gdje treba specifičniji proizvod.'],
                ['question' => 'Što je prednost sprej formata?', 'answer' => 'Brzina, praktičnost i lakše ponovno nanošenje kad ste u pokretu ili želite jednostavan ritual.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Očekivati da jedan sprej preuzme ulogu nekoliko potpuno različitih tipova njege.'],
            ],
            'Aloe First sprej: gdje stvarno pomaže za kožu i kosu, a gdje trebate nešto ciljano',
            'Otkrijte gdje Aloe First sprej ima najviše smisla u njezi kože i kose te kako ga koristiti bez pretjerivanja.',
            'Aloe First'
        ),
        $entry(
            544,
            'Forever Lycium Plus: goji bobice, antioksidansi i realna podrška svakodnevnoj vitalnosti',
            'Antioksidativni proizvodi zvuče najprivlačnije kad se obećanja zamagle u priču o energiji, imunitetu i usporavanju starenja odjednom. Ovdje je kako Forever Lycium Plus procijeniti realnije kroz prehranu, oporavak i svakodnevnu vitalnost bez marketinškog pretjerivanja.',
            '<ul><li>Antioksidativna podrška ima najviše smisla kao dio šire prehrambene i životne rutine, a ne kao samostalni spas za energiju.</li><li>Najveća pogreška je očekivati da će jedan proizvod nadoknaditi lošu prehranu, kronični stres i manjak sna.</li><li>Pametniji pristup gleda Lycium Plus kao dodatni sloj podrške kada je osnovni sustav već relativno posložen.</li></ul>',
            [
                ['question' => 'Što je zapravo Forever Lycium Plus?', 'answer' => 'Riječ je o proizvodu koji se najčešće promatra kroz antioksidativnu podršku, goji bobice i opći osjećaj vitalnosti.'],
                ['question' => 'Može li sam podići energiju?', 'answer' => 'Ne pouzdano. Najveći utjecaj na energiju i dalje imaju san, prehrana, oporavak i dnevni ritam.'],
                ['question' => 'Kada ima najviše smisla?', 'answer' => 'Kad želite dopuniti već pristojno složenu rutinu i tražite dodatni sloj podrške, a ne čudesno rješenje.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Kupiti proizvod zbog velike priče o antioksidansima bez jasnog cilja i bez šireg plana prehrane i navika.'],
            ],
            'Forever Lycium Plus: gdje goji i antioksidansi imaju smisla za vitalnost',
            'Saznajte kada Forever Lycium Plus ima smisla kao antioksidativna podrška i kako ga uklopiti bez nerealnih očekivanja.',
            'Forever Lycium Plus'
        ),
    ],
];
