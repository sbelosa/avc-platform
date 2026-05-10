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

$slugMap = [
    40 => 'omega-3-kapsule-odkrijte-moc-forever-arctic-sea-za-zdravje-srca-in-mozganov',
    41 => 'c9-forever-living-products-detox-program-za-zdravo-uravnavanje-telesne-teze',
    43 => 'forever-multi-maca-povecajte-libido-in-uravnovesite-hormone',
    45 => 'aloe-vera-za-obraz-ugotovite-kako-naravno-negovati-svojo-kozo',
    47 => 'aloe-vera-gel',
    49 => 'forever-active-pro-b-mocan-probiotik-za-zdravo-prebavo-in-imunost',
    50 => 'garcinia-cambogia-za-uravnavanje-telesne-teze',
    51 => 'forever-first-hranilni-sprej-za-popolno-nego-koze-in-las',
    52 => 'aloe-vera-gelly-naravna-zelena-krema-za-nego-koze',
    53 => 'zobna-pasta-brez-fluorida-forever-bright-naravna-nega-zob',
    37 => '10-glavnih-razlogov-zakaj-niste-vodja-in-kako-to-spremeniti',
    38 => 'kako-razmisljate-in-kaksen-vpliv-ima-okolje-na-vas',
    39 => 'kako-narediti-dober-prvi-vtis-kljucni-nasveti-za-uspeh',
    42 => 'vzorci-pozitivnega-razmisljanja-kako-pozitivne-misli-vodijo-do-uspeha',
    55 => 'kako-postati-partner-forever-living-products-in-zgraditi-podjetje-od-doma',
    65 => 'razumevanje-trzenja-mlm-odkrijte-pot-do-uspeha',
    66 => 'kaj-je-mrezni-marketing-mlm-kljucni-koraki-do-uspeha',
    67 => 'kako-uspeti-v-zivljenju-nasveti-za-doseganje-ciljev-in-financno-svobodo',
    88 => 'kako-ostati-motiviran-in-premagati-samega-sebe',
    93 => '14-znakov-custvene-inteligence-razvijte-svoj-eq-na-visjo-raven',
];

$entry = static function (
    int $sourceId,
    string $title,
    string $slugOrExcerpt,
    string $excerptOrSummary,
    string|array $summaryOrFaq,
    array|string $faqOrMetaTitle,
    string|array|null $metaTitle = null,
    string|array|null $metaDescription = null,
    string|array|null $breadcrumbTitle = null,
    ?array $sectionsList = null
) use ($slugMap): array {
    if (is_array($summaryOrFaq)) {
        $slug = $slugMap[$sourceId] ?? '';
        $excerpt = $slugOrExcerpt;
        $summaryHtml = $excerptOrSummary;
        $faqItems = $summaryOrFaq;
        $resolvedMetaTitle = (string) $faqOrMetaTitle;
        $resolvedMetaDescription = (string) $metaTitle;
        $resolvedBreadcrumbTitle = (string) $metaDescription;
        $resolvedSections = $breadcrumbTitle ?? [];
    } else {
        $slug = $slugOrExcerpt;
        $excerpt = $excerptOrSummary;
        $summaryHtml = $summaryOrFaq;
        $faqItems = is_array($faqOrMetaTitle) ? $faqOrMetaTitle : [];
        $resolvedMetaTitle = (string) $metaTitle;
        $resolvedMetaDescription = (string) $metaDescription;
        $resolvedBreadcrumbTitle = (string) $breadcrumbTitle;
        $resolvedSections = $sectionsList ?? [];
    }

    return [
        'source_translation_id' => $sourceId,
        'language_code' => 'sl',
        'title' => $title,
        'slug' => $slug,
        'excerpt' => $excerpt,
        'summary_html' => $summaryHtml,
        'faq_items' => $faqItems,
        'meta_title' => $resolvedMetaTitle,
        'meta_description' => $resolvedMetaDescription,
        'breadcrumb_title' => $resolvedBreadcrumbTitle,
        'sections' => $resolvedSections,
    ];
};

