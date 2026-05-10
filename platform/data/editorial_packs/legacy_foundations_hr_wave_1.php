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
    'key' => 'legacy-foundations-hr-wave-1',
    'name' => 'Legacy foundations (HR) - prvi val',
    'notes' => 'Ručni premium pack za starije Forever/product stranice i legacy business/mindset sadržaj.',
    'entries' => [
        $entry(
            40,
            'Omega 3 kapsule: kada Forever Arctic Sea stvarno ima smisla za srce, mozak i svakodnevni fokus',
            'Omega-3 dodaci imaju najviše smisla kada ih promatramo kroz prehranu, navike i realne ciljeve, a ne samo kroz jednu veliku zdravstvenu tvrdnju. Ovdje je gdje Forever Arctic Sea može biti koristan i kako ga procijeniti bez pretjerivanja.',
            '<ul><li>Omega-3 kapsule najviše vrijede kada nadopunjuju prehranu koja je siromašna masnom ribom i kvalitetnim mastima.</li><li>Najveća pogreška je očekivati da dodatak sam riješi energiju, fokus i zdravlje srca bez boljih navika.</li><li>Pametniji pristup gleda kvalitetu izvora, redovitost uzimanja i širi stil života.</li></ul>',
            $faq(
                'Kome omega-3 kapsule najčešće imaju smisla?',
                'Najčešće ljudima koji rijetko jedu masnu ribu i žele praktičniju dnevnu podršku.',
                'Može li Forever Arctic Sea zamijeniti dobru prehranu?',
                'Ne. Najviše koristi ima kada prati uravnoteženu prehranu i općenito bolje navike.',
                'Za što se ljudi najčešće zanimaju kod omega-3 dodataka?',
                'Najčešće zbog srca, mozga, fokusa i općeg osjećaja vitalnosti.',
                'Koja je česta pogreška?',
                'Kupiti omega-3 proizvod bez provjere kako izgleda ostatak prehrane i rutine.'
            ),
            'Omega 3 kapsule: kada Forever Arctic Sea ima smisla za srce i mozak',
            'Saznajte kada omega-3 kapsule poput Forever Arctic Sea imaju smisla i kako ih procijeniti kroz prehranu i navike.',
            'Omega 3 kapsule'
        ),
        $entry(
            41,
            'C9 Forever Living Products: za koga ovaj reset program ima smisla, a kada treba usporiti',
            'C9 je privlačan jer obećava brzi početni reset, ali stvarna vrijednost programa ovisi o očekivanjima, disciplini i zdravstvenom kontekstu. Ovdje je kako ga gledati realnije prije nego što uopće krenete.',
            '<ul><li>C9 najviše smisla ima kao strukturirani kratki start za osobe koje žele jasniji prehrambeni okvir.</li><li>Najveća pogreška je očekivati trajnu promjenu bez plana što dolazi nakon prvih devet dana.</li><li>Pametniji pristup procjenjuje spremnost, tempo i održiv nastavak nakon programa.</li></ul>',
            $faq(
                'Za koga C9 može imati više smisla?',
                'Za osobe koje žele kratak, jasan plan i spremne su nakon toga nastaviti s boljim navikama.',
                'Je li C9 rješenje za dugoročno mršavljenje sam po sebi?',
                'Ne. Dugoročni rezultat ovisi o prehrani, kretanju i navikama nakon završetka programa.',
                'Što treba procijeniti prije početka?',
                'Zdravstveni kontekst, očekivanja, dnevni ritam i sposobnost da plan odradite dosljedno.',
                'Koja je česta pogreška?',
                'Vidjeti C9 kao prečac umjesto kao kratku fazu unutar šire promjene rutine.'
            ),
            'C9 Forever: kada devetodnevni reset ima smisla, a kada ne',
            'Otkrijte kome C9 Forever Living program može pomoći kao početni reset i kako izbjeći najčešće pogreške.',
            'C9 Forever'
        ),
        $entry(
            43,
            'Forever Multi Maca: kada podrška za libido i energiju ima smisla, a kada očekivanja odu predaleko',
            'Maca je popularna jer se povezuje s energijom, libidom i hormonalnom ravnotežom, ali njezina prava vrijednost dolazi tek kada je gledamo bez čudotvornih obećanja. Ovdje je kako procijeniti Multi Macu kroz cilj, kontekst i realna očekivanja.',
            '<ul><li>Multi Maca najviše smisla ima kada postoji jasan razlog za uvođenje i dovoljno strpljenja za procjenu učinka.</li><li>Najveća pogreška je od jednog dodatka očekivati rješenje za stres, umor i hormonalni disbalans odjednom.</li><li>Pametniji pristup gleda san, prehranu i oporavak prije nego što dodatku pripiše preveliku ulogu.</li></ul>',
            $faq(
                'Zašto ljudi posežu za Multi Macom?',
                'Najčešće zbog energije, vitalnosti i interesa za podršku libidu ili hormonalnoj ravnoteži.',
                'Može li sama maca riješiti pad energije?',
                'Ne. Najviše smisla ima kao dopuna boljoj rutini, ne kao zamjena za osnove.',
                'Koliko je važno imati realna očekivanja?',
                'Vrlo je važno, jer se učinak dodatka procjenjuje tek unutar šire slike navika i stresa.',
                'Koja je česta pogreška?',
                'Kupiti proizvod zbog velikih obećanja bez provjere pravog razloga umora ili pada libida.'
            ),
            'Forever Multi Maca: kada ima smisla za energiju i libido',
            'Saznajte kada Forever Multi Maca može imati smisla i kako je procijeniti bez nerealnih očekivanja.',
            'Forever Multi Maca'
        ),
        $entry(
            45,
            'Aloe vera za lice: kada ova jednostavna njega stvarno pomaže koži, a kada nije dovoljna sama',
            'Aloe vera za lice najviše pomaže kada koži treba smirenje, lagana hidratacija i jednostavnija rutina, ali nije univerzalni odgovor na svaki problem tena. Ovdje je kako je koristiti pametnije i sigurnije.',
            '<ul><li>Aloe vera za lice najčešće ima smisla kada koža traži nježniji, umirujući pristup.</li><li>Najveća pogreška je očekivati da jedan lagani aloe proizvod sam riješi akne, mrlje i narušenu barijeru.</li><li>Pametniji pristup prati tip kože, reakcije i ostatak rutine njege.</li></ul>',
            $faq(
                'Kome aloe vera za lice najviše odgovara?',
                'Često osjetljivijoj, nadraženoj ili dehidriranoj koži koja traži jednostavniju njegu.',
                'Može li pomoći nakon sunca ili iritacije?',
                'Može pružiti osjećaj smirenja i ugode, posebno kao dio lagane rutine.',
                'Je li dovoljna za sve probleme kože?',
                'Ne. Za složenije probleme kože obično treba širi i ciljaniji pristup.',
                'Koja je česta pogreška?',
                'Pretvoriti aloe veru u jedini korak njege bez prilagodbe tipu kože i cilju.'
            ),
            'Aloe vera za lice: kada ima smisla za smirenje i hidrataciju kože',
            'Otkrijte kada aloe vera za lice može pomoći koži i kako je uklopiti u rutinu bez pretjeranih očekivanja.',
            'Aloe vera za lice'
        ),
        $entry(
            47,
            'Aloe Vera Gel: kako procijeniti ovaj klasik za probavu, rutinu i osjećaj vitalnosti',
            'Aloe Vera Gel je jedan od najpoznatijih Forever proizvoda upravo zato što ga ljudi vežu uz probavu, svakodnevni reset i opći osjećaj ravnoteže. Ovdje je kako ga gledati realno i kome može imati više smisla.',
            '<ul><li>Aloe Vera Gel najviše smisla ima kada tražite jednostavan dodatak rutini za probavu i opći osjećaj lakoće.</li><li>Najveća pogreška je od njega očekivati da nadoknadi lošu prehranu i neuredne navike.</li><li>Pametniji pristup gleda dosljednost, podnošljivost i širi prehrambeni kontekst.</li></ul>',
            $faq(
                'Zašto ljudi najčešće uzimaju Aloe Vera Gel?',
                'Najčešće zbog interesa za probavu, svakodnevnu rutinu i osjećaj opće vitalnosti.',
                'Kada može imati više smisla?',
                'Kada je dio mirnije i urednije prehrambene rutine, a ne pokušaj brzog rješenja.',
                'Može li zamijeniti rad na prehrani?',
                'Ne. Najviše vrijedi kada prati kvalitetniju prehranu i ritam obroka.',
                'Koja je česta pogreška?',
                'Uvesti gel bez realne slike ostatka prehrane i očekivati prebrz učinak.'
            ),
            'Aloe Vera Gel: kada ima smisla za probavu i svakodnevnu rutinu',
            'Saznajte kada Aloe Vera Gel ima više smisla za probavu i vitalnost te kako ga procijeniti bez pretjerivanja.',
            'Aloe Vera Gel'
        ),
        $entry(
            49,
            'Forever Active Pro B: kada probiotik stvarno ima smisla za probavu i otpornost organizma',
            'Probiotik je najkorisniji kada postoji jasan razlog za njegovo uvođenje, a ne samo općenita želja da “ojačamo imunitet”. Ovdje je kako Active Pro B procijeniti kroz probavu, rutinu i realna očekivanja.',
            '<ul><li>Active Pro B najviše smisla ima kada postoji potreba za jednostavnijom podrškom probavi i svakodnevnoj ravnoteži.</li><li>Najveća pogreška je od probiotika očekivati trenutačno rješenje bez promjene prehrane i navika.</li><li>Pametniji pristup gleda prehrambena vlakna, ritam obroka i širu sliku crijevne rutine.</li></ul>',
            $faq(
                'Kada probiotik može imati više smisla?',
                'Kada postoji interes za stabilniju probavu, više rutine i podršku crijevnoj ravnoteži.',
                'Može li probiotik sam riješiti sve probavne tegobe?',
                'Ne. Najviše koristi ima kao dio šireg plana prehrane i navika.',
                'Zašto je važna dosljednost?',
                'Zato što se učinak rutine procjenjuje kroz vrijeme, a ne kroz jedan ili dva dana.',
                'Koja je česta pogreška?',
                'Kupiti probiotik bez pregleda prehrane, vlakana i tempa svakodnevice.'
            ),
            'Forever Active Pro B: kada probiotik ima smisla za probavu',
            'Otkrijte kada Forever Active Pro B može imati smisla za probavu i kako ga procijeniti kroz širu rutinu.',
            'Active Pro B'
        ),
        $entry(
            50,
            'Garcinia Cambogia: gdje podrška regulaciji tjelesne težine može imati smisla, a gdje marketing pretjera',
            'Garcinia Cambogia često se predstavlja kao jednostavno rješenje za apetit i kilograme, ali stvarna vrijednost dodatka ovisi o navikama koje ga okružuju. Ovdje je kako joj pristupiti bez nerealnih obećanja.',
            '<ul><li>Garcinia ima više smisla kada je dio šire strategije prehrane, kretanja i kontrole porcija.</li><li>Najveća pogreška je očekivati da dodatak sam pokrene ozbiljan pad kilograma.</li><li>Pametniji pristup gleda apetit, navike grickanja i stvarne razloge zbog kojih težina stagnira.</li></ul>',
            $faq(
                'Zašto ljudi posežu za Garciniom?',
                'Najčešće zbog interesa za apetit, kontrolu unosa i lakše upravljanje tjelesnom težinom.',
                'Može li sama pokrenuti mršavljenje?',
                'Ne. Bez prehrambenog plana i boljih navika učinak dodatka ostaje ograničen.',
                'Kada ima više smisla?',
                'Kad je dio promišljenijeg plana i kada korisnik zna što pokušava promijeniti.',
                'Koja je česta pogreška?',
                'Tražiti prečac umjesto raditi na rutini obroka, kretanju i kalorijskom unosu.'
            ),
            'Garcinia Cambogia: kada ima smisla za regulaciju težine, a kada ne',
            'Saznajte kako Garcinia Cambogia realno stoji u priči o apetitu i regulaciji tjelesne težine.',
            'Garcinia Cambogia'
        ),
        $entry(
            51,
            'Forever First: kada višenamjenski aloe sprej stvarno pomaže koži i kosi',
            'Aloe sprejevi su najkorisniji kada olakšavaju jednostavnu svakodnevnu njegu, a ne kada im pripisujemo čudesne učinke. Ovdje je kako Forever First procijeniti kroz praktičnost, tip kože i realne situacije u kojima ga ljudi najviše vole koristiti.',
            '<ul><li>Forever First ima najviše smisla kao praktičan aloe sprej za jednostavnu njegu kože i vlasišta.</li><li>Najveća pogreška je očekivati da višenamjenski sprej zamijeni ciljanu terapiju ili cijelu rutinu njege.</li><li>Pametniji pristup gleda situacije u kojima brzina, praktičnost i umirujući osjećaj imaju stvarnu vrijednost.</li></ul>',
            $faq(
                'Za što ljudi najčešće koriste Forever First?',
                'Najčešće za kožu nakon sunca, manje iritacije, vlasište i jednostavne situacije svakodnevne njege.',
                'Je li dovoljno snažan za sve probleme kože?',
                'Ne. Ima najviše smisla kao podrška i praktičan korak, ne kao jedino rješenje.',
                'Kada posebno ima smisla?',
                'Kad želite brz, lagan i jednostavan aloe proizvod koji se lako koristi tijekom dana.',
                'Koja je česta pogreška?',
                'Tražiti od spreja više nego što višenamjenski proizvod realno može dati.'
            ),
            'Forever First: kada aloe sprej ima smisla za kožu i kosu',
            'Otkrijte kada Forever First stvarno ima smisla u njezi kože i kose te gdje ga ne treba precijeniti.',
            'Forever First'
        ),
        $entry(
            52,
            'Aloe Vera Gelly: kada gušća aloe njega daje smisleniju podršku koži nego lagani gelovi',
            'Aloe Vera Gelly privlači ljude koji žele deblji, zaštitniji osjećaj na koži, posebno kada je riječ o suhoći, manjim iritacijama i potrebi za praktičnim kućnim proizvodom. Ovdje je kako procijeniti kome ovakav format najviše odgovara.',
            '<ul><li>Aloe Vera Gelly ima najviše smisla kada koža traži deblji zaštitni sloj i dulji osjećaj ugode.</li><li>Najveća pogreška je koristiti ga kao univerzalni odgovor za svaku kožnu tegobu.</li><li>Pametniji pristup gleda gdje gušća aloe tekstura ima prednost, a gdje je bolje birati lakše formule.</li></ul>',
            $faq(
                'Kome Aloe Vera Gelly najviše odgovara?',
                'Najčešće koži koja voli bogatiji, zaštitniji i dulje prisutan osjećaj njege.',
                'Po čemu se razlikuje od laganijih aloe proizvoda?',
                'Po gušćoj teksturi i praktičnijoj primjeni kada koža traži više udobnosti.',
                'Može li biti koristan za kućnu prvu njegu?',
                'Može imati smisla kao praktičan proizvod za jednostavnije situacije njege kože.',
                'Koja je česta pogreška?',
                'Očekivati isti osjećaj i svrhu kao od laganog spreja ili tankog gela.'
            ),
            'Aloe Vera Gelly: kada gušća aloe njega ima više smisla za kožu',
            'Saznajte kada Aloe Vera Gelly ima više smisla od laganijih aloe proizvoda i kome najviše odgovara.',
            'Aloe Vera Gelly'
        ),
        $entry(
            53,
            'Forever Bright pasta za zube bez fluora: kome ovakav pristup oralnoj njezi stvarno odgovara',
            'Pasta za zube bez fluora nije automatski bolja ili lošija, nego jednostavno drugačija opcija koja nekim korisnicima bolje odgovara po sastavu, okusu ili filozofiji njege. Ovdje je kako Forever Bright gledati razumno i bez ideoloških krajnosti.',
            '<ul><li>Forever Bright najviše smisla ima korisnicima koji traže aloe-based i blaži doživljaj paste za zube bez fluora.</li><li>Najveća pogreška je pastu pretvoriti u ideološku odluku bez promatranja cijele oralne rutine.</li><li>Pametniji pristup gleda higijenu, tehniku pranja, redovitost i ono što korisnik stvarno može održati.</li></ul>',
            $faq(
                'Kome pasta bez fluora može biti zanimljiva?',
                'Najčešće onima koji traže drugačiji sastav, blaži osjećaj ili aloe pristup oralnoj njezi.',
                'Je li sama pasta dovoljna za zdravlje zubi?',
                'Ne. Tehnika pranja, učestalost i ukupna oralna higijena i dalje su ključni.',
                'Zašto je važno gledati cijelu rutinu?',
                'Zato što jednu pastu treba promatrati kao dio navike, a ne kao jedino rješenje.',
                'Koja je česta pogreška?',
                'Tražiti savršenu pastu umjesto graditi dosljednu i kvalitetnu oralnu rutinu.'
            ),
            'Forever Bright: kada pasta za zube bez fluora ima smisla',
            'Saznajte kome Forever Bright pasta bez fluora može odgovarati i kako je procijeniti kroz cjelovitu oralnu njegu.',
            'Forever Bright'
        ),
        $entry(
            37,
            'Zašto još niste vođa: 10 obrazaca koji usporavaju utjecaj, povjerenje i osobni rast',
            'Vodstvo se puno češće gubi zbog navika, komunikacije i nejasnog karaktera nego zbog nedostatka titule. Ovdje je kako prepoznati obrasce koji koče utjecaj i što promijeniti da biste postali stabilniji vođa.',
            '<ul><li>Vodstvo najčešće počinje odgovornošću, jasnom komunikacijom i dosljednošću u ponašanju.</li><li>Najveća pogreška je očekivati poštovanje bez rada na karakteru, navikama i povjerenju.</li><li>Pametniji pristup traži poštenu procjenu vlastitih slijepih točaka i spremnost na korekciju.</li></ul>',
            $faq(
                'Zašto netko ne postaje vođa iako je stručan?',
                'Zato što vodstvo traži više od stručnosti: povjerenje, komunikaciju i dosljednost.',
                'Može li se vodstvo naučiti?',
                'Može, ali traži samopromatranje, povratnu informaciju i rad na navikama.',
                'Koji je prvi korak?',
                'Pošteno prepoznati obrasce koji ruše povjerenje i koče utjecaj na druge.',
                'Koja je česta pogreška?',
                'Tražiti status vođe bez spremnosti na odgovornost i osobnu promjenu.'
            ),
            'Zašto još niste vođa: 10 obrazaca koji koče vaš utjecaj',
            'Otkrijte 10 obrazaca zbog kojih ljudi ne rastu u vodstvo i kako graditi veći utjecaj kroz karakter i navike.',
            'Zašto niste vođa'
        ),
        $entry(
            38,
            'Kako razmišljate i kakav utjecaj ima okolina: zašto mindset rijetko raste u pogrešnom okruženju',
            'Način razmišljanja ne formira se u vakuumu. Ljudi, ritam, sadržaj koji pratite i standardi koje tolerirate snažno oblikuju kako razmišljate o sebi, radu i budućnosti. Ovdje je kako to prepoznati na vrijeme.',
            '<ul><li>Mindset se najbrže oblikuje kroz okolinu, svakodnevne razgovore i standarde koje živite.</li><li>Najveća pogreška je pokušavati rasti mentalno, a ostati potpuno uronjen u okruženje koje stalno spušta energiju.</li><li>Pametniji pristup bira ljude, informacije i rutine koje hrane odgovornije razmišljanje.</li></ul>',
            $faq(
                'Zašto je okolina toliko važna za način razmišljanja?',
                'Zato što svakodnevno oblikuje ono što smatramo normalnim, mogućim i vrijednim truda.',
                'Može li se mindset mijenjati bez promjene okoline?',
                'Može djelomično, ali promjena ide sporije kada je okolina stalno sabotira.',
                'Što prvo treba promatrati?',
                'Ljude, navike, sadržaj i razgovore koji vas svakodnevno okružuju.',
                'Koja je česta pogreška?',
                'Podcijeniti utjecaj okoline i sav fokus staviti samo na “pozitivno razmišljanje”.'
            ),
            'Kako razmišljate i kako vas okolina oblikuje svaki dan',
            'Saznajte zašto način razmišljanja snažno ovisi o okolini i kako graditi zdraviji mentalni okvir.',
            'Mindset i okolina'
        ),
        $entry(
            39,
            'Prvi dojam: kako ostaviti dojam povjerenja bez glume, pretjerivanja i prazne samouvjerenosti',
            'Prvi dojam ne gradi se samo izgledom, nego načinom na koji slušate, govorite, ulazite u prostor i ostavljate osjećaj sigurnosti. Ovdje je kako taj dojam izgraditi prirodnije i zrelije.',
            '<ul><li>Dobar prvi dojam najčešće dolazi iz mira, jasnoće i poštovanja prema drugoj osobi.</li><li>Najveća pogreška je glumiti samopouzdanje umjesto pokazati stvarnu prisutnost i interes.</li><li>Pametniji pristup usklađuje govor tijela, ton glasa i jednostavnu profesionalnost.</li></ul>',
            $faq(
                'Što najviše utječe na prvi dojam?',
                'Najčešće kombinacija govora tijela, tona, urednosti i načina komunikacije.',
                'Treba li prvi dojam biti “savršen”?',
                'Ne. Važnije je djelovati pouzdano, smireno i autentično nego savršeno.',
                'Može li se prvi dojam popraviti?',
                'Može, ali je lakše od početka pokazati poštovanje, jasnoću i prisutnost.',
                'Koja je česta pogreška?',
                'Pokušavati impresionirati umjesto graditi osjećaj povjerenja i sigurnosti.'
            ),
            'Prvi dojam: kako ostaviti dojam povjerenja bez pretjerivanja',
            'Otkrijte kako ostaviti snažniji prvi dojam kroz autentičnost, govor tijela i jednostavnu profesionalnost.',
            'Prvi dojam'
        ),
        $entry(
            42,
            'Pozitivno razmišljanje bez naivnosti: kako graditi obrasce koji stvarno vode prema napretku',
            'Pozitivno razmišljanje vrijedi samo kada ne bježi od stvarnosti, odgovornosti i konkretne akcije. Ovdje je kako izgraditi obrasce razmišljanja koji stvarno pomažu, a ne samo dobro zvuče.',
            '<ul><li>Pozitivne misli imaju smisla kada vode boljoj akciji, a ne kada služe kao bijeg od problema.</li><li>Najveća pogreška je zamijeniti disciplinu i odgovornost praznim optimizmom.</li><li>Pametniji pristup spaja realnost, zahvalnost i konkretne dnevne korake.</li></ul>',
            $faq(
                'Što je zdravo pozitivno razmišljanje?',
                'To je način razmišljanja koji vidi problem, ali vjeruje da se na njemu može raditi.',
                'Zašto neki ljudi ne vole priču o pozitivnim mislima?',
                'Zato što se često pretvori u poricanje stvarnih problema i emocija.',
                'Kako ga učiniti korisnim?',
                'Povezati ga s odgovornošću, navikama i konkretnim djelovanjem.',
                'Koja je česta pogreška?',
                'Misliti da su dobre misli dovoljne bez promjene ponašanja.'
            ),
            'Pozitivno razmišljanje: kako graditi obrasce koji stvarno pomažu',
            'Saznajte kako pozitivno razmišljanje učiniti korisnim kroz realnost, odgovornost i konkretne korake.',
            'Pozitivno razmišljanje'
        ),
        $entry(
            55,
            'Kako postati Forever partner i graditi posao od kuće bez iluzije brze zarade',
            'Forever partnerstvo može postati ozbiljan poslovni kanal tek kada se temelji na sadržaju, podršci kupcima i ponovljivom sustavu rada. Ovdje je kako pristupiti poslu od kuće bez priče o lakoj zaradi preko noći.',
            '<ul><li>Posao od kuće s Foreverom najviše ovisi o povjerenju, jasnoći ponude i svakodnevnoj dosljednosti.</li><li>Najveća pogreška je ući u partnerstvo bez plana za sadržaj, preporuke i praćenje kupaca.</li><li>Pametniji pristup gradi publiku, korisnu podršku i jednostavan prodajni sustav.</li></ul>',
            $faq(
                'Može li se Forever partnerstvo graditi od kuće?',
                'Može, ali traži ozbiljan sustav rada, sadržaj i kontinuiranu podršku kupcima.',
                'Što je važnije od početnog entuzijazma?',
                'Ponovljive navike, povjerenje publike i jasan prodajni proces.',
                'Kako izgleda zdrav početak?',
                'Kroz učenje proizvoda, rad na sadržaju i izgradnju publike korak po korak.',
                'Koja je česta pogreška?',
                'Ući u posao očekujući brz prihod bez izgradnje temelja.'
            ),
            'Kako postati Forever partner i graditi posao od kuće realno',
            'Otkrijte kako Forever partnerstvo graditi kroz sadržaj, povjerenje i održiv posao od kuće.',
            'Forever partnerstvo'
        ),
        $entry(
            65,
            'Razumijevanje MLM marketinga: gdje ovaj model ima smisla, a gdje ljudi najčešće pogriješe',
            'MLM marketing ne treba ni romantizirati ni demonizirati. Najviše vrijedi kada ga promatramo kroz proizvod, etiku, podršku kupcu i sposobnost da se posao gradi dugoročno. Ovdje je kako ga razumjeti zrelije.',
            '<ul><li>MLM model ima smisla samo kada iza njega stoje stvarni proizvodi, podrška i etičan pristup prodaji.</li><li>Najveća pogreška je graditi posao isključivo na obećanju zarade umjesto na korisničkoj vrijednosti.</li><li>Pametniji pristup razlikuje održiv sustav preporuke od pritiska i nerealnih očekivanja.</li></ul>',
            $faq(
                'Što MLM čini održivijim modelom?',
                'Kvalitetan proizvod, korisna podrška kupcu i dugoročno povjerenje.',
                'Zašto ljudi često imaju loše iskustvo s MLM-om?',
                'Zato što se model ponekad vodi agresivno i bez stvarne korisničke vrijednosti.',
                'Kako ga gledati realnije?',
                'Kroz etiku, kvalitetu proizvoda i sustav koji nije temeljen samo na hypeu.',
                'Koja je česta pogreška?',
                'Prodavati ideju lake zarade umjesto stvarnog rješenja za kupca.'
            ),
            'MLM marketing: kako ga razumjeti bez iluzija i krajnosti',
            'Saznajte kako MLM marketing procijeniti kroz proizvode, etiku i održivost, a ne samo kroz priču o zaradi.',
            'Razumijevanje MLM-a'
        ),
        $entry(
            66,
            'Što je mrežni marketing: ključni koraci do uspjeha bez pritiska, glume i praznog hypea',
            'Mrežni marketing može raditi samo ako ga gradite kao sustav odnosa, preporuka i korisnog sadržaja. Ovdje je kako razumjeti njegove temelje i što stvarno vodi prema rezultatu.',
            '<ul><li>Mrežni marketing najviše ovisi o povjerenju, jasnoći poruke i dosljednom radu s ljudima.</li><li>Najveća pogreška je misliti da se posao može graditi samo na entuzijazmu i motivacijskim govorima.</li><li>Pametniji pristup stvara jednostavan proces preporuke, podrške i praćenja kupaca.</li></ul>',
            $faq(
                'Što je zapravo mrežni marketing?',
                'To je model prodaje i preporuke koji se oslanja na odnose, mrežu ljudi i dugoročnu podršku kupcu.',
                'Što najviše vodi prema uspjehu?',
                'Dosljednost, dobra komunikacija, povjerenje i jednostavan sustav rada.',
                'Može li uspjeti bez sadržaja i odnosa?',
                'Vrlo teško, jer ljudi najčešće kupuju kada postoji povjerenje i jasnoća.',
                'Koja je česta pogreška?',
                'Tražiti brze rezultate bez izgradnje publike i stvarne vrijednosti.'
            ),
            'Što je mrežni marketing i koji koraci stvarno vode do uspjeha',
            'Otkrijte što je mrežni marketing i kako ga graditi kroz odnose, sadržaj i održive dnevne aktivnosti.',
            'Mrežni marketing'
        ),
        $entry(
            67,
            'Kako uspjeti u životu: ciljevi, karakter i financijska sloboda bez lažnog instant recepta',
            'Uspjeh rijetko dolazi iz jedne velike odluke. Mnogo češće raste iz karaktera, navika i sposobnosti da dugo ostanete vjerni važnim prioritetima. Ovdje je kako tu priču postaviti zdravije i zrelije.',
            '<ul><li>Uspjeh se najčešće gradi kroz navike, odgovornost i dugoročno usklađivanje ciljeva s karakterom.</li><li>Najveća pogreška je tražiti instant recept za financijsku slobodu bez rada na sebi i sustavu.</li><li>Pametniji pristup spaja osobni rast, disciplinu i realan odnos prema vremenu.</li></ul>',
            $faq(
                'Što je temelj zdravog uspjeha?',
                'Jasni prioriteti, karakter, dosljednost i sposobnost da dugoročno ostanete u procesu.',
                'Je li financijska sloboda samo pitanje novca?',
                'Ne. Povezana je i s navikama, odlukama, vrijednostima i načinom rada.',
                'Zašto ljudi često stagniraju?',
                'Zato što traže brze pomake bez sustava, discipline i strpljenja.',
                'Koja je česta pogreška?',
                'Uspjeh svesti na motivaciju, a zanemariti rutinu i karakter.'
            ),
            'Kako uspjeti u životu bez traženja instant recepta za uspjeh',
            'Saznajte kako uspjeh graditi kroz ciljeve, karakter i dugoročne navike, a ne kroz brza obećanja.',
            'Kako uspjeti'
        ),
        $entry(
            88,
            'Kako ostati motiviran: zašto disciplina, smisao i okruženje pobjeđuju kratke nalete volje',
            'Motivacija je važna, ali rijetko traje dovoljno dugo da sama iznese ozbiljan cilj. Ovdje je kako ostati u pokretu kada početni zanos padne i kako graditi sustav koji vas nosi dalje.',
            '<ul><li>Motivacija je korisna za početak, ali dugoročni rezultat najviše ovisi o disciplini i jasnom smislu.</li><li>Najveća pogreška je oslanjati se samo na inspiraciju umjesto graditi rutinu koja radi i kada nije lako.</li><li>Pametniji pristup povezuje cilj, okruženje i jednostavne dnevne korake.</li></ul>',
            $faq(
                'Zašto motivacija tako brzo padne?',
                'Zato što emocija početka slabi, a sustav ostaje ili ne ostaje iza nje.',
                'Što pomaže kad nema volje?',
                'Jasna rutina, manji koraci i okruženje koje podržava cilj.',
                'Je li disciplina važnija od motivacije?',
                'Za dugoročni rezultat uglavnom jest, jer vas vodi i kad entuzijazam padne.',
                'Koja je česta pogreška?',
                'Čekati da se opet osjećate motivirano umjesto da napravite sljedeći mali korak.'
            ),
            'Kako ostati motiviran kad početni zanos nestane',
            'Otkrijte kako ostati motiviran kroz disciplinu, smisao i jednostavan sustav dnevnih koraka.',
            'Kako ostati motiviran'
        ),
        $entry(
            93,
            '14 znakova emocionalne inteligencije: kako prepoznati zreliji EQ u odnosima i radu',
            'Emocionalna inteligencija vidi se manje u velikim riječima, a više u načinu na koji slušate, reagirate pod pritiskom i nosite se s vlastitim emocijama. Ovdje je kako prepoznati snažniji EQ i zašto on toliko mijenja odnose.',
            '<ul><li>Emocionalna inteligencija najviše se vidi u samoregulaciji, empatiji i kvaliteti komunikacije.</li><li>Najveća pogreška je EQ svesti samo na “biti fin” bez granica i unutarnje zrelosti.</li><li>Pametniji pristup promatra kako osoba vodi sebe, konflikt i odnose kroz vrijeme.</li></ul>',
            $faq(
                'Što je emocionalna inteligencija u praksi?',
                'To je sposobnost da razumijete sebe, čitate druge i reagirate zrelije pod pritiskom.',
                'Zašto je važna u radu i odnosima?',
                'Jer utječe na komunikaciju, povjerenje, suradnju i kvalitetu odluka.',
                'Može li se razvijati?',
                'Može, kroz samopromatranje, povratnu informaciju i rad na reakcijama.',
                'Koja je česta pogreška?',
                'Brkati emocionalnu inteligenciju s ugađanjem svima ili potiskivanjem emocija.'
            ),
            '14 znakova emocionalne inteligencije koji grade bolji EQ',
            'Saznajte kako prepoznati emocionalnu inteligenciju u sebi i drugima te zašto EQ snažno utječe na odnose i rad.',
            'Emocionalna inteligencija'
        ),
    ],
];
