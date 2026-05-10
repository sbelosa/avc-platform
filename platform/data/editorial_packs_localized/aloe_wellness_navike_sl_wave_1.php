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
    'key' => 'aloe-wellness-navike-sl-wave-1',
    'name' => 'Aloe, wellness in navade (SL) - prvi val',
    'notes' => 'Ročno lokaliziran premium val za aloe teme, performans, cirkulacijo, informirano izbiro izdelkov in mentalne navade.',
    'entries' => [
        $entry(
            569,
            'Vpliv glasbe na performans: kako sestaviti playlisto, ki res dvigne energijo',
            'vpliv-glasbe-na-performans-playlista-za-energijo',
            'Glasba je lahko močno orodje za fokus, trening in razpoloženje, vendar le, ko jo uporabljamo namenoma in ne kot naključno ozadje. Tukaj je, kako sestaviti playliste, ki performans podpirajo namesto motijo.',
            '<ul><li>Glasba lahko podpira fokus, ritem in energijo, ko se ujema z nalogo in stanjem telesa.</li><li>Največja napaka je uporabljati isto glasbo za vse vrste dela in pričakovati isti učinek.</li><li>Pametnejši pristop gradi playliste po namenu: globoko delo, trening, okrevanje ali dvig energije.</li></ul>',
            [
                ['question' => 'Ali lahko glasba res izboljša performans?', 'answer' => 'Lahko pomaga z ritmom, motivacijo in lažjim vzdrževanjem fokusa ali energije.'],
                ['question' => 'Zakaj ista playlista ne deluje vedno enako?', 'answer' => 'Ker različne naloge in različna stanja telesa navadno potrebujejo drugačno zvočno okolje.'],
                ['question' => 'Kaj naredi playlisto uporabno?', 'answer' => 'Jasen namen, enakomeren ton in skladbe, ki podpirajo koncentracijo.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Vklopiti glasbo avtomatsko, ne da bi preverili, ali pomaga ali moti.'],
            ],
            'Glasba in performans: kako sestaviti playlisto, ki res deluje za vas',
            'Spoznajte, kako uporabiti glasbo za boljši fokus, trening in energijo z bolj smiselnimi playlistami.',
            'Glasba in performans',
            [
                ['heading' => 'Zakaj pravo zvočno okolje spremeni nalogo', 'html' => '<p>Glasba ni nevtralna. Ista playlista lahko eno dejavnost podpre, drugo pa povsem razbije, zato je namen pomembnejši od samega dejstva, da nekaj igra v ozadju.</p>'],
                ['heading' => 'Zakaj playliste najbolje delujejo, ko imajo nalogo', 'html' => '<p>Ljudje pogosto glasbo izbiramo le po okusu, a pri performansu je bolj koristno, da ima playlista jasno funkcijo: fokus, gibanje, okrevanje ali dvig energije.</p>'],
            ]
        ),
        $entry(
            584,
            'Aloe vera ali agava: kakšna je razlika in kje nastane največ zmede',
            'aloe-vera-ali-agava-kaksna-je-razlika',
            'Aloe vera in agava se v wellness vsebinah pogosto mešata, čeprav gre za različni rastlini z različnimi uporabami. Tukaj je, kako ju ločiti bolj jasno in brez običajne marketinške megle.',
            '<ul><li>Aloe vera in agava nista ista rastlina in nimata enake praktične vloge.</li><li>Največja napaka je zamenjati vizualno podobnost za enako funkcionalno vrednost.</li><li>Pametnejši pristop loči rastlino, uporabo in razlog, zakaj se o njej sploh govori.</li></ul>',
            [
                ['question' => 'Ali sta aloe vera in agava isto?', 'answer' => 'Ne. Gre za različni rastlini z različnimi zgodbami in uporabami.'],
                ['question' => 'Zakaj ju ljudje zamenjujejo?', 'answer' => 'Zaradi podobnega videza in splošnega “naravnega” konteksta.'],
                ['question' => 'Kje je razlika najpomembnejša?', 'answer' => 'Pri presoji izdelkov, sestavin in pričakovane funkcije rastline.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Predpostaviti, da vse, kar velja za aloe, velja tudi za agavo.'],
            ],
            'Aloe vera ali agava: kako ju razlikovati in se izogniti najpogostejši zmedi',
            'Razumite razliko med aloe vero in agavo ter kje nastanejo najpogostejše zmote o njuni vlogi in uporabi.',
            'Aloe vera vs agava',
            [
                ['heading' => 'Zakaj vizualna podobnost hitro ustvari mite', 'html' => '<p>Ljudje rastline pogosto najprej povezujemo po videzu, kar hitro ustvari zmedo v naravnih in wellness vsebinah. Jasnost se običajno začne pri tem, za kaj je posamezna rastlina v resnici uporabljena.</p>'],
                ['heading' => 'Zakaj je pomemben kontekst sestavine', 'html' => '<p>Naraven jezik postane veliko bolj uporaben, ko je konkreten. Bolj ko razumemo vlogo rastline v izdelku ali rutini, manj verjetno je, da bomo eno zgodbo zamenjali z drugo.</p>'],
            ]
        ),
        $entry(
            588,
            'Čustvena inteligenca: kako razvijati samozavedanje brez obstanka v teoriji',
            'custvena-inteligenca-samozavedanje-in-obvladovanje-odzivov',
            'Čustvena inteligenca ni le prijeten izraz za osebno rast, ampak skupek veščin, ki postanejo koristne šele v resničnih odnosih in pritiskih. Tukaj je, kako se je lotiti tako, da res spremeni vedenje.',
            '<ul><li>Čustvena inteligenca največ pomeni takrat, ko izboljša odnose, meje in vsakodnevne odzive pod pritiskom.</li><li>Največja napaka je ostati pri teoriji ali osebnostnih etiketah brez praktične uporabe.</li><li>Pametnejši pristop gradi samozavedanje, boljši jezik za čustva in bolj zavestne odzive.</li></ul>',
            [
                ['question' => 'Kaj je čustvena inteligenca v praksi?', 'answer' => 'Sposobnost, da bolje razumete čustva in se nanje bolj spretno odzovete.'],
                ['question' => 'Ali jo je mogoče razvijati?', 'answer' => 'Da, s samoopazovanjem, refleksijo in bolj zavestno komunikacijo.'],
                ['question' => 'Zakaj je samozavedanje tako ključno?', 'answer' => 'Ker brez njega čustva vplivajo na odločitve, še preden jih sploh jasno razumemo.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Čustveno inteligenco obravnavati kot etiketo namesto kot vsakodnevno veščino.'],
            ],
            'Čustvena inteligenca: kako jo pretvoriti v uporabno vsakodnevno veščino',
            'Spoznajte, kako razvijati čustveno inteligenco skozi samozavedanje, boljšo komunikacijo in mirnejše odzive.',
            'Čustvena inteligenca',
            [
                ['heading' => 'Zakaj čustvena veščina pomeni največ v resničnih situacijah', 'html' => '<p>Večina ljudi osnovne čustvene pojme razume veliko prej, preden jih zna dobro uporabljati. Resnična sprememba se običajno zgodi v težjih pogovorih, pod pritiskom in v ponavljajočih se vzorcih vsakdanjega življenja.</p>'],
                ['heading' => 'Zakaj boljši jezik spremeni vedenje', 'html' => '<p>Ko človek zna čustva natančneje poimenovati, ga ta pogosto manj vodijo avtomatsko. Jasnejši jezik običajno odpre več prostora za izbiro in manj za impulziven odziv.</p>'],
            ]
        ),
        $entry(
            591,
            'Jetra in alkohol: kako razmišljati o okrevanju brez mitov o hitrem detoksu',
            'jetra-in-alkohol-koraki-za-okrevanje-in-kako-aloe-pomaga',
            'O jetrih pogosto začnemo razmišljati šele po obdobju pretiravanja, prav takrat pa marketing ponuja največ čudežnih rešitev. Tukaj je, kako o okrevanju razmišljati skozi ritem, hidracijo in bolj realno podporo namesto skozi instant detoks jezik.',
            '<ul><li>Okrevanje po alkoholu je najbolj odvisno od časa, počitka, hidracije in mirnejše rutine, ne od enega čudežnega izdelka.</li><li>Največja napaka je iskati hitro “čiščenje jeter” brez spremembe vzorca, ki je obremenitev ustvaril.</li><li>Pametnejši pristop uporablja preproste korake okrevanja in bolj realna pričakovanja.</li></ul>',
            [
                ['question' => 'Ali si jetra lahko opomorejo?', 'answer' => 'Pogosto da, vendar je to odvisno od vzorcev vedenja, časa in širše podpore telesu.'],
                ['question' => 'Ali obstaja hiter detoks, ki vse popravi?', 'answer' => 'Ne. Veliko več pomenijo čas, počitek in spremembe navad.'],
                ['question' => 'Kje lahko pride aloe v poštev?', 'answer' => 'Le kot majhen del širše rutine, ne kot glavni odgovor.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Ponavljati isti vzorec in pričakovati, da bo izdelek izbrisal posledice.'],
            ],
            'Jetra in alkohol: kako o okrevanju razmišljati brez instant detoks iluzij',
            'Odkrijte, kako po alkoholu podpreti okrevanje jeter z bolj realnimi koraki namesto čudežnih detoks obljub.',
            'Jetra in alkohol',
            [
                ['heading' => 'Zakaj zgodbe o okrevanju privlačijo pretiravanje', 'html' => '<p>Po težjem obdobju si ljudje naravno želimo hitro popravilo, zato detoks marketing pogosto zveni še posebej prepričljivo. V praksi se telo veliko bolj opira na čas, počitek in mirnejšo podporo kot na dramatične izdelke.</p>'],
                ['heading' => 'Zakaj sprememba rutine pomeni več kot “reševanje”', 'html' => '<p>Najbolj uporaben pogovor o jetrih običajno ni o enem izdelku, ampak o vzorcu, ki je telo obremenil. Boljši ritem in manj ponavljanja istega vedenja pogosto prineseta več kot večje obljube.</p>'],
            ]
        ),
        $entry(
            641,
            'Kako izbrati zanesljive proizvajalce superživil: certifikati, sledljivost in varnost',
            'kako-izbrati-zanesljive-proizvajalce-superzivil',
            'Superživila zvenijo impresivno, dokler kakovost izdelka ne začne nihati bolj, kot obljublja etiketa. Tukaj je, kako proizvajalce, certifikate in poreklo oceniti z več previdnosti in manj zaupanja v lepo embalažo.',
            '<ul><li>Pri superživilih kakovost proizvajalca in sledljivost pogosto pomenita več kot sama kategorija izdelka.</li><li>Največja napaka je kupovati zgolj po oznaki “superfood” brez preverjanja izvora in standardov.</li><li>Pametnejši pristop gleda transparentnost, certifikate in daljši ugled znamke.</li></ul>',
            [
                ['question' => 'Zakaj so certifikati pomembni?', 'answer' => 'Ker pomagajo oceniti standarde proizvodnje, varnost in doslednost.'],
                ['question' => 'Ali je beseda “superživilo” dovolj?', 'answer' => 'Ne. Pogosto pove več o marketingu kot o resnični kakovosti.'],
                ['question' => 'Kaj še velja preveriti?', 'answer' => 'Izvor, sledljivost in to, kako jasno znamka razlaga svoje kontrole kakovosti.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Lepo embalažo zamenjati za dokaz prave kakovosti.'],
            ],
            'Superživila in varnost: kako prepoznati bolj zanesljivega proizvajalca',
            'Preverite, kako z več gotovosti izbirati superživila skozi certifikate, sledljivost in transparentnost.',
            'Zanesljiva superživila',
            [
                ['heading' => 'Zakaj se kakovost začne daleč pred polico', 'html' => '<p>Ljudje superživilo pogosto presojamo po imenu, vendar se prava kakovostna zgodba začne pri izvoru, testiranju in standardih proizvodnje. Prav ta skriti del pogosto pomeni veliko več kot sprednja etiketa.</p>'],
                ['heading' => 'Zakaj je transparentnost praktičen znak varnosti', 'html' => '<p>Znamke, ki jasno povedo, kaj delajo in zakaj, kupcu običajno ponudijo več resničnih informacij. Dobra transparentnost ne pomeni popolnosti, pogosto pa loči resnega proizvajalca od praznega marketinga.</p>'],
            ]
        ),
        $entry(
            644,
            'Težave s cirkulacijo: kje lahko masaža in naravni geli res pomagajo',
            'tezave-s-cirkulacijo-masaza-in-naravni-geli',
            'Težave s cirkulacijo se pogosto začnejo kot občutek teže, hladnih nog ali utrujenosti in jih je lahko predolgo prezreti. Tukaj je, kako masažo in naravne gele razumeti kot praktično podporo, ne kot čarobno rešitev za vse.',
            '<ul><li>Pri težkih nogah in občutku slabše cirkulacije majhne ponovljive navade pogosto pomagajo bolj kot občasni poskusi.</li><li>Največja napaka je čakati, da nelagodje postane močno, preden uvedete gibanje ali podporo.</li><li>Pametnejši pristop združuje masažo, lažje gibanje, hlajenje in nego kože v preprosto rutino.</li></ul>',
            [
                ['question' => 'Ali lahko masaža in geli pomagajo?', 'answer' => 'Lahko pomagajo k večjemu udobju in občutku lažjih nog, posebej ob redni uporabi.'],
                ['question' => 'Ali to zadostuje za vse težave s cirkulacijo?', 'answer' => 'Ne. Gre za podporna orodja za udobje, ne za rešitev vseh vzrokov.'],
                ['question' => 'Kaj še običajno pomaga?', 'answer' => 'Več gibanja, manj dolgotrajnega sedenja ali stanja in boljši dnevni ritem.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Uporabiti en podporni izdelek brez spremembe navad, ki težavo ohranjajo.'],
            ],
            'Cirkulacija in masaža: kje imajo naravni geli smisel za vsakodnevno udobje',
            'Spoznajte, kako lahko masaža in naravni geli pomagajo pri občutku težkih nog in slabše cirkulacije.',
            'Težave s cirkulacijo',
            [
                ['heading' => 'Zakaj se udobje pogosto izboljša s ponavljanjem', 'html' => '<p>Ljudem z občutkom težkih nog pogosto najbolj pomagajo zelo majhne, a redne navade. Ponavljajoča podpora običajno prinese več olajšanja kot redek in intenziven poskus.</p>'],
                ['heading' => 'Zakaj podpora najbolje deluje skupaj z gibanjem', 'html' => '<p>Masaža in geli lahko telo res pomirijo, vendar najbolje delujejo ob več gibanja in manj statične obremenitve. Podpora je močnejša, ko postane del vzorca, ne osamljen poskus.</p>'],
            ]
        ),
        $entry(
            647,
            'Antioksidativna moč aloe vere: kaj je vredno vedeti brez prehoda v hype',
            'antioksidativna-moc-aloe-vere-vitamini-polifenoli-in-zdravje',
            'Aloe vera je zanimiva tako zaradi sestave kot zaradi dolge tradicije uporabe, vendar antioksidativne trditve hitro zdrsnejo v pretiravanje. Tukaj je, kako o teh spojinah razmišljati bolj realno skozi nego, prehrano in podporni kontekst.',
            '<ul><li>Antioksidativna zgodba aloe je najbolj uporabna, ko ostane povezana s praktično nego in realno podporo.</li><li>Največja napaka je vsako zanimivo spojino spremeniti v veliko obljubo.</li><li>Pametnejši pristop aloe postavi v okvir nege, podpore in realnih meja ene sestavine.</li></ul>',
            [
                ['question' => 'Zakaj se aloe povezuje z antioksidanti?', 'answer' => 'Ker vsebuje spojine, ki so zanimive v raziskavah in praktični uporabi.'],
                ['question' => 'Ali to pomeni, da aloe reši vse?', 'answer' => 'Ne. Uporabna je v določenih okvirih, ni pa univerzalni odgovor.'],
                ['question' => 'Kje ima ta zgodba največ smisla?', 'answer' => 'V negi, podpori in bolj informiranem razumevanju sestave izdelkov.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Pretiravati z obljubami samo zato, ker profil sestavin zveni impresivno.'],
            ],
            'Aloe in antioksidanti: kako ločiti koristno dejstvo od wellness hypea',
            'Razumite antioksidativno zgodbo aloe vere in kje ima smisel brez pretiranih zdravstvenih trditev.',
            'Aloe in antioksidanti',
            [
                ['heading' => 'Zakaj zanimiva kemija še ne pomeni neomejene obljube', 'html' => '<p>Ko neka sestavina zveni znanstveno zanimivo, jo marketing zelo hitro raztegne čez realne meje. Aloe postane veliko bolj uporabna, ko njene lastnosti beremo znotraj praktičnega in omejenega konteksta.</p>'],
                ['heading' => 'Zakaj podpora ni isto kot čarovnija', 'html' => '<p>Podporne sestavine so lahko zelo koristne, vendar najbolje delujejo takrat, ko od njih ne zahtevamo vsega naenkrat. Prava vrednost je pogosto veliko jasnejša, ko so pričakovanja bolj mirna.</p>'],
            ]
        ),
        $entry(
            649,
            'Sladki koren (licorice): naravni zaveznik za prebavo in dihala ali še en precenjen klasik',
            'sladki-koren-naravni-zaveznik-za-prebavo-in-dihala',
            'Sladki koren je zanimiv, ker povezuje tradicionalno uporabo s sodobnim zanimanjem za prebavo in dihala, a ga prav ta zgodba lahko hitro preveč povzdigne. Tukaj je, kako ga oceniti bolj realistično.',
            '<ul><li>Sladki koren je lahko smiseln kot del podporne rutine za grlo, prebavo in občutek umiritve sluznice.</li><li>Največja napaka je iz njega narediti univerzalni zeliščni odgovor, ne da bi preverili, kdaj zares ustreza.</li><li>Pametnejši pristop gleda tradicijo, način uporabe in širši kontekst simptoma.</li></ul>',
            [
                ['question' => 'Zakaj se sladki koren pogosto omenja za grlo in prebavo?', 'answer' => 'Ker ima dolgo tradicijo uporabe za pomiritev in občutek ugodja.'],
                ['question' => 'Ali ustreza vsakomur?', 'answer' => 'Ne nujno. Tako kot pri drugih rastlinah sta pomembna kontekst in način uporabe.'],
                ['question' => 'Kdaj ima več smisla?', 'answer' => 'Ko je uporabljen zmerno, ciljano in premišljeno.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Rastlini pripisati več moči, kot jo v praksi realno lahko nosi.'],
            ],
            'Sladki koren za prebavo in grlo: kje pomaga in kje se začne pretiravanje',
            'Odkrijte, kdaj ima sladki koren smisel za prebavo in dihalno udobje ter kako ga oceniti bolj realno.',
            'Sladki koren',
            [
                ['heading' => 'Zakaj tradicionalna zelišča hitro dobijo prevelika pričakovanja', 'html' => '<p>Ko ima rastlina dolgo zgodovino uporabe, ljudje nanjo hitro dodamo še sodoben jezik čudežnih učinkov. Sladki koren je veliko bolj smiseln, ko ga vidimo kot podporo, ne kot popoln odgovor.</p>'],
                ['heading' => 'Zakaj kontekst odloča o koristnosti', 'html' => '<p>Rastline redko delujejo smiselno v praznini. Njihova praktična vrednost je veliko jasnejša, ko skupaj gledamo simptom, rutino in razlog za uporabo.</p>'],
            ]
        ),
        $entry(
            651,
            'Kalcij in vitamin D: zakaj je njuna sinergija pomembna za kosti in zobe',
            'kalcij-in-vitamin-d-sinergija-za-mocne-kosti-in-zobe',
            'Kalcij in vitamin D se pogosto omenjata skupaj in ta povezava ima smisel le znotraj širše zgodbe o zdravju kosti in vsakodnevnih navadah. Tukaj je, kako to sinergijo razumeti brez poenostavljanja.',
            '<ul><li>Kalcij in vitamin D imata logično povezavo, ker eden pogosto nima polnega smisla brez drugega.</li><li>Največja napaka je obravnavati ju kot ločena “čarobna” hranila namesto kot del sistema.</li><li>Pametnejši pristop vključuje prehrano, gibanje, sonce in dnevne navade, ne le tablete ali kapsule.</li></ul>',
            [
                ['question' => 'Zakaj se kalcij in vitamin D omenjata skupaj?', 'answer' => 'Ker sta tesno povezana pri podpori kostem in širšem mineralnem ravnovesju.'],
                ['question' => 'Ali zadostuje jemati samo enega?', 'answer' => 'Ne vedno. Njuna vrednost je pogosto odvisna od širšega prehranskega in življenjskega konteksta.'],
                ['question' => 'Kaj še vpliva na zdravje kosti?', 'answer' => 'Gibanje, kakovost hrane, sonce in dolgoročne navade.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Zdravje kosti zreducirati na enega ali dva dodatka brez širše slike.'],
            ],
            'Kalcij in vitamin D: kako njuna sinergija res podpira kosti in zobe',
            'Razumite, zakaj sta kalcij in vitamin D pomembna skupaj in kako ju vključiti v širšo rutino za kosti.',
            'Kalcij in vitamin D',
            [
                ['heading' => 'Zakaj je sinergija hranil pomembnejša od izolirane logike dodatkov', 'html' => '<p>Ljudje pogosto želimo preprost odgovor, telo pa običajno deluje skozi odnose med hranili in ne skozi izolirane zgodbe. Kalcij in vitamin D imata največ smisla prav v tem širšem sistemu.</p>'],
                ['heading' => 'Zakaj navade še vedno odločajo o izidu', 'html' => '<p>Dodatki lahko podprejo zdravje kosti, ne morejo pa nadomestiti gibanja, sonca in kakovostne hrane. Najmočnejša strategija je skoraj vedno širša od same stekleničke ali tablete.</p>'],
            ]
        ),
        $entry(
            885,
            'Perfekcionizem in odlašanje: kako prekiniti zanko v 7 bolj realnih korakih',
            'perfekcionizem-in-odlasanje-kako-prekiniti-zanko',
            'Perfekcionizem in odlašanje pogosto delujeta kot ločena problema, v praksi pa se hranita drug z drugim. Tukaj je, kako ta vzorec prepoznati in ga razbiti brez novega nerealnega načrta samopopravka.',
            '<ul><li>Perfekcionizem pogosto ni znak discipline, temveč strahu pred nepopolnim začetkom ali izidom.</li><li>Največja napaka je poskušati odlašanje rešiti z še strožjimi in večjimi načrti.</li><li>Pametnejši pristop zniža prag začetka, vrne akcijo v manjše korake in spremeni odnos do napake.</li></ul>',
            [
                ['question' => 'Kako sta perfekcionizem in odlašanje povezana?', 'answer' => 'Perfekcionizem pogosto poveča pritisk, pritisk pa nato hrani odlašanje in izogibanje.'],
                ['question' => 'Zakaj strožji načrti pogosto ne pomagajo?', 'answer' => 'Ker običajno še povečajo pritisk in naredijo začetek še težji.'],
                ['question' => 'Kaj najbolj pomaga?', 'answer' => 'Manjši začetni koraki, bolj realni standardi in akcija pred idealnimi pogoji.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Perfekcionizem zamenjati za kakovost in tako ohranjati isti krog.'],
            ],
            'Perfekcionizem in odlašanje: kako zapustiti isti vzorec brez novih iluzij',
            'Spoznajte, kako prekiniti povezavo med perfekcionizmom in odlašanjem z manjšimi koraki in bolj realnimi merili.',
            'Perfekcionizem in odlašanje',
            [
                ['heading' => 'Zakaj se pritisk pogosto skriva za visokimi merili', 'html' => '<p>Perfekcionizem lahko od zunaj izgleda občudovanja vredno, v notranjosti pa pogosto ustvarja napetost, oklevanje in zamik. Ko ta pritisk postane viden, je vzorec veliko lažje obravnavati bolj pošteno.</p>'],
                ['heading' => 'Zakaj manjši začetki ustvarijo več gibanja', 'html' => '<p>Ljudje, ujeti v odlašanje, običajno ne potrebujejo večjega ideala. Veliko bolj pogosto potrebujejo manjši vstop v akcijo, takšnega, ki omogoča začetek še preden je samozavest popolna.</p>'],
            ]
        ),
    ],
];
