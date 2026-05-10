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
    'key' => 'simptomi-imunitet-wellness-hr-wave-1',
    'name' => 'Simptomi, imunitet i wellness (HR) - prvi val',
    'notes' => 'Veći ručni premium editorial pack za probavne i respiratorne tegobe, kronične simptome, imunitet i praktične wellness teme.',
    'entries' => [
        $entry(
            493,
            'Kako probiotici i zdrave dijete mogu ići ruku pod ruku bez prehrambenog fanatizma',
            'Probiotici i prehrana često se predstavljaju kao dva odvojena svijeta, a zapravo imaju najviše smisla kad rade zajedno. Ovdje je kako povezati dodatke i prehrambene obrasce tako da podupru crijeva bez ulaska u rigidna pravila i stalne zabrane.',
            '<ul><li>Probiotici najbolje djeluju kad ih prati prehrana koja crijevima daje manje stresa i više stabilnosti.</li><li>Najveća pogreška je uzimati probiotik dok prehrana i ritam obroka ostaju potpuno kaotični.</li><li>Pametniji pristup gleda cjelinu: što jedete, kako jedete i gdje dodatak može imati smisleno mjesto.</li></ul>',
            [
                ['question' => 'Trebaju li probiotici svima?', 'answer' => 'Ne nužno. Njihova korist ovisi o simptomima, prehrani i stvarnom cilju podrške probavi.'],
                ['question' => 'Može li dobra prehrana biti dovoljna bez dodatka?', 'answer' => 'Ponekad da, osobito ako je probava stabilna i obrazac hrane kvalitetan i raznolik.'],
                ['question' => 'Kada dodatak ima više smisla?', 'answer' => 'Kad postoji jasan razlog, poput oporavka probave ili potrebe za dodatnom strukturom unutar šireg plana.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Tražiti od kapsule ono što zapravo treba riješiti kroz obroke i životni ritam.'],
            ],
            'Probiotici i zdrava prehrana: kako ih povezati da stvarno pomažu crijevima',
            'Saznajte kako probiotike i prehranu spojiti u smislen plan za probavu bez rigidnih pravila i fanatizma.',
            'Probiotici i dijeta'
        ),
        $entry(
            519,
            'Celijakija simptomi kod odraslih: kako prepoznati rane znakove bez samodijagnoze',
            'Celijakija kod odraslih ne izgleda uvijek dramatično niti se uvijek javlja samo kroz očite probavne simptome. Ovdje je kako prepoznati rane obrasce, što ne treba ignorirati i zašto brzopleta samodijagnoza često više šteti nego pomaže.',
            '<ul><li>Celijakija može imati širu sliku simptoma od same nadutosti i proljeva, uključujući umor i nutritivne manjkove.</li><li>Najveća pogreška je samostalno izbaciti gluten prije nego što je napravljena odgovarajuća obrada.</li><li>Pametniji pristup prepoznaje obrasce, ali dijagnozu prepušta ispravnom medicinskom procesu.</li></ul>',
            [
                ['question' => 'Jesu li simptomi celijakije uvijek probavni?', 'answer' => 'Ne. Kod odraslih se mogu pojaviti i kroz umor, manjkove, promjene kože ili opći osjećaj slabosti.'],
                ['question' => 'Zašto ne treba odmah sam izbaciti gluten?', 'answer' => 'Jer to može otežati ili zamagliti kasniju pravilnu dijagnostiku.'],
                ['question' => 'Kada posumnjati na celijakiju?', 'answer' => 'Kad se više simptoma ponavlja i kad postoji obrazac koji upućuje na osjetljivost ili malapsorpciju.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Poistovjetiti svaku probavnu tegobu s glutenom bez dublje procjene uzroka.'],
            ],
            'Celijakija kod odraslih: rani simptomi, obrasci i zašto ne žuriti sa samodijagnozom',
            'Prepoznajte moguće simptome celijakije kod odraslih i saznajte zašto je pravilna dijagnostika važnija od nagađanja.',
            'Celijakija simptomi'
        ),
        $entry(
            556,
            'Uporan kašalj: kako ga smiriti kod odraslih i olakšati nadraženo grlo',
            'Uporan kašalj iscrpljuje i tijelo i živce, osobito kad remeti san i traje dulje nego što ste očekivali. Ovdje je kako mu pristupiti pametnije kroz grlo, vlagu, ritam dana i razlikovanje prolazne iritacije od nečega što traži više pažnje.',
            '<ul><li>Uporan kašalj treba promatrati kroz trajanje, jačinu, dodatne simptome i što ga najviše pogoršava.</li><li>Najveća pogreška je tjednima ga tretirati isto bez obzira na to mijenja li se ili ne prolazi.</li><li>Pametniji pristup smiruje grlo, čuva san i prati signalizira li tijelo potrebu za širim pregledom.</li></ul>',
            [
                ['question' => 'Zašto kašalj nekad traje dugo?', 'answer' => 'Jer se grlo i dišni putovi mogu dulje oporavljati ili postoji obrazac iritacije koji se ne prekida.'],
                ['question' => 'Što najviše smeta odraslima kod upornog kašlja?', 'answer' => 'Najčešće noćno buđenje, škakljanje u grlu i osjećaj da se simptom stalno vraća.'],
                ['question' => 'Kada treba više opreza?', 'answer' => 'Ako kašalj traje neuobičajeno dugo, pogoršava se ili dolazi s težim simptomima.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Mijenjati sredstva bez razumijevanja okidača i uvjeta koji kašalj održavaju.'],
            ],
            'Uporan kašalj kod odraslih: kako ga smiriti i kada promatrati širu sliku',
            'Saznajte kako pristupiti upornom kašlju kod odraslih kroz mirniju rutinu, njegu grla i više opreza kad treba.',
            'Uporan kašalj'
        ),
        $entry(
            557,
            'Čaj protiv kašlja: koje biljne mješavine stvarno imaju smisla za brzo olakšanje',
            'Biljni čaj protiv kašlja može biti koristan, ali najviše kad znate što zapravo želite smiriti: suhoću, iritaciju, osjećaj peckanja ili potrebu za toplinom prije spavanja. Ovdje je kako čaj koristiti razumnije, a ne samo po principu “što god je prirodno”.',
            '<ul><li>Čaj protiv kašlja ima najviše smisla kao umirujući ritual koji pomaže grlu i osjećaju ugode.</li><li>Najveća pogreška je očekivati da jedna biljna mješavina riješi sve tipove kašlja i sve uzroke.</li><li>Pametniji pristup bira čaj prema osjećaju grla, vremenu dana i ukupnoj slici simptoma.</li></ul>',
            [
                ['question' => 'Može li čaj stvarno pomoći kašlju?', 'answer' => 'Može pomoći osjećaju smirenja i ugode, osobito kad je grlo nadraženo ili suho.'],
                ['question' => 'Je li svaki biljni čaj jednako koristan?', 'answer' => 'Nije. Smisao ovisi o vrsti bilja, podnošenju i tome što zapravo pokušavate smiriti.'],
                ['question' => 'Kada je čaj najkorisniji?', 'answer' => 'Često navečer ili u trenucima kad želite smanjiti iritaciju i zaštititi san.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Koristiti čaj kao jedinu pomoć kad simptom traje dugo ili postaje jači.'],
            ],
            'Čaj protiv kašlja: kako odabrati biljnu mješavinu koja stvarno pomaže grlu',
            'Otkrijte kada čaj protiv kašlja ima smisla i kako ga koristiti za više ugode i manje iritacije grla.',
            'Čaj protiv kašlja'
        ),
        $entry(
            558,
            'Psorijaza: prirodne strategije, okidači i kako koži dati više mira',
            'Psorijaza traži strpljenje i širu sliku, jer koža često reagira na stres, rutinu, iritaciju i opće stanje organizma. Ovdje je kako o prirodnim strategijama razmišljati realno, bez lažnih nada i bez agresivnog isprobavanja svega odjednom.',
            '<ul><li>Psorijaza je kronično stanje i traži više kontinuiteta nego “čarobnih” kratkih intervencija.</li><li>Najveća pogreška je prečesto mijenjati proizvode i istodobno ne promatrati stres, okidače i navike.</li><li>Pametniji pristup gradi nježniju rutinu, manje iritansa i bolji uvid u obrasce pogoršanja.</li></ul>',
            [
                ['question' => 'Mogu li prirodne strategije pomoći?', 'answer' => 'Mogu kao dio šire rutine smirivanja kože i okidača, ali ne kroz nerealno obećanje brzog nestanka problema.'],
                ['question' => 'Zašto je stres važan kod psorijaze?', 'answer' => 'Jer mnogima upravo stres pojačava upalne obrasce i reakciju kože.'],
                ['question' => 'Što koža najčešće traži?', 'answer' => 'Mirniju, dosljedniju i manje agresivnu njegu, uz pažnju na okidače i opće zdravlje.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Mijenjati deset stvari odjednom pa ne znati što zapravo pomaže, a što iritira.'],
            ],
            'Psorijaza: prirodna podrška, okidači i mirnija rutina za osjetljivu kožu',
            'Saznajte kako psorijazi pristupiti kroz prirodniju podršku, manje okidača i mirniju rutinu kože.',
            'Psorijaza'
        ),
        $entry(
            562,
            'Prirodno ublažavanje teških nogu: hidromasaže, aloe krema i lakši dan',
            'Teške noge često su rezultat ritma koji uključuje dugo stajanje, sjedenje, toplinu i osjećaj zastoja u cirkulaciji. Ovdje je kako kroz jednostavne rituale poput tuširanja, masaže i njege nogama vratiti malo više lakoće.',
            '<ul><li>Teške noge često najbolje reagiraju na male fizičke rituale koji pomažu osjećaju cirkulacije i olakšanja.</li><li>Najveća pogreška je ignorirati simptom dok ne postane svakodnevno frustrirajući ili bolniji.</li><li>Pametniji pristup koristi redovitost: rashlađivanje, kretanje, pauze i praktičnu njegu kože i tkiva.</li></ul>',
            [
                ['question' => 'Zašto noge postanu teške?', 'answer' => 'Zbog dugog sjedenja, stajanja, topline, manjka kretanja i opterećenja koje se nakuplja tijekom dana.'],
                ['question' => 'Mogu li masaže i tuširanje pomoći?', 'answer' => 'Da, često mogu donijeti osjećaj olakšanja i svježine ako se koriste redovito.'],
                ['question' => 'Gdje aloe krema ima smisla?', 'answer' => 'Kao dio rituala njege i osjećaja hlađenja ili ugode nakon napornog dana.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Čekati da neugoda bude jaka umjesto uvoditi male dnevne preventivne navike.'],
            ],
            'Teške noge: kako ih prirodnije olakšati kroz hlađenje, kretanje i njegu',
            'Otkrijte kako teške noge ublažiti uz hidromasažu, aloe kremu i male navike koje vraćaju lakoću.',
            'Teške noge'
        ),
        $entry(
            567,
            'Parkinson i prehrana: antioksidansi, obroci i podrška svakodnevnom funkcioniranju',
            'Kod Parkinsonove bolesti prehrana ne rješava sve, ali može imati važnu ulogu u energiji, probavi i općem osjećaju dnevne podnošljivosti. Ovdje je kako o antioksidansima i obrocima razmišljati mirnije i praktičnije.',
            '<ul><li>Prehrana kod Parkinsona ima smisla kao podrška kvaliteti svakodnevice, a ne kao obećanje da može zamijeniti terapijski okvir.</li><li>Najveća pogreška je tražiti “čudotvorni” nutrijent umjesto gledati cjelinu obroka, probave i energije.</li><li>Pametniji pristup koristi prehranu za više stabilnosti, lakšu probavu i bolju rutinu kroz dan.</li></ul>',
            [
                ['question' => 'Imaju li antioksidansi smisla?', 'answer' => 'Mogu imati kao dio šire prehrambene slike, ali ne kao jedini odgovor na kompleksno stanje.'],
                ['question' => 'Zašto je prehrana važna kod Parkinsona?', 'answer' => 'Jer može utjecati na energiju, probavu, raspoloženje i ukupnu dnevnu podnošljivost.'],
                ['question' => 'Treba li očekivati čudo od prehrane?', 'answer' => 'Ne. Najviše smisla ima gledati prehranu kao podršku, ne kao zamjenu za liječenje.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Usmjeriti se na jednu “supernamirnicu” i zanemariti ukupnu strukturu obroka i navika.'],
            ],
            'Parkinson i prehrana: gdje antioksidansi pomažu, a gdje treba ostati realan',
            'Saznajte kako prehrana može podržati svakodnevno funkcioniranje kod Parkinsonove bolesti bez nerealnih obećanja.',
            'Parkinson i prehrana'
        ),
        $entry(
            572,
            'Reishi i shiitake: kako ljekovite gljive mogu podržati imunitet i vitalnost',
            'Ljekovite gljive zvuče fascinantno i zato su posebno podložne pretjeranim tvrdnjama. Ovdje je kako reishi i shiitake promatrati kroz imunitet, prehranu i održivu podršku, a ne kroz ideju da jedna namirnica nosi cijeli zdravstveni sustav.',
            '<ul><li>Reishi i shiitake mogu biti zanimljiv dio šire rutine podrške imunitetu i vitalnosti.</li><li>Najveća pogreška je od gljiva očekivati učinak koji ovisi o snu, prehrani i općem zdravstvenom ritmu.</li><li>Pametniji pristup koristi ih kao dodatak raznolikoj prehrani i mirnijoj rutini, ne kao čudo u prahu ili kapsuli.</li></ul>',
            [
                ['question' => 'Zašto su reishi i shiitake toliko popularni?', 'answer' => 'Zbog tradicije, interesa za njihove spojeve i ideje da mogu podržati imunitet i opću vitalnost.'],
                ['question' => 'Mogu li zamijeniti zdravu prehranu?', 'answer' => 'Ne. Najviše smisla imaju kao dio šire i raznolike prehrambene rutine.'],
                ['question' => 'Kada imaju više smisla?', 'answer' => 'Kad tražite blagu, dugoročniju podršku navikama, a ne spektakularan brzi učinak.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Kupiti gljive zbog hypea bez ikakve ideje kako se uklapaju u širu rutinu.'],
            ],
            'Reishi i shiitake: gdje imaju smisla za imunitet, a gdje počinje pretjerivanje',
            'Razumijte kako reishi i shiitake mogu biti dio podrške imunitetu i vitalnosti bez nerealnih očekivanja.',
            'Reishi i shiitake'
        ),
        $entry(
            577,
            'Sezona gripe: kako poduprijeti imunitet cinkom, vitaminom D i probioticima',
            'Sezona gripe uvijek vraća ista pitanja o tome što vrijedi uzeti i kako se tijelo najbolje priprema za veći kontakt s virusima i stresom. Ovdje je kako cink, vitamin D i probiotike gledati trezveno, bez paničnog gomilanja dodataka.',
            '<ul><li>Cink, vitamin D i probiotici mogu imati mjesto u sezonskoj rutini, ali tek uz dobar san, hranu i osnovnu higijenu.</li><li>Najveća pogreška je očekivati da će tri proizvoda sama nositi obranu tijela kroz napornu sezonu.</li><li>Pametniji pristup koristi dodatke kao podršku već uređenoj osnovi, a ne kao zamjenu za nju.</li></ul>',
            [
                ['question' => 'Imaju li cink i vitamin D smisla u sezoni gripe?', 'answer' => 'Mogu imati, posebno ako postoji stvarna potreba ili sezonski kontekst koji to opravdava.'],
                ['question' => 'Zašto se spominju i probiotici?', 'answer' => 'Zato što se često promatraju kao dio šire priče o otpornosti organizma i stanju crijeva.'],
                ['question' => 'Treba li sve uzimati naslijepo?', 'answer' => 'Ne. Najviše smisla ima razmotriti potrebe, prehranu i ritam prije kupnje dodataka.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Kupiti više dodataka iz panike, bez da su osnovne navike uopće sređene.'],
            ],
            'Sezona gripe: cink, vitamin D i probiotici u realnoj rutini za imunitet',
            'Saznajte kada cink, vitamin D i probiotici imaju smisla za sezonu gripe i gdje ipak vode osnovne navike.',
            'Sezona gripe'
        ),
        $entry(
            579,
            'Pokrenuti tijelo nakon pauze: siguran plan povratka aktivnosti bez pretjerivanja',
            'Nakon dulje pauze tijelo ne treba kaznu nego pametan povratak koji gradi sigurnost, kondiciju i osjećaj povjerenja u vlastiti pokret. Ovdje je kako se vratiti aktivnosti bez da prvi tjedan uništi motivaciju za dalje.',
            '<ul><li>Povratak aktivnosti najbolje uspijeva kad je dovoljno blag da ne slomi volju i tijelo već u prvim danima.</li><li>Najveća pogreška je pokušati “nadoknaditi izgubljeno vrijeme” kroz prevelik intenzitet odmah na početku.</li><li>Pametniji pristup gradi rutinu kroz hodanje, osnovne pokrete i postupno povećanje opterećenja.</li></ul>',
            [
                ['question' => 'Kako krenuti nakon duže pauze?', 'answer' => 'Najčešće kroz laganije oblike kretanja, kraće treninge i više pažnje na oporavak.'],
                ['question' => 'Zašto ljudi brzo odustanu?', 'answer' => 'Jer krenu prejako pa se pojave bolovi, iscrpljenost i osjećaj da je povratak pretežak.'],
                ['question' => 'Koliko brzo dizati intenzitet?', 'answer' => 'Postupno, tako da tijelo stigne prihvatiti novi ritam bez preopterećenja.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Uspoređivati se s verzijom sebe iz prošlosti i forsirati razinu na koju tijelo još nije spremno.'],
            ],
            'Povratak aktivnosti nakon pauze: kako krenuti sigurno i bez gubitka motivacije',
            'Otkrijte kako nakon pauze ponovno pokrenuti tijelo kroz siguran i održiv plan povratka aktivnosti.',
            'Povratak aktivnosti'
        ),
    ],
];
