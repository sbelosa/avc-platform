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
    'key' => 'druzina-prebava-podpora-sl-wave-1',
    'name' => 'Družina, prebava in vsakodnevna podpora (SL) - prvi val',
    'notes' => 'Ročno lokaliziran premium val za družinsko podporo, prebavo, dodatke in praktične teme vsakodnevnega počutja.',
    'entries' => [
        $entry(
            574,
            'Otroci z ADHD: tehnike umirjanja, prehrana in podpora brez hitrih etiket',
            'otroci-z-adhd-tehnike-umirjanja-prehrana-in-podpora',
            'Otrokom z ADHD najpogosteje najbolj pomaga podpora, ki postane praktična, nežna in ponovljiva, ne pa popolna. Tukaj je, kako razmišljati o umirjanju, prehrani in dnevni strukturi brez preproste ideje, da obstaja ena rešitev za vse.',
            '<ul><li>Podpora otrokom z ADHD deluje najbolje, ko rutino, prehrano, čustva in okolje gledamo skupaj.</li><li>Največja napaka je iskati en trik, ki bo čez noč rešil vse težave s fokusom in vedenjem.</li><li>Pametnejši pristop gradi majhne ponovljive rituale, ki otroku vračajo varnost in občutek ritma.</li></ul>',
            [
                ['question' => 'Kaj otrokom z ADHD pogosto najbolj pomaga?', 'answer' => 'Jasna rutina, mirnejša komunikacija in podpora, ki se čez dan ponavlja.'],
                ['question' => 'Ali lahko prehrana sama reši vse?', 'answer' => 'Ne. Prehrana je lahko del podpore, ni pa celoten odgovor.'],
                ['question' => 'Zakaj je umirjanje tako pomembno?', 'answer' => 'Ker otrok običajno bolje sodeluje, ko se počuti bolj varno in manj preobremenjeno.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Naenkrat spremeniti preveč stvari in pričakovati takojšen preobrat.'],
            ],
            'Otroci z ADHD: kako graditi podporo skozi rutino, prehrano in umirjanje',
            'Spoznajte, kako otrokom z ADHD pomagati s praktičnimi rutinami, umirjanjem in bolj stabilno dnevno strukturo.',
            'Otroci in ADHD',
            [
                ['heading' => 'Zakaj praktična podpora pogosto pomeni več kot velika teorija', 'html' => '<p>Družine običajno občutijo več olajšanja, ko najdejo nekaj stvari, ki jih lahko vsak dan ponovijo, namesto da lovijo popoln načrt. Majhne strukture pogosto zmanjšajo kaos bolj učinkovito kot stalno iskanje novih rešitev.</p>'],
                ['heading' => 'Zakaj otrok potrebuje regulacijo, ne dodatnega pritiska', 'html' => '<p>Stres in preobremenjenost pri ADHD se pogosto povečata, kadar okolje postane še bolj reaktivno. Mirnejša struktura in jasnejša pričakovanja običajno ustvarita bolj uporabno izhodišče kot stalni konflikti.</p>'],
            ]
        ),
        $entry(
            583,
            'Hipervitaminoza D: kako prepoznati pretiran vnos in ostati na varnejši strani',
            'hipervitaminoza-d-kako-prepoznati-pretiran-vnos',
            'Vitamin D je koristen le, dokler ostane v smiselnem okviru. Tukaj je, kako prepoznati, kdaj dodajanje preide v pretiravanje in zakaj več ni vedno tudi boljše.',
            '<ul><li>Vitamin D je lahko koristen, vendar pretirana uporaba prinaša nepotrebno tveganje.</li><li>Največja napaka je dolgo časa jemati visoke odmerke brez jasnega razloga ali spremljanja.</li><li>Pametnejši pristop gleda potrebo, kontekst in varnejše dolgoročno doziranje.</li></ul>',
            [
                ['question' => 'Kaj je hipervitaminoza D?', 'answer' => 'To je težava, povezana s pretiranim vnosom vitamina D iz dodatkov.'],
                ['question' => 'Zakaj ljudje pretiravajo?', 'answer' => 'Ker ima vitamin D dober ugled in hitro nastane občutek, da več pomeni tudi bolje.'],
                ['question' => 'Kako ostati bolj varen?', 'answer' => 'Izogibati se improvizaciji z visokimi odmerki in vnos vezati na realno potrebo.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kopirati protokol nekoga drugega brez preverjanja lastnega konteksta.'],
            ],
            'Hipervitaminoza D: kdaj koristen dodatek postane nepotrebno tveganje',
            'Razumite, kako nastane pretiran vnos vitamina D in kako ostati na varnejši strani dodajanja.',
            'Hipervitaminoza D',
            [
                ['heading' => 'Zakaj več ni vedno bolj koristno', 'html' => '<p>Koristni dodatki hitro ustvarijo lažen občutek varnosti. Vitamin D najbolje deluje takrat, ko ustreza dejanski potrebi, ne pa ko postane vse večji poskus brez meja.</p>'],
                ['heading' => 'Zakaj je kontekst pomembnejši od ugleda', 'html' => '<p>Dopolnilo je lahko koristno in hkrati hitro zlorabljeno. Pravo vprašanje ni, ali je vitamin D na splošno dober, ampak ali količina in razlog še vedno res imata smisel.</p>'],
            ]
        ),
        $entry(
            586,
            'Najboljši čas za jemanje multivitaminov: zjutraj, zvečer ali preprosto ob pravem obroku',
            'najboljsi-cas-za-jemanje-multivitaminov-zjutraj-ali-zvecer',
            'Multivitamin ne deluje bolje zato, ker ga vzamete v eni popolni minuti dneva, lahko pa način jemanja vpliva na prenašanje in doslednost. Tukaj je, kako izbrati čas, ki se ujema z resničnim življenjem, ne pa s togim pravilom.',
            '<ul><li>Najboljši čas za multivitamin je tisti, ki podpira doslednost in dobro prenašanje.</li><li>Največja napaka je loviti popoln timing in zanemariti, kako telo izdelek dejansko prenaša.</li><li>Pametnejši pristop ga veže na obrok in ritem, ki ga je mogoče zlahka ponavljati.</li></ul>',
            [
                ['question' => 'Ali je jutro vedno najboljše?', 'answer' => 'Ne nujno. Nekaterim bolj ustreza prvi večji obrok, drugim kasnejši del dneva.'],
                ['question' => 'Zakaj pomaga jemanje s hrano?', 'answer' => 'Ker pogosto izboljša prenašanje in navado olajša.'],
                ['question' => 'Kaj pomeni več kot ura?', 'answer' => 'Doslednost in to, da se dodatek ujema z rutino brez nelagodja.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Opustiti navado, ker čas ni popoln, namesto da bi jo prilagodili življenju.'],
            ],
            'Multivitamini: kako izbrati čas, ki ga boste res lahko vzdrževali',
            'Preverite, kdaj jemati multivitamin in zakaj v praksi doslednost pogosto pomeni več kot popoln timing.',
            'Čas za multivitamin',
            [
                ['heading' => 'Zakaj je popoln timing pogosto precenjen', 'html' => '<p>Ljudje imamo radi natančne odgovore, vendar navade z dodatki običajno uspejo zato, ker se ujamejo z življenjem in ne zato, ker sledijo strogemu pravilu. Uporabna navada skoraj vedno pomeni več kot teoretično idealna.</p>'],
                ['heading' => 'Zakaj je obrok pogosto pomembnejši od ure', 'html' => '<p>Hrana pogosto izboljša prenašanje in olajša spomin na navado. Zato je najboljši odgovor običajno praktičen, ne obsesiven.</p>'],
            ]
        ),
        $entry(
            587,
            'Naravna podpora plodnosti: maca, vitex in akupunktura skozi bolj realen pogled',
            'naravna-podpora-plodnosti-maca-vitex-in-akupunktura',
            'Naravna podpora plodnosti lahko ima mesto v širšem načrtu, vendar deluje najbolje, ko ne postane obupano iskanje ene same čudežne rešitve. Tukaj je, kako te možnosti oceniti bolj mirno in z več perspektive.',
            '<ul><li>Dodatki in komplementarne metode imajo največ smisla kot del širše slike podpore plodnosti.</li><li>Največja napaka je položiti vse upanje v eno zelišče, en tretma ali en trend.</li><li>Pametnejši pristop povezuje informacije, potrpežljivost in realnejša pričakovanja.</li></ul>',
            [
                ['question' => 'Zakaj se pogosto omenjata maca in vitex?', 'answer' => 'Ker ju ljudje pogosto povezujejo s hormonskim ravnovesjem in podporo plodnosti.'],
                ['question' => 'Ali je akupunktura univerzalna rešitev?', 'answer' => 'Ne. Lahko je podporna metoda, ni pa celoten odgovor.'],
                ['question' => 'Kaj je najpomembnejše?', 'answer' => 'Realna pričakovanja in širši pogled na zdravje, stres in čas.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Razpršiti energijo med preveč metod brez jasnejših prioritet.'],
            ],
            'Naravna podpora plodnosti: kako maca, vitex in akupunktura dobijo bolj realen okvir',
            'Spoznajte, kako naravno podporo plodnosti oceniti bolj mirno, informirano in realistično.',
            'Podpora plodnosti',
            [
                ['heading' => 'Zakaj je pri podpori plodnosti pomembno bolj mirno odločanje', 'html' => '<p>Ko so čustva močna, skoraj vsaka obetavna metoda zveni večja, kot je. Boljše odločitve pogosto pridejo, ko si dovolimo upočasniti in vsako možnost postaviti v širši načrt.</p>'],
                ['heading' => 'Zakaj nobena metoda ne more nositi cele zgodbe', 'html' => '<p>Plodnost je preveč kompleksna, da bi jo pojasnilo eno zelišče ali ena praksa. Najbolj prizemljena podpora običajno povezuje več plasti namesto iskanja magične rešitve.</p>'],
            ]
        ),
        $entry(
            589,
            'Stres pri otrocih: kako prepoznati znake in prej umiriti vsakodnevno preobremenjenost',
            'stres-pri-otrocih-kako-prepoznati-znake',
            'Stres pri otrocih redko izgleda povsem enako kot pri odraslih, a se pogosto pokaže skozi spanje, vedenje in čustveno napetost. Tukaj je, kako ga prej opaziti in v otrokove dni vrniti več varnosti.',
            '<ul><li>Stres pri otrocih se pogosto pokaže skozi razdražljivost, spremembe v spanju in močnejšo čustveno občutljivost.</li><li>Največja napaka je takšne znake odpisati kot neposlušnost ali zgolj fazo.</li><li>Pametnejši pristop opazuje vzorce ter ustvarja več ritma, varnosti in povezanosti.</li></ul>',
            [
                ['question' => 'Kako se stres pri otrocih najpogosteje pokaže?', 'answer' => 'Pogosto skozi razdražljivost, umik, spremembe spanja ali močnejše čustvene reakcije.'],
                ['question' => 'Zakaj ga je lahko spregledati?', 'answer' => 'Ker odrasli stresne znake pogosto zamenjamo za vedenjske težave.'],
                ['question' => 'Kaj običajno najbolj pomaga?', 'answer' => 'Mirnejši ritem, več povezanosti in manj vsakodnevne preobremenjenosti.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Reagirati le na vedenje in spregledati ponavljajoči se vir napetosti.'],
            ],
            'Stres pri otrocih: kako ga opaziti, preden postane vsakodnevno breme',
            'Razumite, kako se stres pri otrocih pokaže in kako lahko mirnejše rutine pomagajo prej, kot večina pričakuje.',
            'Stres pri otrocih',
            [
                ['heading' => 'Zakaj otroci stres pogosto pokažejo skozi vedenje', 'html' => '<p>Otroci ne znajo vedno pojasniti, kaj se dogaja v njih. Zato stres pogosto postane viden skozi vedenje, spanec ali spremembo čustvenega tona, odrasli pa moramo vzorce brati bolj pozorno.</p>'],
                ['heading' => 'Zakaj varnost pomaga bolj kot pritisk', 'html' => '<p>Ko je otrok preobremenjen, dodatni pritisk redko ustvari pravi mir. Bolj regulirano okolje otroku navadno da veliko več možnosti, da se umiri in ponovno najde ravnovesje.</p>'],
            ]
        ),
        $entry(
            592,
            'Zgaga v nosečnosti: preprostejše naravne poti do olajšanja z manj vsakodnevnega kaosa',
            'zgaga-v-nosecnosti-naravne-resitve-za-olajsanje',
            'Zgaga v nosečnosti izčrpava prav zato, ker se vrača v obdobju, ko je telo že tako bolj občutljivo. Tukaj je, kako pristopiti k olajšanju skozi ritem hrane, nežnejše navade in bolj realen dnevni načrt.',
            '<ul><li>Zgaga v nosečnosti se običajno najbolje odziva na praktične prilagoditve obrokov in ritma, ne na zapletene trike.</li><li>Največja napaka je čakati, da nelagodje postane močno, preden karkoli spremenimo.</li><li>Pametnejši pristop uvaja manjše spremembe, ki čez dan zmanjšajo pritisk na prebavo.</li></ul>',
            [
                ['question' => 'Zakaj je zgaga v nosečnosti tako pogosta?', 'answer' => 'Ker spremembe v telesu in pritisk na prebavo pogosto naraščajo z napredovanjem nosečnosti.'],
                ['question' => 'Kaj pogosto pomaga?', 'answer' => 'Manjši obroki, mirnejši tempo hranjenja in manj sprožilnih živil.'],
                ['question' => 'Ali je treba vse reševati naravno za vsako ceno?', 'answer' => 'Ne. Varnost in realnost sta še vedno najpomembnejši.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Ignorirati očitne vzorce, ki kažejo, kaj nelagodje vedno znova poslabša.'],
            ],
            'Zgaga v nosečnosti: kako jo ublažiti z boljšim ritmom in manj sprožilci',
            'Odkrijte, kako zgago v nosečnosti omiliti s preprostejšimi navadami, mirnejšimi obroki in bolj uporabnim ritmom dneva.',
            'Zgaga v nosečnosti',
            [
                ['heading' => 'Zakaj praktične prilagoditve pogosto pomagajo bolj kot zapleteni nasveti', 'html' => '<p>Nosečnost je že sama po sebi dovolj zahtevna, zato najbolj pomagajo rešitve, ki so preproste in ponovljive. Manjši obroki in mirnejši ritem pogosto pomenijo več kot preveč zapletena priporočila.</p>'],
                ['heading' => 'Zakaj je realizem pomembnejši od popolnega nadzora', 'html' => '<p>Cilj običajno ni popolnoma nadzorovati prebavo, ampak zmanjšati vsakodnevno breme. Praktično izboljšanje pogosto pomeni več kot lov na popolno odpravo vsakega simptoma.</p>'],
            ]
        ),
        $entry(
            630,
            'Naravni laksativi: psilij, aloe gel in suhe slive v bolj pametnem načrtu za prebavo',
            'naravni-laksativi-psilij-aloe-gel-in-suhe-sljive',
            'Pri zaprtju in počasni prebavi veliko ljudi išče najhitrejšo rešitev, prav tam pa se pogosto rodi največ frustracije. Tukaj je, kako naravne laksative vključiti v širši načrt in jih ne obravnavati kot čarobno stikalo.',
            '<ul><li>Naravni laksativi običajno najbolje delujejo skupaj s tekočino, gibanjem in boljšim ritmom obrokov.</li><li>Največja napaka je iskati takojšnje olajšanje brez spremembe stvari, ki težavo stalno vračajo.</li><li>Pametnejši pristop povezuje vlaknine, tekočino in nežno podporo skozi več dni ali tednov.</li></ul>',
            [
                ['question' => 'Zakaj sta psilij in suhe slive tako priljubljena?', 'answer' => 'Ker ju ljudje povezujejo z vlakninami, mehkejšo stolico in bolj naravnim olajšanjem.'],
                ['question' => 'Ali lahko pomaga tudi aloe gel?', 'answer' => 'Lahko ima smisel v nekaterih rutinah, ni pa edini dejavnik, ki določa prebavo.'],
                ['question' => 'Kaj je pomembno ob laksativih?', 'answer' => 'Tekočina, gibanje in navade, ki prebavo ohranjajo bolj dejavno.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Vzeti izdelek in prezreti širši vzrok počasne prebave.'],
            ],
            'Naravni laksativi: kako jih uporabiti kot podporo, ne kot celoten načrt za prebavo',
            'Poglejte, kako lahko psilij, aloe gel in suhe slive pomagajo prebavi, ko jih vključimo v širšo rutino.',
            'Naravni laksativi',
            [
                ['heading' => 'Zakaj se zaprtje redko spremeni samo z enim orodjem', 'html' => '<p>Ritem prebave običajno odraža hrano, tekočino, gibanje in stres skupaj. Zato tudi uporabni izdelki delujejo bolje, ko se bolj podpre celoten vsakdanji vzorec.</p>'],
                ['heading' => 'Zakaj počasnejši reset pogosto deluje bolje kot hitra rešitev', 'html' => '<p>Ljudje si pogosto želimo takojšnjega olajšanja, a prebava je običajno bolj stabilna skozi ponavljanje kot skozi naglico. Nežna doslednost pogosto premaga enkratne reakcije.</p>'],
            ]
        ),
        $entry(
            642,
            'Sirup iz čebule in medu za kašelj: kdaj ima smisel in kako ga uporabljati bolj realno',
            'sirup-iz-cebule-in-medu-za-kaselj',
            'Sirup iz čebule in medu se ohranja skozi generacije zato, ker lahko prinese toplino in občutek olajšanja v grlu. Tukaj je, kako ga uporabljati razumno, ne da bi vsak domač recept spremenili v popolno rešitev.',
            '<ul><li>Domači sirup ima največ smisla kot podporni ritual za grlo in mirnejši ritem okrevanja.</li><li>Največja napaka je narediti iz ljudskega recepta edini načrt, ko kašelj traja predolgo ali postaja močnejši.</li><li>Pametnejši pristop uporablja sirup za udobje, hkrati pa opazuje širši vzorec simptoma.</li></ul>',
            [
                ['question' => 'Zakaj je ta sirup tako priljubljen?', 'answer' => 'Ker združuje toplino, tradicijo in občutek pomiritve grla.'],
                ['question' => 'Ali lahko pomaga vsakomur?', 'answer' => 'Ne enako, vendar ga nekateri res doživijo kot pomirjujočo podporo.'],
                ['question' => 'Kdaj ima največ smisla?', 'answer' => 'Ko ga uporabljamo kot podporo ob počitku, tekočini in spremljanju simptomov.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Zanašati se samo na sirup, ko širša slika kašlja zahteva več pozornosti.'],
            ],
            'Sirup iz čebule in medu: kje res pomaga grlu in kje sam ni dovolj',
            'Spoznajte, kdaj ima sirup iz čebule in medu za kašelj smisel in kako ga uporabiti kot razumno domačo podporo.',
            'Sirup iz čebule in medu',
            [
                ['heading' => 'Zakaj tradicionalni recepti še vedno privlačijo', 'html' => '<p>Domači recepti pogosto preživijo zato, ker prinašajo udobje in občutek skrbi, ne le učinek. To je lahko dragoceno, dokler tega ne zamenjamo s popolnim odgovorom na vsak kašelj.</p>'],
                ['heading' => 'Zakaj je udobje koristno, a ne univerzalno', 'html' => '<p>Podporni rituali imajo svoje mesto, vendar najbolje delujejo skupaj z realnim opazovanjem. Grlo se lahko počuti bolje, človek pa mora še vedno paziti na širši vzorec simptoma.</p>'],
            ]
        ),
        $entry(
            653,
            'Alkohol in kava: kako vplivata na prebavo, spanec in občutek odpornosti',
            'alkohol-in-kava-kako-vplivata-na-prebavo-in-imunost',
            'Alkohol in kavo pogosto presojamo ločeno, čeprav ju v resničnem življenju pogosto združujeta isti ritem stresa, spanja in prebave. Tukaj je, kako razumeti njun skupni vpliv z manj dramatike, a tudi brez ignoriranja telesnih signalov.',
            '<ul><li>Kava in alkohol lahko povečata prebavno občutljivost, porušita spanec in zmanjšata občutek okrevanja.</li><li>Največja napaka je gledati vsako navado posebej in prezreti skupni učinek skozi cel dan.</li><li>Pametnejši pristop spremlja vzorce, količino in trenutke, ko telo jasno pokaže, da mu kombinacija ne ustreza več.</li></ul>',
            [
                ['question' => 'Zakaj ta kombinacija pogosto moti prebavo?', 'answer' => 'Ker oba dejavnika lahko obremenita želodec in porušita ritem prebave.'],
                ['question' => 'Kako vplivata na spanec?', 'answer' => 'Pogosto zmanjšata kakovost okrevanja, tudi ko količina spanja na videz ostane podobna.'],
                ['question' => 'Ali je težava samo količina?', 'answer' => 'Ne. Pomembni so tudi čas, stres in celoten dnevni vzorec.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Navaditi se na nelagodje in nehati opažati, kaj telo sporoča.'],
            ],
            'Alkohol in kava: kako njuna kombinacija oblikuje prebavo in okrevanje',
            'Razumite, kako alkohol in kava vplivata na prebavo, spanec in širši občutek odpornosti, kadar se pogosto združujeta.',
            'Alkohol in kava',
            [
                ['heading' => 'Zakaj je vzorec pomembnejši od posamezne navade', 'html' => '<p>Mnogi sprašujejo, ali je slabša kava ali alkohol, vendar je bolj uporabno vprašanje, kako celoten vzorec deluje v telesu. Prebava in okrevanje se običajno odzoveta na kombinacijo, ne le na en element.</p>'],
                ['heading' => 'Zakaj telesne signale sčasoma lažje prezremo', 'html' => '<p>Ponavljajoča se navada lahko nelagodje normalizira. Jasnejše opazovanje zato pogosto pomaga, da človek učinek znova vidi bolj pošteno.</p>'],
            ]
        ),
        $entry(
            658,
            'Stres pri mladostnikih: kako lahko starši pomagajo brez dodatnega pritiska',
            'stres-pri-mladostnikih-kako-lahko-starsi-pomagajo',
            'Stres pri mladostnikih se pogosto pokaže kot umik, razdražljivost ali odpor, pod tem pa je pogosto preobremenjenost, ki potrebuje več razumevanja kot nadzora. Tukaj je, kako lahko starši pomagajo skozi odnos in ritem namesto le s pravili in pritiskom.',
            '<ul><li>Mladostniki se običajno najbolje odzovejo na podporo, ki spoštuje njihovo avtonomijo, a še vedno daje stabilen okvir.</li><li>Največja napaka je na stres odgovoriti z več kritike, nadzora ali dnevnega pritiska.</li><li>Pametnejši pristop gradi pogovor, zaupanje in majhne spremembe, ki zmanjšujejo preobremenjenost.</li></ul>',
            [
                ['question' => 'Kako se stres pri mladostnikih pogosto pokaže?', 'answer' => 'Skozi umik, spremembe razpoloženja, slabši spanec ali močnejšo razdražljivost.'],
                ['question' => 'Kaj starši pogosto naredijo narobe?', 'answer' => 'Povečajo pritisk prav takrat, ko mladostnik najbolj potrebuje mirnejšo podporo.'],
                ['question' => 'Kako pomagati brez dušenja?', 'answer' => 'S poslušanjem, mirnimi mejami in manj vsakodnevnega kaosa.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Znake stresa zamenjati za čisto neposlušnost in spregledati širšo sliko.'],
            ],
            'Stres pri mladostnikih: kako pomagati skozi zaupanje in ne le nadzor',
            'Poglejte, kako lahko starši mladostnikom pod stresom pomagajo skozi odnos, ritem in manj pritiska.',
            'Stres pri mladostnikih',
            [
                ['heading' => 'Zakaj mladostniki pogosto potrebujejo bolj miren odnos, ne glasnejše reakcije', 'html' => '<p>Stres pri mladostnikih se hitro zamenja za slabo voljo ali upor. Veliko bolj uporaben korak je ustvariti mirnejši prostor odnosa, v katerem se mlada oseba počuti slišana in ne samo nadzorovana.</p>'],
                ['heading' => 'Zakaj zaupanje naredi podporo učinkovitejšo', 'html' => '<p>Mladostniki se običajno lažje odzovejo, ko se ne počutijo stisnjene v kot. Podpora bolje deluje, ko meje ostanejo jasne, splošni ton odnosa pa postane bolj miren in spoštljiv.</p>'],
            ]
        ),
    ],
];