return [
    'key' => 'legacy-foundations-sl-wave-1',
    'name' => 'Legacy foundations (SL) - prvi val',
    'notes' => 'Ročno lokaliziran premium val za starejše Forever/product vsebine in legacy business/mindset članke.',
    'entries' => [
        $entry(
            40,
            'Omega 3 kapsule: kdaj Forever Arctic Sea resnično podpira srce, možgane in vsakodnevni fokus',
            'omega-3-kapsule-odkrijte-moc-forever-arctic-sea-za-zdravje-srca-in-mozganov',
            'Omega-3 dodatki imajo največ smisla, ko jih gledamo skozi prehrano, navade in realne cilje, ne pa skozi eno veliko obljubo. Tukaj je, kje se Forever Arctic Sea lahko smiselno vključi in kako ga oceniti brez pretiravanja.',
            '<ul><li>Omega-3 kapsule imajo največ smisla kot dopolnilo prehrani z malo mastnih rib in kakovostnih maščob.</li><li>Največja napaka je pričakovati, da bo en dodatek sam uredil fokus, energijo in zdravje srca.</li><li>Pametnejši pristop gleda kakovost vira, rednost jemanja in širši življenjski slog.</li></ul>',
            $faq(
                'Komu omega-3 kapsule pogosto najbolj koristijo?',
                'Običajno ljudem, ki redko jedo mastne ribe in želijo praktično dnevno podporo.',
                'Ali lahko Forever Arctic Sea nadomesti dobro prehrano?',
                'Ne. Največjo vrednost ima kot del širše prehranske rutine.',
                'Kaj ljudje najpogosteje iščejo pri omega-3 podpori?',
                'Največkrat podporo srcu, možganom, fokusu in občutku vitalnosti.',
                'Katera je pogosta napaka?',
                'Kupiti omega-3 izdelek brez pogleda na preostalo prehrano in navade.'
            ),
            'Omega 3 kapsule: kdaj Forever Arctic Sea podpira srce in možgane',
            'Spoznajte, kdaj imajo omega-3 kapsule, kot je Forever Arctic Sea, največ smisla in kako jih oceniti skozi prehrano in navade.',
            'Omega 3 kapsule',
            $sections(
                'Zakaj omega-3 hitro zveni večji, kot je',
                'Omega-3 podpora je lahko koristna, a največ pomaga takrat, ko jo razumemo kot del širšega prehranskega vzorca, ne kot bližnjico.',
                'Zakaj je rutina še vedno najpomembnejša',
                'Dodatki največ pomenijo, ko spanec, stres, prehrana in dnevni ritem že gredo v boljšo smer.'
            )
        ),
        $entry(
            41,
            'C9 Forever Living Products: komu ta reset program ustreza in kdaj je bolje upočasniti',
            'C9 privlači, ker obljublja hiter reset, prava vrednost programa pa je odvisna od pričakovanj, discipline in zdravstvenega konteksta. Tukaj je, kako ga pogledati bolj realistično še pred začetkom.',
            '<ul><li>C9 ima največ smisla kot kratek strukturiran začetek za ljudi, ki želijo jasnejši okvir za reset.</li><li>Največja napaka je pričakovati trajno spremembo brez načrta za obdobje po devetih dneh.</li><li>Pametnejši pristop preveri pripravljenost, tempo in vzdržnost še pred začetkom.</li></ul>',
            $faq(
                'Komu je C9 pogosto bolj smiseln?',
                'Ljudem, ki želijo kratek in jasen začetek ter so pripravljeni nadaljevati z boljšimi navadami.',
                'Ali je C9 sam dovolj za dolgoročno spremembo teže?',
                'Ne. Dolgoročni rezultat je še vedno odvisen od prehrane, gibanja in navad po programu.',
                'Kaj je dobro oceniti pred začetkom?',
                'Zdravstveni kontekst, pričakovanja, dnevni ritem in sposobnost, da načrt izvedete dosledno.',
                'Katera je pogosta napaka?',
                'Obravnavati C9 kot bližnjico namesto kot kratko fazo širše spremembe.'
            ),
            'C9 Forever: kdaj devetdnevni reset ima smisel in kdaj ne',
            'Odkrijte, komu je C9 lahko smiseln kot kratek reset in kako se izogniti najpogostejšim napakam.',
            'C9 Forever',
            $sections(
                'Zakaj reset deluje le z nadaljevanjem',
                'Kratki programi lahko ustvarijo zagon, toda trajnejši rezultat pride šele, ko so tudi dnevi po programu dobro načrtovani.',
                'Zakaj je realizem pomembnejši od začetnega navdušenja',
                'Najboljši začetek nastane takrat, ko program ustreza človeku, ne pa takrat, ko ga sili samo navdušenje.'
            )
        ),
        $entry(
            43,
            'Forever Multi Maca: kdaj podpora za libido in energijo resnično pomaga in kdaj pričakovanja zbežijo predaleč',
            'Maca je priljubljena, ker jo ljudje povezujejo z energijo, libidom in hormonskim ravnovesjem, njena prava vrednost pa se pokaže šele brez čudežnih obljub. Tukaj je, kako Multi Maco oceniti bolj zrelo.',
            '<ul><li>Multi Maca ima največ smisla, ko obstaja jasen razlog za uporabo in dovolj potrpežljivosti za oceno učinka.</li><li>Največja napaka je pričakovati, da bo en dodatek hkrati rešil stres, utrujenost in hormonske težave.</li><li>Pametnejši pristop najprej pogleda spanec, prehrano in okrevanje.</li></ul>',
            $faq(
                'Zakaj ljudje posežejo po Multi Maci?',
                'Najpogosteje zaradi energije, vitalnosti ter zanimanja za libido ali hormonsko podporo.',
                'Ali lahko maca sama reši pomanjkanje energije?',
                'Ne. Največ smisla ima kot del boljše dnevne rutine.',
                'Zakaj so realna pričakovanja tako pomembna?',
                'Ker dodatek najbolje ocenimo šele v širši sliki življenjskega sloga in stresa.',
                'Katera je pogosta napaka?',
                'Kupiti izdelek zaradi obljub, ne da bi pogledali pravi razlog za utrujenost ali padec libida.'
            ),
            'Forever Multi Maca: kdaj ima smisel za energijo in libido',
            'Spoznajte, kdaj ima Forever Multi Maca več smisla in kako jo oceniti brez nerealnih pričakovanj.',
            'Forever Multi Maca',
            $sections(
                'Zakaj je kontekst pomembnejši od obljube',
                'Izdelki za vitalnost zvenijo močno, a so res smiselni šele takrat, ko je jasen razlog za uporabo in ko osnove niso prezrte.',
                'Zakaj en dodatek ne more nositi cele zgodbe',
                'Nizek libido ali nizek občutek energije je pogosto del širše slike spanja, stresa in okrevanja.'
            )
        ),
        $entry(
            45,
            'Aloe vera za obraz: kdaj ta preprosta nega koži res pomaga in kdaj sama ni dovolj',
            'Aloe vera za obraz največkrat pomaga takrat, ko koža potrebuje pomiritev, lahkotno hidracijo in enostavnejšo rutino, ni pa univerzalni odgovor za vsako težavo kože. Tukaj je, kako jo uporabljati pametneje.',
            '<ul><li>Aloe vera za obraz pogosto ustreza koži, ki želi bolj nežen in pomirjujoč pristop.</li><li>Največja napaka je pričakovati, da bo en aloe izdelek sam rešil akne, madeže in oslabljeno bariero.</li><li>Pametnejši pristop sledi tipu kože, prenašanju in celotni rutini nege.</li></ul>',
            $faq(
                'Komu aloe vera za obraz pogosto najbolj ustreza?',
                'Občutljivejši, razdraženi ali dehidrirani koži, ki se bolje odziva na preprostejšo rutino.',
                'Ali je lahko koristna po soncu ali draženju?',
                'Da, lahko prinese prijeten občutek pomiritve in hlajenja.',
                'Ali zadostuje za vsako kožno težavo?',
                'Ne. Zapletenejše težave običajno zahtevajo širši in bolj ciljan pristop.',
                'Katera je pogosta napaka?',
                'Pretvoriti aloe vero v edini korak nege ne glede na cilj ali tip kože.'
            ),
            'Aloe vera za obraz: kdaj ima smisel za pomiritev in hidracijo',
            'Odkrijte, kdaj aloe vera za obraz lahko pomaga koži in kako jo umestiti v rutino brez pretiranih pričakovanj.',
            'Aloe vera za obraz',
            $sections(
                'Zakaj včasih deluje preprostejša nega',
                'Koža, ki je obremenjena s preveč izdelki, se pogosto bolje odzove na mirnejšo rutino s poudarkom na udobju in doslednosti.',
                'Zakaj mora aloe ostati v svoji vlogi',
                'Aloe je lahko zelo koristna za pomirjanje, vendar ne more prevzeti vloge celotnega načrta za kožo.'
            )
        ),
        $entry(
            47,
            'Aloe Vera Gel: kako oceniti ta klasični izdelek za prebavo, dnevno rutino in občutek vitalnosti',
            'Aloe Vera Gel je eden najbolj prepoznavnih Forever izdelkov, ker ga ljudje povezujejo s prebavo, dnevnim resetom in občutkom ravnovesja. Tukaj je, kako ga gledati bolj realistično in komu lahko bolj ustreza.',
            '<ul><li>Aloe Vera Gel ima največ smisla, ko iščete preprost dnevni dodatek za prebavo in občutek lahkotnejše rutine.</li><li>Največja napaka je pričakovati, da bo gel popravil slabo prehrano in neurejene navade.</li><li>Pametnejši pristop gleda doslednost, prenašanje in širši prehranski vzorec.</li></ul>',
            $faq(
                'Zakaj ljudje najpogosteje posežejo po Aloe Vera Gelu?',
                'Najpogosteje zaradi prebave, dnevne rutine in zanimanja za splošno vitalnost.',
                'Kdaj ima več smisla?',
                'Ko postane del mirnejše in bolj urejene prehranske rutine, ne pa hitra rešitev.',
                'Ali lahko nadomesti delo na prehrani?',
                'Ne. Največ vrednosti ima kot podpora ob boljši prehrani in ritmu obrokov.',
                'Katera je pogosta napaka?',
                'Začeti z gelom brez realne slike o ostali prehrani in pričakovati hiter čudež.'
            ),
            'Aloe Vera Gel: kdaj ima smisel za prebavo in vsakodnevno rutino',
            'Spoznajte, kdaj je Aloe Vera Gel smiseln del dnevne rutine za prebavo in kako ga oceniti brez pretiravanja.',
            'Aloe Vera Gel',
            $sections(
                'Zakaj rutina pomeni več kot hype',
                'Izdelki, ki postanejo del vsakdana, največ pomenijo takrat, ko se uporabljajo znotraj ritma, ki že podpira boljšo prebavo.',
                'Zakaj mora izdelek ostati del širše slike',
                'Aloe gel je lahko uporaben vsakodnevni korak, vendar deluje najbolje kot del širše zdravstvene rutine.'
            )
        ),
        $entry(
            49,
            'Forever Active Pro B: kdaj probiotik resnično podpira prebavo in vsakodnevno odpornost',
            'Probiotik je najbolj smiseln, kadar obstaja jasen razlog za njegovo uvedbo, ne pa zgolj želja po “močnejši imunosti”. Tukaj je, kako oceniti Active Pro B skozi prebavo, rutino in realna pričakovanja.',
            '<ul><li>Active Pro B ima največ smisla, ko obstaja konkreten cilj okoli prebave in črevesnega ravnovesja.</li><li>Največja napaka je pričakovati, da bo probiotik sam uredil vse brez sprememb v prehrani.</li><li>Pametnejši pristop gleda vlaknine, ritem obrokov in širšo sliko črevesne rutine.</li></ul>',
            $faq(
                'Kdaj ima probiotik več smisla?',
                'Ko želite bolj stabilno prebavo in mirnejšo dnevno črevesno rutino.',
                'Ali lahko probiotik sam reši vse prebavne težave?',
                'Ne. Največ koristi ima kot del širšega načrta prehrane in navad.',
                'Zakaj je doslednost pomembna?',
                'Ker se učinek dnevne rutine ocenjuje skozi čas, ne po enem ali dveh dneh.',
                'Katera je pogosta napaka?',
                'Kupiti probiotik brez pogleda na vlaknine, kakovost hrane in dnevne prehranske navade.'
            ),
            'Forever Active Pro B: kdaj probiotik res podpira prebavo',
            'Odkrijte, kdaj je Forever Active Pro B smiseln del črevesne rutine in kako ga oceniti bolj realistično.',
            'Active Pro B',
            $sections(
                'Zakaj črevesna podpora deluje po plasteh',
                'Probiotik je najkoristnejši takrat, ko se hkrati izboljšujejo hrana, ritem obrokov in dnevni vnos vlaknin.',
                'Zakaj en izdelek ne more nositi vseh pričakovanj',
                'Ljudje pogosto preveč pričakujejo od probiotika, medtem ko večja rutina še vedno ostaja nespremenjena.'
            )
        ),
        $entry(
            50,
            'Garcinia Cambogia: kje podpora pri uravnavanju teže lahko pomaga in kje marketing pretirava',
            'Garcinia Cambogia se pogosto predstavlja kot enostavna rešitev za apetit in telesno težo, njena prava vrednost pa je odvisna od navad okoli nje. Tukaj je, kako pristopiti brez napihnjenih obljub.',
            '<ul><li>Garcinia ima več smisla kot del širše strategije za apetit, porcije in energijsko ravnovesje.</li><li>Največja napaka je pričakovati, da bo dodatek sam povzročil večji padec teže.</li><li>Pametnejši pristop vpraša, zakaj teža stoji in katere dnevne navade je treba dejansko spremeniti.</li></ul>',
            $faq(
                'Zakaj ljudje posežejo po Garciniji?',
                'Najpogosteje zaradi zanimanja za apetit in podporo pri uravnavanju telesne teže.',
                'Ali lahko sama sproži hujšanje?',
                'Ne. Brez boljšega načrta prehrane in gibanja ostane učinek omejen.',
                'Kdaj ima več smisla?',
                'Ko je del premišljene strategije, ne pa bližnjica.',
                'Katera je pogosta napaka?',
                'Uporabiti jo kot nadomestilo za delo na obrokih, porcijah in gibanju.'
            ),
            'Garcinia Cambogia: kdaj ima smisel za podporo teži in kdaj ne',
            'Spoznajte, kako Garcinia Cambogia realno spada v zgodbo o apetitu in uravnavanju teže.',
            'Garcinia Cambogia',
            $sections(
                'Zakaj izdelki za težo zvenijo lažje, kot so',
                'Dodatki za uravnavanje teže so privlačni, ker zvenijo preprosto, vendar dolgoročni rezultat še vedno nosijo ponavljajoče navade.',
                'Zakaj je pravo vprašanje vedenje',
                'Boljši rezultat skoraj vedno pride iz razumevanja apetita, porcij in dnevne rutine, ne iz iskanja enega samega izdelka.'
            )
        ),
        $entry(
            51,
            'Forever First: kdaj večnamenski aloe sprej resnično pomaga koži in lasem',
            'Aloe spreji imajo največ smisla, ko poenostavijo vsakodnevno nego, ne pa ko jim pripisujemo čudeže. Tukaj je, kako Forever First oceniti skozi praktičnost, tip kože in situacije, kjer je res uporaben.',
            '<ul><li>Forever First ima največ smisla kot praktičen aloe sprej za preproste trenutke nege kože in lasišča.</li><li>Največja napaka je pričakovati, da bo večnamenski sprej nadomestil ciljno nego ali celotno rutino.</li><li>Pametnejši pristop gleda, kje hitrost, udobje in enostavnost res prinesejo vrednost.</li></ul>',
            $faq(
                'Za kaj ljudje Forever First najpogosteje uporabljajo?',
                'Največkrat za kožo po soncu, manjša draženja, lasišče in praktično dnevno nego.',
                'Ali je dovolj za vse težave kože?',
                'Ne. Največ smisla ima kot podpora, ne kot celotna rešitev.',
                'Kdaj posebej dobro ustreza?',
                'Ko želite lahek, hiter in enostaven aloe izdelek za uporabo čez dan.',
                'Katera je pogosta napaka?',
                'Pričakovati od večnamenskega spreja več, kot ta format realno ponudi.'
            ),
            'Forever First: kdaj aloe sprej podpira kožo in lase',
            'Odkrijte, kdaj je Forever First smiseln del nege kože in las ter kje ga ni treba precenjevati.',
            'Forever First',
            $sections(
                'Zakaj v praksi pogosto zmaga priročnost',
                'Veliko izdelkov ostane v rutini prav zato, ker so hitri, enostavni in prijetni za uporabo v vsakodnevnih situacijah.',
                'Zakaj podpora ni isto kot terapija',
                'Praktičen sprej je lahko zelo koristen, ne da bi moral igrati vlogo celotne ciljne nege.'
            )
        ),
        $entry(
            52,
            'Aloe Vera Gelly: kdaj gostejša aloe formula koži nudi bolj smiselno podporo',
            'Aloe Vera Gelly pritegne ljudi, ki želijo na koži občutiti debelejšo in bolj zaščitno teksturo, posebno pri suhosti, manjših draženjih in domači praktični uporabi. Tukaj je, komu takšen format najbolj ustreza.',
            '<ul><li>Aloe Vera Gelly ima največ smisla, ko koža želi gostejši in dlje trajajoč občutek zaščite.</li><li>Največja napaka je uporabljati jo kot univerzalni odgovor za vse težave kože.</li><li>Pametnejši pristop gleda, kje gostejša aloe tekstura deluje bolje kot lahek gel ali sprej.</li></ul>',
            $faq(
                'Komu Aloe Vera Gelly pogosto najbolj ustreza?',
                'Koži, ki ji bolj ustreza bogatejša in zaščitnejša tekstura ter daljši občutek udobja.',
                'Po čem se razlikuje od lažjih aloe izdelkov?',
                'Predvsem po teksturi, občutku in trenutkih, ko je gostejša plast bolj uporabna.',
                'Ali se lahko vključi v preprosto domačo prvo nego?',
                'Da, lahko je praktičen izdelek za vsakodnevne situacije podpore koži.',
                'Katera je pogosta napaka?',
                'Pričakovati enako vlogo in občutek kot pri tankem spreju ali lahkem aloe gelu.'
            ),
            'Aloe Vera Gelly: kdaj bogatejša aloe tekstura bolj ustreza koži',
            'Spoznajte, kdaj Aloe Vera Gelly bolj ustreza koži kot lažji aloe izdelki in kje je najbolj praktična.',
            'Aloe Vera Gelly',
            $sections(
                'Zakaj tekstura spremeni izkušnjo',
                'Ista osnovna ideja lahko deluje zelo različno glede na teksturo, nekateri ljudje pa preprosto bolje prenašajo bogatejšo formulo.',
                'Zakaj bogatejše ne pomeni univerzalno',
                'Gostejši izdelek je lahko zelo uporaben v pravem trenutku, vendar mora še vedno ustrezati tipu kože in situaciji.'
            )
        ),
        $entry(
            53,
            'Forever Bright zobna pasta brez fluorida: komu ta pristop k ustni negi res ustreza',
            'Zobna pasta brez fluorida ni samodejno boljša ali slabša. Je preprosto druga možnost, ki nekaterim bolje ustreza po občutku, sestavi ali filozofiji nege. Tukaj je, kako Forever Bright oceniti bolj razumno.',
            '<ul><li>Forever Bright ima največ smisla za uporabnike, ki želijo aloe pristop in bolj blag občutek zobne paste brez fluorida.</li><li>Največja napaka je pretvoriti izbiro paste v ideologijo namesto pogledati celotno rutino ustne nege.</li><li>Pametnejši pristop gleda kakovost ščetkanja, doslednost in to, kaj človek realno lahko vzdržuje.</li></ul>',
            $faq(
                'Komu je lahko zobna pasta brez fluorida zanimiva?',
                'Ljudem, ki želijo drugačno sestavo, bolj blag občutek ali aloe pristop k negi.',
                'Ali je sama pasta dovolj za zdravje zob?',
                'Ne. Tehnika ščetkanja, rednost in širša rutina ustne nege ostajajo ključne.',
                'Zakaj je treba gledati celotno rutino?',
                'Ker je pasta le en del dnevne nege, ne pa celoten odgovor.',
                'Katera je pogosta napaka?',
                'Iskati popolno pasto namesto graditi dosledno in kakovostno ustno rutino.'
            ),
            'Forever Bright: kdaj zobna pasta brez fluorida ima smisel',
            'Odkrijte, komu Forever Bright lahko ustreza in kako jo oceniti znotraj celotne ustne rutine.',
            'Forever Bright',
            $sections(
                'Zakaj je ustna nega več kot oznaka izdelka',
                'Ljudje se pogosto osredotočijo na vrsto paste, pozabijo pa, da rezultat veliko bolj oblikujejo tehnika, rutina in doslednost.',
                'Zakaj je ustreznost pomembnejša od ideologije',
                'Najuporabnejši izdelek je pogosto tisti, ki ga človek z veseljem in redno uporablja kot del dobre navade.'
            )
        ),
        $entry(
            37,
            'Zakaj še niste vodja: 10 vzorcev, ki zavirajo vpliv, zaupanje in osebno rast',
            'Vodstvo se veliko pogosteje izgubi zaradi navad, slabe komunikacije in nejasnega značaja kot pa zaradi pomanjkanja naziva. Tukaj je, kako prepoznati vzorce, ki zavirajo vpliv, in kaj spremeniti.',
            '<ul><li>Vodstvo se običajno začne z odgovornostjo, jasno komunikacijo in doslednostjo v vedenju.</li><li>Največja napaka je pričakovati spoštovanje brez dela na značaju in zaupanju.</li><li>Pametnejši pristop se začne z iskreno samopresojo in pripravljenostjo na popravek slepih točk.</li></ul>',
            $faq(
                'Zakaj nekdo kljub strokovnosti ne postane vodja?',
                'Ker vodstvo zahteva več kot strokovnost: zaupanje, komunikacijo in osebno stabilnost.',
                'Ali se je vodstva mogoče naučiti?',
                'Da, vendar zahteva samorefleksijo, povratne informacije in delo na vedenjskih vzorcih.',
                'Kaj je prvi korak?',
                'Prepoznati vzorce, ki tiho rušijo zaupanje in vpliv.',
                'Katera je pogosta napaka?',
                'Želeti naziv vodje brez pripravljenosti na odgovornost.'
            ),
            'Zakaj še niste vodja: 10 vzorcev, ki vas držijo nazaj',
            'Odkrijte 10 vzorcev, zaradi katerih ljudje ne zrastejo v vodje, in kako graditi vpliv skozi značaj in zaupanje.',
            'Zakaj še niste vodja',
            $sections(
                'Zakaj se vodstvo začne pred avtoriteto',
                'Ljudje vodstvo pogosto začutijo še preden ga formalno priznajo, predvsem skozi zanesljivost, jasnost in čustveno stabilnost.',
                'Zakaj so slepe točke tako pomembne',
                'Majhni vzorci, ki rušijo zaupanje, pogosto ostanejo nevidni ravno osebi, ki jih ponavlja.'
            )
        ),
        $entry(
            38,
            'Kako razmišljate in kako vas oblikuje okolje: zakaj mindset težko raste v napačnem prostoru',
            'Način razmišljanja ne raste v praznini. Ljudje okoli vas, standardi, ki jih sprejemate, in informacije, ki jih spremljate, tiho oblikujejo pogled na delo, sebe in prihodnost. Tukaj je, kako to hitreje opaziti.',
            '<ul><li>Mindset se najmočneje oblikuje skozi vsakodnevne pogovore, standarde in vzdušje okoli vas.</li><li>Največja napaka je poskušati rasti mentalno, medtem ko okolje stalno vleče nazaj.</li><li>Pametnejši pristop izbira ljudi, navade in informacije, ki podpirajo odgovornejše razmišljanje.</li></ul>',
            $faq(
                'Zakaj je okolje tako pomembno za mindset?',
                'Ker vsak dan oblikuje to, kar se nam zdi normalno, možno in vredno truda.',
                'Ali se lahko mindset spremeni brez spremembe okolja?',
                'Delno da, vendar je pot običajno počasnejša, če okolje nenehno vleče nazaj.',
                'Kaj je dobro najprej opazovati?',
                'Ljudi, navade, medije in pogovore, ki sestavljajo vsakdan.',
                'Katera je pogosta napaka?',
                'Podceniti okolje in ves pritisk dati le na pozitivno samogovorjenje.'
            ),
            'Kako okolje vsak dan oblikuje vaš način razmišljanja',
            'Spoznajte, zakaj mindset tako močno zavisi od okolja in kako si ustvariti bolj zdrav mentalni okvir.',
            'Mindset in okolje',
            $sections(
                'Zakaj dnevni vplivi postanejo identiteta',
                'Standardi in sporočila okoli vas sčasoma postanejo normalni, tudi ko so omejujoči.',
                'Zakaj boljše razmišljanje pogosto potrebuje boljši prostor',
                'Rast postane lažja, ko odnosi, prostor in ritem nehajo hraniti stare vzorce.'
            )
        ),
        $entry(
            39,
            'Prvi vtis: kako zgraditi zaupanje brez igranja, pretirane samozavesti in prazne predstave',
            'Prvi vtis ni zgrajen le z videzom. Oblikujeta ga tudi način poslušanja, govorjenja, vstopa v prostor in občutek varnosti, ki ga ustvarite. Tukaj je, kako to zgraditi bolj naravno.',
            '<ul><li>Dober prvi vtis običajno izhaja iz miru, jasnosti in spoštovanja do druge osebe.</li><li>Največja napaka je igrati samozavest namesto pokazati resnično prisotnost in zanimanje.</li><li>Pametnejši pristop uskladi govorico telesa, ton in preprosto profesionalnost.</li></ul>',
            $faq(
                'Kaj najbolj oblikuje prvi vtis?',
                'Najpogosteje govorica telesa, ton, prisotnost, urejenost in način komunikacije.',
                'Ali mora biti prvi vtis popoln?',
                'Ne. Pomembneje je delovati zaupanja vredno, mirno in pristno.',
                'Ali se prvi vtis lahko popravi?',
                'Včasih da, vendar je lažje zaupanje ustvariti že na začetku.',
                'Katera je pogosta napaka?',
                'Poskušati impresionirati namesto pomagati drugi osebi, da se počuti spoštovano in varno.'
            ),
            'Prvi vtis: kako zgraditi zaupanje brez pretiravanja',
            'Odkrijte, kako ustvariti močnejši prvi vtis skozi pristnost, govorico telesa in preprosto profesionalnost.',
            'Prvi vtis',
            $sections(
                'Zakaj zaupanje premaga predstavo',
                'Ljudje si pogosto bolj zapomnijo občutek, ki ste ga ustvarili, kot pa natančne besede, ki ste jih izrekli.',
                'Zakaj je mir pogosto močnejši od naprezanja',
                'Prisotnost, poslušanje in tiha samozavest pogosto ustvarijo boljši vtis kot prisiljena karizma.'
            )
        ),
        $entry(
            42,
            'Pozitivno razmišljanje brez naivnosti: kako graditi vzorce, ki res vodijo v napredek',
            'Pozitivno razmišljanje postane koristno šele, ko ne beži pred realnostjo, odgovornostjo in konkretnim dejanjem. Tukaj je, kako graditi vzorce razmišljanja, ki res pomagajo.',
            '<ul><li>Pozitivne misli so koristne, kadar vodijo v boljše delovanje, ne pa v pobeg pred težavami.</li><li>Največja napaka je zamenjati disciplino in odgovornost s praznim optimizmom.</li><li>Pametnejši pristop združuje realizem, hvaležnost in konkretne dnevne korake.</li></ul>',
            $faq(
                'Kaj je zdravo pozitivno razmišljanje?',
                'To je način razmišljanja, ki težavo vidi jasno, a še vedno verjame v možnost spremembe.',
                'Zakaj nekateri zavračajo nasvete o pozitivnem razmišljanju?',
                'Ker se pogosto predstavijo kot zanikanje resničnih težav ali čustev.',
                'Kako postane tak pristop koristen?',
                'Ko ga povežemo z odgovornostjo, navadami in konkretnim delovanjem.',
                'Katera je pogosta napaka?',
                'Verjeti, da so dobre misli dovolj brez spremembe vedenja.'
            ),
            'Pozitivno razmišljanje: kako graditi vzorce, ki res pomagajo',
            'Spoznajte, kako pozitivno razmišljanje povezati z realizmom, odgovornostjo in konkretnim delovanjem.',
            'Pozitivno razmišljanje',
            $sections(
                'Zakaj mindset potrebuje dejanje',
                'Vzorec razmišljanja je najbolj uporaben, ko vodi do boljših odločitev, navad in bolj mirnega napredka.',
                'Zakaj realizem ščiti rast',
                'Optimizem je veliko močnejši, ko zna pogledati omejitve, padec energije in neprijetna čustva brez zanikanja.'
            )
        ),
        $entry(
            55,
            'Kako postati Forever partner in graditi posel od doma brez iluzije hitrega zaslužka',
            'Forever partnerstvo lahko postane resen poslovni kanal le, ko je zgrajeno na koristni vsebini, podpori kupcem in ponovljivem sistemu dela. Tukaj je, kako pristopiti brez zgodbe o lahkem zaslužku čez noč.',
            '<ul><li>Posel od doma s Foreverjem je najbolj odvisen od zaupanja, jasnosti in ponovljivih dnevnih aktivnosti.</li><li>Največja napaka je vstopiti brez načrta za vsebino, priporočila in nadaljnjo podporo kupcu.</li><li>Pametnejši pristop gradi publiko, podporo in preprost prodajni sistem skozi čas.</li></ul>',
            $faq(
                'Ali je mogoče Forever posel graditi od doma?',
                'Da, vendar zahteva resen sistem, koristno vsebino in dosledno podporo kupcem.',
                'Kaj je pomembnejše od začetnega navdušenja?',
                'Ponovljive navade, zaupanje publike in jasen prodajni proces.',
                'Kako izgleda zdrav začetek?',
                'Spoznavanje izdelkov, gradnja vsebine in rast publike korak za korakom.',
                'Katera je pogosta napaka?',
                'Pričakovati hiter zaslužek brez grajenja temeljev.'
            ),
            'Kako postati Forever partner in graditi posel od doma realno',
            'Odkrijte, kako Forever partnerstvo graditi skozi zaupanje, vsebino in trajen sistem dela od doma.',
            'Forever partnerstvo',
            $sections(
                'Zakaj je zaupanje pravo premoženje',
                'V poslu, ki temelji na priporočilih, je zaupanje pogosto vrednejše od hitrosti, saj ohrani odnos še dolgo po prvi prodaji.',
                'Zakaj je sistem pomembnejši od motivacije',
                'Preprost ponovljiv proces običajno nese posel dlje kot navdušenje brez strukture.'
            )
        ),
        $entry(
            65,
            'Razumevanje MLM marketinga: kje ta model ima smisel in kje ljudje najpogosteje zgrešijo',
            'MLM modela ni smiselno ne romantizirati ne demonizirati. Smisel ima le takrat, ko ga gledamo skozi izdelke, etiko, podporo kupcu in sposobnost dolgoročne gradnje. Tukaj je bolj zrel pogled nanj.',
            '<ul><li>MLM ima lahko smisel le takrat, ko so v središču resnični izdelki, vrednost za kupca in etična prodaja.</li><li>Največja napaka je graditi zgodbo le okoli zaslužka namesto okoli koristi za kupca.</li><li>Pametnejši pristop loči trajen priporočilni sistem od pritiska in hypea.</li></ul>',
            $faq(
                'Kaj naredi MLM bolj vzdržen?',
                'Kakovost izdelkov, podpora kupcem, etika in dolgoročno zaupanje.',
                'Zakaj imajo mnogi z MLM-om slabe izkušnje?',
                'Ker je model včasih voden z agresijo, pretiravanjem in brez resnične vrednosti za kupca.',
                'Kako ga oceniti bolj pošteno?',
                'Skozi izdelke, vedenje, etiko in vprašanje, ali sistem zdrži dolgoročno.',
                'Katera je pogosta napaka?',
                'Prodajati sanje o zaslužku namesto resnične rešitve za kupca.'
            ),
            'MLM marketing: kako ga razumeti brez iluzij in skrajnosti',
            'Spoznajte, kako MLM marketing oceniti skozi vrednost izdelkov, etiko in vzdržnost namesto samo skozi obljube o zaslužku.',
            'Razumevanje MLM',
            $sections(
                'Zakaj je etika srce modela',
                'Priporočilni posel preživi le, kadar se ljudje počutijo spoštovane, obveščene in resnično podprte.',
                'Zakaj je vrednost za kupca odločilno vprašanje',
                'Če kupec ni jasno na boljšem, poslovna zgodba prej ali slej razpade ne glede na privlačno predstavitev.'
            )
        ),
        $entry(
            66,
            'Kaj je mrežni marketing: ključni koraki do rezultata brez pritiska in praznega hypea',
            'Mrežni marketing lahko deluje le, če je zgrajen kot sistem odnosov, priporočil in koristne vsebine. Tukaj je, kako razumeti njegove temelje in kaj ljudi res premika naprej.',
            '<ul><li>Mrežni marketing je najbolj odvisen od zaupanja, jasnega sporočila in doslednega dela z ljudmi.</li><li>Največja napaka je verjeti, da lahko resen posel stoji le na navdušenju.</li><li>Pametnejši pristop gradi jasen proces priporočila, podpore in nadaljnjega stika s kupcem.</li></ul>',
            $faq(
                'Kaj je mrežni marketing v preprostih besedah?',
                'To je prodajni model, ki temelji na priporočilih, odnosih in dolgoročnejši podpori kupcu.',
                'Kaj najbolj vodi do rezultata?',
                'Doslednost, komunikacija, zaupanje in preprost ponovljiv proces.',
                'Ali lahko deluje brez vsebine in odnosov?',
                'Zelo težko, ker ljudje navadno kupujejo takrat, ko že obstajata zaupanje in jasnost.',
                'Katera je pogosta napaka?',
                'Iskati hitre rezultate brez grajenja resničnega zaupanja publike.'
            ),
            'Kaj je mrežni marketing in kateri koraki res vodijo do rezultata',
            'Odkrijte, kaj mrežni marketing v resnici je in kako ga graditi skozi zaupanje, vsebino in vzdržne dnevne aktivnosti.',
            'Mrežni marketing',
            $sections(
                'Zakaj so odnosi v središču modela',
                'V takšnem poslu ljudje redko reagirajo le na informacijo. Premakne jih občutek, da je odnos verodostojen in uporaben.',
                'Zakaj sistem odloča o rezultatu',
                'Jasen proces ustvari zagon, medtem ko nejasna motivacija hitro izgine, ko življenje postane bolj naporno.'
            )
        ),
        $entry(
            67,
            'Kako uspeti v življenju: cilji, značaj in finančna svoboda brez lažnega instant recepta',
            'Uspeh redko zraste iz ene velike odločitve. Pogosteje se gradi skozi značaj, navade in sposobnost, da dolgo ostanete zvesti pravim prioritetam. Tukaj je, kako na to gledati bolj zdravo.',
            '<ul><li>Uspeh se najpogosteje gradi skozi navade, odgovornost in usklajenost med cilji in značajem.</li><li>Največja napaka je iskati instant recept za finančno svobodo brez sistema.</li><li>Pametnejši pristop združuje osebno rast, disciplino in realen odnos do časa.</li></ul>',
            $faq(
                'Kaj je temelj zdravega uspeha?',
                'Jasne prioritete, značaj, doslednost in sposobnost ostati v procesu.',
                'Ali je finančna svoboda samo vprašanje denarja?',
                'Ne. Odvisna je tudi od navad, vrednot, odločitev in načina dela.',
                'Zakaj ljudje pogosto obstanejo?',
                'Ker si želijo hiter premik brez strukture, potrpežljivosti in discipline.',
                'Katera je pogosta napaka?',
                'Uspeh skrčiti na motivacijo in zanemariti rutino ter osebni značaj.'
            ),
            'Kako uspeti v življenju brez lova na instant recept',
            'Spoznajte, kako uspeh graditi skozi cilje, značaj in dolgoročne navade, ne pa skozi hitre obljube.',
            'Kako uspeti',
            $sections(
                'Zakaj je uspeh pogosto počasnejši in globlji',
                'Najpomembnejši napredek običajno nastaja skozi ponavljajoče se odločitve, ki se v trenutku zdijo majhne, dolgoročno pa zelo močne.',
                'Zakaj značaj ohranja rezultat',
                'Brez integritete in discipline je tudi dobre priložnosti težko zadržati dovolj dolgo.'
            )
        ),
        $entry(
            88,
            'Kako ostati motiviran: zakaj disciplina, smisel in okolje premagajo kratke nalete volje',
            'Motivacija je pomembna, vendar redko traja dovolj dolgo, da sama nosi resen cilj. Tukaj je, kako ostati v gibanju, ko začetni zagon popusti, in kako zgraditi sistem, ki vas nosi naprej.',
            '<ul><li>Motivacija lahko zažene gibanje, dolgoročni rezultat pa bolj nosita disciplina in smisel.</li><li>Največja napaka je nasloniti se le na inspiracijo namesto zgraditi rutino za težje dni.</li><li>Pametnejši pristop poveže namen, okolje in majhne dnevne korake.</li></ul>',
            $faq(
                'Zakaj motivacija tako hitro pade?',
                'Ker začetno čustvo oslabi, sistem pa ali obstaja ali pa ne.',
                'Kaj pomaga, ko volje ni?',
                'Jasna rutina, manjši koraki in okolje, ki cilj podpira.',
                'Ali je disciplina pomembnejša od motivacije?',
                'Za dolgoročni rezultat običajno da, ker vodi naprej tudi po padcu navdušenja.',
                'Katera je pogosta napaka?',
                'Čakati, da se motivacija vrne, namesto narediti naslednji majhen korak.'
            ),
            'Kako ostati motiviran po začetnem navdušenju',
            'Odkrijte, kako ostati motiviran skozi disciplino, smisel in preprost sistem dnevnega napredka.',
            'Kako ostati motiviran',
            $sections(
                'Zakaj sistemi rešujejo težke dni',
                'Dobra rutina zmanjša potrebo po vsakodnevnem pogajanju s seboj, ko energija pade.',
                'Zakaj smisel ohranja trud',
                'Ljudje so veliko bolj dosledni, kadar cilj ostaja povezan z nečim osebno pomembnim.'
            )
        ),
        $entry(
            93,
            '14 znakov čustvene inteligence: kako močnejši EQ prepoznamo v odnosih in delu',
            'Čustvena inteligenca se manj kaže v velikih besedah, veliko bolj pa v načinu poslušanja, odzivanju pod pritiskom in nošenju lastnih čustev skozi težke trenutke. Tukaj je, kako prepoznati močnejši EQ.',
            '<ul><li>Čustvena inteligenca se najbolj vidi skozi samoregulacijo, empatijo in kakovost komunikacije.</li><li>Največja napaka je EQ zreducirati le na prijaznost brez meja in notranje zrelosti.</li><li>Pametnejši pristop gleda, kako nekdo vodi sebe, konflikt in odnose skozi čas.</li></ul>',
            $faq(
                'Kaj je čustvena inteligenca v praksi?',
                'To je sposobnost razumeti sebe, zaznati druge in pod pritiskom reagirati bolj zrelo.',
                'Zakaj je pomembna v delu in odnosih?',
                'Ker vpliva na komunikacijo, zaupanje, sodelovanje in kakovost odločitev.',
                'Ali se EQ lahko razvija?',
                'Da, skozi samorefleksijo, povratne informacije in delo na čustvenih odzivih.',
                'Katera je pogosta napaka?',
                'Zamenjati čustveno inteligenco z ugajanjem vsem ali potiskanjem čustev.'
            ),
            '14 znakov čustvene inteligence, ki gradijo močnejši EQ',
            'Spoznajte, kako prepoznati čustveno inteligenco pri sebi in drugih ter zakaj je EQ tako pomemben v odnosih in delu.',
            'Čustvena inteligenca',
            $sections(
                'Zakaj se EQ najbolj vidi pod pritiskom',
                'Čustvena zrelost se najjasneje pokaže takrat, ko je človek utrujen, razočaran ali izzvan in kljub temu ostane stabilen.',
                'Zakaj čustvena inteligenca gradi zaupanje',
                'Odnosi postanejo močnejši, ko zna človek uravnavati sebe, dobro poslušati in se odzivati brez nepotrebne drame.'
            )
        ),
    ],
];
