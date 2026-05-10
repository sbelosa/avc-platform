<?php

declare(strict_types=1);

$entry = static fn (
    int $sourceId,
    string $title,
    string $slug,
    string $excerpt,
    string $summaryHtml,
    array $faqItems,
    string $metaTitle,
    string $metaDescription,
    string $breadcrumbTitle,
    array $sections
): array => [
    'source_translation_id' => $sourceId,
    'language_code' => 'sl',
    'title' => $title,
    'slug' => $slug,
    'excerpt' => $excerpt,
    'summary_html' => $summaryHtml,
    'faq_items' => $faqItems,
    'meta_title' => $metaTitle,
    'meta_description' => $metaDescription,
    'breadcrumb_title' => $breadcrumbTitle,
    'sections' => $sections,
];

return [
    'key' => 'simptomi-imunitet-wellness-sl-wave-1',
    'name' => 'Simptomi, imunost in wellness (SL) - prvi val',
    'notes' => 'Večji lokalizirani uredniški val za prebavne in respiratorne težave, kronične simptome, imunost in praktične wellness rutine.',
    'entries' => [
        $entry(
            493,
            'Kako lahko probiotiki in zdrava prehrana sodelujejo brez prehranskega fanatizma',
            'kako-lahko-probiotiki-in-zdrava-prehrana-sodelujejo',
            'Probiotiki in prehrana se pogosto obravnavajo kot dva ločena sveta, v resnici pa najbolje delujejo skupaj. Tukaj je, kako dodatke in prehranske vzorce povezati tako, da res podprejo črevesje brez rigidnih pravil in pretirane strogosti.',
            '<ul><li>Probiotiki najbolje delujejo, ko jih spremlja prehrana, ki črevesju prinaša manj stresa in več stabilnosti.</li><li>Največja napaka je jemati probiotik, medtem ko obroki in prebavni sprožilci ostajajo popolnoma kaotični.</li><li>Pametnejši pristop gleda celoto: kaj jeste, kako jeste in kje ima dodatek res smiselno mesto.</li></ul>',
            [
                ['question' => 'Ali probiotiki koristijo vsem?', 'answer' => 'Ne samodejno. Njihova vrednost je odvisna od simptomov, prehrane in dejanskega cilja podpore prebavi.'],
                ['question' => 'Ali je lahko dobra prehrana dovolj brez dodatka?', 'answer' => 'Včasih da, posebej ko je prebava stabilna in je prehranski vzorec že kakovosten in raznolik.'],
                ['question' => 'Kdaj dodatek bolj ustreza?', 'answer' => 'Ko obstaja jasen razlog, na primer podpora okrevanju prebave ali večja potreba po strukturi.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Od kapsule pričakovati delo, ki ga morajo še vedno opraviti obroki in življenjski ritem.'],
            ],
            'Probiotiki in zdrava prehrana: kako jih povezati, da res podprejo črevesje',
            'Spoznajte, kako probiotike in prehrano združiti v bolj smiseln načrt podpore prebavi brez rigidnih pravil.',
            'Probiotiki in prehrana',
            [
                ['heading' => 'Zakaj črevesju najbolj pomaga kombiniran pristop', 'html' => '<p>Dodatki in prehranske navade se pogosto predstavljajo ločeno, vendar ju črevesje doživlja skupaj. Prav zato se največji napredek običajno zgodi takrat, ko se bolj uskladita namesto da delujeta vsak zase.</p>'],
                ['heading' => 'Zakaj širša prehranska struktura vodi glavno zgodbo', 'html' => '<p>Dopolnilo lahko pomaga, vendar dnevni vzorec obrokov in tolerance hrane običajno oblikuje prebavo veliko močneje. Boljši ritem zato probiotiku da bolj jasno in koristno vlogo.</p>'],
            ]
        ),
        $entry(
            519,
            'Simptomi celiakije pri odraslih: kako prepoznati zgodnje znake brez samodiagnoze',
            'simptomi-celiakije-pri-odraslih-kako-prepoznati-zgodnje-znake',
            'Celiakija pri odraslih ne izgleda vedno dramatično in se ne pokaže nujno samo skozi prebavne težave. Tukaj je, kako prepoznati zgodnje vzorce, česa ne prezreti in zakaj prehitra samodiagnoza pogosto prinese več zmede kot jasnosti.',
            '<ul><li>Celiakija pri odraslih se lahko pokaže tudi skozi utrujenost, pomanjkanja in širši občutek slabšega počutja.</li><li>Največja napaka je izločiti gluten, preden je diagnostični postopek pravilno zaključen.</li><li>Pametnejši pristop opazi vzorce, diagnozo pa prepusti pravemu medicinskemu postopku.</li></ul>',
            [
                ['question' => 'Ali so simptomi celiakije vedno prebavni?', 'answer' => 'Ne. Pri odraslih se lahko kažejo tudi skozi utrujenost, pomanjkanja, kožne težave ali širšo slabšo kondicijo.'],
                ['question' => 'Zakaj glutena ne odstranimo prezgodaj?', 'answer' => 'Ker lahko to oteži ali zamegli pravilno kasnejšo diagnostiko.'],
                ['question' => 'Kdaj je sum na celiakijo bolj upravičen?', 'answer' => 'Ko se ponavlja več simptomov in vzorec kaže na slabo toleranco ali slabšo absorpcijo.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Vsako prebavno težavo takoj povezati z glutenom brez globlje ocene.'],
            ],
            'Celiakija pri odraslih: zgodnji znaki in zakaj samodiagnoza lahko zmede',
            'Prepoznajte možne zgodnje vzorce celiakije pri odraslih in zakaj je pravilna diagnostika pomembnejša od ugibanja.',
            'Simptomi celiakije',
            [
                ['heading' => 'Zakaj so vzorci celiakije pri odraslih pogosto manj očitni', 'html' => '<p>Mnogi ljudje pričakujemo samo eno jasno prebavno sliko, vendar se celiakija pri odraslih pogosto pokaže bolj subtilno. Prav zato je pomembno več pozornosti nameniti vzorcem, preden začnemo sklepati prehitro.</p>'],
                ['heading' => 'Zakaj diagnoza potrebuje pravi vrstni red', 'html' => '<p>Prehitro “reševanje” težave na lastno roko pogosto oteži pot do pravega odgovora. Jasnejše odločitve običajno pridejo, ko spoštujemo diagnostični proces namesto improvizacije.</p>'],
            ]
        ),
        $entry(
            556,
            'Vztrajen kašelj: kako ga pri odraslih umiriti in olajšati razdraženo grlo',
            'vztrajen-kaselj-kako-ga-pri-odraslih-umiriti',
            'Vztrajen kašelj izčrpa telo in živce, posebej kadar moti spanec in traja dlje, kot smo pričakovali. Tukaj je, kako k njemu pristopiti bolj pametno skozi nego grla, vlago, ritem dneva in boljše razumevanje vzorca.',
            '<ul><li>Vztrajen kašelj je smiselno gledati skozi trajanje, moč, spremljajoče simptome in sprožilce, ki ga ohranjajo.</li><li>Največja napaka je tedne uporabljati enak pristop brez opazovanja, ali se vzorec spreminja.</li><li>Pametnejši pristop pomiri grlo, zaščiti spanec in spremlja, ali širša slika zahteva več pozornosti.</li></ul>',
            [
                ['question' => 'Zakaj kašelj včasih traja tako dolgo?', 'answer' => 'Ker se grlo in dihalne poti lahko umirjajo dlje ali ker draženje vztraja v ozadju.'],
                ['question' => 'Kaj odrasle pri vztrajnem kašlju najbolj moti?', 'answer' => 'Pogosto prekinjen spanec, praskanje v grlu in občutek, da se simptom vedno vrača.'],
                ['question' => 'Kdaj je potreben večji oprez?', 'answer' => 'Če kašelj traja neobičajno dolgo, se slabša ali ga spremljajo resnejši simptomi.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Menjavati sredstva brez razumevanja, kaj draženje pravzaprav ohranja.'],
            ],
            'Vztrajen kašelj pri odraslih: kako ga umiriti in kdaj pogledati širšo sliko',
            'Spoznajte, kako pri vztrajnem kašlju pri odraslih pomagajo mirnejša podpora grlu, boljši ritem in pravočasna previdnost.',
            'Vztrajen kašelj',
            [
                ['heading' => 'Zakaj vztrajen kašelj potrebuje razmišljanje o vzorcu', 'html' => '<p>Daljši kašelj postane posebno frustrirajoč, ker deluje ponavljajoče in nejasno. Zato podpora deluje bolje, ko razumemo, kaj draženje podaljšuje, ne le kako ga utišati.</p>'],
                ['heading' => 'Zakaj zaščita spanja pomeni toliko', 'html' => '<p>Ko kašelj moti počitek, je celotno okrevanje veliko težje. Prav zato nega grla in mirnejši večerni ritem pogosto pomagata bolj, kot si sprva mislimo.</p>'],
            ]
        ),
        $entry(
            557,
            'Čaj proti kašlju: katere zeliščne mešanice res pomagajo pri hitrejšem olajšanju',
            'caj-proti-kaslju-katere-zeliscne-mesanice-res-pomagajo',
            'Zeliščni čaj proti kašlju je lahko uporaben, vendar predvsem takrat, ko vemo, kaj želimo umiriti: suhost, draženje, toplino pred spanjem ali občutek praskanja v grlu. Tukaj je, kako čaj uporabljati pametneje in ne samo po logiki “naravno je vedno dovolj”.',
            '<ul><li>Čaj proti kašlju najbolje deluje kot pomirjujoč ritual za grlo in bolj miren ritem okrevanja.</li><li>Največja napaka je pričakovati, da bo ena mešanica rešila vse vrste kašlja in vse vzroke.</li><li>Pametnejši pristop izbere čaj glede na občutek v grlu, čas dneva in širšo sliko simptomov.</li></ul>',
            [
                ['question' => 'Ali čaj res pomaga pri kašlju?', 'answer' => 'Lahko pomaga z občutkom pomiritve in ugodja, posebej pri suhem ali razdraženem grlu.'],
                ['question' => 'Ali je vsak zeliščni čaj enako uporaben?', 'answer' => 'Ne. Najboljša izbira je odvisna od zelišč in od tega, kaj točno želite umiriti.'],
                ['question' => 'Kdaj je čaj najkoristnejši?', 'answer' => 'Pogosto zvečer ali v trenutkih, ko želite zmanjšati draženje in bolje zaščititi spanec.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Nasloniti se samo na čaj, kadar simptom traja dolgo ali postaja izrazitejši.'],
            ],
            'Čaj proti kašlju: kako izbrati zeliščno mešanico, ki res pomaga grlu',
            'Odkrijte, kdaj ima čaj proti kašlju smisel in kako zeliščne mešanice uporabiti za več ugodja in bolj mirne noči.',
            'Čaj proti kašlju',
            [
                ['heading' => 'Zakaj čaj najbolje deluje kot podporna pomoč', 'html' => '<p>Topel zeliščni napitek pogosto pomaga zato, ker upočasni ritem in grlu prinese nekaj miru. Ta podpora je dragocena, vendar ostane najbolj koristna, kadar ohranimo realna pričakovanja.</p>'],
                ['heading' => 'Zakaj je pomembno uskladiti čaj s simptomom', 'html' => '<p>Različne vrste kašlja ne potrebujejo vedno istega pristopa. Nekaj več pozornosti do časa in občutka v grlu pogosto naredi čaj precej bolj pameten del rutine.</p>'],
            ]
        ),
        $entry(
            558,
            'Psoriaza: naravne strategije, sprožilci in kako koži vrniti več miru',
            'psoriaza-naravne-strategije-sprozilci-in-mirnejsa-nega',
            'Psoriaza zahteva potrpežljivost in širši pogled, ker koža pogosto reagira na stres, draženje, rutino in splošno stanje telesa. Tukaj je, kako o naravnih strategijah razmišljati bolj realno, brez lažnega upanja in brez agresivnega preizkušanja vsega naenkrat.',
            '<ul><li>Psoriaza potrebuje kontinuiteto bolj kot čudežne kratke intervencije.</li><li>Največja napaka je prepogosto menjavati izdelke in hkrati ignorirati stres, sprožilce in navade.</li><li>Pametnejši pristop gradi nežnejšo rutino, manj dražilcev in boljši vpogled v vzorce poslabšanja.</li></ul>',
            [
                ['question' => 'Ali naravne strategije lahko pomagajo?', 'answer' => 'Lahko kot del širše rutine umirjanja kože, ne pa skozi nerealno obljubo hitrega izginotja težave.'],
                ['question' => 'Zakaj je stres tako pomemben?', 'answer' => 'Ker mnogim prav stres jasno okrepi vnetne vzorce in reaktivnost kože.'],
                ['question' => 'Kaj koža najpogosteje potrebuje?', 'answer' => 'Več doslednosti, manj draženja in mirnejši dolgoročni pristop k negi.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Naenkrat zamenjati preveč stvari in nato ne vedeti, kaj je pomagalo in kaj je škodilo.'],
            ],
            'Psoriaza: naravna podpora, sprožilci in mirnejši vzorec nege kože',
            'Spoznajte, kako psoriazo podpreti z bolj dosledno rutino, manj sprožilci in nežnejšo nego.',
            'Psoriaza',
            [
                ['heading' => 'Zakaj nega psoriaze potrebuje več potrpežljivosti kot novosti', 'html' => '<p>Ljudje pogosto želimo hitro vidno spremembo, vendar se psoriaza običajno bolje odzove na mirno ponavljanje in manj draženja kot na stalne nove poskuse. Prav ta bolj stabilen pristop kožo pogosto zaščiti bolje.</p>'],
                ['heading' => 'Zakaj so sprožilci enako pomembni kot izdelki', 'html' => '<p>Tudi dobra rutina lahko razočara, če vzorec sprožilcev še naprej obremenjuje kožo. Zato se boljši rezultati pogosto pojavijo takrat, ko se izboljšata tako izbira izdelkov kot življenjski kontekst.</p>'],
            ]
        ),
        $entry(
            562,
            'Naravno olajšanje za težke noge: hidromasaža, aloe krema in lažji dan',
            'naravno-olajsanje-za-tezke-noge-hidromasaza-in-aloe-krema',
            'Težke noge pogosto odražajo dolgotrajno sedenje, stanje, toploto in občutek upočasnjenega pretoka proti koncu dneva. Tukaj je, kako lahko preprosti rituali, kot so izpiranje, masaža in nega kože, vrnejo več lahkotnosti in udobja.',
            '<ul><li>Težke noge se pogosto dobro odzovejo na male ponavljajoče se telesne rituale, ki izboljšajo občutek pretoka in lahkotnosti.</li><li>Največja napaka je ignorirati simptom, dokler ne postane vsakodnevno frustrirajoč ali izrazitejši problem.</li><li>Pametnejši pristop dosledno uporablja hlajenje, krajše premore hoje in praktično nego telesa.</li></ul>',
            [
                ['question' => 'Zakaj noge postanejo težke?', 'answer' => 'Dolgo sedenje, stanje, toplota in premalo gibanja pogosto postopno ustvarijo tak občutek čez dan.'],
                ['question' => 'Ali lahko masaža in izpiranje pomagata?', 'answer' => 'Da, pogosto prineseta občutek olajšanja in svežine, posebej ob redni uporabi.'],
                ['question' => 'Kje aloe krema pride prav?', 'answer' => 'Kot del rituala nege in hlajenja po bolj napornem dnevu.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Čakati do močnega nelagodja namesto uvajati majhne vsakodnevne navade za olajšanje.'],
            ],
            'Težke noge: naravna podpora s hlajenjem, gibanjem in preprosto nego',
            'Odkrijte, kako lahko težke noge olajšate s hidromasažo, aloe kremo in praktičnimi dnevnimi rituali.',
            'Težke noge',
            [
                ['heading' => 'Zakaj težke noge pogosto potrebujejo male ponovljive navade', 'html' => '<p>Telo se pogosto dobro odzove na majhne, redne trenutke olajšanja namesto na en velik poseg. Zato lahko nekaj praktičnih navad pomembno spremeni občutek celotnega dneva.</p>'],
                ['heading' => 'Zakaj preventiva pogosto pomaga lažje kot “reševanje”', 'html' => '<p>Ko rutine za težke noge postanejo redne, običajno delujejo bolje kot čakanje na močno nelagodje. Zgodnejša podpora skoraj vedno prinese več lahkotnosti kot pozna reakcija.</p>'],
            ]
        ),
        $entry(
            567,
            'Parkinsonova bolezen in prehrana: antioksidanti, obroki in podpora vsakdanjemu delovanju',
            'parkinsonova-bolezen-in-prehrana-antioksidanti-in-podpora-vsakdanu',
            'Prehrana pri Parkinsonovi bolezni ne reši vsega, lahko pa vpliva na energijo, prebavo in občutek, kako obvladljiv je dan. Tukaj je, kako o antioksidantih in obrokih razmišljati mirneje, z manj hypea in več praktične podpore.',
            '<ul><li>Prehrana ima pri Parkinsonovi bolezni smisel kot podpora kakovosti vsakdana, ne kot zamenjava za zdravljenje.</li><li>Največja napaka je iskati eno čudežno hranilo namesto gledati obroke, prebavo in energijo kot celoto.</li><li>Pametnejši pristop uporablja hrano za več stabilnosti, lažjo prebavo in boljši dnevni ritem.</li></ul>',
            [
                ['question' => 'Ali so antioksidanti smiselni?', 'answer' => 'Lahko kot del širšega prehranskega vzorca, ne pa kot celoten odgovor na kompleksno stanje.'],
                ['question' => 'Zakaj prehrana sploh vpliva?', 'answer' => 'Ker lahko podpira energijo, prebavo, udobje in dnevno obvladljivost simptomov.'],
                ['question' => 'Ali naj hrana reši vse?', 'answer' => 'Ne. Največ smisla ima kot ena plast znotraj širšega medicinskega in praktičnega okvira.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Usmeriti se v eno “superživilo” in zanemariti celotno strukturo obrokov in navad.'],
            ],
            'Parkinsonova bolezen in prehrana: kje antioksidanti pomagajo in kje šteje realizem',
            'Razumite, kako lahko prehrana podpira vsakdan pri Parkinsonovi bolezni brez pretvarjanja hrane v čudež.',
            'Parkinson in prehrana',
            [
                ['heading' => 'Zakaj prehranska podpora nekaj pomeni, tudi ko ni celotna rešitev', 'html' => '<p>Ljudje včasih podcenimo prehrano, ker ne more nadomestiti zdravljenja. V resnici pa lahko dnevni vzorec obrokov pomembno vpliva na udobje, energijo in občutek, kako obvladljiv je dan.</p>'],
                ['heading' => 'Zakaj širša struktura premaga hype o enem hranilu', 'html' => '<p>Ena sama sestavina redko bistveno spremeni kompleksno stanje. Veliko bolj uporabna strategija navadno temelji na ritmu obrokov, prebavi in bolj stabilnem dnevu kot celoti.</p>'],
            ]
        ),
        $entry(
            572,
            'Reishi in shiitake: kako lahko zdravilne gobe podprejo imunost in vitalnost',
            'reishi-in-shiitake-kako-lahko-gobe-podprejo-imunost',
            'Zdravilne gobe zvenijo fascinantno, zato so še posebej dovzetne za pretirane zdravstvene trditve. Tukaj je, kako o reishiju in shiitake razmišljati skozi imunost, prehrano in dolgoročnejšo podporo namesto skozi čudežno razmišljanje.',
            '<ul><li>Reishi in shiitake sta lahko zanimiv del širše rutine za imunost in vitalnost.</li><li>Največja napaka je od gob pričakovati tisto, kar spanec, prehrana in okrevanje še vedno ne urejajo.</li><li>Pametnejši pristop jih uporablja kot dodatek raznoliki prehrani, ne kot čudežni prašek ali kapsulo.</li></ul>',
            [
                ['question' => 'Zakaj sta reishi in shiitake tako priljubljena?', 'answer' => 'Zaradi tradicije, zanimanja za njune spojine in ideje o podpori imunosti in vitalnosti.'],
                ['question' => 'Ali lahko nadomestita zdravo prehrano?', 'answer' => 'Ne. Najbolje se vključita kot del širšega in bolj raznolikega prehranskega vzorca.'],
                ['question' => 'Kdaj imata več smisla?', 'answer' => 'Ko želite nežnejšo dolgoročnejšo podporo in ne dramatičnega hitrega učinka.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kupiti izdelke z gobami zaradi hypea brez razmisleka, kako sploh sodijo v širšo rutino.'],
            ],
            'Reishi in shiitake: kje imata smisel za imunost in kje se začne hype',
            'Spoznajte, kako lahko reishi in shiitake sodita v podporo imunosti in vitalnosti brez nerealnih pričakovanj.',
            'Reishi in shiitake',
            [
                ['heading' => 'Zakaj zdravilne gobe hitro privabijo velika pričakovanja', 'html' => '<p>Ljudje naravno projiciramo veliko upanja v nenavadne “funkcionalne” sestavine. Prav zato se reishi in shiitake najbolje uporabljata znotraj mirnejšega in bolj realnega okvira.</p>'],
                ['heading' => 'Zakaj najbolje delujeta kot del širšega prehranskega vzorca', 'html' => '<p>Njuna vrednost je najjasnejša takrat, ko dopolnjujeta dobro osnovo in ne poskušata nositi celotne zgodbe o imunosti sama. Velika slika prehrane običajno pomeni veliko več kot ena posebna sestavina.</p>'],
            ]
        ),
        $entry(
            577,
            'Sezona gripe: kako lahko cink, vitamin D in probiotiki podprejo imunost bolj realno',
            'sezona-gripe-kako-cink-vitamin-d-in-probiotiki-podprejo-imunost',
            'Sezona gripe vsako leto odpira ista vprašanja o tem, kaj je smiselno jemati in kako telo pripraviti na večjo obremenitev. Tukaj je, kako o cinku, vitaminu D in probiotikih razmišljati z manj panike in več praktičnega smisla.',
            '<ul><li>Cink, vitamin D in probiotiki imajo lahko mesto v sezonski rutini, vendar le na osnovi dobrega spanja, prehrane in higiene.</li><li>Največja napaka je pričakovati, da bodo trije izdelki sami nosili celoten imunski sistem skozi zahtevno sezono.</li><li>Pametnejši pristop jih uporablja kot podporo boljši osnovi, ne kot njeno zamenjavo.</li></ul>',
            [
                ['question' => 'Ali sta cink in vitamin D smiselna v sezoni gripe?', 'answer' => 'Lahko, posebej ko sezona in potrebe posameznika to res upravičujejo.'],
                ['question' => 'Zakaj se omenjajo tudi probiotiki?', 'answer' => 'Ker se pogosto obravnavajo kot del širše zgodbe odpornosti in podpore črevesju.'],
                ['question' => 'Ali vse jemljemo kar na slepo?', 'answer' => 'Ne. Najprej ima več smisla pogledati potrebe, prehrano in dnevni ritem.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Iz panike kupiti veliko dodatkov, medtem ko osnovna rutina ostaja šibka.'],
            ],
            'Sezona gripe: cink, vitamin D in probiotiki v bolj realni rutini za imunost',
            'Odkrijte, kdaj imajo cink, vitamin D in probiotiki smisel v sezoni gripe in zakaj osnove še vedno vodijo zgodbo.',
            'Sezona gripe',
            [
                ['heading' => 'Zakaj podpora v sezoni gripe še vedno začne pri osnovah', 'html' => '<p>Ko se obremenitev poveča, ljudje pogosto želimo hiter odgovor v nekaj dopolnilih. V resnici pa se imunski sistem najgloblje odziva na spanec, prehrano, stres in splošni dnevni ritem.</p>'],
                ['heading' => 'Zakaj mirnejše odločitve prinesejo boljšo sezonsko podporo', 'html' => '<p>Manj panike praviloma pomeni tudi boljše izbire. Bolj umirjen pristop pomaga izbrati le tisto, kar res ustreza, namesto da bi na problem vrgli vse hkrati.</p>'],
            ]
        ),
        $entry(
            579,
            'Ponovni zagon telesa po pavzi: varnejša vrnitev k gibanju brez pretiravanja',
            'ponovni-zagon-telesa-po-pavzi-varnejsa-vrnitev-k-gibanju',
            'Po daljši pavzi telo ne potrebuje kazni, ampak vrnitev, ki znova gradi zaupanje, zmogljivost in občutek varnosti v gibanju. Tukaj je, kako se vrniti k aktivnosti, ne da bi prvi teden pokopal motivacijo za naprej.',
            '<ul><li>Vrnitev k aktivnosti najbolje deluje, ko je dovolj nežna, da ne zlomi motivacije in telesa že v prvih dneh.</li><li>Največja napaka je poskusiti “nadoknaditi izgubljeni čas” z visoko intenzivnostjo takoj na začetku.</li><li>Pametnejši pristop gradi rutino skozi hojo, osnovne gibe in postopno povečevanje obremenitve.</li></ul>',
            [
                ['question' => 'Kako začeti po daljši pavzi?', 'answer' => 'Najpogosteje z lažjim gibanjem, krajšimi treningi in več pozornosti na okrevanje.'],
                ['question' => 'Zakaj ljudje hitro spet odnehamo?', 'answer' => 'Ker začnemo premočno, nato pa pridejo bolečine, utrujenost in občutek, da je vsega preveč.'],
                ['question' => 'Kako hitro dvigovati intenzivnost?', 'answer' => 'Postopno, da ima telo čas na prilagoditev brez preobremenitve.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Primerjati se s preteklim telesom in siliti raven, na katero še nismo pripravljeni.'],
            ],
            'Vrnitev k aktivnosti po pavzi: kako začeti varno in brez izgube motivacije',
            'Spoznajte, kako telo po pavzi ponovno zagnati skozi varen in vzdržen načrt vrnitve k gibanju.',
            'Vrnitev k aktivnosti',
            [
                ['heading' => 'Zakaj prvi teden določi čustveni ton povratka', 'html' => '<p>Ko je povratek preveč kaznovalen, motivacija pogosto hitro pade. Prav zato bolj nežen začetek pogosto ustvari močnejšo dolgoročno doslednost kot agresiven ponovni start.</p>'],
                ['heading' => 'Zakaj je treba ponovno zgraditi tudi zaupanje v gibanje', 'html' => '<p>Po času brez aktivnosti telo pogosto ne potrebuje le kondicije, ampak tudi občutek varnosti in zaupanja. Počasnejše napredovanje zato pogosto gradi oboje hkrati.</p>'],
            ]
        ),
    ],
];
