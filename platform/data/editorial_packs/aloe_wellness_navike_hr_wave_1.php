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
    'key' => 'aloe-wellness-navike-hr-wave-1',
    'name' => 'Aloe, wellness i navike (HR) - prvi val',
    'notes' => 'Ručni premium pack za aloe teme, performanse, cirkulaciju, mentalne navike i informirani odabir dodataka.',
    'entries' => [
        $entry(
            569,
            'Utjecaj glazbe na performanse: kako složiti playlistu koja stvarno diže energiju',
            'Glazba može biti snažan alat za fokus, trening i raspoloženje, ali samo kada ju koristimo svjesno, a ne nasumično. Ovdje je kako slagati playlistu koja podržava performanse umjesto da postane samo pozadinska buka.',
            '<ul><li>Glazba može pomoći fokusu, ritmu i energiji kada odgovara zadatku i trenutnom stanju tijela.</li><li>Najveća pogreška je koristiti istu glazbu za svaki posao i očekivati isti učinak.</li><li>Pametniji pristup gradi playliste prema namjeni: duboki rad, trening, oporavak ili podizanje ritma.</li></ul>',
            [
                ['question' => 'Može li glazba stvarno poboljšati performanse?', 'answer' => 'Može pomoći kroz ritam, motivaciju i lakše održavanje fokusa ili energije.'],
                ['question' => 'Zašto ista playlista nekad ne djeluje?', 'answer' => 'Zato što različiti zadaci i različita stanja traže drukčiji glazbeni okvir.'],
                ['question' => 'Što čini dobru playlistu?', 'answer' => 'Jasna namjena, stabilan ton i dovoljno pjesama koje ne razbijaju koncentraciju.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Pustiti glazbu automatski, bez razmišljanja pomaže li ili odvlači pažnju.'],
            ],
            'Glazba i performanse: kako složiti playlistu koja stvarno radi za vas',
            'Saznajte kako glazbu koristiti za bolji fokus, energiju i performanse kroz smislenije playliste.',
            'Glazba i performanse'
        ),
        $entry(
            584,
            'Aloe vera vs. agava: u čemu je razlika i gdje nastaje najviše zabune',
            'Aloe vera i agava često se miješaju u sadržaju o biljkama, kozmetici i prirodnim proizvodima, iako se radi o različitim pričama i primjenama. Ovdje je kako ih razlikovati bez marketinške magle.',
            '<ul><li>Aloe vera i agava nisu ista biljka niti imaju istu glavnu ulogu u praksi ili proizvodima.</li><li>Najveća pogreška je miješati njihovu botaničku sličnost s istom funkcionalnom vrijednošću.</li><li>Pametniji pristup razlikuje biljku, način uporabe i razlog zašto se o njima uopće govori.</li></ul>',
            [
                ['question' => 'Jesu li aloe vera i agava isto?', 'answer' => 'Ne. Riječ je o različitim biljkama s različitim pričama i primjenama.'],
                ['question' => 'Zašto ih ljudi miješaju?', 'answer' => 'Zbog sličnog izgleda i površnog marketinškog spominjanja u istom kontekstu.'],
                ['question' => 'Gdje je razlika najvažnija?', 'answer' => 'Kad procjenjujete proizvode, sastojke i očekivanu funkciju biljke.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Pretpostaviti da sve što vrijedi za aloe automatski vrijedi i za agavu.'],
            ],
            'Aloe vera ili agava: kako ih razlikovati i izbjeći najčešće zabune',
            'Razumijte razliku između aloe vere i agave te gdje nastaju najčešće zablude o njihovoj ulozi i vrijednosti.',
            'Aloe vera vs agava'
        ),
        $entry(
            588,
            'Emocionalna inteligencija: kako razvijati samosvijest bez teorije bez primjene',
            'Emocionalna inteligencija nije samo lijep pojam za osobni razvoj nego skup vještina koje postaju korisne tek kada ih živimo u stvarnim odnosima i pritiscima. Ovdje je kako joj pristupiti praktičnije.',
            '<ul><li>Emocionalna inteligencija najviše vrijedi kada pomaže u odnosima, granicama i regulaciji svakodnevnih reakcija.</li><li>Najveća pogreška je ostati na čitanju i testovima osobnosti bez stvarne primjene u životu.</li><li>Pametniji pristup gradi samosvijest, jezik za emocije i svjesniji odgovor pod pritiskom.</li></ul>',
            [
                ['question' => 'Što je emocionalna inteligencija u praksi?', 'answer' => 'To je sposobnost da bolje razumijete svoje emocije, reakcije i odnose s drugima.'],
                ['question' => 'Može li se razvijati?', 'answer' => 'Može, kroz samopromatranje, komunikaciju i svjesnije reagiranje u stvarnim situacijama.'],
                ['question' => 'Zašto je samosvijest važna?', 'answer' => 'Jer bez nje teško razlikujemo što stvarno osjećamo i kako to utječe na odluke.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Pretvoriti emocionalnu inteligenciju u teorijsku etiketu bez promjene ponašanja.'],
            ],
            'Emocionalna inteligencija: kako je pretvoriti u korisnu svakodnevnu vještinu',
            'Saznajte kako razvijati emocionalnu inteligenciju kroz samosvijest, komunikaciju i zreliji odgovor pod pritiskom.',
            'Emocionalna inteligencija'
        ),
        $entry(
            591,
            'Jetra i alkohol: kako pristupiti oporavku bez mitova o čudesnom detoksu',
            'Jetra se često spominje tek nakon razdoblja pretjerivanja, a upravo tada marketing nudi najviše brzih “rješenja”. Ovdje je kako oporavak nakon alkohola gledati kroz ritam, prehranu i realizam, a ne kroz mitove o instant detoksu.',
            '<ul><li>Oporavak nakon alkohola najviše ovisi o vremenu, odmoru, hidraciji i smirenijem ritmu, ne o jednom čudesnom proizvodu.</li><li>Najveća pogreška je tražiti instant “čišćenje jetre” bez promjene ponašanja i navika koje su problem stvorile.</li><li>Pametniji pristup gradi oporavak kroz jednostavne i dosljedne korake, bez pretjerivanja.</li></ul>',
            [
                ['question' => 'Može li se jetra oporaviti?', 'answer' => 'Često može, ali to ovisi o obrascima ponašanja, vremenu i ukupnoj brizi o organizmu.'],
                ['question' => 'Postoji li brzi detoks koji sve rješava?', 'answer' => 'Ne. Najviše pomažu vrijeme, odmor i promjene u navikama.'],
                ['question' => 'Gdje aloe vera može ući u priču?', 'answer' => 'Samo kao dio šire rutine, ne kao glavni odgovor na posljedice pretjerivanja.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Ponoviti isti obrazac i očekivati da će ga dodatak ili detoks plan popraviti.'],
            ],
            'Jetra i alkohol: kako gledati oporavak bez brzih detoks mitova',
            'Otkrijte kako nakon alkohola podržati oporavak jetre kroz realne korake, a ne kroz mitove o instant rješenjima.',
            'Jetra i alkohol'
        ),
        $entry(
            641,
            'Kako birati pouzdane proizvođače superhrane: certifikati, trag porijekla i sigurnost',
            'Superhrana zvuči impresivno sve dok ne postane jasno koliko je kvaliteta proizvoda zapravo neujednačena. Ovdje je kako procjenjivati proizvođače, certifikate i porijeklo bez padanja na lijep marketing.',
            '<ul><li>Kod superhrane kvaliteta proizvođača i kontrola podrijetla često znače više od samog naziva namirnice.</li><li>Najveća pogreška je kupovati isključivo po etiketi “superfood” bez provjere izvora i standarda.</li><li>Pametniji pristup gleda transparentnost, certifikate i reputaciju proizvođača kroz dulje vrijeme.</li></ul>',
            [
                ['question' => 'Zašto su certifikati važni?', 'answer' => 'Jer pomažu procijeniti standard proizvodnje, sigurnost i dosljednost kvalitete.'],
                ['question' => 'Je li naziv “superhrana” dovoljan?', 'answer' => 'Ne. Taj naziv često više govori o marketingu nego o realnoj kvaliteti proizvoda.'],
                ['question' => 'Što još treba gledati osim certifikata?', 'answer' => 'Porijeklo, transparentnost brenda i način na koji komunicira kvalitetu i kontrolu.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Zamijeniti dobar dizajn pakiranja za dokaz stvarne kvalitete.'],
            ],
            'Superhrana i sigurnost: kako prepoznati pouzdanijeg proizvođača',
            'Saznajte kako birati pouzdanije proizvođače superhrane kroz certifikate, porijeklo i stvarnu transparentnost.',
            'Pouzdana superhrana'
        ),
        $entry(
            644,
            'Problemi s cirkulacijom: gdje masaža i prirodni gelovi mogu stvarno pomoći',
            'Problemi s cirkulacijom često počinju kao osjećaj težine, hladnoće ili umora u nogama, pa ih ljudi dugo ignoriraju. Ovdje je kako masažu i prirodne gelove gledati kao praktičnu podršku, a ne kao čudesno rješenje za sve.',
            '<ul><li>Kod osjećaja teških nogu i slabije cirkulacije male ponovljive navike često znače više od jednokratnih pokušaja.</li><li>Najveća pogreška je čekati da nelagoda postane velika prije nego što uvedete podršku i kretanje.</li><li>Pametniji pristup kombinira masažu, lagano kretanje, hlađenje i njegu kože u jednostavnu rutinu.</li></ul>',
            [
                ['question' => 'Mogu li masaža i gelovi pomoći?', 'answer' => 'Mogu pomoći osjećaju lakših nogu i većeg komfora, posebno kad se koriste redovito.'],
                ['question' => 'Je li to dovoljno za svaki problem cirkulacije?', 'answer' => 'Ne. To je podrška za komfor i rutinu, a ne univerzalno rješenje za sve uzroke.'],
                ['question' => 'Što još pomaže?', 'answer' => 'Više kretanja, manje dugog sjedenja ili stajanja i bolji dnevni ritam.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Koristiti gel ili masažu bez promjene osnovnih navika koje stvaraju problem.'],
            ],
            'Cirkulacija i masaža: gdje prirodni gelovi imaju smisla za svakodnevni komfor',
            'Otkrijte kako masaža i prirodni gelovi mogu pomoći kod problema s cirkulacijom i osjećaja teških nogu.',
            'Problemi s cirkulacijom'
        ),
        $entry(
            647,
            'Antioksidativna snaga aloe vere: što vrijedi znati bez pretvaranja u hype',
            'Aloe vera je zanimljiva i zbog sastava i zbog tradicije uporabe, ali antioksidativna priča lako sklizne u pretjerivanje. Ovdje je kako te spojeve i koristi gledati realno, kroz njegu, prehranu i širi kontekst zdravlja.',
            '<ul><li>Antioksidativna svojstva aloe zanimljiva su kao dio šire priče o njezi i podršci organizmu, ne kao samostalni spas.</li><li>Najveća pogreška je svaku znanstvenu natuknicu pretvoriti u veliko obećanje za sve probleme.</li><li>Pametniji pristup aloe smješta u realan okvir: njegu, podršku i granice onoga što jedan sastojak može.</li></ul>',
            [
                ['question' => 'Zašto se aloe povezuje s antioksidansima?', 'answer' => 'Zbog spojeva koji su zanimljivi u znanstvenom i praktičnom kontekstu biljke i njezine primjene.'],
                ['question' => 'Znači li to da aloe rješava sve?', 'answer' => 'Ne. Korisna je u određenim okvirima, ali nije univerzalno rješenje.'],
                ['question' => 'Gdje takva priča ima najviše smisla?', 'answer' => 'U kontekstu njege, podrške i informiranijeg razumijevanja sastava proizvoda.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Pretjerati s obećanjima samo zato što sastav biljke zvuči impresivno.'],
            ],
            'Antioksidansi i aloe vera: kako razlikovati korisnu činjenicu od hypea',
            'Razumijte antioksidativnu priču aloe vere i gdje ona stvarno ima smisla bez pretjeranih obećanja.',
            'Aloe i antioksidansi'
        ),
        $entry(
            649,
            'Sladić (licorice): prirodni saveznik za probavu i pluća ili još jedan precijenjeni klasik',
            'Sladić je zanimljiv jer spaja tradicionalnu primjenu i suvremeni interes za probavu i dišne puteve, ali baš zato lako dobije više zasluga nego što zaslužuje. Ovdje je kako ga gledati realnije i informiranije.',
            '<ul><li>Sladić može imati smisla kao dio šireg pristupa grlu, probavi i osjećaju umirenja sluznice.</li><li>Najveća pogreška je pretvoriti ga u univerzalni biljni odgovor bez razumijevanja kada i komu odgovara.</li><li>Pametniji pristup promatra tradiciju, način uporabe i širi kontekst simptoma.</li></ul>',
            [
                ['question' => 'Zašto se sladić često spominje za grlo i probavu?', 'answer' => 'Zato što ima dugu tradiciju uporabe i povezuje se s osjećajem smirenja sluznice.'],
                ['question' => 'Je li dobar za svakoga?', 'answer' => 'Ne nužno. Kao i s drugim biljkama, važan je kontekst i način uporabe.'],
                ['question' => 'Kada ima više smisla?', 'answer' => 'Kada je dio razumnog, umjerenog i ciljano korištenog pristupa.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Dodijeliti biljci više moći nego što stvarno može imati u praksi.'],
            ],
            'Sladić za probavu i grlo: gdje ima smisla, a gdje očekivanja rastu previše',
            'Saznajte kada sladić može imati smisla za probavu i dišne puteve te kako ga gledati bez pretjerivanja.',
            'Sladić'
        ),
        $entry(
            651,
            'Kalcij i vitamin D: zašto njihova sinergija ima smisla za kosti i zube',
            'Kalcij i vitamin D često se spominju zajedno, ali njihova zajednička priča ima smisla samo kada ih smjestimo u širu sliku prehrane i zdravlja kostiju. Ovdje je kako razumjeti tu sinergiju bez prejednostavljivanja.',
            '<ul><li>Kalcij i vitamin D zajedno imaju logiku jer jedan bez drugog često ne donosi puni smisao za zdravlje kostiju.</li><li>Najveća pogreška je gledati ih kao odvojene “čarobne” nutrijente umjesto kao dio sustava.</li><li>Pametniji pristup uključuje prehranu, kretanje, sunce i dosljedne navike, a ne samo kapsule ili tablete.</li></ul>',
            [
                ['question' => 'Zašto se kalcij i vitamin D spominju zajedno?', 'answer' => 'Jer su povezani u podršci kostima i općem mineralnom balansu organizma.'],
                ['question' => 'Je li dovoljno uzeti samo jedan od njih?', 'answer' => 'Ne uvijek. Njihova vrijednost često ovisi o zajedničkom kontekstu i ukupnoj prehrani.'],
                ['question' => 'Što još utječe na kosti osim dodataka?', 'answer' => 'Kretanje, prehrana, sunce i dugoročne dnevne navike.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Svesti zdravlje kostiju samo na jedan ili dva dodatka bez šire slike.'],
            ],
            'Kalcij i vitamin D: kako njihova sinergija stvarno podržava kosti i zube',
            'Otkrijte zašto kalcij i vitamin D imaju smisla zajedno i kako ih uklopiti u širi plan za zdravlje kostiju.',
            'Kalcij i vitamin D'
        ),
        $entry(
            885,
            'Perfekcionizam i odgađanje: kako izaći iz petlje u 7 realnih koraka',
            'Perfekcionizam i odgađanje često izgledaju kao dva odvojena problema, ali u praksi se hrane jedan drugim. Ovdje je kako ih prepoznati kao zajednički obrazac i razbiti ga bez još jednog nerealnog plana samopopravljanja.',
            '<ul><li>Perfekcionizam često nije znak visoke discipline nego straha od nesavršenog početka ili ishoda.</li><li>Najveća pogreška je pokušati riješiti odgađanje stvaranjem još strožih i većih planova.</li><li>Pametniji pristup smanjuje prag početka, vraća akciju u male korake i gradi realniji odnos prema grešci.</li></ul>',
            [
                ['question' => 'Kako su perfekcionizam i odgađanje povezani?', 'answer' => 'Perfekcionizam često povećava pritisak, a pritisak zatim pojačava odgađanje.'],
                ['question' => 'Zašto stroži planovi često ne pomažu?', 'answer' => 'Jer dodatno pojačavaju osjećaj da sve mora biti idealno prije nego što počnete.'],
                ['question' => 'Što najviše pomaže?', 'answer' => 'Manji početni koraci, realniji standardi i češće pokretanje akcije bez čekanja savršenog trenutka.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Brkati perfekcionizam s kvalitetom rada i tako održavati isti obrazac unedogled.'],
            ],
            'Perfekcionizam i odgađanje: kako izaći iz istog obrasca bez novih iluzija',
            'Saznajte kako razbiti vezu između perfekcionizma i odgađanja kroz 7 realnijih i održivijih koraka.',
            'Perfekcionizam i odgađanje'
        ),
    ],
];
