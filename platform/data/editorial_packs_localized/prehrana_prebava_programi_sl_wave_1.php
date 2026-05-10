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
    'key' => 'prehrana-prebava-programi-sl-wave-1',
    'name' => 'Prehrana, prebava in programi (SL) - prvi val',
    'notes' => 'Ročno lokaliziran premium val za prehranske načrte, prebavo, mikrohranila in Clean 9 vsebine.',
    'entries' => [
        $entry(
            566,
            'Medicinska dieta: kaj realno pričakovati od 15-dnevnega načrta hujšanja',
            'medicinska-dieta-15-dnevni-nacrt-hujsanja',
            'Kratki dietni načrti privlačijo, ker obljubljajo hiter premik, smisel pa imajo le, če se ne spremenijo v še en cikel restrikcije in vračanja k starim navadam. Tukaj je, kako tak načrt oceniti bolj realno in varneje.',
            '<ul><li>Kratki dietni načrti imajo smisel le kot začasen okvir, ne kot dolgoročna filozofija prehranjevanja.</li><li>Največja napaka je hiter padec teže zamenjati za trajno spremembo.</li><li>Pametnejši pristop gleda energijo, vzdržnost in to, kaj sledi po koncu načrta.</li></ul>',
            [
                ['question' => 'Zakaj je medicinska dieta tako priljubljena?', 'answer' => 'Ker obljublja hiter rezultat in zelo jasen kratkoročni okvir.'],
                ['question' => 'Ali je hiter padec teže vedno dober znak?', 'answer' => 'Ne. Pomembno je, kako se telo počuti in kaj se zgodi po koncu načrta.'],
                ['question' => 'Kdaj ima tak načrt več smisla?', 'answer' => 'Ko služi kot kratki reset z realnimi pričakovanji, ne kot ponavljajoča skrajnost.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Takoj po koncu diete se vrniti v popolnoma enak stari vzorec.'],
            ],
            'Medicinska dieta: kako presoditi 15-dnevni načrt brez iluzij o hitri rešitvi',
            'Spoznajte, kdaj je lahko medicinska dieta smiselna in kako se izogniti največjim napakam kratkih dietnih pristopov.',
            'Medicinska dieta',
            [
                ['heading' => 'Zakaj kratki načrti delujejo le, če imajo nadaljevanje', 'html' => '<p>Hitri načrti pogosto delujejo močno, ker prinesejo takojšnjo strukturo. Njihova prava vrednost pa se pokaže šele, ko ima človek po koncu še vedno mirnejši in bolj vzdržen okvir.</p>'],
                ['heading' => 'Zakaj hitrost ni dovolj za dobro presojo', 'html' => '<p>Hitra sprememba se lahko zdi zelo motivirajoča, vendar sama po sebi še ne pomeni, da je pristop dober za vsakdan. Trajnejši rezultat običajno pride takrat, ko se izboljša tudi odnos do rutine.</p>'],
            ]
        ),
        $entry(
            593,
            'Omega-3 maščobne kisline: 7 koristi, ki imajo smisel le v pravem kontekstu',
            'omega-3-mascobne-kisline-7-kljucnih-koristi',
            'Omega-3 so tako znane, da hitro postanejo pretiran odgovor za skoraj vse. Tukaj je, kako njihove koristi razumeti bolj realno skozi prehrano, vnetno ravnovesje in dolgoročne navade.',
            '<ul><li>Omega-3 imajo največ smisla v širši sliki prehrane, okrevanja in podpore srcu.</li><li>Največja napaka je pričakovati, da bo en dodatek opravil delo celotnega življenjskega sloga.</li><li>Pametnejši pristop gleda doslednost, prehranske vire in pravi razlog za uporabo.</li></ul>',
            [
                ['question' => 'Zakaj ljudje najpogosteje jemljejo omega-3?', 'answer' => 'Najpogosteje zaradi podpore srcu, možganom in ravnovesju vnetnih procesov.'],
                ['question' => 'Ali lahko omega-3 nadomestijo slabo prehrano?', 'answer' => 'Ne. Imajo smisel kot podpora, ne pa kot nadomestek za boljše navade.'],
                ['question' => 'Kdaj imajo več smisla?', 'answer' => 'Ko obstaja jasen cilj in ko so del širše rutine.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kupiti omega-3 samo zato, ker zvenijo zdravo, brez jasnega razloga.'],
            ],
            'Omega-3 koristi: kdaj imajo res smisel in kako jih razumeti bolj realistično',
            'Razumite glavne koristi omega-3 maščobnih kislin in kje imajo res smisel v vsakdanji prehrani in okrevanju.',
            'Omega-3',
            [
                ['heading' => 'Zakaj je zgodba o omega-3 večja od enega dodatka', 'html' => '<p>Omega-3 se pogosto predstavlja kot rešitev za skoraj vse, vendar je njena prava vrednost največja takrat, ko podpira širši vzorec prehrane in okrevanja. Prav zato je pomembneje razumeti kontekst kot slediti hypeu.</p>'],
                ['heading' => 'Zakaj je razlog za uporabo tako pomemben', 'html' => '<p>Dopolnila so običajno najbolj koristna, ko obstaja jasen razlog za njihovo uporabo. Bolj kot je cilj razumljiv, lažje je oceniti, ali omega-3 res spada v rutino.</p>'],
            ]
        ),
        $entry(
            594,
            'Vitamin D: zakaj je pomemben in kako ga izboljšati brez pretiravanja',
            'vitamin-d-zakaj-je-pomemben-in-kako-ga-optimalno-vnesti',
            'Vitamin D je skoraj postal sinonim za imunost in dobro počutje, njegova prava vrednost pa se pokaže šele ob premišljeni uporabi. Tukaj je, kako se ga lotiti skozi sonce, hrano in dodatke brez ugibanja.',
            '<ul><li>Vitamin D je pomemben za kosti, imunost in širšo vitalnost, vendar ne potrebuje vsak človek iste strategije.</li><li>Največja napaka je kopirati tuje dopolnilne navade brez lastnega konteksta.</li><li>Pametnejši pristop gleda sonce, prehrano in smiselno podporo skozi čas.</li></ul>',
            [
                ['question' => 'Zakaj je vitamin D tako pomemben?', 'answer' => 'Ker sodeluje pri zdravju kosti, imunosti in številnih regulacijskih procesih v telesu.'],
                ['question' => 'Ali vsak potrebuje dodatek?', 'answer' => 'Ne vedno. Odvisno je od sezone, sonca, prehrane in osebnega konteksta.'],
                ['question' => 'Kako ga je najbolje izboljšati?', 'answer' => 'Običajno s smiselno kombinacijo sonca, prehrane in dodatkov, ko za to obstaja razlog.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Vitamin D spremeniti v univerzalni odgovor namesto širšega pogleda na zdravje.'],
            ],
            'Vitamin D: kako ga izboljšati bolj pametno in brez nepotrebnega pretiravanja',
            'Preverite, zakaj je vitamin D pomemben in kako se ga lotiti skozi sonce, hrano in dodatke z več realizma.',
            'Vitamin D',
            [
                ['heading' => 'Zakaj vitamin D potrebuje kontekst in ne le hypea', 'html' => '<p>Vitamin D ima zelo močan ugled, zato hitro zveni kot odgovor na skoraj vse. Njegova prava vrednost pa je veliko jasnejša, ko ga povežemo z letnim časom, navadami in dejansko potrebo posameznika.</p>'],
                ['heading' => 'Zakaj običajno zmaga uravnotežen pristop', 'html' => '<p>Najbolj smiseln pristop k vitaminu D običajno vključuje nekaj sonca, boljšo prehrano in dodatke takrat, ko so zares potrebni. Skrajnosti v eno ali drugo smer redko prinesejo največ koristi.</p>'],
            ]
        ),
        $entry(
            606,
            'Spomladanski detoks z juhami, smutiji in vadbo: kaj res pomaga in kaj je predvsem občutek',
            'spomladanski-detoks-z-juhami-smutiji-in-vadbo',
            'Spomladanski detoks zveni privlačno, ker obljublja nov začetek, prava korist pa se pokaže le, če reset ne postane še ena kratka skrajnost. Tukaj je, kako juhe, smutije in gibanje uporabiti brez dramatične detoks mitologije.',
            '<ul><li>Spomladanski reset najbolje deluje kot korak k preprostejši prehrani in več gibanja, ne kot mitsko “čiščenje”.</li><li>Največja napaka je ustvariti kratek val navdušenja brez načrta za naprej.</li><li>Pametnejši pristop sezono uporabi za več reda, hidracije in lažji ritem obrokov.</li></ul>',
            [
                ['question' => 'Ali mora biti detoks ekstremen, da pomaga?', 'answer' => 'Ne. Mirnejši in preprostejši reset je pogosto bolj koristen in lažje vzdržen.'],
                ['question' => 'Zakaj so juhe in smutiji tako priljubljeni?', 'answer' => 'Ker olajšajo občutek lahkotnosti, hidracije in vnosa zelenjave.'],
                ['question' => 'Kje je prostor za vadbo?', 'answer' => 'Kot del širšega ritma energije, ne kot kazen za prejšnje navade.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kratek reset zamenjati za resnično spremembo prehranskih in gibalnih navad.'],
            ],
            'Spomladanski detoks: kako ga spremeniti v lažji reset namesto kratke skrajnosti',
            'Odkrijte, kako je lahko spomladanski detoks z juhami, smutiji in vadbo smiseln brez pretiranih obljub.',
            'Spomladanski detoks',
            [
                ['heading' => 'Zakaj reset jezik hkrati pomaga in zavaja', 'html' => '<p>Veliko ljudi potrebuje občutek novega začetka in prav zato reset zgodbe zvenijo tako privlačno. Težava se začne takrat, ko simbolni nov začetek zamenjamo za čudežno spremembo brez nadaljnje strukture.</p>'],
                ['heading' => 'Zakaj lažji sistemi običajno trajajo dlje', 'html' => '<p>Preproste spremembe v hrani in gibanju je veliko lažje obdržati kot stroge kratke protokole. Zato so pogosto prav najmanj dramatične spremembe tiste, ki ostanejo najdlje.</p>'],
            ]
        ),
        $entry(
            608,
            'C9 recepti: 5 hitrih obrokov, ki olajšajo Clean 9 program',
            'c9-recepti-5-hitrih-jedi-za-uspeh-v-clean-9-programu',
            'Največji izziv pri strukturiranem programu pogosto ni volja, ampak praktičnost. Tukaj je, kako lahko nekaj hitrih obrokov Clean 9 naredi lažje izvedljiv in manj stresen.',
            '<ul><li>Preprosti in predvidljivi obroki običajno najbolj pomagajo pri sledenju strukturiranemu programu.</li><li>Največja napaka je začeti Clean 9 brez jasnega načrta hrane in nekaj varnih hitrih možnosti.</li><li>Pametnejši pristop zmanjša število odločitev in obroke spremeni v podporo, ne dodatno breme.</li></ul>',
            [
                ['question' => 'Zakaj so recepti na Clean 9 tako pomembni?', 'answer' => 'Ker preprosti obroki povečajo doslednost in zmanjšajo prehranski stres.'],
                ['question' => 'Ali morajo biti jedi zapletene?', 'answer' => 'Ne. Najbolj pomagajo jasne, hitre in ponovljive jedi.'],
                ['question' => 'Kaj je ključno pred začetkom?', 'answer' => 'Osnovni nakup, preprost načrt in nekaj zanesljivih obrokov.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Prepustiti hrano naključju medtem ko skušate slediti strukturiranemu programu.'],
            ],
            'C9 recepti: 5 praktičnih jedi za lažji in bolj vzdržen Clean 9',
            'Preverite, kako lahko 5 hitrih C9 receptov olajša Clean 9 program in zmanjša prehranski stres.',
            'C9 recepti',
            [
                ['heading' => 'Zakaj je praktična hrana boljša od popolne hrane', 'html' => '<p>Ko je načrt že dovolj strukturiran, so najbolj koristni tisti obroki, ki zmanjšajo trenje v dnevu. Hrana začne res pomagati takrat, ko poenostavi rutino namesto da ustvarja še več odločitev.</p>'],
                ['heading' => 'Zakaj je ponavljanje pogosto prednost', 'html' => '<p>Veliko ljudi misli, da je raznolikost vedno boljša, vendar pri kratkem programu pogosto zmaga prav doslednost. Nekaj zanesljivih jedi lahko celoten načrt naredi bistveno lažji.</p>'],
            ]
        ),
        $entry(
            610,
            'Kuhanje pri nizkih temperaturah: kako ohraniti več hranil v vsakdanjih obrokih',
            'kuhanje-pri-nizkih-temperaturah-ohranjanje-hranil',
            'Način priprave hrane pogosto pomeni skoraj toliko kot sama izbira živil. Tukaj je, kako lahko nižje temperature pomagajo ohraniti teksturo, vlago in del občutljivejših hranil brez pretvarjanja kuhanja v laboratorij.',
            '<ul><li>Nežnejša toplota lahko pomaga ohraniti teksturo, vlago in del občutljivejših hranilnih lastnosti.</li><li>Največja napaka je misliti, da mora biti bolj zdravo kuhanje nujno bolj zapleteno.</li><li>Pametnejši pristop išče ravnotežje med praktičnostjo, okusom in potrebami sestavin.</li></ul>',
            [
                ['question' => 'Kaj pomeni kuhati pri nizkih temperaturah?', 'answer' => 'To pomeni hrano pripravljati z nežnejšo toploto, da se bolje ohranita okus in tekstura.'],
                ['question' => 'Ali so vsa hranila občutljiva na toploto?', 'answer' => 'Ne enako, a nekatere spojine in vitamini so bolj občutljivi od drugih.'],
                ['question' => 'Ali mora biti ta metoda zapletena?', 'answer' => 'Ne. Tudi preprosto vsakodnevno kuhanje je lahko bolj nežno do hrane.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Bolj zdravo kuhanje spremeniti v nerealno zahteven projekt.'],
            ],
            'Kuhanje pri nizkih temperaturah: kako ohraniti več okusa in hranil',
            'Spoznajte, kako lahko kuhanje pri nizkih temperaturah pomaga ohraniti kakovost hrane brez večje zapletenosti.',
            'Kuhanje pri nizkih temperaturah',
            [
                ['heading' => 'Zakaj način kuhanja oblikuje obrok bolj, kot mislimo', 'html' => '<p>Kakovost hrane ni odvisna le od sestavin, ampak tudi od tega, kaj se z njimi zgodi med pripravo. Nežnejše kuhanje pogosto pomaga ohraniti okus, vlago in prijetnejši občutek jedi.</p>'],
                ['heading' => 'Zakaj preprosta tehnika pogosto pomeni več kot popolna tehnika', 'html' => '<p>Cilj ni kuhati kot znanstvenik, temveč razumeti, kdaj je nežnejša toplota smiselna in kako to prenesti v vsakdan. Že majhne prilagoditve lahko naredijo razliko.</p>'],
            ]
        ),
        $entry(
            613,
            'Ajurvedska prehrana: doše, letni časi in kako idejo uporabiti brez mistike',
            'ajurvedski-pristop-k-prehrani-skozi-dose-in-letne-case',
            'Ajurveda je privlačna, ker hrano povezuje z ritmom telesa in letnih časov, uporabna pa postane šele, ko jo prevedemo v vsakdan. Tukaj je, kako ta pogled uporabiti brez togih in mističnih pravil.',
            '<ul><li>Ajurvedski pogled je lahko koristen, ko pomaga opaziti ritem, prebavo in sezonske spremembe.</li><li>Največja napaka je iz njega narediti tog sistem z malo praktične koristi.</li><li>Pametnejši pristop ohrani uporabne dele: toplino obrokov, sezono in osebni odziv na hrano.</li></ul>',
            [
                ['question' => 'Kaj so doše v praksi?', 'answer' => 'Tradicionalni okvir za opis različnih vzorcev telesa, energije in prebave.'],
                ['question' => 'Ali je treba vse slediti dobesedno?', 'answer' => 'Ne. Največ smisla ima vzeti tisto, kar je uporabno in izvedljivo v vsakdanu.'],
                ['question' => 'Zakaj so letni časi pomembni?', 'answer' => 'Ker lahko vplivajo na izbiro hrane, ritem obrokov in občutek v telesu.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Praktično pozornost do hrane zamenjati s strogim sistemom prepričanj.'],
            ],
            'Ajurvedska prehrana: kako uporabiti doše in sezono brez pretiravanja',
            'Odkrijte, kako ajurvedski pristop k prehrani praktično uporabiti skozi doše, sezono in boljši občutek ravnovesja.',
            'Ajurvedska prehrana',
            [
                ['heading' => 'Zakaj ljudi privlačijo sezonski prehranski okviri', 'html' => '<p>Veliko ljudi ima občutek, da je sodobna prehrana preveč odrezana od ritma telesa in letnih časov, zato so starejši okviri še vedno privlačni. Njihova moč je pogosto v večji pozornosti, ne v slepi poslušnosti pravilom.</p>'],
                ['heading' => 'Zakaj je praktičnost pomembnejša od ideologije', 'html' => '<p>Najbolj uporabni deli tradicionalnih sistemov so običajno tisti, ki pomagajo človeku bolje opaziti svoje telo. Dober okvir življenje poenostavi, ne pa dodatno zaplete.</p>'],
            ]
        ),
        $entry(
            632,
            'Alergije na hrano: kako sestaviti varen plan brez mleka, glutena in arašidov',
            'alergije-na-hrano-vodic-brez-mleka-glutena-in-arasidov',
            'Prehranski plan brez mleka, glutena ali arašidov hitro postane vir stresa, če nima jasne strukture. Tukaj je, kako alergije na hrano obravnavati z več varnosti, organizacije in manj prehranske panike.',
            '<ul><li>Pri alergijah na hrano so najpomembnejši varnost, jasnost in dobro načrtovanje obrokov.</li><li>Največja napaka je panično izločiti preveč živil brez jasnega razumevanja sprožilca in nove strukture prehrane.</li><li>Pametnejši pristop gradi varen, hranljiv in izvedljiv vsakdanji načrt.</li></ul>',
            [
                ['question' => 'Zakaj je tak načrt tako zahteven?', 'answer' => 'Ker zahteva več branja deklaracij, načrtovanja in zanesljivih zamenjav.'],
                ['question' => 'Ali je treba izločiti več živil naenkrat?', 'answer' => 'Le, če za to obstaja jasen razlog in če načrt ostane prehransko smiseln.'],
                ['question' => 'Kaj najbolj pomaga v praksi?', 'answer' => 'Načrtovanje, varni rezervni obroki in preproste zamenjave, ki zmanjšajo kaos.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Izločilno prehrano spremeniti v široko prepoved brez trajnejše strukture.'],
            ],
            'Alergije na hrano: kako zgraditi varnejši plan brez mleka, glutena in arašidov',
            'Spoznajte, kako pri alergijah na hrano prehrano urediti bolj varno, jasno in vzdržno brez nepotrebne panike.',
            'Alergije na hrano',
            [
                ['heading' => 'Zakaj je pri alergijah vse odvisno od jasnosti', 'html' => '<p>Ko je hrana lahko tveganje, negotovost zelo hitro ustvari stres. Zato so najmočnejše alergijske rutine običajno najpreprostejše: jasne sestavine, jasni rezervni obroki in manj zadnjih minut ugibanja.</p>'],
                ['heading' => 'Zakaj mora omejevanje ostati organizirano', 'html' => '<p>Izločiti sprožilec je le prvi del naloge. Drugi del je zagotoviti, da človek še vedno vsak dan dobi dovolj varne, hranljive in praktične hrane.</p>'],
            ]
        ),
        $entry(
            636,
            'Avtoimuno vnetje črevesja: prehrana in probiotiki pri Crohnu in kolitisu v realnem okviru',
            'avtoimuno-vnetje-crevesja-probiotiki-in-prehrana-za-crohn-in-kolitis',
            'Pri Crohnu in kolitisu je hrana pomembna, ni pa čarobno stikalo za kompleksno stanje. Tukaj je, kako prehrano in probiotike gledati kot podporo vsakdanjemu življenju in ne kot preveč poenostavljeno obljubo.',
            '<ul><li>Prehrana in probiotiki lahko podprejo počutje, ritem prebave in dnevno stabilnost, ne pa enako pri vsakem.</li><li>Največja napaka je iskati eno popolno dieto, ki bi morala delovati za vse z isto diagnozo.</li><li>Pametnejši pristop gleda osebno toleranco, fazo simptomov in postopne spremembe.</li></ul>',
            [
                ['question' => 'Ali lahko probiotiki pomagajo pri Crohnu ali kolitisu?', 'answer' => 'Lahko so del podpore, vendar ne enako v vsaki fazi ali pri vsaki osebi.'],
                ['question' => 'Zakaj prehrana ni enaka za vse?', 'answer' => 'Ker se toleranca hrane in intenzivnost simptomov močno razlikujeta.'],
                ['question' => 'Kaj je v praksi najbolj koristno?', 'answer' => 'Opazovanje vzorcev, zapisovanje in počasno, premišljeno uvajanje sprememb.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kopirati tuj prehranski načrt brez preverjanja lastnih reakcij in omejitev.'],
            ],
            'Crohn in kolitis: kako sta prehrana in probiotiki lahko podpora brez lažnih obljub',
            'Razumite, kako lahko prehrana in probiotiki pri Crohnu in kolitisu podpirajo bolj realno in postopno.',
            'Crohn in kolitis',
            [
                ['heading' => 'Zakaj mora prebavna podpora ostati osebna', 'html' => '<p>Ljudje si pogosto želimo enega pravila, ki bi povedalo, kaj jesti in čemu se izogniti, vendar vnetne črevesne bolezni redko delujejo tako enostavno. Podpora je veliko bolj uporabna, ko spoštuje posameznikov vzorec in ne išče univerzalne formule.</p>'],
                ['heading' => 'Zakaj so postopne spremembe pogosto varnejše od dramatičnih', 'html' => '<p>Veliki prehranski obrati lahko ustvarijo dodatno zmedo, če je telo že občutljivo. Počasnejše prilagoditve navadno olajšajo opazovanje, kaj res pomaga in kaj samo dodaja šum.</p>'],
            ]
        ),
        $entry(
            813,
            'Clean 9 in zdravstvene težave: ali lahko začnete in kako načrt pametno prilagoditi',
            'clean-9-in-zdravstvene-tezave-ali-lahko-zacnete',
            'Clean 9 ni program, v katerega je smiselno skočiti na slepo, posebej ob obstoječih zdravstvenih težavah ali bolj občutljivem telesu. Tukaj je, kako oceniti, ali je program smiseln in kaj pomeni pametna prilagoditev.',
            '<ul><li>Strukturirani programi imajo smisel le, ko se ujemajo z resničnim zdravstvenim kontekstom osebe.</li><li>Največja napaka je začeti Clean 9 z miselnostjo “samo zdržati moram”.</li><li>Pametnejši pristop sprašuje po varnosti, prilagoditvi in tem, ali je program sploh prava izbira za ta trenutek.</li></ul>',
            [
                ['question' => 'Ali lahko vsak začne Clean 9?', 'answer' => 'Ne nujno. Obstoječe zdravstvene težave lahko pomembno vplivajo na primernost programa.'],
                ['question' => 'Kaj pomeni prilagoditi načrt?', 'answer' => 'Zmanjšati togost in ga uskladiti z energijo, toleranco in resničnim kontekstom osebe.'],
                ['question' => 'Kdaj ima manj smisla?', 'answer' => 'Ko bi ustvaril več obremenitve kot koristi ali ko telo potrebuje drugačen pristop.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Program obravnavati kot test discipline namesto kot orodje, ki mora služiti človeku.'],
            ],
            'Clean 9 in zdravje: kdaj program ustreza in kdaj ga je treba prilagoditi',
            'Poglejte, kdaj ima Clean 9 ob zdravstvenih težavah smisel in kaj pomeni varnejša, bolj pametna prilagoditev.',
            'Clean 9 in zdravje',
            [
                ['heading' => 'Zakaj noben program ne sme biti pomembnejši od človeka', 'html' => '<p>Strukturirani sistemi so privlačni, ker prinašajo jasnost, a jasnost še ne pomeni primernosti. Zdravstveni kontekst posameznika mora vedno pomeniti več kot eleganca samega programa.</p>'],
                ['heading' => 'Zakaj pametna prilagoditev ni znak neuspeha', 'html' => '<p>Ljudje pogosto prilagoditev doživimo kot šibkost, v resnici pa je prav prilagoditev pogosto tista, ki program naredi bolj varen in uporaben. Fleksibilnost je pogosto znak realizma, ne manjše discipline.</p>'],
            ]
        ),
    ],
];
