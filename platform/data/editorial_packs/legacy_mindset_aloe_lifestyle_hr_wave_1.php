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
    'key' => 'legacy-mindset-aloe-lifestyle-hr-wave-1',
    'name' => 'Legacy mindset, aloe i lifestyle (HR) - prvi val',
    'notes' => 'Ručni premium pack za starije mindset tekstove, practical aloe use sadržaj i osnovne wellness teme.',
    'entries' => [
        $entry(
            92,
            'Uvjerenja: kako promjena razmišljanja mijenja odluke, navike i cijeli smjer života',
            'Uvjerenja određuju kako tumačimo svijet, koliko si dopuštamo pokušati i gdje sami sebi postavljamo granice. Ovdje je kako ih prepoznati, testirati i mijenjati bez površne motivacije i praznih fraza.',
            '<ul><li>Uvjerenja oblikuju odluke, razinu samopouzdanja i način na koji gledamo mogućnosti u životu.</li><li>Najveća pogreška je misliti da će se uvjerenja promijeniti samo pozitivnim mislima bez rada na ponašanju.</li><li>Pametniji pristup prepoznaje ograničavajuće obrasce i povezuje novu perspektivu s konkretnim koracima.</li></ul>',
            $faq(
                'Što su zapravo uvjerenja?',
                'To su duboki obrasci mišljenja koji utječu na to kako tumačimo sebe, druge i svijet oko sebe.',
                'Zašto su uvjerenja toliko važna?',
                'Zato što oblikuju odluke, samopouzdanje i granice onoga što mislimo da je za nas moguće.',
                'Mogu li se uvjerenja stvarno promijeniti?',
                'Mogu, ali promjena traži svjesnost, ponavljanje i nova ponašanja, ne samo motivacijske rečenice.',
                'Koja je česta pogreška?',
                'Pokušati promijeniti život bez da se preispitaju stara uvjerenja koja i dalje vode iste odluke.'
            ),
            'Uvjerenja: kako promijeniti razmišljanje i život kroz stvarne obrasce',
            'Saznajte kako uvjerenja utječu na životne odluke i kako ih mijenjati na način koji zaista vodi promjeni ponašanja.',
            'Uvjerenja'
        ),
        $entry(
            94,
            'Odbijanje: zašto boli, što nas stvarno pogađa i kako ga pretvoriti u rast umjesto kočenja',
            'Odbijanje nije teško samo zbog onoga što se dogodilo, nego i zbog značenja koje mu odmah pripišemo. Ovdje je kako ga razumjeti mirnije, nositi ga zrelije i iz njega izvući korist umjesto trajne blokade.',
            '<ul><li>Odbijanje najčešće pogađa identitet, ego i osjećaj pripadanja, ne samo konkretan događaj.</li><li>Najveća pogreška je odbijanje tumačiti kao konačan dokaz vlastite nedovoljnosti.</li><li>Pametniji pristup razlikuje emociju trenutka od stvarne životne vrijednosti osobe.</li></ul>',
            $faq(
                'Zašto odbijanje toliko boli?',
                'Zato što često dotiče osjećaj vrijednosti, pripadanja i prihvaćenosti.',
                'Znači li odbijanje da nešto nije u redu s nama?',
                'Ne nužno. Često više govori o kontekstu, vremenu i podudarnosti nego o našoj vrijednosti.',
                'Kako se zdravije nositi s odbijanjem?',
                'Tako da dopustimo emociju, ali ne gradimo cijeli identitet oko jednog ishoda.',
                'Koja je česta pogreška?',
                'Od jednog “ne” napraviti generalni zaključak o cijelom životu ili vlastitim mogućnostima.'
            ),
            'Odbijanje: kako ga razumjeti i pretvoriti u osobni rast',
            'Otkrijte zašto odbijanje toliko pogađa i kako ga nositi zrelije bez trajnog gubitka samopouzdanja i fokusa.',
            'Odbijanje'
        ),
        $entry(
            95,
            'Osobnost ili karakter: što se doista mijenja kada želimo postati bolja i stabilnija osoba',
            'Osobnost opisuje kako djelujemo, ali karakter određuje tko smo pod pritiskom, u odgovornosti i kad nema publike. Ovdje je zašto je karakter presudniji za stvarnu promjenu i dugoročno povjerenje.',
            '<ul><li>Osobnost je važna, ali karakter nosi odluke, integritet i način na koji živimo vrijednosti.</li><li>Najveća pogreška je raditi samo na dojmu, a zanemariti dubinu osobne odgovornosti i dosljednosti.</li><li>Pametniji pristup gradi karakter kroz male odluke koje se ponavljaju i pod pritiskom ostaju postojane.</li></ul>',
            $faq(
                'Koja je razlika između osobnosti i karaktera?',
                'Osobnost je način na koji se izražavamo, a karakter je način na koji donosimo moralne i životne odluke.',
                'Zašto je karakter važniji za promjenu?',
                'Zato što nosi dosljednost, odgovornost i povjerenje koje ostaje i kad dojam prestane biti važan.',
                'Može li se karakter razvijati?',
                'Može, kroz navike, samodisciplinu, poštenje i ponavljanje ispravnih izbora.',
                'Koja je česta pogreška?',
                'Misliti da je dovoljno izgledati uvjerljivo umjesto zaista postati stabilnija osoba.'
            ),
            'Osobnost ili karakter: što zaista donosi istinsku promjenu',
            'Saznajte zašto je karakter važniji od dojma i kako stvarna osobna promjena nastaje kroz navike i odgovornost.',
            'Osobnost ili karakter'
        ),
        $entry(
            96,
            'Mapa želja i Feng Shui: kako ovakve alate koristiti kao fokus, a ne kao zamjenu za akciju',
            'Mapa želja može biti koristan alat za usmjeravanje pažnje, ali samo ako ne zamijeni stvarne odluke, plan i disciplinu. Ovdje je kako ovakve rituale koristiti na zdraviji i funkcionalniji način.',
            '<ul><li>Mapa želja ima smisla kao vizualni podsjetnik na prioritete, a ne kao čarobni mehanizam ispunjavanja želja.</li><li>Najveća pogreška je emocionalnu motivaciju zamijeniti konkretnim planiranjem i djelovanjem.</li><li>Pametniji pristup povezuje simboliku, fokus i svakodnevne korake u stvarnost.</li></ul>',
            $faq(
                'Čemu zapravo služi mapa želja?',
                'Najviše kao alat za fokus, pregled ciljeva i emocionalno usmjeravanje pažnje.',
                'Može li sama mapa želja promijeniti život?',
                'Ne. Vrijedi onoliko koliko vodi stvarnim izborima i ponašanju.',
                'Zašto je ljudima privlačna?',
                'Jer spaja maštu, emociju i jasniju sliku onoga što žele graditi.',
                'Koja je česta pogreška?',
                'Stati na vizualizaciji i preskočiti sve konkretne korake koji bi ciljeve približili stvarnosti.'
            ),
            'Mapa želja: kako je koristiti kao alat fokusa, a ne fantazije',
            'Otkrijte kako mapu želja i Feng Shui pristup koristiti pametnije, kao podršku fokusu i ciljevima, a ne kao zamjenu za djelovanje.',
            'Mapa želja'
        ),
        $entry(
            97,
            'Dobar odnos počinje sa sobom: kako graditi zdravije granice, komunikaciju i bliskost',
            'Odnosi se ne grade samo kroz ono što dajemo drugima, nego i kroz način na koji tretiramo sebe, svoje granice i vlastitu vrijednost. Ovdje je kako zdrav odnos sa sobom mijenja sve druge odnose.',
            '<ul><li>Zdrav odnos sa sobom snažno utječe na granice, komunikaciju i kvalitetu bliskih odnosa.</li><li>Najveća pogreška je stalno tražiti dobar odnos izvana dok iznutra ostajemo bez samopoštovanja i mira.</li><li>Pametniji pristup gradi samopoštovanje, emocionalnu jasnoću i odgovornost prema vlastitim potrebama.</li></ul>',
            $faq(
                'Zašto dobar odnos sa sobom toliko utječe na druge odnose?',
                'Zato što određuje granice, samopoštovanje i način na koji dopuštamo drugima da se odnose prema nama.',
                'Što znači imati zdrav odnos sa sobom?',
                'To znači spoj samopoštovanja, iskrenosti prema sebi i odgovornosti za vlastite potrebe i ponašanje.',
                'Može li se to učiti?',
                'Može, kroz granice, samorefleksiju i postupno jačanje unutarnje stabilnosti.',
                'Koja je česta pogreška?',
                'Tražiti potvrdu i mir samo kroz druge ljude, bez gradnje vlastitog unutarnjeg oslonca.'
            ),
            'Dobar odnos sa sobom: temelj zdravijih odnosa s drugima',
            'Saznajte zašto dobar odnos sa sobom mijenja granice, komunikaciju i kvalitetu svih važnih odnosa u životu.',
            'Dobar odnos'
        ),
        $entry(
            99,
            'Rizik u životu: kako ga prihvatiti pametnije i rasti bez nepotrebne impulzivnosti',
            'Rizik je neizbježan u svakom rastu, ali ne treba ga miješati s kaosom, brzopletošću ili dokazivanjem. Ovdje je kako razlikovati zdravi rizik od loše odluke i kako kroz njega sazrijevati.',
            '<ul><li>Zdrav rizik pomaže rastu, učenju i širenju mogućnosti koje bez hrabrosti ostaju zatvorene.</li><li>Najveća pogreška je ili bježati od svakog rizika ili ga romantizirati kao dokaz hrabrosti.</li><li>Pametniji pristup razlikuje promišljeni korak od impulzivnog skoka bez temelja.</li></ul>',
            $faq(
                'Zašto je rizik važan za rast?',
                'Zato što bez izlaska iz poznatog nema većeg razvoja, iskustva ni novih prilika.',
                'Znači li prihvatiti rizik biti nepromišljen?',
                'Ne. Zdrav rizik uključuje procjenu, odgovornost i spremnost na učenje.',
                'Kako znati je li neki rizik vrijedan?',
                'Pomaže pitati što možete dobiti, što možete izgubiti i koliko ste spremni nositi posljedice.',
                'Koja je česta pogreška?',
                'Odlučivati iz straha od propuštanja ili iz ega, a ne iz promišljenog razloga.'
            ),
            'Rizik u životu: kako ga prihvatiti bez brzopletosti i rasti',
            'Otkrijte kako zdravije gledati na rizik, donositi promišljenije odluke i kroz izazove stvarno rasti.',
            'Rizik u životu'
        ),
        $entry(
            100,
            'MLM marketing uz Forever Living: kako posao od kuće graditi kao sustav, a ne kao kratki entuzijazam',
            'Posao od kuće s Foreverom ima smisla samo kada se gradi kroz sadržaj, povjerenje i ponovljive dnevne aktivnosti. Ovdje je kako MLM rad kod kuće postaviti zdravije i održivije.',
            '<ul><li>MLM posao od kuće najviše ovisi o povjerenju, korisnoj podršci i jednostavnom procesu rada.</li><li>Najveća pogreška je očekivati ozbiljan rezultat bez sadržaja, rutine i realnog prodajnog sustava.</li><li>Pametniji pristup gradi publiku, odnos i preporuku korak po korak.</li></ul>',
            $faq(
                'Može li se MLM posao s Foreverom stvarno graditi od kuće?',
                'Može, ali samo ako postoji sustav, sadržaj i dosljedan rad s ljudima.',
                'Što najviše odlučuje o uspjehu?',
                'Povjerenje, svakodnevna aktivnost, podrška kupcima i jasna poruka.',
                'Zašto mnogi ne uspiju?',
                'Jer traže brzu zaradu bez izgradnje publike i stvarne vrijednosti za kupca.',
                'Koja je česta pogreška?',
                'Ući u posao vođen samo motivacijom, bez procesa koji se može ponavljati.'
            ),
            'MLM marketing od kuće: kako Forever posao graditi održivije',
            'Saznajte kako MLM posao uz Forever Living postaviti kroz sadržaj, povjerenje i ponovljive dnevne aktivnosti umjesto kratkog hypea.',
            'MLM marketing od kuće'
        ),
        $entry(
            109,
            'Stres na poslu: prirodne metode za opuštanje i bolji fokus bez bijega od uzroka',
            'Stres na poslu ne rješava se samo tehnikama disanja ili čajem, nego i boljim granicama, organizacijom i oporavkom. Ovdje je kako spojiti prirodnije metode s pametnijim svakodnevnim navikama rada.',
            '<ul><li>Prirodne metode za stres mogu pomoći samo ako se istodobno mijenja i način rada i oporavka.</li><li>Najveća pogreška je smirivati posljedice, a ignorirati stvarne uzroke preopterećenja.</li><li>Pametniji pristup spaja granice, male rituale opuštanja i bolju organizaciju fokusa.</li></ul>',
            $faq(
                'Zašto je stres na poslu tako iscrpljujući?',
                'Zato što često uključuje pritisak, stalnu dostupnost, odgovornost i manjak pravog oporavka.',
                'Mogu li prirodne metode stvarno pomoći?',
                'Mogu pomoći kao podrška, ali najviše vrijede kada prate i promjenu radnih navika.',
                'Što najviše pomaže fokusu pod stresom?',
                'Kraće pauze, bolja prioritizacija, granice i manje raspršivanja pažnje.',
                'Koja je česta pogreška?',
                'Tražiti opuštanje bez da se mijenja način na koji se stres svakodnevno stvara.'
            ),
            'Stres na poslu: kako spojiti opuštanje, granice i bolji fokus',
            'Otkrijte kako smanjiti stres na poslu kroz prirodnije metode, ali i kroz pametnije granice, oporavak i organizaciju rada.',
            'Stres na poslu'
        ),
        $entry(
            112,
            'Trudnoća i dodaci prehrani: sigurni izbori, oprez i što nije pametno uvoditi naslijepo',
            'Trudnoća traži posebno pažljiv odnos prema dodacima prehrani jer “prirodno” ne znači automatski i prikladno. Ovdje je kako promatrati sigurnije izbore i zašto je kontekst važniji od trenda.',
            '<ul><li>Dodaci prehrani u trudnoći trebaju se birati s više opreza, konteksta i jasne potrebe.</li><li>Najveća pogreška je uvoditi proizvode naslijepo samo zato što su popularni ili prirodni.</li><li>Pametniji pristup gleda sigurnost, svrhu i širu prehrambenu sliku trudnice.</li></ul>',
            $faq(
                'Zašto su dodaci prehrani u trudnoći posebno osjetljiva tema?',
                'Zato što sigurnost i kontekst primjene postaju važniji nego u mnogim drugim životnim fazama.',
                'Znači li prirodno automatski sigurno?',
                'Ne. Prirodni sastojci nisu automatski prikladni za trudnoću samo zato što su biljnog podrijetla.',
                'Kako pristupiti sigurnijem izboru?',
                'Kroz oprez, jasnu potrebu i bolje razumijevanje vlastite prehrane i konteksta.',
                'Koja je česta pogreška?',
                'Uvjeriti se da nešto mora biti dobro samo zato što ga koriste druge trudnice.'
            ),
            'Trudnoća i dodaci prehrani: kako birati sigurnije i promišljenije',
            'Saznajte kako u trudnoći razumnije pristupiti dodacima prehrani i zašto je sigurnost važnija od trendova i brzih preporuka.',
            'Trudnoća i dodaci'
        ),
        $entry(
            113,
            'Glavobolja: najčešći uzroci i prirodniji pristupi koji imaju smisla prije nego posegnete za rutinom panike',
            'Glavobolja može doći iz napetosti, dehidracije, stresa, prehrane ili ritma života, pa je korisno prvo razumjeti obrazac, a tek onda tražiti rješenje. Ovdje je kako joj pristupiti smirenije i pametnije.',
            '<ul><li>Glavobolja ima više mogućih uzroka, zato korisniji pristup počinje promatranjem obrasca, a ne nagađanjem.</li><li>Najveća pogreška je svaku glavobolju tretirati isto bez gledanja okidača i ritma života.</li><li>Pametniji pristup povezuje hidraciju, stres, san i prehranu s osjećajem boli i napetosti.</li></ul>',
            $faq(
                'Koji su česti uzroci glavobolje?',
                'Napetost, stres, dehidracija, neredovit san, prehrambeni obrasci i preopterećenje.',
                'Mogu li prirodniji pristupi pomoći?',
                'Mogu pomoći kao dio šire rutine koja smanjuje okidače i poboljšava oporavak.',
                'Zašto je važno pratiti obrazac glavobolje?',
                'Jer ponavljajući okidači često otkrivaju gdje problem stvarno nastaje.',
                'Koja je česta pogreška?',
                'Pokušavati ugasiti simptom bez razumijevanja što ga redovito izaziva.'
            ),
            'Glavobolja: kako prepoznati uzroke i smirenije pristupiti olakšanju',
            'Otkrijte kako razumjeti najčešće uzroke glavobolje i gdje prirodniji pristupi mogu imati smisla unutar šire rutine oporavka.',
            'Glavobolja'
        ),
        $entry(
            127,
            'Smoothie ili cijeđeni sok: prednosti, mane i gdje aloe ima više smisla u svakodnevnoj rutini',
            'Smoothie i cijeđeni sok nisu ista stvar, iako ih ljudi često stavljaju u istu kategoriju “zdravih navika”. Ovdje je kako ih razlikovati i gdje se aloe može smislenije uklopiti.',
            '<ul><li>Smoothie i cijeđeni sok razlikuju se po vlaknima, sitosti i mjestu koje imaju u prehrani.</li><li>Najveća pogreška je smatrati ih jednakima samo zato što oba uključuju voće i povrće.</li><li>Pametniji pristup gleda cilj: više sitosti, lakši unos, energija ili podrška rutini s aloe proizvodom.</li></ul>',
            $faq(
                'Koja je glavna razlika između smoothija i cijeđenog soka?',
                'Smoothie obično zadržava više vlakana, dok cijeđeni sok daje lakši i brži unos tekuće hrane.',
                'Kada smoothie ima više smisla?',
                'Kada želite više sitosti i obrok ili međuobrok s više vlakana.',
                'Gdje se aloe bolje uklapa?',
                'Ovisi o rutini i cilju, ali najviše smisla ima kad ne narušava preglednost i svrhu napitka.',
                'Koja je česta pogreška?',
                'Piti kalorične napitke i dalje ih doživljavati kao “laganu” zdravu opciju bez gledanja konteksta.'
            ),
            'Smoothie ili cijeđeni sok: kako ih razlikovati i gdje aloe ima smisla',
            'Saznajte kada izabrati smoothie, kada cijeđeni sok i kako aloe uključiti u rutinu bez konfuzije i precjenjivanja.',
            'Smoothie ili cijeđeni sok'
        ),
        $entry(
            131,
            'Emocija glad: kako razlikovati stvarnu glad od dosade, stresa i potrebe za utjehom',
            'Emocionalna glad često izgleda kao potreba za hranom, iako zapravo tražimo smirenje, prekid, nagradu ili bijeg. Ovdje je kako to prepoznati i graditi pametniji odnos s hranom.',
            '<ul><li>Emocionalna glad često je povezana s dosadom, stresom, umorom i potrebom za regulacijom osjećaja.</li><li>Najveća pogreška je svaku želju za hranom tumačiti kao fizičku potrebu tijela.</li><li>Pametniji pristup uči razlikovati tijelesne signale od emocionalnih okidača i automatizama.</li></ul>',
            $faq(
                'Što je emocionalna glad?',
                'To je osjećaj da trebamo hranu iako uzrok nije fizička potreba nego emocija ili navika.',
                'Kako razlikovati emocionalnu od stvarne gladi?',
                'Prava glad često raste postupno, a emocionalna dolazi naglo i traži vrlo specifičnu hranu ili osjećaj utjehe.',
                'Zašto je ovo važno za prehranu?',
                'Jer pomaže spriječiti automatsko jedenje koje ne rješava stvarni problem.',
                'Koja je česta pogreška?',
                'Pokušati disciplinom ugušiti emocionalnu glad bez razumijevanja što je zapravo pokreće.'
            ),
            'Emocionalna glad: kako razlikovati glad, dosadu i stresnu potrebu za hranom',
            'Otkrijte kako prepoznati emocionalnu glad i razviti mirniji, svjesniji odnos prema hrani i vlastitim okidačima.',
            'Emocionalna glad'
        ),
        $entry(
            135,
            'Prirodno izbjeljivanje zubi: što aloe vera i soda bikarbona mogu, a što ne trebaju obećavati',
            'Prirodno izbjeljivanje zvuči privlačno jer obećava brz i jednostavan sjaj, ali zubi i caklina traže više opreza nego entuzijazma. Ovdje je kako realnije gledati aloe, sodu bikarbonu i blistav osmijeh.',
            '<ul><li>Prirodni pristupi izbjeljivanju mogu imati smisla samo kada ne narušavaju dugoročnu udobnost i zaštitu zubi.</li><li>Najveća pogreška je agresivno koristiti kućne metode bez razmišljanja o caklini i osjetljivosti.</li><li>Pametniji pristup gleda nježnost, umjerenost i cjelinu oralne rutine.</li></ul>',
            $faq(
                'Može li prirodno izbjeljivanje pomoći osmijehu?',
                'Može dati osjećaj svježine i urednije njege, ali ne treba ga pretvarati u čudesnu promjenu preko noći.',
                'Zašto ljudi spajaju sodu bikarbonu i aloe veru?',
                'Zbog dojma prirodnosti i jednostavnosti kućnog pristupa.',
                'Zašto je oprez važan?',
                'Jer zubi i caklina ne vole pretjeranu grubost i česte eksperimentalne tretmane.',
                'Koja je česta pogreška?',
                'Ponavljati kućne metode prečesto i zanemariti dugoročnu zaštitu zubi.'
            ),
            'Prirodno izbjeljivanje zubi: što aloe i soda bikarbona realno mogu',
            'Saznajte kako realno gledati na prirodno izbjeljivanje zubi i zašto je nježnost važnija od brzog efekta.',
            'Prirodno izbjeljivanje zubi'
        ),
        $entry(
            137,
            'Plan prehrane za zdravu probavu: kako graditi ravniji trbuh kroz ritam, vlakna i jednostavne navike',
            'Zdrava probava ne dolazi iz jednog “čudotvornog” plana, nego iz dosljednih koraka koji smanjuju napuhnutost, kaos u obrocima i opterećenje probavnog sustava. Ovdje je kako takav plan postaviti održivo.',
            '<ul><li>Plan za zdravu probavu ima najviše smisla kada se temelji na ritmu obroka, vlaknima, vodi i jednostavnosti.</li><li>Najveća pogreška je tražiti ravan trbuh kroz kratkoročne restrikcije bez sređivanja osnovnih navika.</li><li>Pametniji pristup gleda probavu kroz dnevni obrazac, a ne samo kroz izgled trbuha.</li></ul>',
            $faq(
                'Što je temelj zdravije probave?',
                'Redovitiji ritam obroka, dovoljno vlakana, vode i manje prehrambenog kaosa.',
                'Zašto ljudi često misle da imaju problem samo s “trbuhom”?',
                'Jer napuhnutost vide kroz izgled, a ne kroz navike koje probavu opterećuju.',
                'Može li jednostavan plan pomoći?',
                'Može, osobito kada je održiv i ne traži ekstremne promjene odjednom.',
                'Koja je česta pogreška?',
                'Pokušati sve riješiti kroz zabrane umjesto kroz stabilniju dnevnu rutinu.'
            ),
            'Plan prehrane za zdravu probavu: kako graditi ravnotežu bez restrikcija',
            'Otkrijte kako postaviti održiv plan prehrane za zdraviju probavu, manje napuhnutosti i stabilniji osjećaj u tijelu.',
            'Plan prehrane za probavu'
        ),
        $entry(
            138,
            'Osjetljiva koža i atopijski dermatitis: gdje aloe vera može pomoći, a gdje treba više opreza',
            'Osjetljiva koža traži manje agresije, manje eksperimentiranja i više razumijevanja onoga što joj godi. Ovdje je gdje aloe vera može imati smisla i kako izbjeći uobičajene pogreške kod atopične kože.',
            '<ul><li>Aloe vera može biti koristan umirujući korak kod kože koja traži manje iritacije i više ugode.</li><li>Najveća pogreška je osjetljivu kožu zatrpati “prirodnim” rješenjima bez provjere što joj stvarno odgovara.</li><li>Pametniji pristup gleda barijeru kože, podnošljivost i jednostavniju rutinu njege.</li></ul>',
            $faq(
                'Može li aloe vera pomoći osjetljivoj koži?',
                'Može pomoći kao nježniji umirujući korak, posebno kada koža traži manje nadražaja.',
                'Je li to dovoljno za atopijski dermatitis?',
                'Ne nužno. Najčešće ima smisla kao dio šireg i opreznijeg pristupa njezi.',
                'Što je najvažnije kod ovakve kože?',
                'Manje iritacije, više predvidljivosti i zaštita kožne barijere.',
                'Koja je česta pogreška?',
                'Previše proizvoda i stalno isprobavanje novih “spasonosnih” opcija.'
            ),
            'Osjetljiva koža i aloe vera: gdje ima smisla, a gdje treba oprez',
            'Saznajte kako aloe vera može pomoći osjetljivoj i atopičnoj koži te zašto jednostavna rutina često vrijedi više od eksperimentiranja.',
            'Osjetljiva koža i aloe'
        ),
        $entry(
            143,
            'Aloe vera u kućanstvu: 5 praktičnih načina da biljku koristite pametnije i realnije',
            'Aloe vera u kućanstvu privlači jer djeluje kao višenamjenska biljka, ali najkorisnija je kada njezinu upotrebu zadržimo jednostavnom i smislenom. Ovdje je kako je uključiti u dom bez pretjerivanja.',
            '<ul><li>Aloe vera u kućanstvu ima najviše smisla kada je koristimo praktično i u situacijama u kojima doista donosi vrijednost.</li><li>Najveća pogreška je od jedne biljke očekivati da postane rješenje za cijeli dom.</li><li>Pametniji pristup bira nekoliko korisnih i održivih načina primjene umjesto deset improvizacija.</li></ul>',
            $faq(
                'Zašto je aloe vera popularna u kućanstvu?',
                'Zato što djeluje praktično, prirodno i lako uklopivo u male svakodnevne situacije.',
                'Treba li imati deset različitih načina korištenja?',
                'Ne. Puno je korisnije imati nekoliko provjerenih i smislenih primjena.',
                'Što ovu biljku čini zanimljivom za dom?',
                'Spoj jednostavnosti, dostupnosti i osjećaja prirodne pomoći u svakodnevici.',
                'Koja je česta pogreška?',
                'Pretvoriti aloe veru u univerzalni kućni alat za sve, bez realne koristi.'
            ),
            'Aloe vera u kućanstvu: kako je koristiti praktično i pametnije',
            'Otkrijte kako aloe veru uključiti u kućanstvo na nekoliko korisnih i realnih načina bez nepotrebnog pretjerivanja.',
            'Aloe vera u kućanstvu'
        ),
        $entry(
            144,
            'Zdravi krumpiri s aloe marinadama: kako recepte učiniti lakšima bez gubitka okusa',
            'Zdraviji recepti imaju najviše smisla kada su stvarno ukusni i održivi, a ne samo “fit” na papiru. Ovdje je kako jednostavniji krumpir i aloe marinade mogu pomoći lakšoj prehrambenoj rutini.',
            '<ul><li>Zdraviji recepti djeluju samo ako ostaju ukusni, praktični i dovoljno jednostavni za ponavljanje.</li><li>Najveća pogreška je zdravo kuhanje pretvoriti u kaznu bez okusa i užitka.</li><li>Pametniji pristup koristi male promjene u pripremi i mariniranju kako bi obrok ostao privlačan.</li></ul>',
            $faq(
                'Mogu li zdraviji recepti i dalje biti ukusni?',
                'Da, i upravo to je ključno ako želite da ih stvarno ponavljate.',
                'Zašto su male promjene u pripremi važne?',
                'Zato što često donose veliku razliku u kalorijama i osjećaju lakoće, bez drastičnih odricanja.',
                'Gdje aloe marinade imaju smisla?',
                'Tamo gdje daju zanimljiv dodatak rutini pripreme i pomažu održati raznolikost.',
                'Koja je česta pogreška?',
                'Složiti “zdrav” recept koji nitko ne želi ponovno jesti.'
            ),
            'Zdravi krumpiri i aloe marinade: kako zadržati okus i smanjiti težinu obroka',
            'Saznajte kako zdravije recepte učiniti ukusnima i održivima te kako male promjene u pripremi mogu dati veliku razliku.',
            'Zdravi krumpiri'
        ),
        $entry(
            147,
            'Aloe vera i šećer u krvi: kako o ovoj temi razmišljati oprezno, bez velikih obećanja',
            'Tema aloe vere i glukoze privlači puno pažnje, ali upravo zato traži više opreza, više konteksta i manje brzih zaključaka. Ovdje je kako o toj povezanosti razmišljati informiranije i smirenije.',
            '<ul><li>Aloe vera i šećer u krvi tema su koja traži oprez i jasno razlikovanje interesa od tvrdnji.</li><li>Najveća pogreška je aloe proizvod odmah vidjeti kao rješenje za složenu metaboličku priču.</li><li>Pametniji pristup gleda prehranu, kretanje, rutinu i širi zdravstveni kontekst zajedno s interesom za aloe.</li></ul>',
            $faq(
                'Zašto ljude zanima aloe vera i šećer u krvi?',
                'Zato što traže prirodnije načine kako podržati stabilniju glukozu i bolji metabolički ritam.',
                'Znači li to da aloe rješava regulaciju glukoze?',
                'Ne. To je preveliko pojednostavljenje teme koja je puno šira od jednog proizvoda.',
                'Što je ovdje važnije od hypea?',
                'Prehrana, kretanje, rutina i razumijevanje cjeline metabolizma.',
                'Koja je česta pogreška?',
                'Tražiti u jednom proizvodu ono što zapravo traži promjenu životnog stila.'
            ),
            'Aloe vera i šećer u krvi: kako temi pristupiti opreznije i pametnije',
            'Otkrijte kako o aloe veri i regulaciji glukoze razmišljati bez pretjerivanja te zašto kontekst znači više od hypea.',
            'Aloe vera i šećer u krvi'
        ),
        $entry(
            148,
            'Prirodni piling za tijelo: kako zob, šećer i aloe mogu dati ugodnu njegu bez pretjerane grubosti',
            'Kućni piling za tijelo ima smisla kada koži daje svježinu i ugodu, a ne kada postane pretjerano agresivan ritual. Ovdje je kako kombinaciju zobi, šećera i aloe koristiti nježnije i pametnije.',
            '<ul><li>Prirodni piling za tijelo ima više smisla kada poboljšava osjećaj kože bez dodatnog nadraživanja.</li><li>Najveća pogreška je piling pretvoriti u grubu rutinu koja više oštećuje nego pomaže.</li><li>Pametniji pristup bira nježniju teksturu, razumnu učestalost i dobar osjećaj kože nakon tretmana.</li></ul>',
            $faq(
                'Zašto ljudi vole prirodne pilinge za tijelo?',
                'Zbog osjećaja svježine, jednostavnosti i kućne kontrole nad sastojcima.',
                'Mogu li zob, šećer i aloe biti dobra kombinacija?',
                'Mogu, ako se koriste nježno i bez pretjerivanja u učestalosti ili grubosti.',
                'Koliko je važna učestalost?',
                'Vrlo je važna, jer previše pilinga može narušiti ugodu i ravnotežu kože.',
                'Koja je česta pogreška?',
                'Misliti da jači i češći piling nužno znači ljepšu kožu.'
            ),
            'Prirodni piling za tijelo: kako ga koristiti nježno i smisleno',
            'Saznajte kako prirodni piling za tijelo od zobi, šećera i aloe može pomoći koži bez nepotrebne grubosti i pretjerivanja.',
            'Prirodni piling za tijelo'
        ),
        $entry(
            152,
            'Domaća aloe vera maska za kosu sklonu peruti: kada prirodni recept ima smisla, a kada treba širi pristup',
            'Prirodni recepti za kosu mogu pomoći osjećaju njege i ugode vlasišta, ali perut često traži šire razumijevanje uzroka i rutine. Ovdje je kako masku s aloe verom koristiti razumnije i bez lažnih obećanja.',
            '<ul><li>Domaća maska za kosu ima više smisla kao nježna podrška vlasištu i rutini njege, ne kao jedino rješenje za perut.</li><li>Najveća pogreška je tretirati perut kao problem koji će jedan kućni recept trajno ukloniti.</li><li>Pametniji pristup gleda vlasište, učestalost njege i moguće šire uzroke nelagode i ljuštenja.</li></ul>',
            $faq(
                'Može li domaća aloe maska pomoći kosi sklonoj peruti?',
                'Može pomoći osjećaju njege i smirenijem vlasištu, posebno kao blag dodatak rutini.',
                'Je li to dovoljno za svaki oblik peruti?',
                'Ne. Neki uzroci peruti traže širi i pažljiviji pristup njezi vlasišta.',
                'Zašto ljudi vole ovakve recepte?',
                'Zbog osjećaja prirodnosti, jednostavnosti i kontrole nad sastojcima.',
                'Koja je česta pogreška?',
                'Očekivati da će jedna maska riješiti sve bez promjene ostatka rutine njege kose.'
            ),
            'Aloe vera maska za kosu: kada ima smisla kod vlasišta sklonog peruti',
            'Otkrijte kako domaću aloe vera masku za kosu koristiti pametnije i gdje prirodni recept može pomoći, a gdje treba širi pristup.',
            'Aloe maska za kosu'
        ),
    ],
];
