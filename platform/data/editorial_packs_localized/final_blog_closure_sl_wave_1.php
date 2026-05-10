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

$sections = static fn (
    string $h1,
    string $p1,
    string $h2,
    string $p2
): array => [
    ['heading' => $h1, 'html' => "<p>{$p1}</p>"],
    ['heading' => $h2, 'html' => "<p>{$p2}</p>"],
];

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
    array $sectionsList
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
    'sections' => $sectionsList,
];

return [
    'key' => 'final-blog-closure-sl-wave-1',
    'name' => 'Zaključni blog closure (SL) - prvi val',
    'notes' => 'Ročna premium slovenska lokalizacija za zadnjih 24 preostalih AVC blog URL-jev.',
    'entries' => [
        $entry(
            460,
            'Ingver: protivnetni zaveznik, prebava in kako ga pametno uporabljati vsak dan',
            'ingver-protivnetni-zaveznik-in-kako-ga-najbolje-uporabiti',
            'Ingver je priljubljen, ker ga ljudje povezujejo z vnetji, prebavo in občutkom topline v telesu, največ vrednosti pa ima kot del širše rutine prehrane in okrevanja.',
            '<ul><li>Ingver je zanimiv zaradi prebave, topline in svoje protivnetne reputacije.</li><li>Največja napaka je pričakovati, da bo eno živilo samo rešilo dolgotrajne težave.</li><li>Pametnejši pristop ingver uporablja kot podporo prehrani, spanju in okrevanju.</li></ul>',
            $faq(
                'Zakaj je ingver tako priljubljen?',
                'Ker ga ljudje pogosto povezujejo s prebavo, toplino, okrevanjem in bolj naravnim wellness pristopom.',
                'Ali ga je smiselno uporabljati vsak dan?',
                'Lahko je smiselno, če osebi ustreza, vendar ga ni treba spremeniti v obvezen ritual za vsakogar.',
                'Katera je pogosta napaka?',
                'Od ingverja pričakovati učinke, ki so v resnici odvisni od celotne prehrane, spanja in življenjskega ritma.',
                'Kako ga je najbolje razumeti?',
                'Kot koristno živilo in dodatek rutini, ne kot čudežno rešitev.'
            ),
            'Ingver: prebava, vnetja in pametna vsakodnevna uporaba',
            'Spoznajte, kje ima ingver smisel za prebavo in okrevanje ter kako ga uporabljati brez pretiravanja.',
            'Ingver',
            $sections(
                'Zakaj se ingver pogosto pojavlja v pogovorih o zdravju',
                'Združuje močan okus, občutek topline in dolgo tradicijo uporabe, zato ga ljudje hitro sprejmejo kot del domače podpore.',
                'Zakaj rutina še vedno pomeni več kot ena sestavina',
                'Ingver največ doda takrat, ko podpira že urejeno prehrano, dovolj tekočine, spanec in mirnejši dnevni ritem.'
            )
        ),
        $entry(
            461,
            'Kako hitro in zdravo shujšati: 10 preverjenih nasvetov brez jo-jo učinka',
            'kako-hitro-in-zdravo-shujsati-10-preverjenih-nasvetov-za-dolgotrajne-rezultate',
            'Hitro hujšanje se pogosto konča s hitrim vračanjem kilogramov, če za njim ni vzdržnih navad. Ta vodnik pokaže, kako ustvariti rezultat brez kaotičnih diet.',
            '<ul><li>Hitri rezultati imajo vrednost samo, če ne uničijo dolgoročne vzdržnosti.</li><li>Največja napaka je izbrati ekstremno dieto, ki ne zdrži resničnega življenja.</li><li>Pametnejši pristop gradi zmeren primanjkljaj, več sitosti in boljši dnevni ritem.</li></ul>',
            $faq(
                'Ali je mogoče shujšati hitro in zdravo?',
                'Hitrejši začetek je mogoč, vendar mora zdrav pristop ostati vzdržen in brez skrajnih prepovedi.',
                'Zakaj ljudje pogosto ponovno pridobijo kilograme?',
                'Ker številni načrti ustvarijo kratek rezultat, ne zgradijo pa navad, ki bi ga ohranile.',
                'Katera je pogosta napaka?',
                'Izbrati najbolj dramatičen plan namesto tistega, ki se ujema z vsakdanjim življenjem.',
                'Kaj je koristneje graditi?',
                'Navade, ki zmanjšajo vnos, a ostanejo praktične, okusne in ponovljive.'
            ),
            'Kako hitro in zdravo shujšati brez jo-jo učinka',
            'Odkrijte, kako hujšati z vzdržnimi navadami, boljšim ritmom obrokov in realnim načrtom.',
            'Kako shujšati',
            $sections(
                'Zakaj hitrost sama ni dovolj',
                'Veliko ljudi lahko ustvari kratkoročen premik, pravi preizkus pa se začne, ko mora rezultat preživeti običajen teden.',
                'Zakaj struktura varuje rezultat',
                'Bolj kot je načrt praktičen, sitosten in ponovljiv, manjša je možnost, da se po prvem napredku poruši.'
            )
        ),
        $entry(
            466,
            'Občasno postenje 16/8: kako začeti, komu ustreza in katere napake delajo začetniki',
            'obcasno-postenje-kako-dieta-16-8-pomaga-in-prakticni-nasveti-za-zacetnike',
            'Občasno postenje mnogim poenostavi prehrano, vendar ni čaroben format, ki bi ustrezal vsem enako. Ključno je, da ga preizkusimo premišljeno.',
            '<ul><li>16/8 lahko pomaga ljudem, ki bolje delujejo z manj prehranskimi odločitvami.</li><li>Največja napaka je post spremeniti v stradanje ali kaotično prenajedanje v oknu prehranjevanja.</li><li>Pametnejši pristop spremlja kakovost hrane, energijo in dolgoročno vzdržnost.</li></ul>',
            $faq(
                'Zakaj je post 16/8 tako priljubljen?',
                'Ker je enostaven za razumevanje, lahko ga je preizkusiti in mnogim zmanjša prehranski kaos.',
                'Ali občasno postenje ustreza vsem?',
                'Ne. Nekaterim zelo ustreza, drugim pa povzroči več lakote, stresa ali neravnovesja.',
                'Katera je pogosta napaka?',
                'Preskakovati obroke, nato pa v kratkem oknu jesti brez strukture in mere.',
                'Kako ga je bolje preizkusiti?',
                'Z opazovanjem energije, lakote, kakovosti prehrane in počutja, ne samo dolžine posta.'
            ),
            'Občasno postenje 16/8: pameten začetek brez nepotrebnih napak',
            'Spoznajte, komu lahko post 16/8 ustreza in kako se izogniti pogostim začetniškim napakam.',
            'Občasno postenje',
            $sections(
                'Zakaj jasen prehranski okvir pomaga nekaterim ljudem',
                'Ko je manj odločitev, se nekateri lažje držijo ritma, zmanjšajo prigrizke in bolje prepoznajo resnično lakoto.',
                'Zakaj post ni dovoljenje za slabšo prehrano',
                'Če je okno prehranjevanja napolnjeno s kaosom, post izgubi glavno prednost. Še vedno šteje kakovost obrokov.'
            )
        ),
        $entry(
            467,
            'Keto dieta: praktičen jedilnik, osnovna pravila in kaj pričakovati na začetku',
            'keto-dieta-prakticni-jedilnik-in-nasveti-za-ucinkovito-hujsanje',
            'Keto dieta privlači zaradi jasnih pravil in pogosto hitrega začetnega premika, vendar zahteva dobro načrtovanje, dovolj vlaknin in realna pričakovanja.',
            '<ul><li>Keto ima smisel samo, ko je jedilnik jasen, sitosten in hranilno premišljen.</li><li>Največja napaka je keto zmanjšati na maščobe in prepoved kruha brez strukture.</li><li>Pametnejši pristop načrtuje obroke, elektrolite, sitost in prehodno obdobje.</li></ul>',
            $faq(
                'Zakaj je keto dieta tako privlačna?',
                'Ker ponuja jasen okvir, občutek nadzora in pogosto hiter začetni premik na tehtnici.',
                'Kaj je največji problem pri začetnikih?',
                'Pogosto začnejo brez dobrega jedilnika, zato postanejo lačni, utrujeni ali zmedeni.',
                'Ali je keto samo prehrana brez ogljikovih hidratov?',
                'Ne. Pomembna je tudi kakovost hrane, ne samo zmanjšanje ogljikovih hidratov.',
                'Kako se je bolje lotiti keto prehrane?',
                'Z načrtom obrokov, dovolj sitosti, tekočine in realnimi pričakovanji glede prilagoditve telesa.'
            ),
            'Keto dieta: jedilnik, pravila in prvi tedni brez kaosa',
            'Odkrijte, kako sestaviti praktičen keto jedilnik in se izogniti najpogostejšim začetniškim napakam.',
            'Keto dieta',
            $sections(
                'Zakaj keto potrebuje več kot seznam prepovedi',
                'Uspešen začetek ni odvisen samo od izločanja živil, temveč od tega, ali ima oseba dovolj jasnih, sitostnih in ponovljivih obrokov.',
                'Zakaj je prilagoditveno obdobje pomembno',
                'Prvi dnevi lahko prinesejo spremembe energije, apetita in hidracije, zato je bolje začeti mirno in opazovati odziv telesa.'
            )
        ),
        $entry(
            474,
            'Veganska prehrana: kako dobiti dovolj beljakovin, železa in ključnih hranil',
            'veganska-prehrana-kako-dobiti-dovolj-beljakovin-in-hranil',
            'Veganska prehrana je lahko izjemno kakovostna, če je dobro sestavljena. Največji izziv ni odsotnost živalskih živil, temveč načrtovanje beljakovin, železa, B12 in sitosti.',
            '<ul><li>Veganski jedilnik je lahko hranilen, vendar potrebuje več načrtovanja kot naključno izločanje živil.</li><li>Največja napaka je jesti samo priloge, kruh in nadomestke brez dovolj beljakovin.</li><li>Pametnejši pristop gradi obroke okoli stročnic, žit, semen, oreškov in dopolnil, kadar so potrebna.</li></ul>',
            $faq(
                'Ali lahko veganska prehrana zagotovi dovolj beljakovin?',
                'Da, če so obroki načrtovani z dovolj stročnic, tofuja, tempeha, semen, oreškov in drugih virov.',
                'Na katera hranila je treba posebej paziti?',
                'Najpogosteje na vitamin B12, železo, omega-3 maščobe, cink, jod in skupno energijo.',
                'Katera je pogosta napaka?',
                'Zanašati se na vegansko oznako, namesto na kakovost in sestavo celotnega obroka.',
                'Kako naj bo veganski krožnik bolj uravnotežen?',
                'Naj ima vir beljakovin, zelenjavo, kakovostne ogljikove hidrate in maščobe, ne samo eno skupino živil.'
            ),
            'Veganska prehrana: beljakovine, hranila in pameten jedilnik',
            'Spoznajte, kako sestaviti uravnoteženo vegansko prehrano z dovolj beljakovin in ključnih hranil.',
            'Veganska prehrana',
            $sections(
                'Zakaj vegansko ne pomeni samodejno uravnoteženo',
                'Izločitev živalskih živil je samo okvir. Kakovost je odvisna od tega, kaj pride na krožnik namesto njih.',
                'Zakaj beljakovine zaslužijo posebno pozornost',
                'Ko so beljakovine dobro načrtovane, je veganska prehrana lažje sitostna, stabilna in dolgoročno izvedljiva.'
            )
        ),
        $entry(
            477,
            'Dieta brez glutena: komu koristi, kdaj je nujna in katere napake se najpogosteje ponavljajo',
            'dieta-brez-glutena-odkrijte-zakaj-je-pomembna-za-zdravje',
            'Prehrana brez glutena je za nekatere nujna, za druge pa modna odločitev brez pravega razloga. Razlika je pomembna, ker izločanje glutena samo po sebi ne pomeni boljše prehrane.',
            '<ul><li>Brezglutenska prehrana je ključna pri celiakiji in lahko pomembna pri potrjeni občutljivosti.</li><li>Največja napaka je misliti, da oznaka brez glutena samodejno pomeni zdravo.</li><li>Pametnejši pristop išče pravi razlog, kakovost živil in dovolj vlaknin.</li></ul>',
            $faq(
                'Komu je dieta brez glutena res potrebna?',
                'Predvsem osebam s celiakijo in tistim, pri katerih je strokovno potrjena občutljivost ali drug jasen razlog.',
                'Ali je brezglutenska prehrana vedno bolj zdrava?',
                'Ne. Lahko je zelo kakovostna ali zelo predelana, odvisno od izbire živil.',
                'Katera je pogosta napaka?',
                'Zamenjati običajne izdelke z brezglutenskimi industrijskimi izdelki, ne da bi izboljšali prehrano.',
                'Kaj je pomembno spremljati?',
                'Vlaknine, raznolikost, energijo, prebavo in dejanski razlog za izločanje glutena.'
            ),
            'Dieta brez glutena: kdaj ima smisel in kako jo izvesti kakovostno',
            'Odkrijte, komu prehrana brez glutena koristi in kako se izogniti najpogostejšim napakam.',
            'Dieta brez glutena',
            $sections(
                'Zakaj razlog za izločanje glutena spremeni vse',
                'Če je gluten medicinsko pomemben problem, je pristop drugačen kot pri osebi, ki samo preizkuša trend.',
                'Zakaj kakovost izdelkov ostaja ključna',
                'Brezglutenska oznaka ne pove dovolj o vlakninah, sladkorju, beljakovinah ali skupni hranilni vrednosti obroka.'
            )
        ),
        $entry(
            479,
            'Koliko vode dnevno popiti: realen vodnik za hidracijo, energijo in boljše navade',
            'koliko-vode-dnevno-popiti',
            'Vprašanje, koliko vode popiti dnevno, nima enega odgovora za vse. Potrebe se spreminjajo glede na telesno maso, prehrano, aktivnost, temperaturo in individualni občutek žeje.',
            '<ul><li>Dnevne potrebe po vodi niso enake za vse ljudi in vse dni.</li><li>Največja napaka je slepo slediti številki, ne da bi opazovali telo in okoliščine.</li><li>Pametnejši pristop poveže žejo, barvo urina, aktivnost, prehrano in temperaturo okolja.</li></ul>',
            $faq(
                'Ali mora vsak piti enako količino vode?',
                'Ne. Potrebe so različne glede na telo, gibanje, vreme, prehrano in zdravstveni kontekst.',
                'Ali je žeja zanesljiv znak?',
                'Pogosto pomaga, vendar jo je dobro povezati še z energijo, urinom, aktivnostjo in navadami skozi dan.',
                'Katera je pogosta napaka?',
                'Piti premalo večino dneva, nato pa poskušati vse nadoknaditi zvečer.',
                'Kako si najlažje pomagati?',
                'Z rednimi majhnimi vnosi, steklenico pri roki in več tekočine ob gibanju ali vročini.'
            ),
            'Koliko vode dnevno popiti: praktičen vodnik za hidracijo',
            'Spoznajte, kako oceniti svoje potrebe po vodi in zgraditi boljšo rutino hidracije skozi dan.',
            'Koliko vode piti',
            $sections(
                'Zakaj univerzalna številka ni dovolj',
                'Dva človeka imata lahko popolnoma različne potrebe, če se razlikujeta po aktivnosti, prehrani, temperaturi okolja in telesni masi.',
                'Zakaj majhne navade delujejo bolje od večernega nadomeščanja',
                'Hidracija je lažja, ko se razporedi skozi dan, namesto da postane še ena pozna obveznost.'
            )
        ),
        $entry(
            480,
            'Paleo dieta: vrnitev k prvinski prehrani ali samo še en način za boljši izbor živil',
            'paleo-dieta-vracanje-k-primitivni-prehrani-za-boljse-zdravje',
            'Paleo dieta poudarja nepredelana živila, beljakovine, zelenjavo, oreške in preprostejši način prehranjevanja. Njena vrednost je največja, ko ne postane toga ideologija.',
            '<ul><li>Paleo lahko pomaga zmanjšati visoko predelano hrano in vrniti fokus na osnovna živila.</li><li>Največja napaka je iz pravila narediti strogo identiteto namesto uporabnega okvirja.</li><li>Pametnejši pristop vzame najboljše: kakovost živil, sitost in manj prehranskega kaosa.</li></ul>',
            $faq(
                'Kaj je osnovna ideja paleo diete?',
                'Poudarek je na nepredelanih živilih, beljakovinah, zelenjavi, sadju, oreških in manj industrijskih izdelkov.',
                'Ali mora biti paleo zelo strog?',
                'Ni nujno. Marsikomu bolj koristi kot smer, ne kot absolutno pravilo.',
                'Katera je pogosta napaka?',
                'Preveč energije porabiti za prepovedi in premalo za kakovost, sitost in praktičnost obrokov.',
                'Komu lahko tak pristop ustreza?',
                'Ljudem, ki potrebujejo jasnejši okvir in želijo zmanjšati predelano hrano.'
            ),
            'Paleo dieta: kako uporabiti idejo brez pretirane strogosti',
            'Spoznajte, kaj paleo dieta poudarja in kako jo prilagoditi za bolj praktično vsakodnevno prehrano.',
            'Paleo dieta',
            $sections(
                'Zakaj paleo ljudi hitro pritegne',
                'Ideja preproste hrane je privlačna, ker zmanjšuje izbire, vrača fokus na osnovna živila in mnogim pomaga urediti krožnik.',
                'Zakaj togost lahko zmanjša koristi',
                'Ko pravila postanejo pomembnejša od kakovosti življenja, je bolje ohraniti uporabne principe in jih prilagoditi resničnemu ritmu.'
            )
        ),
        $entry(
            490,
            'Recepti za zdravo prehrano: 7 idej za kosila in obroke, ki so sitostni in izvedljivi',
            'recepti-za-zdravo-prehrano-7-idej-za-zdrava-kosila-in-polnovredne-obroke',
            'Zdrava prehrana ne potrebuje popolnosti, temveč nekaj ponovljivih obrokov, ki so okusni, sitostni in dovolj enostavni za običajen teden.',
            '<ul><li>Najboljši zdravi recepti so tisti, ki jih lahko res ponavljamo.</li><li>Največja napaka je izbrati preveč zapletene obroke, ki delujejo samo v idealnih pogojih.</li><li>Pametnejši pristop združi beljakovine, zelenjavo, vlaknine in praktično pripravo.</li></ul>',
            $faq(
                'Kaj naredi recept zares zdrav?',
                'Ravnotežje beljakovin, vlaknin, zelenjave, kakovostnih ogljikovih hidratov in maščob.',
                'Ali morajo biti zdravi obroki zapleteni?',
                'Ne. Pogosto so najboljši tisti, ki jih lahko pripravimo hitro in ponovimo večkrat.',
                'Katera je pogosta napaka?',
                'Načrtovati obroke, ki so prehransko dobri, a popolnoma nepraktični za vsakdan.',
                'Kako si olajšati pripravo?',
                'Z osnovnimi kombinacijami, pripravo dela sestavin vnaprej in nekaj prilagodljivimi recepti.'
            ),
            'Recepti za zdravo prehrano: praktična kosila in polnovredni obroki',
            'Odkrijte ideje za zdrave obroke, ki so sitostni, enostavni in primerni za vsakodnevno pripravo.',
            'Zdravi recepti',
            $sections(
                'Zakaj ponovljivost zmaga nad popolnostjo',
                'Če je obrok preveč zapleten, hitro izgine iz rutine. Dober recept mora preživeti utrujen dan, ne samo idealen vikend.',
                'Zakaj naj ima vsak obrok jasno funkcijo',
                'Ko vemo, kateri del obroka daje beljakovine, vlaknine, energijo in okus, je veliko lažje sestavljati zdravo prehrano brez stresa.'
            )
        ),
        $entry(
            494,
            'Parkinsonova bolezen in prehrana: kako lahko antioksidanti podprejo vsakdanjo rutino',
            'parkinsonova-bolezen-in-prehrana-kako-antioksidanti-pomagajo-pri-simptomih',
            'Pri Parkinsonovi bolezni prehrana ne nadomešča terapije, lahko pa pomaga urediti energijo, prebavo, splošno podporo in kakovost vsakodnevnega ritma.',
            '<ul><li>Prehrana pri Parkinsonovi bolezni je podporni del širše oskrbe, ne zamenjava za zdravljenje.</li><li>Največja napaka je iskati eno živilo, ki bi rešilo zapleteno nevrološko stanje.</li><li>Pametnejši pristop vključuje beljakovine, vlaknine, antioksidante, hidracijo in sodelovanje s strokovnjaki.</li></ul>',
            $faq(
                'Ali prehrana lahko pozdravi Parkinsonovo bolezen?',
                'Ne. Prehrana ne nadomešča zdravljenja, lahko pa podpira splošno stanje, energijo in prebavo.',
                'Zakaj se omenjajo antioksidanti?',
                'Ker so del kakovostne prehrane, ki podpira celice in širši občutek vitalnosti.',
                'Katera je pogosta napaka?',
                'Preveč upanja položiti v eno živilo ali dodatek namesto v celoten načrt podpore.',
                'Kaj je pomembno uskladiti z zdravnikom?',
                'Zdravila, čas obrokov, beljakovine, prehranska dopolnila in posebne individualne potrebe.'
            ),
            'Parkinsonova bolezen in prehrana: antioksidanti kot del podpore',
            'Spoznajte, kako prehrana, vlaknine, hidracija in antioksidanti lahko dopolnijo vsakdanjo podporo pri Parkinsonovi bolezni.',
            'Parkinson in prehrana',
            $sections(
                'Zakaj je prehrana podporni del oskrbe',
                'Pri kroničnih nevroloških stanjih je pomembno ohraniti realna pričakovanja. Prehrana lahko pomaga pri rutini, ne more pa sama rešiti bolezni.',
                'Zakaj je individualni načrt pomemben',
                'Vsaka oseba ima drugačen potek, zdravila in potrebe, zato je najboljši pristop tisti, ki vključuje strokovno spremljanje.'
            )
        ),
        $entry(
            497,
            'Forever Fiber: kako vlaknine pomagajo sitosti, prebavi in bolj urejeni prehranski rutini',
            'forever-fiber-kako-vlaknine-zavirajo-apetit-in-spodbujajo-zdravo-prebavo',
            'Forever Fiber je zanimiv kot praktičen način za povečanje vnosa vlaknin, vendar največ koristi, ko dopolnjuje celotno prehrano, ne ko poskuša nadomestiti zelenjavo, stročnice in polnovredna živila.',
            '<ul><li>Vlaknine so pomembne za prebavo, sitost in bolj stabilen prehranski ritem.</li><li>Največja napaka je dodatek uporabljati kot izgovor za prehrano z malo vlakninami.</li><li>Pametnejši pristop združi Forever Fiber z vodo, zelenjavo, sadjem, stročnicami in rednimi obroki.</li></ul>',
            $faq(
                'Zakaj so vlaknine pomembne?',
                'Povezane so s prebavo, sitostjo, rednostjo in bolj kakovostno prehransko strukturo.',
                'Ali lahko Forever Fiber nadomesti zelenjavo?',
                'Ne. Lahko dopolni vnos vlaknin, vendar ne nadomesti raznolike hrane.',
                'Kaj je pomembno ob jemanju vlaknin?',
                'Dovolj tekočine, postopno uvajanje in poslušanje odziva prebave.',
                'Komu je lahko tak izdelek praktičen?',
                'Ljudem, ki težko vsak dan dosežejo dovolj vlaknin in želijo preprost dodatek rutini.'
            ),
            'Forever Fiber: vlaknine za sitost in podporo prebavi',
            'Odkrijte, kako Forever Fiber lahko dopolni prehrano z vlakninami in podpre bolj urejeno prebavno rutino.',
            'Forever Fiber',
            $sections(
                'Zakaj vlaknine pogosto manjkajo v sodobni prehrani',
                'Hitra hrana, malo stročnic in premalo zelenjave lahko hitro zmanjšajo dnevni vnos vlaknin, tudi pri ljudeh, ki jedo navidezno normalno.',
                'Zakaj dodatek najbolje deluje kot dopolnilo',
                'Forever Fiber ima največ smisla, ko podpira boljšo prehrano, ne ko poskuša nadomestiti temeljne prehranske izbire.'
            )
        ),
        $entry(
            501,
            'Avtoimunske bolezni in naravna podpora imunosti: popoln vodnik za bolj mirno rutino',
            'avtoimunske-bolezni-in-naravna-imunska-podpora-popoln-vodnik',
            'Pri avtoimunskih boleznih je cilj podpora, ne agresivno spodbujanje imunosti. Kakovostna rutina lahko pomaga energiji, prebavi, spanju in lažjemu upravljanju vsakdana.',
            '<ul><li>Avtoimunska stanja potrebujejo previden, individualen in realen pristop.</li><li>Največja napaka je imunski sistem samo spodbujati, ne da bi razumeli naravo avtoimunosti.</li><li>Pametnejši pristop gradi protivnetno prehrano, mirnejši ritem, spanje in strokovno spremljanje.</li></ul>',
            $faq(
                'Ali naravna podpora lahko nadomesti terapijo?',
                'Ne. Lahko dopolni vsakdanjo rutino, vendar ne sme nadomestiti dogovorjenega zdravljenja.',
                'Kaj pomeni podpora imunosti pri avtoimunosti?',
                'Predvsem bolj uravnotežen pristop, manj nepotrebnega stresa za telo in boljše osnovne navade.',
                'Katera je pogosta napaka?',
                'Uporabljati močne imunske trditve brez razumevanja diagnoze in individualnega stanja.',
                'Kaj je smiselno spremljati?',
                'Simptome, energijo, prebavo, spanje, odziv na hrano in priporočila zdravnika.'
            ),
            'Avtoimunske bolezni: naravna podpora brez pretiranih obljub',
            'Spoznajte, kako prehrana, spanje, stres in premišljena rutina lahko podprejo vsakdan pri avtoimunskih boleznih.',
            'Avtoimunske bolezni',
            $sections(
                'Zakaj beseda imunost pri avtoimunosti zahteva previdnost',
                'Pri avtoimunskih stanjih ne gre za preprosto krepitev imunskega sistema, temveč za bolj premišljeno podporo telesu.',
                'Zakaj temelji štejejo več kot trendi',
                'Kakovost spanja, obroki, hidracija, gibanje in stres pogosto ustvarijo več koristi kot naključno dodajanje novih izdelkov.'
            )
        ),
        $entry(
            502,
            'Revmatoidni artritis: prehrana, sklepi in podpora avtoimunskemu odzivu',
            'revmatoidni-artritis-prehrana-za-podporo-avtoimunskega-odziva',
            'Revmatoidni artritis zahteva medicinsko spremljanje, prehrana pa lahko postane pomemben podporni del rutine za sklepe, energijo, prebavo in splošno počutje.',
            '<ul><li>Prehrana pri revmatoidnem artritisu podpira vsakdan, vendar ne nadomešča terapije.</li><li>Največja napaka je iskati hitro rešitev namesto celotnega protivnetnega okvirja.</li><li>Pametnejši pristop vključuje kakovostne maščobe, vlaknine, beljakovine, gibanje in opazovanje odzivov.</li></ul>',
            $faq(
                'Ali prehrana vpliva na revmatoidni artritis?',
                'Lahko podpira splošno počutje, energijo in telesno težo, vendar ne nadomešča zdravljenja.',
                'Katera živila se pogosto omenjajo kot koristna?',
                'Zelenjava, sadje, ribe, oreški, olivno olje, stročnice in druga živila z dobro hranilno vrednostjo.',
                'Katera je pogosta napaka?',
                'Prehitro izločiti veliko živil brez jasnega razloga in brez strokovne podpore.',
                'Kaj je pomembno spremljati?',
                'Bolečino, jutranjo togost, energijo, prebavo in morebitne sprožilce.'
            ),
            'Revmatoidni artritis: prehranska podpora za sklepe in energijo',
            'Odkrijte, kako prehrana in navade lahko dopolnijo podporo pri revmatoidnem artritisu.',
            'Revmatoidni artritis',
            $sections(
                'Zakaj protivnetni okvir zveni smiselno',
                'Kakovostna prehrana ne obljublja ozdravitve, lahko pa zmanjša prehranski kaos in podpira telo, ki je že pod obremenitvijo.',
                'Zakaj je individualno spremljanje nujno',
                'Simptomi, zdravila, telesna teža in odzivi na hrano so različni, zato je najboljši pristop prilagojen osebi.'
            )
        ),
        $entry(
            504,
            'Simptomi lupusa: kako prepoznati zgodnje znake in zgraditi varnejšo podporno rutino',
            'simptomi-lupusa-prepoznajte-zgodnje-znake-in-odkrijte-naravna-zdravila',
            'Lupus ima lahko zelo različne obraze, zato zgodnjih znakov ni dobro poenostavljati. Pravočasno prepoznavanje in strokovna obravnava sta pomembnejša od samodiagnosticiranja.',
            '<ul><li>Lupus lahko vključuje utrujenost, bolečine, kožne spremembe, vročino in druge sistemske znake.</li><li>Največja napaka je simptome razlagati samo kot stres ali pomanjkanje energije.</li><li>Pametnejši pristop združi zdravniško diagnostiko, spremljanje simptomov in nežno podporno rutino.</li></ul>',
            $faq(
                'Kateri so možni zgodnji znaki lupusa?',
                'Lahko vključujejo izrazito utrujenost, bolečine v sklepih, kožne spremembe, vročino, občutljivost na sonce in druge simptome.',
                'Ali se lupus lahko potrdi samo po simptomih?',
                'Ne. Potrebni so zdravniški pregled, laboratorijski izvidi in celotna klinična slika.',
                'Katera je pogosta napaka?',
                'Predolgo odlašati, ker se simptomi zdijo nejasni ali se pripisujejo samo stresu.',
                'Kaj lahko podpira vsakdan?',
                'Zaščita pred soncem, mirnejši ritem, kakovostna prehrana, spanje in sodelovanje z zdravnikom.'
            ),
            'Simptomi lupusa: zgodnji znaki in podporna rutina',
            'Spoznajte možne zgodnje znake lupusa in zakaj sta diagnostika ter varna rutina podpore tako pomembni.',
            'Simptomi lupusa',
            $sections(
                'Zakaj je lupus težko prepoznati',
                'Ker se lahko simptomi prekrivajo z utrujenostjo, stresom, okužbami ali drugimi avtoimunskimi težavami.',
                'Zakaj naravna podpora ne sme zamenjati diagnostike',
                'Prehrana in navade lahko pomagajo vsakdanu, vendar pri lupusu najprej potrebujemo jasno medicinsko sliko.'
            )
        ),
        $entry(
            516,
            'Hashimoto simptomi: 15 zgodnjih znakov in kako jih ublažiti z boljšo rutino',
            'hashimoto-simptomi-15-zgodnjih-znakov-in-kako-jih-omiliti',
            'Hashimotov tiroiditis se lahko razvija počasi, zato ljudje utrujenost, mraz, nihanje teže ali meglo v glavi pogosto pripišejo stresu. Pravi korak je kombinacija diagnostike in pametne podpore.',
            '<ul><li>Hashimoto je avtoimunsko stanje ščitnice, ki lahko vpliva na energijo, težo, razpoloženje in prebavo.</li><li>Največja napaka je simptome dolgo ignorirati ali jih razlagati samo kot utrujenost.</li><li>Pametnejši pristop vključuje laboratorije, zdravnika, prehrano, spanje, stres in postopne navade.</li></ul>',
            $faq(
                'Kateri so pogosti znaki Hashimota?',
                'Utrujenost, občutek mraza, spremembe teže, suha koža, zaprtje, megla v glavi in nihanje razpoloženja.',
                'Ali simptomi sami zadostujejo za diagnozo?',
                'Ne. Potrebni so laboratorijski testi, pregled ščitnice in zdravniška razlaga.',
                'Katera je pogosta napaka?',
                'Samostojno uvajati velike spremembe ali dodatke brez spremljanja ščitničnega stanja.',
                'Kaj lahko pomaga kot podpora?',
                'Urejeni obroki, dovolj beljakovin, spanje, obvladovanje stresa in spremljanje odziva telesa.'
            ),
            'Hashimoto simptomi: zgodnji znaki in podpora ščitnici',
            'Odkrijte pogoste simptome Hashimota in kako zgraditi varno rutino podpore ob strokovnem spremljanju.',
            'Hashimoto simptomi',
            $sections(
                'Zakaj se Hashimoto pogosto spregleda',
                'Simptomi se lahko razvijajo počasi in so podobni običajni utrujenosti, zato ljudje dolgo ne poiščejo jasne razlage.',
                'Zakaj je cilj stabilnost, ne popoln nadzor',
                'Pri ščitnici je pomembno spremljanje, doslednost in postopno urejanje navad, ne hitri ekstremni posegi.'
            )
        ),
        $entry(
            518,
            'Psoriaza: naravne strategije, kožna rutina in holističen pristop k umirjanju kože',
            'psoriaza-naravne-strategije-in-holisticni-pristop-k-zdravljenju',
            'Psoriaza ni samo površinska težava kože, temveč stanje, ki lahko vključuje imunski odziv, stres, vnetja in vsakodnevne sprožilce. Podpora zato potrebuje večplastni pristop.',
            '<ul><li>Psoriaza pogosto zahteva kombinacijo dermatološke oskrbe, nege kože in življenjskih navad.</li><li>Največja napaka je iskati samo kremo ali samo prehransko rešitev.</li><li>Pametnejši pristop spremlja sprožilce, vlaženje kože, stres, prehrano in strokovna priporočila.</li></ul>',
            $faq(
                'Ali je psoriaza samo težava kože?',
                'Ne. Pri mnogih ljudeh vključuje širši imunski in vnetni kontekst.',
                'Ali prehrana lahko pomaga?',
                'Lahko podpira splošno stanje, vendar se odzivi razlikujejo in ne nadomešča dermatološke oskrbe.',
                'Katera je pogosta napaka?',
                'Prehitro menjavati izdelke ali diete, ne da bi spremljali sprožilce in odzive kože.',
                'Kaj lahko pomaga vsakdanji rutini?',
                'Nežna nega, vlaženje, manj draženja, urejen spanec, stresna higiena in individualno spremljanje.'
            ),
            'Psoriaza: naravna podpora in bolj mirna rutina kože',
            'Spoznajte, kako nega kože, prehrana, stres in spremljanje sprožilcev lahko podprejo življenje s psoriazo.',
            'Psoriaza',
            $sections(
                'Zakaj psoriaza potrebuje več kot en odgovor',
                'Koža je vidni del zgodbe, vendar vsakdanji sprožilci, stres in imunski odziv pogosto vplivajo na celotno sliko.',
                'Zakaj je spremljanje sprožilcev dragoceno',
                'Ko oseba bolje razume, kaj poslabša stanje, lažje izbere rutino, ki je nežna, ponovljiva in manj kaotična.'
            )
        ),
        $entry(
            520,
            'Bolečine v sklepih: najpogostejši vzroki in naravni načini za podporo gibljivosti',
            'bolecine-v-sklepih-prepoznajte-vzroke-in-naravne-nacine-lajsanja',
            'Bolečine v sklepih imajo lahko veliko vzrokov, od preobremenitve in premalo gibanja do vnetnih stanj. Zato je pomembno razumeti kontekst, ne samo iskati hitro olajšanje.',
            '<ul><li>Bolečine v sklepih niso ena težava z enim univerzalnim odgovorom.</li><li>Največja napaka je dolgo ignorirati bolečino ali jo stalno prekrivati brez razumevanja vzroka.</li><li>Pametnejši pristop združi gibanje, regeneracijo, telesno težo, prehrano in pregled, kadar je potreben.</li></ul>',
            $faq(
                'Kaj lahko povzroča bolečine v sklepih?',
                'Preobremenitev, poškodbe, premalo gibanja, starostne spremembe, vnetna stanja ali drugi zdravstveni razlogi.',
                'Kdaj je smiselno poiskati zdravniško pomoč?',
                'Če je bolečina močna, traja dolgo, se pojavi oteklina, rdečina, vročina ali omejitev gibanja.',
                'Katera je pogosta napaka?',
                'Popoln počitek ali popolno ignoriranje bolečine, namesto premišljene prilagoditve gibanja.',
                'Kaj lahko podpira sklepe?',
                'Postopno gibanje, moč mišic, uravnotežena telesna teža, kakovostna prehrana in dovolj regeneracije.'
            ),
            'Bolečine v sklepih: vzroki in naravna podpora gibljivosti',
            'Odkrijte, kaj lahko stoji za bolečinami v sklepih in kako podpreti gibljivost z varnejšo rutino.',
            'Bolečine v sklepih',
            $sections(
                'Zakaj sklepov ne smemo gledati ločeno od mišic',
                'Moč, mobilnost, telesna teža in način gibanja pogosto odločajo, koliko obremenitve sklep vsak dan prenaša.',
                'Zakaj je postopnost boljša od ekstremov',
                'Pri sklepih redko pomaga skok iz mirovanja v intenziven trening. Boljša je mirna, postopna gradnja podpore.'
            )
        ),
        $entry(
            529,
            'Zeliščni čaj iz cvetov aloe: okus, sestavine in možne koristi za mirnejšo rutino',
            'zeliscni-caj-iz-cvetov-aloe-okus-sestavine-in-mozne-koristi',
            'Aloe Blossom Herbal Tea je zeliščni čaj, ki lahko postane prijeten del večerne, prebavne ali sprostitvene rutine, posebej za ljudi, ki želijo topel napitek brez težkega občutka.',
            '<ul><li>Zeliščni čaj iz cvetov aloe je zanimiv kot ritual topline, miru in lahkotnejše rutine.</li><li>Največja napaka je od čaja pričakovati učinke, ki zahtevajo širše prehranske in življenjske navade.</li><li>Pametnejši pristop ga uporablja kot prijeten podporni napitek, ne kot glavno rešitev.</li></ul>',
            $faq(
                'Kaj je Aloe Blossom Herbal Tea?',
                'Zeliščni čaj Forever, namenjen prijetnemu napitku in lažji podpori vsakodnevni rutini.',
                'Kdaj ga je smiselno piti?',
                'Mnogim ustreza zvečer, po obroku ali takrat, ko želijo topel in bolj umirjen trenutek.',
                'Ali čaj nadomesti zdravo prehrano?',
                'Ne. Lahko dopolni rutino, vendar ne nadomesti obrokov, hidracije in drugih navad.',
                'Komu je lahko zanimiv?',
                'Ljudem, ki želijo zeliščni napitek kot del sprostitve, prebavne podpore ali večernega rituala.'
            ),
            'Aloe Blossom Herbal Tea: zeliščni čaj za mirnejšo rutino',
            'Spoznajte okus, sestavine in praktično uporabo zeliščnega čaja iz cvetov aloe v vsakdanji rutini.',
            'Aloe Blossom Herbal Tea',
            $sections(
                'Zakaj čaj pogosto deluje kot sidro rutine',
                'Topel napitek lahko označi prehod iz hitrega dneva v mirnejši tempo, kar je včasih enako pomembno kot sama mešanica.',
                'Zakaj je najboljša uporaba preprosta',
                'Ko čaj postane prijeten ponovljiv ritual, ni treba ustvarjati pretiranih obljub. Dovolj je, da podpira bolj miren trenutek.'
            )
        ),
        $entry(
            532,
            'ARGI za športnike: kako L-arginin podpira vzdržljivost, pretok in aktivno rutino',
            'argi-za-sportnike-kako-l-arginin-izboljsa-vzdrzljivost',
            'Forever ARGI+ je izdelek, ki ga aktivni ljudje pogosto gledajo skozi energijo, pretok, trening in regeneracijo. Največ smisla ima kot del urejene športne in prehranske rutine.',
            '<ul><li>L-arginin je zanimiv zaradi povezave z dušikovim oksidom, pretokom in športno zmogljivostjo.</li><li>Največja napaka je od izdelka pričakovati rezultat brez treninga, prehrane in regeneracije.</li><li>Pametnejši pristop ARGI+ poveže z gibanjem, hidracijo, beljakovinami in realnimi cilji.</li></ul>',
            $faq(
                'Kaj je Forever ARGI+?',
                'Forever izdelek z L-argininom, ki ga ljudje pogosto uporabljajo v aktivni in športni rutini.',
                'Ali je namenjen samo športnikom?',
                'Ne nujno, vendar je posebej zanimiv ljudem, ki razmišljajo o energiji, gibanju in podpori aktivnosti.',
                'Katera je pogosta napaka?',
                'Pričakovati, da bo izdelek nadomestil trening, spanec, hidracijo ali kakovostno prehrano.',
                'Kako ga je bolje vključiti?',
                'Kot del širše rutine gibanja, okrevanja in premišljene prehranske podpore.'
            ),
            'ARGI za športnike: L-arginin in podpora aktivni rutini',
            'Spoznajte, kako Forever ARGI+ in L-arginin lahko dopolnita športno rutino, energijo in vzdržljivost.',
            'Forever ARGI+',
            $sections(
                'Zakaj aktivni ljudje iščejo podporo za pretok in energijo',
                'Pri treningu ne šteje samo moč volje, temveč tudi hidracija, prehrana, regeneracija in občutek pripravljenosti.',
                'Zakaj ARGI+ najbolje deluje v kontekstu',
                'Izdelek ima več smisla, ko podpira že obstoječo aktivno rutino, namesto da postane nadomestilo zanjo.'
            )
        ),
        $entry(
            541,
            'Izdelki Forever Living: vse, kar morate vedeti o zdravju, podpori in pametnem izboru',
            'izdelki-forever-living-vse-kar-morate-vedeti-o-zdravju',
            'Izdelki Forever Living so najuporabnejši, ko jih ne gledamo kot naključen katalog, temveč kot sistem podpore za prebavo, nego kože, energijo, hidracijo in vsakodnevno rutino.',
            '<ul><li>Forever izdelki pokrivajo aloe vero, prehranska dopolnila, nego kože, programe in vsakodnevno podporo.</li><li>Največja napaka je izbrati izdelek samo po popularnosti, brez cilja in konteksta.</li><li>Pametnejši pristop najprej določi potrebo, nato izbere izdelek, rutino in realna pričakovanja.</li></ul>',
            $faq(
                'Po čem so Forever Living izdelki najbolj znani?',
                'Najbolj po aloe vera gelih, dodatkih prehrani, izdelkih za nego kože in programih podpore.',
                'Kako izbrati pravi Forever izdelek?',
                'Najprej je treba vedeti, ali je cilj prebava, energija, koža, hidracija, trening ali splošna rutina.',
                'Katera je pogosta napaka?',
                'Kupiti več izdelkov hkrati brez jasnega plana uporabe.',
                'Zakaj je priporočilo koristno?',
                'Ker pomaga povezati izdelek s ciljem, načinom uporabe in realnimi pričakovanji.'
            ),
            'Izdelki Forever Living: vodnik za pameten izbor',
            'Spoznajte glavne kategorije Forever izdelkov in kako izbrati podporo za svoje potrebe.',
            'Forever Living izdelki',
            $sections(
                'Zakaj katalog potrebuje osebni filter',
                'Veliko izdelkov lahko hitro zmede obiskovalca. Najboljši izbor se začne z vprašanjem, kaj oseba res želi podpreti.',
                'Zakaj rutine prodajajo bolje kot posamezni izdelki',
                'Ko obiskovalec razume, kako izdelek uporabiti v vsakdanjem življenju, se lažje odloči in bolj verjetno ostane zadovoljen.'
            )
        ),
        $entry(
            549,
            'Program Clean 9: devetdnevni reset za prehrano, navade in bolj lahek začetek',
            'program-clean-9-razstrupljanje-in-izguba-teze-v-samo-9-dneh',
            'Clean 9 je kratek Forever program, ki ga ljudje pogosto uporabljajo kot strukturiran začetek za prehranski reset, lažji občutek in več discipline v prvih devetih dneh.',
            '<ul><li>Clean 9 ponuja jasen devetdnevni okvir, kar mnogim pomaga začeti bolj urejeno.</li><li>Največja napaka je program razumeti kot čarobno rešitev namesto kot začetni reset.</li><li>Pametnejši pristop ga poveže z vodo, lahkimi obroki, gibanjem in načrtom za obdobje po programu.</li></ul>',
            $faq(
                'Kaj je Clean 9 program?',
                'Forever devetdnevni program, namenjen strukturiranemu začetku prehranskega reseta in urejanja navad.',
                'Ali je Clean 9 dolgoročna dieta?',
                'Ne. Bolj smiselno ga je gledati kot kratek začetek, po katerem mora slediti vzdržna rutina.',
                'Katera je pogosta napaka?',
                'Vse upe položiti v devet dni, brez načrta za prehrano po programu.',
                'Komu lahko ustreza?',
                'Ljudem, ki potrebujejo jasen okvir, motivacijo in prvi korak v bolj urejeno rutino.'
            ),
            'Clean 9 program: devetdnevni reset brez nerealnih obljub',
            'Odkrijte, kako Clean 9 uporabiti kot strukturiran začetek in kako pripraviti rutino po programu.',
            'Clean 9',
            $sections(
                'Zakaj kratek program lahko pomaga začetku',
                'Nekateri ljudje lažje začnejo, ko imajo jasen okvir, seznam korakov in občutek, da se premik začne danes.',
                'Zakaj je obdobje po programu najpomembnejše',
                'Prava vrednost Clean 9 ni samo v devetih dneh, temveč v tem, ali po njem nastane boljša vsakodnevna prehranska struktura.'
            )
        ),
        $entry(
            553,
            'Keto dieta jedilnik za 7 dni: recepti, izkušnje in praktičen začetek ketogene prehrane',
            'ketogena-dieta-jedilnik-za-7-dni-recepti-in-izkusnje-za-uspesno-ketogeno-dieto',
            'Sedemdnevni keto jedilnik lahko začetnikom odstrani veliko zmede, vendar mora biti dovolj hranilen, sitosten in realno izvedljiv, da ne postane samo kratek eksperiment.',
            '<ul><li>7-dnevni keto jedilnik pomaga, ker zmanjša odločanje in začetni kaos.</li><li>Največja napaka je načrtovati obroke brez vlaknin, zelenjave in dovolj tekočine.</li><li>Pametnejši pristop vključuje preproste recepte, elektrolite, sitost in pripravo sestavin vnaprej.</li></ul>',
            $faq(
                'Zakaj je 7-dnevni keto jedilnik koristen?',
                'Ker začetniku pokaže konkretne obroke in zmanjša strah pred vprašanjem, kaj sploh jesti.',
                'Kaj mora imeti dober keto jedilnik?',
                'Dovolj beljakovin, kakovostnih maščob, zelenjave z manj ogljikovimi hidrati, tekočine in praktične priprave.',
                'Katera je pogosta napaka?',
                'Vnaprej izločiti veliko hrane, ne da bi pripravili dovolj dobrih alternativ.',
                'Kako se pripraviti na prvi teden?',
                'Z nakupovalnim seznamom, osnovnimi recepti, pripravo obrokov in realnimi pričakovanji.'
            ),
            'Keto dieta jedilnik za 7 dni: praktičen načrt za začetek',
            'Spoznajte, kako sestaviti 7-dnevni keto jedilnik z recepti, sitostjo in manj začetniškega kaosa.',
            'Keto jedilnik',
            $sections(
                'Zakaj primer jedilnika odstrani največ zmede',
                'Začetniki pogosto ne potrebujejo še več teorije, temveč jasen občutek, kako izgleda običajen keto dan.',
                'Zakaj mora biti keto teden logistično izvedljiv',
                'Če recepti zahtevajo preveč časa ali posebnih sestavin, se načrt hitro poruši. Zato so preprosti obroki ključni.'
            )
        ),
        $entry(
            580,
            'Liposukcijska dieta: kaj obljublja, kje so tveganja in kako razmišljati bolj pametno',
            'liposukcijska-dieta',
            'Liposukcijska dieta se pogosto predstavlja kot hiter način za izgubo kilogramov, vendar mora vsak tak pristop prestati vprašanje varnosti, vzdržnosti in dolgoročnih navad.',
            '<ul><li>Liposukcijska dieta privlači ljudi, ki želijo hiter in viden začetek.</li><li>Največja napaka je slediti agresivnemu planu brez razumevanja tveganj in osebnega konteksta.</li><li>Pametnejši pristop preveri varnost, realen vnos, beljakovine, hidracijo in načrt po dieti.</li></ul>',
            $faq(
                'Kaj je liposukcijska dieta?',
                'Običajno gre za zelo strukturiran prehranski pristop, ki se promovira kot hitrejši način za izgubo teže.',
                'Ali je tak pristop primeren za vsakogar?',
                'Ne. Pri hitrih dietah je pomembno preveriti zdravje, zdravila, energijo in individualne potrebe.',
                'Katera je pogosta napaka?',
                'Osredotočiti se samo na hitro izgubo kilogramov, brez plana za čas po dieti.',
                'Kaj je varnejše vprašanje?',
                'Ali lahko oseba po začetnem planu zgradi prehrano, ki je sitostna, hranilna in vzdržna.'
            ),
            'Liposukcijska dieta: hiter rezultat ali tvegana bližnjica?',
            'Spoznajte, kaj liposukcijska dieta obljublja, katere napake so pogoste in zakaj je vzdržnost ključna.',
            'Liposukcijska dieta',
            $sections(
                'Zakaj nas hitri sistemi tako močno pritegnejo',
                'Ko želi oseba spremembo takoj, jasna pravila in obljuba hitrega premika delujejo zelo privlačno.',
                'Zakaj mora dolgoročni načrt obstajati že na začetku',
                'Če po dieti ni realne prehranske strukture, se kratkoročen rezultat pogosto hitro izgubi.'
            )
        ),
        $entry(
            581,
            'UN dieta: pravila, izkušnje in kako jo oceniti brez prehranskega kaosa',
            'ne-dieta',
            'UN dieta je priljubljena zaradi jasnih dnevov in enostavnih pravil, vendar je pomembno razumeti, kaj tak sistem lahko ponudi in kje se lahko hitro spremeni v nepotrebno omejevanje.',
            '<ul><li>UN dieta privlači zaradi strukture, razporeda dni in občutka jasnega načrta.</li><li>Največja napaka je slepo slediti pravilom, ne da bi opazovali energijo, sitost in kakovost prehrane.</li><li>Pametnejši pristop oceni, ali sistem res pomaga navadam ali samo začasno omeji izbiro hrane.</li></ul>',
            $faq(
                'Kaj je UN dieta?',
                'Gre za prehranski sistem z razdeljenimi dnevi, ki mnogim daje občutek jasne strukture.',
                'Ali je UN dieta primerna za dolgoročno prehrano?',
                'Za nekatere je lahko kratek okvir, vendar dolgoročno šteje uravnotežena in praktična prehrana.',
                'Katera je pogosta napaka?',
                'Osredotočiti se na pravila dni, ne pa na kakovost obrokov in resnično sitost.',
                'Kako jo je bolje oceniti?',
                'Po tem, ali pomaga ustvariti boljše navade, več kontrole in manj kaosa, ne samo po začetnem padcu teže.'
            ),
            'UN dieta: pravila, izkušnje in realen pogled',
            'Odkrijte, kako deluje UN dieta, zakaj je priljubljena in kako jo oceniti brez pretiranih pričakovanj.',
            'UN dieta',
            $sections(
                'Zakaj sistemi z jasnimi dnevi delujejo privlačno',
                'Ko je prehrana kaotična, vsaka struktura hitro ustvari občutek nadzora in lažjega odločanja.',
                'Zakaj se prava kakovost vidi kasneje',
                'Najpomembnejše vprašanje ni, ali se lahko držimo pravil nekaj dni, temveč ali po dieti ostanejo boljše navade.'
            )
        ),
    ],
];
