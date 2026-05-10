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
    'key' => 'legacy-storefront-skincare-sl-wave-1',
    'name' => 'Legacy storefront in skincare (SL) - prvi val',
    'notes' => 'Ročno lokaliziran premium val za legacy storefront strani, aloe foundation vsebine in starejše skincare/product URL-je.',
    'entries' => [
        $entry(
            74,
            'Forever Fiber: kdaj dnevne vlaknine resnično pomagajo apetitu, prebavi in ritmu prehrane',
            'forever-fiber-vas-dnevni-odmerek-vlaknin',
            'Forever Fiber ima največ smisla, ko dopolnjuje prehrano, ki je še vedno preskromna z vlakninami, in ne ko poskuša nadomestiti zelenjavo, vodo in boljši ritem obrokov. Tukaj je, kako vlakninsko podporo oceniti bolj realno.',
            '<ul><li>Podpora z vlakninami ima največ smisla, ko je vsakodnevna prehrana še vedno prešibka pri skupnem vnosu vlaknin.</li><li>Največja napaka je pričakovati, da bodo vlaknine same uredile apetit, zaprtje in telesno težo brez spremembe navad.</li><li>Pametnejši pristop skupaj gleda vodo, ritem obrokov in celoten vnos rastlinske hrane.</li></ul>',
            $faq(
                'Kdaj ima Forever Fiber več smisla?',
                'Ko prehrana redno vsebuje premalo vlaknin in želite preprost podporni korak za prebavo.',
                'Ali lahko izdelek z vlakninami nadomesti zdravo prehrano?',
                'Ne. Najbolje deluje kot podpora in ne kot nadomestilo za kakovostno prehrano.',
                'Zakaj so vlaknine pomembne tudi za apetit?',
                'Ker boljši vnos vlaknin pogosto pomaga pri sitosti in bolj umirjenem ritmu hranjenja.',
                'Katera je pogosta napaka?',
                'Dodati vlaknine brez dovolj vode in brez izboljšanja preostale prehrane.'
            ),
            'Forever Fiber: kdaj ima podpora z vlakninami največ smisla',
            'Spoznajte, kdaj je Forever Fiber lahko koristen in kako ga vključiti v boljšo prehransko rutino brez pretiranih pričakovanj.',
            'Forever Fiber',
            $sections(
                'Zakaj so vlaknine še vedno podcenjene',
                'Veliko ljudi pomisli na vlaknine šele, ko prebava postane počasna, čeprav vplivajo tudi na sitost, ritem obrokov in kakovost prehrane.',
                'Zakaj podpora deluje najbolje ob boljših navadah',
                'Izdelki z vlakninami so najbolj uporabni takrat, ko podpirajo močnejše navade, ne pa ko sami poskušajo rešiti slabo rutino.'
            )
        ),
        $entry(
            75,
            'Gentleman’s Pride: kdaj losjon po britju brez alkohola resnično pomaga koži',
            'gentlemans-pride-losjon-po-britju-brez-alkohola-z-aloe-vero',
            'Nega po britju deluje najbolje, kadar zmanjša pekoč občutek, suhost in draženje, ne pa kadar samo lepo diši. Tukaj je, kje ima Gentleman’s Pride več smisla in komu lahko bolje ustreza aloe formula brez alkohola.',
            '<ul><li>Losjon po britju brez alkohola ima več smisla za kožo, ki po britju hitro peče, se suši ali pordi.</li><li>Največja napaka je aftershave razumeti le kot dišavo in ne kot odločitev za več udobja kože.</li><li>Pametnejši pristop gleda udobje, draženje in to, kako dobro izdelek umiri celotno rutino britja.</li></ul>',
            $faq(
                'Komu lahko Gentleman’s Pride bolj ustreza?',
                'Moškim, ki želijo po britju blažji občutek in manj pekoče kože.',
                'Zakaj je formula brez alkohola pomembna?',
                'Ker je lahko prijetnejša za kožo, ki se po britju hitro izsuši ali pordi.',
                'Ali lahko dober aftershave izboljša celotno rutino britja?',
                'Da, vendar sta pomembna tudi tehnika britja in priprava kože.',
                'Katera je pogosta napaka?',
                'Izdelek izbrati samo po vonju in ne po tem, kako se koža počuti po britju.'
            ),
            'Gentleman’s Pride: kdaj aftershave brez alkohola bolj ustreza koži',
            'Odkrijte, komu lahko Gentleman’s Pride najbolj ustreza in zakaj aloe aftershave brez alkohola nekaterim kožam bolj godi.',
            'Gentleman’s Pride',
            $sections(
                'Zakaj je udobje po britju pomembnejše od vtisa',
                'Najboljši izdelek po britju je običajno tisti, ki kožo pusti bolj mirno in prijetno, ne pa le dišečo.',
                'Zakaj blažja podpora pogosto zmaga dolgoročno',
                'Moški ostanejo bolj dosledni pri izdelkih, ki britje naredijo prijaznejše za kožo in ne bolj agresivno.'
            )
        ),
        $entry(
            76,
            'Forever Activator: kdaj globinska hidracija in priprava kože pomenita več kot agresivni aktivni izdelki',
            'forever-activator-skrivnost-globoke-hidracije-in-obnove-koze',
            'Forever Activator ima vrednost, ko koža potrebuje več vode, manj draženja in boljšo osnovo za nadaljnjo rutino. Tukaj je, kako ga oceniti brez pretvarjanja hidracijskega koraka v veliko obljubo.',
            '<ul><li>Forever Activator ima največ smisla, ko koža potrebuje več hidracije, miru in boljšo pripravo za nadaljnjo nego.</li><li>Največja napaka je od hidratacijskega izdelka pričakovati rezultate, ki sodijo v čisto druge kategorije aktivne nege.</li><li>Pametnejši pristop gleda udobje kožne bariere, prenašanje in dosledno uporabo.</li></ul>',
            $faq(
                'Za kaj se Forever Activator najpogosteje uporablja?',
                'Predvsem kot hidratantni in pomirjujoč korak v rutini nege obraza.',
                'Komu lahko bolj ustreza?',
                'Koži, ki želi več udobja, vlage in manj agresivno rutino.',
                'Ali lahko nadomesti celotno anti-age rutino?',
                'Ne. Največ smisla ima kot en uporaben hidratacijski korak.',
                'Katera je pogosta napaka?',
                'Pričakovati dramatično preobrazbo namesto stabilne podpore in udobja.'
            ),
            'Forever Activator: kdaj globinska hidracija kože res pomeni največ',
            'Spoznajte, kdaj je Forever Activator lahko koristen in zakaj močna hidracija pogosto prinese več kot preagresivna rutina.',
            'Forever Activator',
            $sections(
                'Zakaj priprava in hidracija spreminjata celotno rutino',
                'Koža pogosto bolje prenaša preostalo nego takrat, ko sta hidracija in udobje urejena že na začetku.',
                'Zakaj preproste podpore ne smemo podceniti',
                'Dober hidratacijski korak morda ne zveni spektakularno, vendar pogosto ustvari osnovo, na kateri rutina sploh začne delovati bolje.'
            )
        ),
        $entry(
            77,
            'Forever Awakening Eye Cream: kdaj krema za predel okoli oči res doda več udobja in svežine',
            'forever-awakening-eye-cream-skrivnost-mladostnega-videza',
            'Predel okoli oči ne potrebuje čudežev. Običajno potrebuje nežnost, vlago in rutino, ki jo je mogoče vzdrževati. Tukaj je, kako Awakening Eye Cream oceniti skozi resnične potrebe kože.',
            '<ul><li>Krema za oči ima več smisla, kadar prinaša več udobja, manj suhosti in bolj nežen občutek na občutljivem predelu.</li><li>Največja napaka je pričakovati, da bo ena krema izbrisala pomanjkanje spanja, stres in vsakodnevni življenjski slog.</li><li>Pametnejši pristop gleda teksturo, prenašanje in realna pričakovanja od nege predela okoli oči.</li></ul>',
            $faq(
                'Kdaj ima krema za oči več smisla?',
                'Ko je predel okoli oči suh, občutljiv ali hitro kaže utrujenost.',
                'Ali lahko sama krema odstrani podočnjake in zabuhlost?',
                'Ne v celoti. Več smisla ima kot del širše rutine in boljšega okrevanja.',
                'Zakaj ljudje radi uporabljajo poseben izdelek za oči?',
                'Ker občutljiv predel pogosto bolje prenaša bolj nežno in ciljno teksturo.',
                'Katera je pogosta napaka?',
                'Od kreme pričakovati rešitev za težave, ki so močno povezane s spanjem in stresom.'
            ),
            'Awakening Eye Cream: kdaj ima nega okoli oči res smisla',
            'Odkrijte, kdaj Forever Awakening Eye Cream lahko podpira predel okoli oči in zakaj so realna pričakovanja pomembnejša od hypea.',
            'Awakening Eye Cream',
            $sections(
                'Zakaj predel okoli oči potrebuje drugačen tempo',
                'Koža okoli oči pogosto hitreje pokaže suhost, utrujenost in preobremenjenost kot preostanek obraza.',
                'Zakaj nega deluje najbolje, ko ostane realna',
                'Ciljana nega oči je lahko zelo koristna, vendar deluje najbolje takrat, ko podpira rutino in ne pretvarja se, da lahko nadomesti okrevanje.'
            )
        ),
        $entry(
            78,
            'Aloe Moisturising Lotion: zakaj ima ta klasična Forever krema še vedno mesto v vsakodnevni negi',
            'aloe-vlazilni-losjon-skrivnost-vijolicne-kreme-forever',
            'Nekatere kreme ostanejo priljubljene zato, ker so preproste, udobne in jih je lahko živeti vsak dan. Tukaj je, zakaj ima Aloe Moisturising Lotion še vedno smisel in komu lahko najbolj ustreza.',
            '<ul><li>Klasična vlažilna krema ima največ smisla, ko uporabnik želi stabilno, prijetno in preprosto vsakodnevno nego.</li><li>Največja napaka je podcenjevati osnovno hidracijo in nenehno loviti bolj kompleksne izdelke.</li><li>Pametnejši pristop gleda doslednost, občutek na koži in to, ali izdelek res podpira rutino.</li></ul>',
            $faq(
                'Komu lahko Aloe Moisturising Lotion bolj ustreza?',
                'Ljudem, ki imajo radi preprosto, klasično in udobno vsakodnevno hidracijo.',
                'Zakaj nekatere osnovne kreme ostanejo tako priljubljene?',
                'Ker so praktične, jih je lahko uporabljati in jih koža pogosto dobro prenaša.',
                'Ali je lahko osnovna krema dovolj za veliko ljudi?',
                'Da, še posebej kadar koža ne potrebuje zelo kompleksne aktivne rutine.',
                'Katera je pogosta napaka?',
                'Preskočiti osnovno hidracijo in pričakovati, da bodo aktivni izdelki rešili vse sami.'
            ),
            'Aloe Moisturising Lotion: zakaj preprosta krema pogosto pomeni največ',
            'Spoznajte, komu Aloe Moisturising Lotion lahko najbolj ustreza in zakaj preprosta hidracija pogosto najbolj spremeni občutek kože.',
            'Aloe Moisturising Lotion',
            $sections(
                'Zakaj osnovna hidracija pogosto nosi rutino',
                'Ljudje včasih pozabijo, kako veliko vlogo ima stabilen vlažilni korak pri občutku kože iz dneva v dan.',
                'Zakaj udobje pomaga ohraniti doslednost',
                'Izdelki, ki so prijetni za uporabo, pogosto ostanejo v rutini dovolj dolgo, da res kaj spremenijo.'
            )
        ),
        $entry(
            79,
            'Forever Alpha E Factor: kdaj bogatejša in bolj razkošna tekstura resnično doda vrednost',
            'forever-alpha-e-factor-globinsko-vlazenje-in-obnova-koze',
            'Alpha E Factor ima smisel, ko koža želi več udobja, hranljivosti in bogatejši občutek rutine, vendar brez iluzije, da ena krema spremeni vse. Tukaj je, kako ga oceniti bolj zrelo.',
            '<ul><li>Bolj bogata luksuzna krema ima več smisla, ko koža želi več hranljivosti in močnejši občutek zaščite.</li><li>Največja napaka je domnevati, da bogatejša ali dražja formula avtomatsko pomeni boljši rezultat za vsakogar.</li><li>Pametnejši pristop gleda tip kože, željo po teksturi in dolgoročno vlogo izdelka v rutini.</li></ul>',
            $faq(
                'Komu lahko Alpha E Factor bolj ustreza?',
                'Koži, ki ima rada bogatejšo teksturo, več udobja in bolj hranilen občutek nege.',
                'Ali je bogatejša krema vedno boljša izbira?',
                'Ne. Vse je odvisno od tipa kože, letnega časa in osebnega občutka.',
                'Zakaj imajo ljudje radi takšne izdelke?',
                'Zaradi občutka udobja, kakovosti in bolj zaščitene kože.',
                'Katera je pogosta napaka?',
                'Izbrati teksturo zaradi luksuznega vtisa in ne zaradi dejanskih potreb kože.'
            ),
            'Forever Alpha E Factor: kdaj bogatejša nega kože res doda vrednost',
            'Odkrijte, kdaj ima Forever Alpha E Factor več smisla in zakaj bogatejša nega ni samodejno boljša za vsakogar.',
            'Alpha E Factor',
            $sections(
                'Zakaj bogata tekstura deluje le, ko jo koža res želi',
                'Bogatejša formula je lahko čudovita na pravi koži in pretežka na napačni, zato je ujemanje tako pomembno.',
                'Zakaj naj tudi luksuz ostane praktičen',
                'Bolj premium krema si zasluži mesto le, če res podpira rutino in ne samo obljubo.'
            )
        ),
        $entry(
            80,
            'Forever MSM Gel: kdaj kombinacija aloe in žvepla bolj smiselno podpira lokalno nego in občutek olajšanja',
            'forever-msm-gel-naravno-zveplo-z-gelom-aloe-vere',
            'MSM Gel je priljubljen zato, ker ga ljudje povezujejo z lokalno uporabo, hladilnim občutkom in praktično podporo. Tukaj je, kako o njem razmišljati bolj uporabno in z manj pretiravanja.',
            '<ul><li>MSM Gel ima največ smisla kot praktičen lokalni izdelek za nego ciljanih območij.</li><li>Največja napaka je obravnavati ga kot univerzalni odgovor na vsako lokalno nelagodje.</li><li>Pametnejši pristop gleda situacijo uporabe, doslednost in realna pričakovanja od topikalnega izdelka.</li></ul>',
            $faq(
                'Za kaj ljudje MSM Gel najpogosteje uporabljajo?',
                'Najpogosteje za lokalno nego in občutek udobja na določenih območjih.',
                'Zakaj je kombinacija aloe in žvepla zanimiva?',
                'Ker združuje enostaven nanos, prijetno teksturo in bolj praktičen občutek lokalne podpore.',
                'Ali lahko gel sam reši vsako lokalno težavo?',
                'Ne. Največ smisla ima kot podpora, ne kot popoln odgovor.',
                'Katera je pogosta napaka?',
                'Od topikalnega gela pričakovati več, kot ta format realno lahko ponudi.'
            ),
            'Forever MSM Gel: kdaj ima lokalna nega z aloe in žveplom smisel',
            'Spoznajte, kdaj je Forever MSM Gel lahko koristen in kako ga vključiti v bolj realistično lokalno rutino nege.',
            'Forever MSM Gel',
            $sections(
                'Zakaj ima lokalna podpora svojo vlogo',
                'Ljudem pogosto pomaga praktičen topikalni korak, tudi kadar ni celoten odgovor sam po sebi.',
                'Zakaj format izdelka določa pričakovanja',
                'Topikalni gel je smiselno ocenjevati po udobju, situaciji uporabe in doslednosti, ne pa po nemogočih obljubah.'
            )
        ),
        $entry(
            81,
            'Forever R3 Factor: kdaj anti-age krema bolje podpira bariero kože kot pa velike obljube',
            'forever-r3-factor-krema-za-popolno-polt-in-mladosten-videz',
            'R3 Factor deluje bolj smiselno, ko ga gledamo skozi suhost, udobje zrelejše kože in bogatejši občutek rutine, ne pa skozi čudežne obljube. Tukaj je, kako ga oceniti pametneje in bolj realistično.',
            '<ul><li>R3 Factor ima največ smisla, ko koža potrebuje več udobja, občutka elastičnosti in bogatejši anti-age občutek.</li><li>Največja napaka je eno kremo spremeniti v glavni odgovor za vse znake staranja.</li><li>Pametnejši pristop skupaj gleda bariero kože, zaščito pred soncem in širšo rutino.</li></ul>',
            $faq(
                'Komu lahko R3 Factor bolj ustreza?',
                'Običajno bolj suhi ali zrelejši koži, ki ji ustreza bogatejši občutek nege.',
                'Ali lahko ena anti-age krema sama naredi veliko razliko?',
                'Običajno ne. Največ koristi pride iz širše rutine in dolgotrajne doslednosti.',
                'Zakaj takšni izdelki ostajajo priljubljeni?',
                'Ker prinašajo udobje, bogatejšo teksturo in bolj zaščiten občutek kože.',
                'Katera je pogosta napaka?',
                'Prezreti osnove, kot je zaščita pred soncem, in od ene kreme pričakovati vse.'
            ),
            'Forever R3 Factor: kdaj anti-age krema res podpira rutino',
            'Odkrijte, kdaj ima Forever R3 Factor smisel in zakaj anti-age izdelki najbolje delujejo v širši rutini nege kože.',
            'Forever R3 Factor',
            $sections(
                'Zakaj bogatejša anti-age nega še vedno potrebuje sistem',
                'Podpora bariere, zaščita pred soncem in dosledna rutina pomenijo več kot katerakoli posamezna obljuba izdelka.',
                'Zakaj je tudi udobje lahko velika vrednost',
                'Krema ni uporabna le takrat, ko je dramatična. Veliko pomeni že to, da se koža počuti bolj mirno in zaščiteno.'
            )
        ),
        $entry(
            82,
            'Infinite by Forever: kdaj anti-age komplet resnično pomaga in kdaj je preprostejša rutina boljša',
            'infinite-by-forever-revolucionaren-komplet-za-nego-in-preprecevanje-staranja',
            'Kompleti za nego privlačijo, ker obljubljajo celovit odgovor, njihova vrednost pa je odvisna od tega, ali sistem res ustreza koži in osebi, ki ga uporablja. Tukaj je, kako o Infinite by Forever razmišljati bolj realistično.',
            '<ul><li>Anti-age komplet ima več smisla, kadar uporabnik želi bolj jasen sistem in ga lahko dosledno uporablja.</li><li>Največja napaka je kupiti celoten komplet brez preverjanja, ali se koža in vsakdan res ujemata s takim pristopom.</li><li>Pametnejši pristop gleda prenašanje, uporabnost in vzdržnost rutine skozi čas.</li></ul>',
            $faq(
                'Komu lahko bolj ustreza celoten skincare komplet?',
                'Ljudem, ki želijo bolj jasno strukturirano rutino in manj ugibanja pri korakih.',
                'Ali je komplet vedno boljši od enostavne rutine?',
                'Ne. Nekaterim bolj ustreza le nekaj skrbno izbranih izdelkov.',
                'Zakaj je pri kompletih doslednost tako pomembna?',
                'Ker sistem pokaže vrednost šele, ko ga človek res uporablja redno in dovolj dolgo.',
                'Katera je pogosta napaka?',
                'Kupiti premium komplet zaradi vtisa in ga nato ne uporabljati dosledno.'
            ),
            'Infinite by Forever: kdaj ima celoten anti-age komplet največ smisla',
            'Poglejte, kdaj je Infinite by Forever lahko pametna izbira in zakaj popoln komplet ni vedno boljši od preprostejše rutine.',
            'Infinite by Forever',
            $sections(
                'Zakaj se celotni sistemi zdijo tako privlačni',
                'Ljudem pogosto ustreza jasnost vnaprej pripravljenega sistema, še posebej, če se v skincareu hitro izgubijo.',
                'Zakaj lahko manjša rutina vseeno zmaga',
                'Preprostejša rutina je pogosto učinkovitejša, če jo je lažje izvajati in bolje ustreza koži.'
            )
        ),
        $entry(
            83,
            'Sonya Deep Moisturizing Cream: kdaj globinska hidracija koži prinese več kot trendovski aktivni izdelki',
            'sonya-deep-moisturizing-cream-globinsko-vlazenje-koze',
            'Globinska hidracija je pogosto podcenjena, ker ne zveni spektakularno, vendar mnogim tipom kože prinese največjo razliko v udobju. Tukaj je, kako Sonya kremo oceniti skozi resnične potrebe kože.',
            '<ul><li>Globinska hidracija ima največ smisla, ko je koža napeta, dehidrirana ali brez občutka ravnovesja.</li><li>Največja napaka je preskočiti hidracijo in vse staviti le na aktivne sestavine.</li><li>Pametnejši pristop gleda vlago, udobje bariere in doslednost rutine.</li></ul>',
            $faq(
                'Komu lahko Sonya Deep Moisturizing Cream bolj ustreza?',
                'Koži, ki je pogosto napeta, dehidrirana ali neudobna in želi več vlage.',
                'Zakaj je globinska hidracija tako pomembna?',
                'Ker brez nje koža pogosto težje prenaša preostalo rutino.',
                'Ali je globinska hidracija dovolj za vse?',
                'Ne, je pa zelo pogosto eden najpomembnejših temeljev mirnejše nege.',
                'Katera je pogosta napaka?',
                'Podceniti, koliko lahko dobra hidracija spremeni celoten občutek kože.'
            ),
            'Sonya Deep Moisturizing Cream: kdaj globinska hidracija pomeni največ',
            'Spoznajte, kdaj lahko Sonya Deep Moisturizing Cream najbolj koristi koži in zakaj hidracija pogosto pomeni več kot trendi.',
            'Sonya Deep Moisturizing Cream',
            $sections(
                'Zakaj hidracija spremeni prenašanje rutine',
                'Dobro navlažena kožna bariera običajno bolje prenaša tudi druge korake nege in ostaja mirnejša skozi dan.',
                'Zakaj trendi ne smejo zamenjati osnov',
                'Aktivne sestavine imajo svoje mesto, vendar se veliko rutin izboljša šele, ko je hidracija res urejena.'
            )
        ),
        $entry(
            84,
            'Forever Balancing Toner: kdaj tonik resnično pomaga rutini in kdaj je samo dekoracija',
            'forever-balancing-toner-popolno-ravnovesje-za-vaso-kozo',
            'Tonik si svoje mesto zasluži le, ko izboljša udobje, ravnovesje ali pripravo kože za nadaljnjo nego. Tukaj je, kako Forever Balancing Toner oceniti bolj realistično in se izogniti uporabi “na slepo”.',
            '<ul><li>Tonik ima največ smisla, kadar je koža po njem bolj mirna, sveža in pripravljena na naslednje korake.</li><li>Največja napaka je tonik uporabljati samo zato, ker se zdi obvezen del nege.</li><li>Pametnejši pristop gleda odziv kože in to, ali izdelek dejansko doda vrednost.</li></ul>',
            $faq(
                'Kdaj ima tonik več smisla?',
                'Kadar izboljša občutek svežine, udobja in priprave kože za naslednje korake.',
                'Ali tonik potrebuje vsakdo?',
                'Ne nujno. Nekaterim tipom kože pomeni več kot drugim.',
                'Zakaj ljudje radi uporabljajo tonike?',
                'Ker prinesejo občutek ravnovesja, čistoče in lepšega ritma rutine.',
                'Katera je pogosta napaka?',
                'Dodajati tonik samodejno, brez preverjanja, ali koži sploh koristi.'
            ),
            'Forever Balancing Toner: kdaj tonik res doda vrednost negi',
            'Odkrijte, kdaj je Forever Balancing Toner lahko koristen in zakaj tonik pomeni nekaj le, ko rutino dejansko izboljša.',
            'Forever Balancing Toner',
            $sections(
                'Zakaj mora tonik upravičiti svoj korak',
                'Dober tonik mora kožo pustiti v boljšem stanju. Če tega ne naredi, je lahko le dodatna navlaka.',
                'Zakaj je kakovost rutine pomembnejša od števila izdelkov',
                'Veliko ljudi ima več koristi od manjše, uporabne rutine kot od sistema z veliko nepotrebnimi koraki.'
            )
        ),
        $entry(
            85,
            'Aloe vera v stanovanju: kako gojiti uporabno rastlino brez pretiranega zalivanja in skrbi',
            'aloe-vera-v-stanovanju-nasveti-za-gojenje-nego-in-uporabo-zdravilnega-gela',
            'Aloe vera najbolje uspeva v stanovanju, ko ima dovolj svetlobe, dobro drenažo in manj panične nege. Tukaj je, kako jo doma ohraniti zdravo in kako bolj realistično razmišljati o uporabi rastline.',
            '<ul><li>Aloe vera v stanovanju najbolje uspeva z več svetlobe, manj vode in bolj zračnim substratom.</li><li>Največja napaka je aloe obravnavati kot rastlino, ki ves čas potrebuje zalivanje in posredovanje.</li><li>Pametnejši pristop gleda stabilne pogoje in realistično domačo uporabo rastline.</li></ul>',
            $faq(
                'Kaj aloe v stanovanju najbolj potrebuje?',
                'Veliko svetlobe, zračen substrat in zmerno zalivanje.',
                'Zakaj domača aloe pogosto propade?',
                'Najpogosteje zaradi preveč vode, slabe drenaže ali premalo svetlobe.',
                'Ali je lahko doma vzgojena aloe tudi praktična?',
                'Da, če je rastlina zdrava in so pričakovanja glede uporabe realna.',
                'Katera je pogosta napaka?',
                'Preveč pomagati rastlini, ki v resnici bolje uspeva z mirnejšo nego.'
            ),
            'Aloe vera v stanovanju: kako jo gojiti bolj preprosto in uspešno',
            'Spoznajte, kako aloe vero gojiti v stanovanju z manj napakami in bolj realnimi pričakovanji glede nege in uporabe.',
            'Aloe vera v stanovanju',
            $sections(
                'Zakaj aloe nagrajuje preprostost',
                'Aloe pogosto uspeva bolje, ko jo prenehamo obravnavati kot občutljivo rastlino in ji ponudimo bolj mirne, suhe razmere.',
                'Zakaj je zdrava rast pomembnejša od popolne uporabe',
                'Ljudje dobijo največ od sobne aloe takrat, ko je rastlina res zdrava, ne pa ko iz nje poskušajo iztisniti preveč uporabe.'
            )
        ),
        $entry(
            86,
            'Smoothing Exfoliator: kdaj piling za obraz pomaga svežini kože in kdaj je manj bolj pametno',
            'smoothing-exfoliator-piling-za-obraz-odkrijte-skrivnost-sveze-in-gladke-polti',
            'Piling za obraz ima smisel, ko koži vrne bolj gladek in svež občutek brez dodatnega draženja, vendar le, če se ujema s tipom kože in pogostostjo uporabe. Tukaj je, kako oceniti Smoothing Exfoliator brez pretiravanja.',
            '<ul><li>Piling za obraz ima več smisla, ko podpira bolj gladko in svežo kožo brez dodatnega draženja.</li><li>Največja napaka je misliti, da pogostejši piling samodejno pomeni boljši rezultat.</li><li>Pametnejši pristop gleda občutljivost, pogostost in preostanek rutine.</li></ul>',
            $faq(
                'Kdaj ima piling za obraz več smisla?',
                'Ko koža dobro prenaša luščenje in si želi bolj svežega občutka površine.',
                'Ali je preveč pilinga lahko problem?',
                'Da, še posebej pri bolj občutljivi koži, ki slabo prenaša preveč trenja ali aktivnosti.',
                'Zakaj imajo ljudje radi takšne izdelke?',
                'Ker pogosto dajo takojšen občutek gladkosti in svežine.',
                'Katera je pogosta napaka?',
                'Piling uporabljati prepogosto in zamenjati svežino za agresivnost.'
            ),
            'Smoothing Exfoliator: kdaj piling za obraz najbolj pomaga',
            'Odkrijte, kdaj lahko Smoothing Exfoliator koristi svežemu tenu in zakaj nežnejša pogostost pogosto deluje bolje.',
            'Smoothing Exfoliator',
            $sections(
                'Zakaj bolj gladko ni vedno boljše, če koža plača ceno',
                'Dober piling mora izboljšati občutek kože, ne da bi jo pustil preobremenjeno ali razdraženo.',
                'Zakaj je čas uporabe enako pomemben kot izdelek',
                'Tudi dober piling postane slab izbor, če ga za tip kože uporabljamo prepogosto.'
            )
        ),
        $entry(
            87,
            'Forever Living Products izdelki: kako se znajti v ponudbi in izbrati tisto, kar res ustreza vašemu cilju',
            'forever-living-products-izdelki',
            'Velik katalog pomaga le, če veste, kaj v njem iščete. Tukaj je, kako se v Forever ponudbi znajti bolj pametno, povezati cilj s pravo kategorijo in se izogniti impulzivnemu nakupu.',
            '<ul><li>Najboljši izbor izdelkov se običajno začne z jasnim ciljem še pred odprtjem kataloga.</li><li>Največja napaka je kupovati na podlagi hypea, priljubljenosti ali ene same priporočene stvari brez konteksta.</li><li>Pametnejši pristop loči prebavo, skincare, vitalnost in bolj usmerjene podporne kategorije.</li></ul>',
            $faq(
                'Kako se je najbolje znajti v velikem Forever katalogu?',
                'Tako, da najprej določite cilj in nato zožite izbiro na ustrezno kategorijo.',
                'Zakaj ljudje pogosto kupijo napačen izdelek?',
                'Ker začnejo pri hypeu ali vtisu namesto pri resnični potrebi.',
                'Ali je pametneje kupiti veliko izdelkov hkrati?',
                'Ne vedno. Pogosto je bolje začeti z manjšim in bolj jasnim izborom.',
                'Katera je pogosta napaka?',
                'Kupiti celo skupino izdelkov brez razumevanja, kaj rutina v resnici potrebuje.'
            ),
            'Forever Living Products izdelki: kako jih iz kataloga izbirati pametneje',
            'Odkrijte, kako se znajti v Forever ponudbi in izbrati izdelke, ki se res ujemajo z vašim ciljem in rutino.',
            'Forever izdelki katalog',
            $sections(
                'Zakaj jasnost izboljša nakupne odločitve',
                'Ljudje izberejo bolje, ko vedo, kakšen rezultat želijo, namesto da brez smeri brskajo po celotnem katalogu.',
                'Zakaj pogosto zmaga manj, a boljši izbor',
                'Manjša in bolj osredotočena rutina običajno ustvari več vrednosti kot velik nabor izdelkov brez doslednosti.'
            )
        ),
        $entry(
            89,
            'Aloe vera sok: kdaj ima smisel za dnevno rutino in zakaj ga ni dobro skrčiti samo na imuniteto',
            'sok-aloe-vere-naraven-nacin-za-krepitev-imunosti',
            'Ljudje aloe vera sok pogosto pijejo zaradi imunosti, njegova prava vrednost pa je običajno širša in povezana z rutino, prebavo in splošnim občutkom vitalnosti. Tukaj je, kako nanj gledati bolj realistično.',
            '<ul><li>Aloe vera sok ima največ smisla kot del dnevne rutine in ne kot enkraten odziv na sezonski strah.</li><li>Največja napaka je celotno zgodbo o soku skrčiti samo na imuniteto in spregledati širši življenjski slog.</li><li>Pametnejši pristop gleda prebavo, rutino in doslednost skupaj z osnovnimi zdravstvenimi navadami.</li></ul>',
            $faq(
                'Zakaj ljudje pijejo aloe vera sok?',
                'Najpogosteje zaradi prebave, imunosti in širšega občutka dnevne vitalnosti.',
                'Ali je aloe sok samo izdelek za imuniteto?',
                'Ne. Veliko ljudi ga uporablja tudi kot del širše prebavne in wellness rutine.',
                'Kdaj ima več smisla?',
                'Ko ga uvedete dosledno in kot del bolj urejenega dnevnega ritma.',
                'Katera je pogosta napaka?',
                'Pričakovati, da bo sok sam nadomestil slab spanec, prehrano in stres.'
            ),
            'Aloe vera sok: kdaj ima smisel za rutino, prebavo in vitalnost',
            'Spoznajte, kdaj je aloe vera sok lahko koristen in zakaj si zasluži širši pogled od same zgodbe o imuniteti.',
            'Aloe vera sok',
            $sections(
                'Zakaj rutina spremeni pomen soka',
                'Aloe sok običajno pokaže več smisla, ko postane del stalne dnevne navade in ne le kratkoročni odziv.',
                'Zakaj je imuniteta le del širše zgodbe',
                'Ljudje imajo pogosto več koristi, ko hkrati razmišljajo o prebavi, ritmu in vsakodnevnem ravnovesju.'
            )
        ),
        $entry(
            90,
            'Aloe vera skozi zgodovino: kaj je o tej dolgo cenjeni rastlini še danes res pomembno',
            'aloe-vera-skozi-zgodovino-kaj-moramo-vedeti-o-tej-rastlini',
            'Zgodovina aloe vere je pomembna zato, ker pokaže, kako globoko je rastlina skozi čas vstopila v kulturo nege in vsakodnevne podpore. Tukaj je, kaj iz te zgodbe še danes ostaja pomembno in kako jo brati brez pretiravanja.',
            '<ul><li>Zgodovina aloe vere pomaga razumeti, zakaj je rastlina še danes tako prisotna v negi in wellnessu.</li><li>Največja napaka je zgodovinsko fascinacijo zamenjati za avtomatski dokaz vseh sodobnih trditev.</li><li>Pametnejši pristop povezuje tradicijo s premišljeno presojo sodobnih izdelkov.</li></ul>',
            $faq(
                'Zakaj je aloe vera tako močno prisotna skozi zgodovino?',
                'Ker so jo številne kulture cenile pri negi kože in širši vsakodnevni podpori.',
                'Kaj nam zgodovina aloe pove danes?',
                'Da gre za zelo spoštovano rastlino, vendar je sodobne izdelke še vedno treba ocenjevati posamezno.',
                'Zakaj imajo ljudje radi takšne zgodbe o rastlinah?',
                'Ker moderni uporabi izdelkov dodajo širši pomen in kontekst.',
                'Katera je pogosta napaka?',
                'Zgodovinski pomen zamenjati z avtomatično kakovostjo vsakega sodobnega izdelka.'
            ),
            'Aloe vera skozi zgodovino: kaj je še danes res pomembno',
            'Odkrijte, kaj zgodba o aloe veri skozi zgodovino še danes lahko nauči uporabnika, ki želi izbirati bolj pametno.',
            'Aloe vera skozi zgodovino',
            $sections(
                'Zakaj zgodovina rastline še vedno vpliva na zaupanje',
                'Ljudje se z izdelki pogosto lažje povežejo, ko razumejo širšo kulturno zgodbo sestavine, ki jo uporabljajo.',
                'Zakaj mora zgodovino še vedno spremljati kakovost',
                'Spoštovanje do rastline je dragoceno, toda današnje izbire so še vedno odvisne od izvora, obdelave in integritete izdelka.'
            )
        ),
        $entry(
            91,
            'Vital 5: kako ta Forever koncept zdravja razumeti brez poenostavljanja in praznega slogana',
            'vital-5-za-optimalno-zdravje-in-vitalnost',
            'Vital 5 postane koristen šele, ko ga obravnavamo kot okvir za navade in ne kot marketinški slogan. Tukaj je, kako razumeti koncept in kje lahko ljudem res pomaga organizirati bolj zdravo rutino.',
            '<ul><li>Vital 5 ima več smisla kot okvir za razmišljanje o zdravju kot pa kot čudežen seznam korakov.</li><li>Največja napaka je zdravstveni koncept spremeniti v slogan brez resnične vsakodnevne uporabe.</li><li>Pametnejši pristop gleda, kako se ključni stebri rutine dejansko živijo iz dneva v dan.</li></ul>',
            $faq(
                'Kaj je pravzaprav koncept Vital 5?',
                'Gre za okvir, ki poskuša poenostaviti ključne stebre bolj zdravega in vitalnega življenja.',
                'Ali lahko koncept sam spremeni zdravje?',
                'Le, če postane resnično vedenje in ne ostane samo ideja.',
                'Zakaj ljudem takšen okvir koristi?',
                'Ker zdravje naredi bolj pregledno in lažje za organizacijo v vsakdanjiku.',
                'Katera je pogosta napaka?',
                'Ostati pri ideji in je nikoli ne pretvoriti v dejanska vsakodnevna dejanja.'
            ),
            'Vital 5: kako koncept pretvoriti v resničen okvir zdravja',
            'Spoznajte, kako Vital 5 razumeti bolj jasno in kje lahko res pomaga pri gradnji bolj zdrave dnevne strukture.',
            'Vital 5',
            $sections(
                'Zakaj so okviri lahko še vedno koristni',
                'Preprosti zdravstveni okviri mnogim pomagajo organizirati pozornost, še posebej, ko jih poplava informacij hitro zmede.',
                'Zakaj ideje štejejo le, ko postanejo rutina',
                'Vrednost koncepta se pokaže šele takrat, ko spremeni način življenja, ne pa samo govor o wellnessu.'
            )
        ),
        $entry(
            98,
            'Forever cene izdelkov in katalog: kako presojati vrednost in ne samo številke ob izdelku',
            'cene-izdelkov-forever-living-products-katalog',
            'Katalog in cenik pomagata le, ko ju znate brati skozi vrednost, pogostost uporabe in resnično potrebo. Tukaj je, kako na Forever cene gledati bolj pametno in z manj impulza.',
            '<ul><li>Ceno izdelka je vedno smiselno gledati skupaj z namenom, količino in dejansko pogostostjo uporabe.</li><li>Največja napaka je primerjati cene brez razumevanja kategorije in razloga za nakup.</li><li>Pametnejši pristop gleda vrednost v rutini in dolgoročno uporabnost, ne pa le številke.</li></ul>',
            $faq(
                'Kako je cenik najbolje brati bolj pametno?',
                'Tako, da ceno primerjate z namenom izdelka, količino in pogostostjo realne uporabe.',
                'Zakaj nižja cena ni vedno boljši nakup?',
                'Ker mora izdelek še vedno ustrezati cilju in rutini, sicer nima prave vrednosti.',
                'Kaj pomaga pri pametnejši izbiri?',
                'Jasen cilj, ožji fokus in boljši občutek za dejansko vrednost izdelka.',
                'Katera je pogosta napaka?',
                'Kupovati zaradi občutka ugodne ponudbe namesto zaradi resnične potrebe.'
            ),
            'Forever cene izdelkov: kako katalog brati skozi resnično vrednost',
            'Odkrijte, kako Forever cenik in katalog gledati bolj pametno z več fokusa na vrednost in manj na impulzivni vtis.',
            'Forever cene in katalog',
            $sections(
                'Zakaj je cena pomembna le v kontekstu',
                'Številka dobi pravi pomen šele, ko jo primerjamo z namenom, količino in dejansko uporabo izdelka.',
                'Zakaj je vrednost izdelka skozi čas lahko drugačna',
                'Nekateri izdelki se na prvi pogled zdijo dragi, vendar imajo več smisla, ko jih človek res uporablja dosledno in z jasnim namenom.'
            )
        ),
        $entry(
            101,
            'Forever Aloe Vera Gel: zakaj ta klasik mnogim postane osnovni izdelek vitalnosti, ne le občasna pomoč',
            'forever-aloe-vera-gel-naravna-resitev-za-vitalnost',
            'Forever Aloe Vera Gel ima več smisla, ko ga razumemo kot del rutine in vzorca vitalnosti, ne pa kot izdelek, ki ga uporabimo le, ko nekaj ni v redu. Tukaj je, kako o njem razmišljati bolj realistično.',
            '<ul><li>Forever Aloe Vera Gel najbolje deluje, ko podpira bolj ustaljeno wellness in prebavno rutino.</li><li>Največja napaka je gel uporabljati le občasno, hkrati pa pričakovati poln učinek rutinskega izdelka.</li><li>Pametnejši pristop gel vidi kot osnovni izdelek v širšem vzorcu skrbi za telo in navade.</li></ul>',
            $faq(
                'Zakaj ljudje Forever Aloe Vera Gel pogosto doživljajo kot osnovni izdelek?',
                'Ker ga mnogi povezujejo z dnevno rutino, prebavo in širšim občutkom vitalnosti.',
                'Ali je pri takem izdelku doslednost res pomembna?',
                'Običajno da, saj prav rutina pogosto daje takemu izdelku smisel.',
                'Ali lahko gel sam ustvari celoten učinek vitalnosti?',
                'Ne v celoti. Najbolj uporaben je kot del širšega bolj zdravega ritma življenja.',
                'Katera je pogosta napaka?',
                'Na gel gledati kot na sezonski rešilni izdelek namesto kot na rutinski korak.'
            ),
            'Forever Aloe Vera Gel: kdaj postane osnovni izdelek rutine in vitalnosti',
            'Odkrijte, zakaj ima Forever Aloe Vera Gel pogosto največ smisla kot del stalne dnevne rutine in ne le kot občasna pomoč.',
            'Forever Aloe Vera Gel',
            $sections(
                'Zakaj osnovni izdelki oblikujejo rutine',
                'Nekateri izdelki postanejo pomembni ne zato, ker so dramatični, ampak zato, ker jih je mogoče uporabljati dolgo in redno.',
                'Zakaj doslednost spremeni rezultat',
                'Rutinski izdelki pogosto pokažejo največjo vrednost šele takrat, ko postanejo reden del vsakdana in ne le občasna pomoč.'
            )
        ),
        $entry(
            102,
            'Proteinski dodatki: kaj zares deluje za gradnjo mišic in kaj je le draga zmeda',
            'proteinski-dodatki-kaj-resnicno-deluje-za-izgradnjo-misic',
            'Proteinski dodatki imajo smisel le, ko podpirajo trening, okrevanje in prehrano, ki še potrebuje pomoč pri doseganju ciljanega vnosa beljakovin. Tukaj je, kako jih oceniti bolj resno in brez fitnes mitov.',
            '<ul><li>Proteinski dodatki imajo največ smisla, ko z običajno prehrano težko dosežete potreben vnos beljakovin.</li><li>Največja napaka je pričakovati, da bo protein sam gradil mišice brez treninga in okrevanja.</li><li>Pametnejši pristop gleda skupni vnos beljakovin, obremenitev pri treningu in praktično vrednost dodatka.</li></ul>',
            $faq(
                'Kdaj ima proteinski dodatek več smisla?',
                'Ko je s hrano težko doseči količino beljakovin, ki jo dejansko potrebujete.',
                'Ali lahko protein sam zgradi mišice?',
                'Ne. Rast mišic je odvisna od treninga, okrevanja in skupnega vnosa beljakovin.',
                'Zakaj ljudje proteinske praške pogosto precenjujejo?',
                'Ker zvenijo preprosto in učinkovito, zato se njihova vloga hitro napihne.',
                'Katera je pogosta napaka?',
                'Kupiti protein, še preden sta trening in osnovna prehrana sploh urejena.'
            ),
            'Proteinski dodatki: kdaj res delujejo za mišice in okrevanje',
            'Spoznajte, kdaj imajo proteinski dodatki smisel in kako jih oceniti brez fitnes mitov in marketinške megle.',
            'Proteinski dodatki',
            $sections(
                'Zakaj so proteinski praški podpora in ne celotna strategija',
                'Dodatki so lahko zelo praktični, vendar najbolje delujejo takrat, ko sta trening in prehrana že dobro postavljena.',
                'Zakaj je preprost fitnes jezik lahko zavajajoč',
                'Gradnja mišic zveni enostavno, ko jo skrčimo na napitke, v resnici pa rezultat nosi celotna rutina.'
            )
        ),
    ],
];
