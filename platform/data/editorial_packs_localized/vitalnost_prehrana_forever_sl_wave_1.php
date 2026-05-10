<?php

declare(strict_types=1);

$entry = static fn (
    int $sourceId,
    string $languageCode,
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
    'language_code' => $languageCode,
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
    'key' => 'vitalnost-prehrana-forever-sl-wave-1',
    'name' => 'Vitalnost, prehrana in Forever vodiči (SL) - prvi val',
    'notes' => 'Večji lokalizirani uredniški val za vitalnost, prehrano, migrene, sezonsko podporo in izbrane Forever vodiče.',
    'entries' => [
        $entry(
            141,
            'sl',
            'Aloe vera in telo: kje imajo hidrolat in nežne rutine res smiseln prostor',
            'aloe-vera-in-telo-kje-imata-hidrolat-in-nezna-rutina-res-smisel',
            'O aloe veri se veliko govori za notranjo in zunanjo uporabo, največ koristi pa prinese takrat, ko sta izdelek in pričakovanje jasno določena. Tukaj je, kako o aloe veri razmišljati skozi kožo, rutino in praktično vsakodnevno uporabo.',
            '<ul><li>Aloe vera najbolje deluje, ko ima v rutini jasno mesto in namen.</li><li>Največja napaka je mešati različne oblike in namene izdelkov brez razumevanja, čemu so namenjeni.</li><li>Pametnejši pristop izbere formulo glede na kožo, potrebo in resničen način uporabe.</li></ul>',
            [
                ['question' => 'Ali je vsak aloe izdelek enak?', 'answer' => 'Ne. Sestava, obdelava in namen lahko močno spremenijo izkušnjo in uporabnost.'],
                ['question' => 'Kdaj ima aloe vera največ smisla?', 'answer' => 'Ko koža potrebuje nežnejšo podporo, občutek svežine ali rutino, ki jo je mogoče ponavljati.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Od aloe vere pričakovati učinek, ki ga v resnici zahteva širši načrt nege ali okrevanja.'],
                ['question' => 'Kako izbirati bolj pametno?', 'answer' => 'Po namenu, sestavi in po tem, kako dobro se izdelek ujema z vašo rutino.'],
            ],
            'Aloe vera in telo: kje res pomaga in kje marketing pretirava',
            'Spoznajte, kje ima aloe vera res smisla za nego in vsakodnevno udobje ter kako jo uporabljati bolj realno.',
            'Aloe in telo',
            [
                ['heading' => 'Zakaj aloe potrebuje jasno vlogo', 'html' => '<p>Aloe vera je veliko bolj uporabna, ko natančno vemo, zakaj jo uporabljamo. Najboljše rutine ji običajno dajo osredotočeno vlogo namesto pričakovanja, da bo rešila vse naenkrat.</p>'],
                ['heading' => 'Zakaj je oblika izdelka pomembnejša od same besede aloe', 'html' => '<p>Različne oblike aloe vere služijo različnim namenom. Zato je razumevanje funkcije izdelka pogosto veliko koristnejše od zaupanja samo oznaki na embalaži.</p>'],
            ]
        ),
        $entry(
            150,
            'sl',
            'Kako pojesti 5 porcij sadja in zelenjave na dan brez občutka stroge diete',
            'kako-pojesti-5-porcij-sadja-in-zelenjave-na-dan-brez-obcutka-diete',
            'Pet porcij sadja in zelenjave na dan zveni preprosto, dokler ne prideta vmes služba, nakupovanje in priprava obrokov. Tukaj je, kako to navado narediti bolj lahkotno, cenovno izvedljivo in res uporabno za navaden teden.',
            '<ul><li>Več sadja in zelenjave najlažje pride v prehrano skozi male ponovljive navade, ne skozi popolne jedilnike.</li><li>Največja napaka je poskusiti vse urediti naenkrat in nato hitro odnehati, ko teden postane naporen.</li><li>Pametnejši pristop ustvari nekaj predvidljivih trenutkov v dnevu, kjer sadje in zelenjava prideta skoraj samoumevno.</li></ul>',
            [
                ['question' => 'Kaj šteje kot porcija?', 'answer' => 'Porcija je lahko pest zelenjave, kos sadja ali podobna praktična količina, ki jo z lahkoto spremljate.'],
                ['question' => 'Ali mora biti vse sveže?', 'answer' => 'Ne. Zamrznjene in hitro pripravljene možnosti pogosto naredijo navado veliko bolj vzdržno.'],
                ['question' => 'Kako to olajšati družini?', 'answer' => 'S preprostimi kombinacijami, ki se ponavljajo in ne ustvarjajo dodatnega stresa pri pripravi.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kupiti preveč brez načrta in nato veliko hrane zavreči.'],
            ],
            '5 porcij sadja in zelenjave na dan: kako jih vključiti lažje in brez stresa',
            'Odkrijte, kako z majhnimi navadami lažje priti do 5 porcij sadja in zelenjave vsak dan.',
            '5 porcij na dan',
            [
                ['heading' => 'Zakaj je tukaj doslednost pomembnejša od ambicije', 'html' => '<p>Večina ljudi ne potrebuje dodatnega prepričevanja, da sta sadje in zelenjava koristna. Pravi izziv je, kako ju narediti dovolj enostavna, da ostaneta del vsakdana tudi v napornem tednu.</p>'],
                ['heading' => 'Zakaj malo strukture zmanjša odpad in frustracijo', 'html' => '<p>Preprost načrt običajno pomeni manj pozabljenih živil in več uporabe tega, kar kupimo. Nekaj stalnih obrokov je pogosto veliko močnejših od velikih namer brez sistema.</p>'],
            ]
        ),
        $entry(
            151,
            'sl',
            'Napolnite baterije: kako se lotiti kronične utrujenosti brez hitrih trikov',
            'napolnite-baterije-kako-se-lotiti-kronicne-utrujenosti-brez-hitrih-trikov',
            'Kronična utrujenost redko nastane zaradi enega samega vzroka in še redkeje izgine zaradi enega hitrega popravka. Tukaj je, kako prepoznati globlje vzorce izčrpanosti in kje lahko prehrana, ritem dneva in podpora telesu zares pomagajo.',
            '<ul><li>Kronična utrujenost je pogosto posledica več hkratnih pritiskov, ne enega samega manjka.</li><li>Največja napaka je utrujenost gasiti samo s kofeinom in kratkimi naleti motivacije.</li><li>Pametnejši pristop pogleda spanec, stres, obroke in to, koliko prostora za okrevanje sploh obstaja.</li></ul>',
            [
                ['question' => 'Zakaj se utrujenost vedno vrača?', 'answer' => 'Ker vzrok pogosto ni eden, ampak kombinacija spanja, stresa, prehrane in slabega okrevanja.'],
                ['question' => 'Ali dodatki pomagajo?', 'answer' => 'Včasih, vendar šele takrat, ko bolje razumemo glavne vzorce in obremenitve.'],
                ['question' => 'Kaj najprej opazovati?', 'answer' => 'Spanec, nihanje energije, obroke, gibanje in znake, da telo nikoli ne pride do prave regeneracije.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Globoko izčrpanost zamenjati za pomanjkanje discipline in jo predolgo ignorirati.'],
            ],
            'Kronična utrujenost: kako vrniti več energije brez hitrih obljub',
            'Razumite večje vzorce kronične utrujenosti in kako skozi navade vrniti bolj stabilno energijo.',
            'Kronična utrujenost',
            [
                ['heading' => 'Zakaj je utrujenost pogosto vprašanje vzorcev', 'html' => '<p>Mnogi ljudje iščemo eno samo razlago, vendar se dolgotrajna utrujenost običajno gradi iz več manjših pritiskov. Prav zato boljša energija pogosto pride iz prepoznavanja vzorcev in ne iz enega samega dodatka.</p>'],
                ['heading' => 'Zakaj mora okrevanje postati del sistema', 'html' => '<p>Energija se redko vrne samo zato, ker si jo močno želimo. Telo običajno potrebuje resničen prostor za spanec, počitek, prehrano in manj notranjega trenja.</p>'],
            ]
        ),
        $entry(
            153,
            'sl',
            'Kuhanje z aloe vero: 3 nenavadni recepti, ki še vedno ostanejo smiselni',
            'kuhanje-z-aloe-vero-3-nenavadni-recepti-ki-se-vedno-delujejo',
            'Kuhanje z aloe vero lahko zveni eksotično ali po nepotrebnem zapleteno, vendar je lahko zanimivo, če jo uporabljamo zmerno in z jasno kulinarično idejo. Tukaj so trije receptni pristopi, ki združujejo radovednost, okus in praktičnost.',
            '<ul><li>Aloe vera v kuhinji najbolje deluje, ko recept dopolni, ne pa ko želi prevladati nad vsem.</li><li>Največja napaka je pretiravati s količino ali jo siliti v jedi, kjer okus in tekstura ne delujeta.</li><li>Pametnejši pristop ohrani recepte preproste in aloe veri dodeli podporno vlogo.</li></ul>',
            [
                ['question' => 'Ali se aloe vera res lahko uporablja pri kuhanju?', 'answer' => 'Lahko, če je uporabljena v pravi obliki in v receptih, kjer ima kulinaričen smisel.'],
                ['question' => 'Katere jedi najbolje delujejo?', 'answer' => 'Lažje kombinacije in bolj sveži recepti, kjer aloe ostane subtilna.'],
                ['question' => 'Katera je najpogostejša napaka?', 'answer' => 'Uporabiti je preveč in nenavadno sestavino spremeniti v celo zgodbo jedi.'],
                ['question' => 'Ali morajo biti recepti zapleteni?', 'answer' => 'Ne. Najboljši so tisti, ki jih je mogoče pripraviti tudi v običajnem tednu.'],
            ],
            'Kuhanje z aloe vero: 3 recepti, ki ostanejo zanimivi in hkrati izvedljivi',
            'Preizkusite tri preprostejše smeri kuhanja z aloe vero brez izgube okusa in praktičnosti.',
            'Kuhanje z aloe'
            ,
            [
                ['heading' => 'Zakaj aloe najbolje deluje kot podporna sestavina', 'html' => '<p>V hrani ima aloe vera največ smisla, ko doda subtilen ton in ne postane celotna ideja jedi. Tako praviloma ostaneta bolj prijetna tudi okus in tekstura.</p>'],
                ['heading' => 'Zakaj radovednost še vedno potrebuje mero', 'html' => '<p>Kuhinjski poskusi so lahko zabavni, vendar največ koristi prinesejo tisti, ki jih je mogoče ponoviti. Mirnejši pristop pogosto naredi nenavadne sestavine veliko bolj uporabne.</p>'],
            ]
        ),
        $entry(
            161,
            'sl',
            'Najboljši Forever paketi za začetnike: kako izbrati tistega, ki vam res ustreza',
            'najboljsi-forever-paketi-za-zacetnike-kako-izbrati-pravi-zacetek',
            'Začetni paket ima smisel samo, če se ujema z vašim ciljem, proračunom in navadami, ki jih boste res ohranili. Tukaj je, kako izbrati Forever paket brez preobremenitve in brez občutka, da morate začeti z vsem naenkrat.',
            '<ul><li>Najboljši začetni paket ni največji, ampak tisti, ki ga boste uporabljali dovolj dosledno.</li><li>Največja napaka je izbirati po tujem navdušenju namesto po svojem cilju in vsakdanu.</li><li>Pametnejši pristop izbira glede na energijo, prebavo, nego ali enostaven vstop v rutino.</li></ul>',
            [
                ['question' => 'Po čem izbrati začetni paket?', 'answer' => 'Po resničnem cilju, proračunu in navadah, ne pa po sami velikosti paketa.'],
                ['question' => 'Ali začetniki potrebujejo več izdelkov hkrati?', 'answer' => 'Običajno ne. Manjši in bolj jasen začetek je pogosto boljši od preobremenitve.'],
                ['question' => 'Ali je en paket pravi za vse?', 'answer' => 'Ne. Uporabnost je odvisna od osebe, cilja in tega, kar bo dejansko uporabljala.'],
                ['question' => 'Katera je pogosta napaka začetnikov?', 'answer' => 'Kupiti preveč in nato redno uporabljati le manjši del.'],
            ],
            'Forever paketi za začetnike: kako izbrati pametneje brez kupovanja preveč',
            'Spoznajte, kako izbrati Forever paket za začetek glede na cilj, proračun in rutino, ki jo boste res ohranili.',
            'Forever paketi',
            [
                ['heading' => 'Zakaj je najboljši začetek običajno najjasnejši začetek', 'html' => '<p>Začetniki pogosto čutimo pritisk, da moramo izbrati večji paket, da bi “začeli prav”. V resnici pa dolgoročna uporaba veliko bolj temelji na jasnosti in ujemanju kot na številu izdelkov v škatli.</p>'],
                ['heading' => 'Zakaj so navade pomembnejše od velikosti paketa', 'html' => '<p>Izdelek ustvari vrednost šele takrat, ko postane del resničnega življenja. Zato je najboljši začetni paket običajno tisti, ki ga uporabnik dejansko razume in redno uporablja.</p>'],
            ]
        ),
        $entry(
            162,
            'sl',
            'Kolagen in Forever Marine Collagen: kje so koristi realne in kje hype premočan',
            'kolagen-in-forever-marine-collagen-kje-so-koristi-realne',
            'Kolagen je eden najbolj priljubljenih dodatkov za kožo in sklepe, vendar pričakovanja pogosto prerastejo to, kar lahko dodatek v resnici naredi. Tukaj je, kako o Marine Collagenu razmišljati skozi rutino, doslednost in širšo podporo telesu.',
            '<ul><li>Kolagen ima lahko smisel kot del širše podpore koži, sklepom in vezivnim tkivom.</li><li>Največja napaka je pričakovati dramatične spremembe brez časa, doslednosti in osnovne skrbi za telo.</li><li>Pametnejši pristop kolagen poveže s hidracijo, beljakovinami in navadami, ki res ščitijo kožo in sklepe.</li></ul>',
            [
                ['question' => 'Ali kolagen lahko pomaga koži?', 'answer' => 'Lahko kot del rutine, posebej če je uporaba dovolj redna in podprta še z drugimi dobrimi navadami.'],
                ['question' => 'Ali ima smisel tudi za sklepe?', 'answer' => 'Lahko, zlasti če je del širšega načrta za gibanje, okrevanje in podporo tkivom.'],
                ['question' => 'Kdaj pričakovati rezultat?', 'answer' => 'Ne takoj. Pri takih dodatkih sta pomembna čas in doslednost.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Od kolagena pričakovati učinek, medtem ko spanec, beljakovine in osnove nege ostajajo slabi.'],
            ],
            'Marine Collagen: realna pričakovanja za kožo, sklepe in vsakodnevno uporabo',
            'Poglejte, kje ima kolagen smisel za kožo in sklepe ter kako ga uporabljati z bolj realnimi pričakovanji.',
            'Marine Collagen',
            [
                ['heading' => 'Zakaj kolagen najbolje deluje znotraj širše rutine', 'html' => '<p>Dodatki pogosto dobijo preveč zaslug ali krivde sami po sebi. V praksi kolagen največ pomeni takrat, ko ga spremljajo hidracija, dovolj beljakovin in vsakodnevna skrb za telo.</p>'],
                ['heading' => 'Zakaj je potrpežljivost pomembnejša od hypea', 'html' => '<p>Podpora s kolagenom skoraj nikoli ni dramatična čez noč. Veliko bolj smiselno ga je gledati kot del daljše rutine kot pa kot hitro lepotno ali sklepno rešitev.</p>'],
            ]
        ),
        $entry(
            163,
            'sl',
            'Forever in športniki: ali lahko profesionalci FLP dodatke uporabljajo pametno',
            'forever-in-sportniki-ali-lahko-profesionalci-flp-dodatke-uporabljajo-pametno',
            'Športniki potrebujejo več jasnosti in varnosti kot povprečni kupec dodatkov, saj mora imeti vsak izdelek smisel v resničnem trening okolju. Tukaj je, kako Forever dodatke gledati skozi okrevanje, rutino in bolj odgovorno izbiro.',
            '<ul><li>Dodatki za športnike imajo smisel le, če rešujejo resnično potrebo in stojijo na močni osnovi treninga in prehrane.</li><li>Največja napaka je, da dodatki postanejo glavna strategija namesto podpornega orodja.</li><li>Pametnejši pristop uporablja manj izdelkov, z jasnejšim namenom in varnejšo rutino.</li></ul>',
            [
                ['question' => 'Ali športniki potrebujejo posebne dodatke?', 'answer' => 'Včasih da, vendar šele takrat, ko obstaja jasna potreba in so osnove že urejene.'],
                ['question' => 'Ali so vsi izdelki koristni za vsakega športnika?', 'answer' => 'Ne. Potrebe so odvisne od športa, obremenitve in cilja.'],
                ['question' => 'Kaj pomeni več kot dodatki?', 'answer' => 'Kakovost treninga, okrevanje, spanec in osnovna prehrana.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Posnemanje rutine drugih športnikov brez razumevanja lastnih potreb.'],
            ],
            'Forever in športniki: kako FLP dodatke izbirati bolj pametno in varno',
            'Preverite, kako lahko športniki Forever dodatke uporabljajo bolj odgovorno skozi jasen namen in dobre osnove.',
            'Forever in šport',
            [
                ['heading' => 'Zakaj športniki potrebujejo več natančnosti, ne več izdelkov', 'html' => '<p>Športna rutina je močnejša, ko ima vsak element jasen namen. Prav zato športnikom pogosto veliko bolj koristi premišljena izbira kot dodajanje vedno novih izdelkov.</p>'],
                ['heading' => 'Zakaj okrevanje še vedno vodi celo zgodbo', 'html' => '<p>Tudi dobri dodatki ne morejo nadomestiti slabega okrevanja. Trening prilagoditev je praviloma odvisna predvsem od spanja, prehrane in sposobnosti telesa, da opravljeno delo res predela.</p>'],
            ]
        ),
        $entry(
            165,
            'sl',
            'Naravna pomoč pri migrenah: aloe, magnezij in riboflavin v pametnejšem načrtu',
            'naravna-pomoc-pri-migrenah-aloe-magnezij-in-riboflavin',
            'Podpora migrenam najbolje deluje, ko razumemo sprožilce in ritem, ne pa ko naenkrat preizkusimo vse “naravno”. Tukaj je, kako magnezij, riboflavin in dnevni ritem vključiti v bolj strukturiran načrt podpore.',
            '<ul><li>Naravna podpora migrenam najbolj pomaga, ko jo povežemo s sprožilci, spanjem in stresom.</li><li>Največja napaka je reagirati le na napad, ne pa tudi na vzorec, ki do njega vodi.</li><li>Pametnejši pristop poveže preventivo, beleženje simptomov in bolj ciljno podporo.</li></ul>',
            [
                ['question' => 'Ali lahko magnezij in riboflavin pomagata?', 'answer' => 'Pri nekaterih ljudeh sta lahko koristna kot del bolj dosledne in ciljne podpore.'],
                ['question' => 'Zakaj je spremljanje migren pomembno?', 'answer' => 'Ker postane podpora veliko bolj uporabna, ko so sprožilci in vzorci bolj jasni.'],
                ['question' => 'Ali je dovolj reagirati šele ob napadu?', 'answer' => 'Običajno ne. Preventiva in bolj stabilen ritem sta pogosto enako pomembna kot akutna pomoč.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Naenkrat zamenjati preveč stvari in nato ne vedeti, kaj je sploh pomagalo.'],
            ],
            'Migrene: podpora z magnezijem, riboflavinom in bolj pametno preventivo',
            'Razumite, kako migrenam pristopiti bolj naravno skozi beleženje sprožilcev, ritem in ciljno podporo.',
            'Migrene'
            ,
            [
                ['heading' => 'Zakaj je pri migrenah ključno prepoznavanje vzorcev', 'html' => '<p>Mnogi ljudje se osredotočimo le na bolečinski napad, vendar se najbolj koristna podpora pogosto začne že prej. Spanec, hrana, stres in časovni vzorci pogosto razkrijejo, zakaj so nekateri dnevi bolj ranljivi od drugih.</p>'],
                ['heading' => 'Zakaj manj kaosa pomeni boljšo preventivo', 'html' => '<p>Ko so rutine mirnejše in izbire podpore bolj ciljne, postanejo migrene lažje razumljive. Prav ta jasnost naredi preventivo veliko bolj realistično kot naključno eksperimentiranje.</p>'],
            ]
        ),
        $entry(
            166,
            'sl',
            'Vitamin D in K2 pozimi: kako pametneje podpreti kosti in sezonsko ravnovesje',
            'vitamin-d-in-k2-pozimi-kako-pametneje-podpreti-kosti',
            'Pozimi se pogosto odpre vprašanje vitamina D, vendar brez konteksta hitro zdrsnemo v naključno ali pretirano dodajanje. Tukaj je, kako o D in K2 razmišljati skozi sezono, kosti in resnične potrebe telesa namesto skozi sezonski hype.',
            '<ul><li>Vitamin D je pozimi pogosto pomembnejši, vendar ima dodajanje smisel le v pravem kontekstu in potrebi.</li><li>Največja napaka je jemati visoke odmerke brez načrta ali razumevanja širše slike.</li><li>Pametnejši pristop poveže vitamin D, K2, prehrano in dejanske potrebe telesa.</li></ul>',
            [
                ['question' => 'Zakaj je vitamin D pozimi posebej pomemben?', 'answer' => 'Ker je sončne svetlobe manj in se raven lahko hitreje zniža, če ni dovolj vnosa ali zaloge.'],
                ['question' => 'Zakaj se omenja tudi K2?', 'answer' => 'Ker ga pogosto obravnavamo kot del širše zgodbe o kosteh in ravnovesju povezanih hranil.'],
                ['question' => 'Ali naj vsi jemljemo enak odmerek?', 'answer' => 'Ne. Potrebe se razlikujejo glede na posameznika, prehrano, sonce in včasih laboratorijske podatke.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Prevzeti tujo zimsko rutino dopolnil, ne da bi vedeli, ali ustreza tudi vam.'],
            ],
            'Vitamin D in K2 pozimi: kako ju uporabljati bolj pametno in brez pretiravanja',
            'Spoznajte, kdaj imata vitamin D in K2 pozimi smisel ter kako se izogniti najpogostejšim sezonskim napakam.',
            'Vitamin D in K2'
            ,
            [
                ['heading' => 'Zakaj zima spremeni kontekst', 'html' => '<p>Sezona je pomembna, ker se izpostavljenost soncu pogosto močno zmanjša. Že samo ta sprememba je dovolj, da vitamin D postane veliko pomembnejše vprašanje za mnoge ljudi.</p>'],
                ['heading' => 'Zakaj kontekst bolje ščiti kot kopiranje trendov', 'html' => '<p>Spletne rutine dopolnil pogosto delujejo preprosto, vendar skoraj nikoli niso univerzalne. Pametnejša odločitev je navadno odvisna od posameznika, prehrane in tega, kako zima dejansko vpliva nanj.</p>'],
            ]
        ),
        $entry(
            170,
            'sl',
            'Hormoni sreče: kako razpoloženje podpreti s hrano, gibanjem in ritmom dneva',
            'hormoni-srece-kako-razpolozenje-podpreti-s-hrano-gibanjem-in-ritmom-dneva',
            'Tako imenovani hormoni sreče zvenijo enostavno, dokler ne vidimo, kako močno so povezani s spanjem, gibanjem, hrano in odnosi. Tukaj je, kako serotonin, dopamin in podobne poti podpreti brez lovljenja nerealnih hitrih rešitev.',
            '<ul><li>Razpoloženje in notranja energija sta odvisna od več sistemov, ne od enega “hormona sreče”.</li><li>Največja napaka je iskati hiter dvig razpoloženja, medtem ko osnovni ritem dneva ostaja slab.</li><li>Pametnejši pristop gradi male vire dobrega počutja skozi svetlobo, hrano, gibanje, počitek in povezanost.</li></ul>',
            [
                ['question' => 'Ali lahko hrana vpliva na razpoloženje?', 'answer' => 'Da, zlasti kot del širšega ritma, ki podpira stabilnejšo energijo in boljšo prebavo.'],
                ['question' => 'Kako pomembno je gibanje?', 'answer' => 'Zelo, saj redno gibanje pogosto pomaga tako živčnemu sistemu kot občutku notranje svežine.'],
                ['question' => 'Ali obstaja en dodatek za “srečo”?', 'answer' => 'Ne. Razpoloženje je rezultat več povezanih dejavnikov, ne ene same formule.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Ignorirati spanec, svetlobo, stres in ritem dneva, medtem ko pričakujemo boljše počutje samo od motivacije.'],
            ],
            'Hormoni sreče: kaj res podpira razpoloženje skozi hrano in vsakdan',
            'Razumite, kako razpoloženje podpreti skozi prehrano, gibanje, spanec in male ponovljive navade dobrega počutja.',
            'Hormoni sreče',
            [
                ['heading' => 'Zakaj je razpoloženje večje od ene kemijske oznake', 'html' => '<p>Ljudje imamo radi preproste razlage, vendar se čustveno počutje gradi iz več povezanih sistemov. Prav zato podpora najbolje deluje, ko postane bolj podporen celoten ritem življenja, ne le en njegov del.</p>'],
                ['heading' => 'Zakaj male dnevne zmage pogosto pomenijo največ', 'html' => '<p>Svetloba, gibanje, hranljiva hrana in boljši počitek se morda zdijo osnovni, vendar prav ti dejavniki pogosto najzanesljiveje oblikujejo razpoloženje. Hitre čustvene bližnjice so redko tako stabilne kot dober vsakdan.</p>'],
            ]
        ),
    ],
];
