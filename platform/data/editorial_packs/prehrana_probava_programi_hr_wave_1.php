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
    'key' => 'prehrana-probava-programi-hr-wave-1',
    'name' => 'Prehrana, probava i programi (HR) - prvi val',
    'notes' => 'Ručni premium pack za planove prehrane, probavne teme, mikro-nutrijente i Clean 9 sadržaj.',
    'entries' => [
        $entry(
            566,
            'Medicinska dijeta: što realno očekivati od 15-dnevnog plana mršavljenja',
            'Medicinska dijeta privlači jer obećava brz pomak, ali kratki plan ima smisla samo ako ga ne pretvorite u još jedan ciklus restrikcije i povratka starim navikama. Ovdje je kako taj pristup procijeniti realnije i sigurnije.',
            '<ul><li>Kratki dijetni planovi imaju smisla samo kao privremeni okvir, ne kao dugoročna prehrambena filozofija.</li><li>Najveća pogreška je zamijeniti brz pad motivacijskim uzletom i zanemariti što slijedi nakon plana.</li><li>Pametniji pristup gleda održivost, energiju i povratak u normalniji ritam prehrane bez kaosa.</li></ul>',
            [
                ['question' => 'Zašto je medicinska dijeta toliko popularna?', 'answer' => 'Zato što obećava brz rezultat i vrlo jasan kratkoročni okvir.'],
                ['question' => 'Je li brz pad kilograma uvijek dobar znak?', 'answer' => 'Ne. Važno je kako se osjećate, što gubite i što se događa nakon završetka plana.'],
                ['question' => 'Kada takav plan ima više smisla?', 'answer' => 'Kad služi kao kratki reset uz realna očekivanja, a ne kao stalno ponavljana krajnost.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Vratiti se starim navikama odmah po završetku dijete i izgubiti sav osjećaj strukture.'],
            ],
            'Medicinska dijeta: kako procijeniti 15-dnevni plan bez brzih iluzija',
            'Saznajte kada medicinska dijeta može imati smisla i kako izbjeći najčešće greške kratkih planova mršavljenja.',
            'Medicinska dijeta'
        ),
        $entry(
            593,
            'Omega-3 masne kiseline: 7 koristi koje imaju smisla samo u pravom kontekstu',
            'Omega-3 su među najpoznatijim dodacima i upravo zato lako postanu preuveličano rješenje za sve. Ovdje je kako njihove koristi gledati realno, kroz prehranu, upalne procese i dugoročni ritam navika.',
            '<ul><li>Omega-3 najviše vrijede kao dio šire slike prehrane, srca i oporavka, a ne kao samostalan “super dodatak”.</li><li>Najveća pogreška je očekivati od jednog proizvoda učinak koji zapravo ovisi o ukupnoj kvaliteti života.</li><li>Pametniji pristup gleda dosljednost, prehrambene izvore i razlog zbog kojeg omega-3 uopće uvodite.</li></ul>',
            [
                ['question' => 'Za što ljudi najčešće uzimaju omega-3?', 'answer' => 'Najčešće za podršku srcu, mozgu i ravnoteži upalnih procesa.'],
                ['question' => 'Mogu li omega-3 zamijeniti lošu prehranu?', 'answer' => 'Ne. Imaju smisla kao dopuna, a ne kao rješenje za loše obrasce prehrane.'],
                ['question' => 'Kada imaju više smisla?', 'answer' => 'Kad postoji jasan cilj i kad su dio šireg plana prehrane i oporavka.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Kupiti omega-3 zato što su popularne, bez jasnog razloga ili plana korištenja.'],
            ],
            'Omega-3 koristi: kada stvarno imaju smisla i kako ih gledati realnije',
            'Razumijte 7 ključnih koristi omega-3 masnih kiselina i gdje one stvarno imaju smisla u svakodnevnoj rutini.',
            'Omega-3'
        ),
        $entry(
            594,
            'Vitamin D: zašto je važan i kako ga unositi bez pretjerivanja',
            'Vitamin D postao je gotovo sinonim za imunitet i dobro raspoloženje, ali prava vrijednost dolazi tek kad ga koristimo promišljeno. Ovdje je kako ga unositi kroz sunce, hranu i dodatke bez improvizacije i pretjerivanja.',
            '<ul><li>Vitamin D je važan za kosti, imunitet i širi osjećaj vitalnosti, ali ne treba svaka osoba isti pristup.</li><li>Najveća pogreška je uzimati ga po tuđem protokolu bez vlastitog konteksta i potrebe.</li><li>Pametniji pristup gleda izloženost suncu, prehranu i razumnu suplementaciju kroz dulje razdoblje.</li></ul>',
            [
                ['question' => 'Zašto je vitamin D toliko važan?', 'answer' => 'Jer sudjeluje u zdravlju kostiju, imunitetu i brojnim regulatornim procesima u tijelu.'],
                ['question' => 'Mora li ga svatko uzimati kao dodatak?', 'answer' => 'Ne uvijek. To ovisi o sezoni, izloženosti suncu, prehrani i individualnim okolnostima.'],
                ['question' => 'Kako ga je najbolje unositi?', 'answer' => 'Kombinacijom razumnog izlaganja suncu, prehrane i dodataka kada za to postoji smislen razlog.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Pretvoriti vitamin D u univerzalni odgovor bez provjere šire slike zdravlja i navika.'],
            ],
            'Vitamin D: kako ga unositi pametnije i bez nepotrebnog pretjerivanja',
            'Saznajte zašto je vitamin D važan i kako ga unositi kroz sunce, hranu i dodatke s više realizma.',
            'Vitamin D'
        ),
        $entry(
            606,
            'Proljetni detoks uz juhe, smoothieje i vježbu: što je korisno, a što je samo dojam',
            'Proljetni detoks zvuči privlačno jer obećava novi početak, ali stvarna korist dolazi samo kada taj reset ne postane još jedan kratki ekstrem. Ovdje je kako juhe, smoothieje i vježbu uklopiti bez dramatične detoks retorike.',
            '<ul><li>Proljetni reset najviše vrijedi kao povratak jednostavnijoj prehrani i kretanju, ne kao mitološko “čišćenje”.</li><li>Najveća pogreška je kratki nalet entuzijazma bez plana što slijedi nakon tog tjedna ili dva.</li><li>Pametniji pristup koristi sezonu za više reda, hidratacije i laganiji ritam obroka.</li></ul>',
            [
                ['question' => 'Mora li detoks biti ekstreman da bi imao učinka?', 'answer' => 'Ne. Često najviše znači upravo jednostavniji, mirniji i održiviji pristup.'],
                ['question' => 'Zašto su juhe i smoothieji popularni?', 'answer' => 'Jer olakšavaju unos tekućine, povrća i laganiji osjećaj obroka.'],
                ['question' => 'Gdje vježba ulazi u priču?', 'answer' => 'Kao dio općeg ritma i energije, ne kao kazna za prethodne navike.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Zamijeniti kratki reset stvarnom promjenom životnog ritma i prehrane.'],
            ],
            'Proljetni detoks: kako ga pretvoriti u laganiji reset, a ne kratki ekstrem',
            'Otkrijte kako proljetni detoks uz juhe, smoothieje i vježbu može imati smisla bez pretjeranih obećanja.',
            'Proljetni detoks'
        ),
        $entry(
            608,
            'C9 recepti: 5 brzih jela koja olakšavaju Clean 9 program',
            'Najveći problem na C9 planu često nije volja nego praktičnost. Ovdje je kako brza i jednostavna jela mogu pomoći da program bude lakši za pratiti i manje stresan za svakodnevicu.',
            '<ul><li>Na strukturiranom programu prehrane najviše pomažu jela koja su jednostavna, brza i predvidiva.</li><li>Najveća pogreška je ulaziti u C9 bez unaprijed pripremljenog plana obroka i osnovnih namirnica.</li><li>Pametniji pristup uklanja višak odluka i hranu pretvara u podršku, a ne dodatni stres.</li></ul>',
            [
                ['question' => 'Zašto su recepti važni na C9 programu?', 'answer' => 'Zato što jednostavni obroci olakšavaju dosljednost i smanjuju svakodnevnu improvizaciju.'],
                ['question' => 'Moraju li jela biti komplicirana?', 'answer' => 'Ne. Što su jasnija i brža, to je program obično lakše pratiti.'],
                ['question' => 'Što je ključno prije početka?', 'answer' => 'Osnovna kupnja, plan i nekoliko sigurnih jela koja se lako ponavljaju.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Ostaviti hranu slučaju pa tijekom programa stalno donositi odluke pod stresom.'],
            ],
            'C9 recepti: 5 praktičnih jela za lakši i održiviji Clean 9 plan',
            'Saznajte kako 5 brzih C9 recepata može olakšati Clean 9 program i smanjiti prehrambeni stres.',
            'C9 recepti'
        ),
        $entry(
            610,
            'Kuhanje na niskim temperaturama: kako bolje očuvati nutrijente u obroku',
            'Način pripreme hrane često znači jednako puno kao i sam izbor namirnica. Ovdje je kako kuhanje na nižim temperaturama može pomoći očuvanju okusa, teksture i dijela osjetljivijih nutrijenata bez kulinarskog kompliciranja.',
            '<ul><li>Niže temperature često pomažu očuvati teksturu, vlagu i dio osjetljivijih spojeva u hrani.</li><li>Najveća pogreška je misliti da je “zdravije” kuhanje nužno kompliciranije ili sporije za svakodnevicu.</li><li>Pametniji pristup gleda ravnotežu između praktičnosti, okusa i načina pripreme namirnica.</li></ul>',
            [
                ['question' => 'Što znači kuhati na niskim temperaturama?', 'answer' => 'To znači pripremati hranu nježnijom toplinom kako bi se bolje sačuvali okus i tekstura.'],
                ['question' => 'Jesu li svi nutrijenti osjetljivi na toplinu?', 'answer' => 'Ne jednako, ali neki spojevi i vitamini osjetljiviji su od drugih.'],
                ['question' => 'Mora li takvo kuhanje biti komplicirano?', 'answer' => 'Ne. I jednostavnije metode mogu biti nježnije prema hrani.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Pretvoriti zdraviju pripremu hrane u nerealno zahtjevan kuharski projekt.'],
            ],
            'Kuhanje na nižim temperaturama: kako sačuvati više okusa i nutrijenata',
            'Otkrijte kako kuhanje na niskim temperaturama može pomoći očuvati kvalitetu obroka bez kompliciranja.',
            'Kuhanje na niskim temperaturama'
        ),
        $entry(
            613,
            'Ajurvedski pristup prehrani: doše, godišnja doba i kako ga primijeniti bez mistike',
            'Ajurveda mnogima zvuči zanimljivo jer hranu povezuje s ritmom tijela i sezone, ali u praksi vrijedi samo ono što se može stvarno živjeti. Ovdje je kako taj pristup prevesti u korisne, a ne maglovite svakodnevne izbore.',
            '<ul><li>Ajurvedski pristup može biti koristan kada služi kao okvir za više pažnje prema tijelu i ritmu dana.</li><li>Najveća pogreška je pretvoriti ga u rigidna pravila ili mističnu priču bez praktične koristi.</li><li>Pametniji pristup uzima ono što je primjenjivo: sezonu, probavu, toplinu obroka i osobni osjećaj ravnoteže.</li></ul>',
            [
                ['question' => 'Što su doše u praksi?', 'answer' => 'One služe kao tradicionalni okvir za opis različitih obrazaca tijela, energije i probave.'],
                ['question' => 'Mora li se sve pratiti doslovno?', 'answer' => 'Ne. Najviše smisla ima uzeti ono što je korisno i primjenjivo u svakodnevici.'],
                ['question' => 'Zašto se spominju godišnja doba?', 'answer' => 'Jer sezona može utjecati na izbor hrane, ritam obroka i osjećaj u tijelu.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Zamijeniti praktičnu prehrambenu pažnju za strogu filozofiju koju je teško živjeti.'],
            ],
            'Ajurvedska prehrana: kako uzeti korisno iz doša i sezone bez pretjerivanja',
            'Saznajte kako ajurvedski pristup prehrani primijeniti praktično, kroz doše, sezonu i bolji osjećaj ravnoteže.',
            'Ajurvedska prehrana'
        ),
        $entry(
            632,
            'Alergije na hranu: kako se snaći u planu bez mlijeka, glutena i kikirikija',
            'Plan prehrane bez mlijeka, glutena ili kikirikija može brzo postati izvor stresa ako nema jasnu strukturu. Ovdje je kako pristupiti alergijama na hranu s više organizacije, sigurnosti i manje prehrambene panike.',
            '<ul><li>Kod alergija na hranu najvažniji su sigurnost, jasnoća i dobra organizacija obroka.</li><li>Najveća pogreška je panično izbacivati sve redom bez jasnog razumijevanja što je stvarni problem.</li><li>Pametniji pristup gradi plan koji je siguran, hranjiv i održiv za svakodnevni život.</li></ul>',
            [
                ['question' => 'Zašto je plan bez mlijeka, glutena ili kikirikija toliko zahtjevan?', 'answer' => 'Zato što traži pažljivije čitanje deklaracija i bolju organizaciju obroka.'],
                ['question' => 'Treba li izbaciti više namirnica odjednom?', 'answer' => 'Samo kada za to postoji jasan razlog i struktura koja ostaje nutritivno smislenija.'],
                ['question' => 'Što najviše pomaže?', 'answer' => 'Planiranje, sigurni obroci i jasne zamjene koje ne stvaraju dodatni kaos.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Pretvoriti eliminacijsku prehranu u preširoku zabranu bez održivog plana.'],
            ],
            'Alergije na hranu: kako složiti sigurniji plan bez mlijeka, glutena i kikirikija',
            'Otkrijte kako prehranu kod alergija na hranu organizirati sigurnije i održivije, bez nepotrebne panike.',
            'Alergije na hranu'
        ),
        $entry(
            636,
            'Autoimuna upala crijeva: probiotici i prehrana za Crohn i kolitis kroz realniji okvir',
            'Kod Crohna i kolitisa hrana je važna, ali nije čarobni prekidač koji sam rješava kompleksno stanje. Ovdje je kako probiotike i prehranu promatrati kao podršku svakodnevici, a ne kao pojednostavljeno obećanje.',
            '<ul><li>Prehrana i probiotici imaju smisla kao podrška kvaliteti života, probavi i ritmu simptoma.</li><li>Najveća pogreška je tražiti jednu savršenu dijetu koja mora odgovarati svima s istom dijagnozom.</li><li>Pametniji pristup gleda individualnu toleranciju, fazu simptoma i postupne promjene.</li></ul>',
            [
                ['question' => 'Mogu li probiotici pomoći kod Crohna ili kolitisa?', 'answer' => 'Mogu biti dio podrške, ali ne djeluju jednako u svakoj fazi ili kod svake osobe.'],
                ['question' => 'Zašto prehrana nije ista za sve?', 'answer' => 'Zato što tolerancija hrane i težina simptoma mogu biti vrlo različiti.'],
                ['question' => 'Što je najkorisnije u praksi?', 'answer' => 'Promatrati obrasce, voditi bilješke i uvoditi promjene postupno i promišljeno.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Kopirati tuđi plan prehrane bez procjene vlastitih reakcija i ograničenja.'],
            ],
            'Crohn i kolitis: kako prehrana i probiotici mogu biti podrška bez iluzija',
            'Saznajte kako prehranu i probiotike kod Crohna i kolitisa koristiti kao realnu podršku, a ne kao pojednostavljeno rješenje.',
            'Crohn i kolitis'
        ),
        $entry(
            813,
            'Clean 9 i zdravstveni problemi: smijete li krenuti i kako pametno prilagoditi plan',
            'Clean 9 nije program koji treba ulaziti napamet, osobito kada već postoje zdravstvene poteškoće ili osjetljiviji organizam. Ovdje je kako procijeniti ima li program smisla i što znači pametna prilagodba umjesto tvrdog forsiranja plana.',
            '<ul><li>Strukturirani programi imaju smisla tek kad odgovaraju stvarnom zdravstvenom kontekstu osobe koja ih koristi.</li><li>Najveća pogreška je ulaziti u Clean 9 s logikom “samo da izdržim”, bez procjene tijela i okolnosti.</li><li>Pametniji pristup gleda sigurnost, prilagodbu i pitanje treba li program uopće biti sadašnji izbor.</li></ul>',
            [
                ['question' => 'Može li svatko krenuti na Clean 9?', 'answer' => 'Ne nužno. Kod zdravstvenih problema važno je prvo procijeniti odgovara li takav okvir uopće osobi.'],
                ['question' => 'Što znači prilagoditi plan?', 'answer' => 'To znači smanjiti rigidnost i uzeti u obzir stvarno stanje organizma, obveze i toleranciju.'],
                ['question' => 'Kada program nema smisla?', 'answer' => 'Kad bi stvorio više opterećenja nego koristi ili kada sigurnost nije dovoljno jasna.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Gledati program kao test discipline umjesto kao alat koji mora služiti stvarnoj osobi.'],
            ],
            'Clean 9 i zdravlje: kada program ima smisla, a kada treba stati i prilagoditi',
            'Razumijte kada Clean 9 ima smisla kod zdravstvenih problema i što znači sigurnija prilagodba plana.',
            'Clean 9 i zdravlje'
        ),
    ],
];
