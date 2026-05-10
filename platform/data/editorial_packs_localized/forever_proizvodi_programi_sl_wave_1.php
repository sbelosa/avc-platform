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
    'key' => 'forever-proizvodi-programi-sl-wave-1',
    'name' => 'Forever izdelki in programi (SL) - prvi val',
    'notes' => 'Večji lokalizirani uredniški val za Forever izdelke, FIT programe in bolj pametno izbiro izdelkov glede na cilj.',
    'entries' => [
        $entry(
            489,
            'Forever Arctic Sea: omega-3 podpora za srce, možgane in vsakodnevno ravnovesje',
            'forever-arctic-sea-omega-3-za-srce-mozgane-in-vsakodnevno-ravnovesje',
            'Izdelki z omega-3 imajo pravi smisel šele, ko jih gledamo kot del širšega načrta prehrane, ravnovesja in vsakodnevne rutine. Tukaj je, kako Forever Arctic Sea oceniti bolj realno in brez pretvarjanja ene kapsule v rešitev za vse.',
            '<ul><li>Podpora z omega-3 najbolje deluje kot del širše prehranske in vsakodnevne rutine.</li><li>Največja napaka je pričakovati, da bo en izdelek popravil prehrano brez kakovostnih maščob in slab življenjski ritem.</li><li>Pametnejši pristop gleda doslednost, skupni vnos maščobnih kislin in resničen razlog za uporabo.</li></ul>',
            [
                ['question' => 'Zakaj ljudje sploh uporabljajo omega-3?', 'answer' => 'Najpogosteje zaradi podpore srcu, možganom in širšemu ravnovesju vnetnih procesov.'],
                ['question' => 'Ali Arctic Sea nadomesti dobro prehrano?', 'answer' => 'Ne. Največ smisla ima kot dopolnilo, ne kot zamenjava za kakovostno hrano.'],
                ['question' => 'Kdaj ima največ smisla?', 'answer' => 'Ko podpira rutino, ki že gre v bolj zdravo smer in potrebuje več doslednosti.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kupiti omega-3 brez jasnega razloga in brez načrta za redno uporabo.'],
            ],
            'Forever Arctic Sea: kdaj ima omega-3 res smisel za srce in možgane',
            'Spoznajte, kje ima Forever Arctic Sea smisel kot omega-3 podpora in kako ga uporabljati z bolj realnimi pričakovanji.',
            'Forever Arctic Sea',
            [
                ['heading' => 'Zakaj je omega-3 več kot samo en izdelek', 'html' => '<p>Ljudje pogosto iščemo preprost odgovor za podporo srcu in ravnovesju v telesu, vendar je uporabnost omega-3 običajno največja znotraj širšega prehranskega vzorca. Prav to daje izdelku, kot je Arctic Sea, bolj jasen pomen.</p>'],
                ['heading' => 'Zakaj je razlog za uporabo najpomembnejši', 'html' => '<p>Dopolnila imajo največjo vrednost, ko rešujejo konkreten cilj in ne sledijo samo trendu. Boljši razlog navadno pomeni tudi bolj dosledno in smiselno uporabo.</p>'],
            ]
        ),
        $entry(
            500,
            'C9 vs. F15: kateri Forever FIT program se bolje ujema z vašim resničnim ciljem',
            'c9-vs-f15-kateri-forever-fit-program-se-bolje-ujema-z-vasim-ciljem',
            'C9 in F15 zvenita kot naravni nadaljevanji istega sistema, vendar nista enako orodje in ne ustrezata isti začetni točki. Tukaj je, kako izbrati glede na energijo, rutino in dejansko zmožnost, ne le po obljubi hitrega rezultata.',
            '<ul><li>C9 in F15 se razlikujeta po vlogi, tempu in strukturi, zato je boljša izbira odvisna od posameznika in cilja.</li><li>Največja napaka je odločitev po vtisu ali po izkušnji nekoga drugega brez lastne realne ocene.</li><li>Pametnejši pristop gleda, koliko strukture, trajanja in podpore res ustreza vašemu življenju.</li></ul>',
            [
                ['question' => 'Ali sta C9 in F15 skoraj ista stvar?', 'answer' => 'Ne. Sta del istega sistema, vendar ustvarjata drugačen ritem in drugačno izkušnjo.'],
                ['question' => 'Komu običajno bolj ustreza C9?', 'answer' => 'Ljudem, ki želijo krajši, bolj jasen začetek in strukturiran prvi reset navad.'],
                ['question' => 'Kdaj ima F15 več smisla?', 'answer' => 'Ko vam bolj ustreza daljši okvir in bolj postopen prehod v trajnejšo rutino.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Vstopiti v program brez priprave urnika, obrokov in realnega načrta za sledenje.'],
            ],
            'C9 ali F15: kako izbrati Forever FIT program, ki vam res ustreza',
            'Primerjajte C9 in F15 po tempu, strukturi in dolgoročni uporabnosti, ne samo po hypeu.',
            'C9 vs F15',
            [
                ['heading' => 'Zakaj boljši program vedno zavisi od začetne točke', 'html' => '<p>Ljudje pogosto primerjamo programa, kot da bi bil eden samodejno boljši. V resnici je boljša izbira običajno odvisna od trenutne rutine, stresa in tega, kakšna struktura se zdi vzdržna.</p>'],
                ['heading' => 'Zakaj je ujemanje pomembnejše od intenzivnosti', 'html' => '<p>Program deluje le, če ga oseba lahko dejansko sledi. Zato realno ujemanje pogosto pomeni več kot bolj dramatična ali strožja izbira.</p>'],
            ]
        ),
        $entry(
            535,
            'Clean 9 program: prvi korak k več vitalnosti ali preveč ambiciozen reset',
            'clean-9-program-prvi-korak-k-vec-vitalnosti-ali-prevec-ambiciozen-reset',
            'Clean 9 je lahko uporaben začetni okvir, vendar le, če ga ne obravnavate kot kratko skrajnost, po kateri se vse vrne na staro. Tukaj je, kako ga gledati kot strukturiran začetek in ne kot čudežni reset.',
            '<ul><li>Clean 9 ima več smisla kot vstop v boljše navade kot pa kot izoliran sprint.</li><li>Največja napaka je pričakovati, da bo devet dni rešilo več let kaotične rutine brez nadaljevanja.</li><li>Pametnejši pristop program uporabi za jasnost, zagon in bolj realen naslednji korak.</li></ul>',
            [
                ['question' => 'Zakaj ljudje začnejo s Clean 9?', 'answer' => 'Najpogosteje zaradi želje po jasnejši strukturi in občutku urejenega začetka.'],
                ['question' => 'Ali je program sam po sebi dovolj?', 'answer' => 'Ne dolgoročno. Največ koristi ima, ko vodi v bolj vzdržno nadaljevanje.'],
                ['question' => 'Komu se lahko zdi pretežak?', 'answer' => 'Ljudem brez priprave, s slabim okrevanjem ali z nerealnimi pričakovanji.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Končati program in se takoj vrniti v stare vzorce.'],
            ],
            'Clean 9 program: kako ga uporabiti kot pameten začetek, ne kot kratek ekstrem',
            'Poglejte, kdaj ima Clean 9 res smisel kot strukturiran začetek in kako se izogniti najpogostejšim napakam.',
            'Clean 9',
            [
                ['heading' => 'Zakaj program nekaj pomeni le, če spremeni nadaljevanje', 'html' => '<p>Kratek program je lahko koristen, vendar le če izboljša to, kar pride po njem. Prava vrednost Clean 9 pogosto ni v samih dneh programa, ampak v navadah, ki jih lahko po njem ohranimo.</p>'],
                ['heading' => 'Zakaj je struktura koristna, ne pa čarobna', 'html' => '<p>Jasna pravila lahko ustvarijo zagon, vendar ne morejo nadomestiti širših življenjskih navad. Zato je nadaljevanje pomembnejše od same intenzivnosti.</p>'],
            ]
        ),
        $entry(
            536,
            'Forever Alpha E Factor: globinska hidratacija ali le drag občutek luksuza',
            'forever-alpha-e-factor-unlock-the-secret-of-deep-hydration',
            'Pri bogatejših izdelkih za nego kože ni bistveno le, ali se zdijo prestižni, ampak ali formula res ustreza koži in rutini. Tukaj je, kako Alpha E Factor pogledati skozi kožno bariero, suhost in bolj realna pričakovanja.',
            '<ul><li>Hidratantni in hranilni izdelki delujejo najbolje, ko se ujemajo s tipom kože in celotno rutino.</li><li>Največja napaka je presojati formulo samo po luksuznem občutku namesto po resnični uporabnosti in prenašanju.</li><li>Pametnejši pristop opazuje, kako izdelek skozi čas pomaga suhosti, barieri in občutku kože.</li></ul>',
            [
                ['question' => 'Komu Alpha E Factor najpogosteje ustreza?', 'answer' => 'Pogosto bolj suhi, utrujeni ali dehidrirani koži, ki potrebuje bogatejši občutek nege.'],
                ['question' => 'Ali lahko nadomesti celotno rutino?', 'answer' => 'Ne. Največ smisla ima kot del celotne nege, ne kot edina rešitev za vse.'],
                ['question' => 'Ali je luksuzna tekstura dovolj za nakup?', 'answer' => 'Ne sama po sebi. Pomembneje je, ali koža takšno formulo res potrebuje in dobro prenaša.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kupiti bogatejšo nego brez preverjanja, ali se res ujema s tipom kože.'],
            ],
            'Forever Alpha E Factor: kdaj ima globinska hidratacija res smisel za kožo',
            'Spoznajte, komu Alpha E Factor najbolje ustreza in kje bogatejša nega res prinaša korist.',
            'Alpha E Factor',
            [
                ['heading' => 'Zakaj bogatejša nega potrebuje boljše ujemanje', 'html' => '<p>Vsaka koža ne potrebuje enake stopnje bogate nege. Zato ima izdelek, kot je Alpha E Factor, največ smisla takrat, ko razumemo tip kože in celotno rutino, v katero vstopa.</p>'],
                ['heading' => 'Zakaj udobje pomeni več kot občutek luksuza', 'html' => '<p>Prijetna tekstura lahko hitro pritegne, vendar je pomembnejše, ali je koža ob uporabi dejansko bolj mirna in stabilna. Prav ta praktični učinek na dolgi rok pomeni največ.</p>'],
            ]
        ),
        $entry(
            537,
            'Forever Lean: podpora uravnavanju teže z bolj pametnimi pričakovanji',
            'forever-lean-a-practical-path-to-healthy-weight-management',
            'Izdelki za podporo teži pogosto dobijo nalogo, da opravijo delo celotnega načrta. Tukaj je, kje lahko Forever Lean bolj realno sede in zakaj ga je smiselno razumeti kot podporo, ne pa kot glavni motor rezultata.',
            '<ul><li>Forever Lean ima največ smisla kot manjše podporno orodje znotraj širšega prehranskega in življenjskega načrta.</li><li>Največja napaka je pričakovati, da bo izdelek sam nosil zgodbo apetita, obrokov, spanja in spremembe teže.</li><li>Pametnejši pristop Lean uporabi kot en del boljšega sistema, ne kot njegovo zamenjavo.</li></ul>',
            [
                ['question' => 'Kdaj ima Forever Lean največ smisla?', 'answer' => 'Ko oseba že ima delujoč načrt in želi dodatno plast strukture ali podpore.'],
                ['question' => 'Ali lahko sam zmanjša težo?', 'answer' => 'Ne. Brez boljših prehranskih navad in rutine en izdelek ne more nositi rezultata.'],
                ['question' => 'Kdaj je manj uporaben?', 'answer' => 'Ko so obroki, apetit in dnevni ritem še vedno povsem neurejeni.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Lean uporabljati kot zamenjavo za prehranski načrt namesto kot podporo znotraj njega.'],
            ],
            'Forever Lean: kje pomaga in kje še vedno odločajo širše navade hujšanja',
            'Razumite, kje lahko Forever Lean podpira uravnavanje teže in zakaj je odvisen od širšega načrta.',
            'Forever Lean',
            [
                ['heading' => 'Zakaj izdelki za težo potrebujejo boljši okvir', 'html' => '<p>Takšne izdelke najlažje napačno razumemo takrat, ko pričakujemo, da bodo nadomestili celoten prehranski in življenjski sistem. V resnici največ koristijo znotraj že urejene širše slike.</p>'],
                ['heading' => 'Zakaj je sistem pomembnejši od dodatka', 'html' => '<p>Ritem obrokov, občutek sitosti in doslednost navad običajno odločajo veliko več kot en sam izdelek. Bolj realen okvir pogosto pomeni tudi bolj smiseln rezultat.</p>'],
            ]
        ),
        $entry(
            538,
            'C9 Forever Detox: reset navad ali marketinška zgodba o čiščenju',
            'c9-forever-detox-discover-how-to-reset-your-body',
            'Beseda detox pogosto ustvari več pričakovanj, kot bi jih moral nositi sam program. Tukaj je, kako C9 Forever Detox razumeti kot strukturiran ponovni začetek navad in ne kot čarobno čiščenje vsega, kar se je nabralo.',
            '<ul><li>Detox programi imajo več smisla kot okvir za nov ritem kot pa kot dobesedno čudežno “čiščenje”.</li><li>Največja napaka je pričakovati, da bo nekaj dni popravilo celoten življenjski vzorec brez nadaljevanja.</li><li>Pametnejši pristop reset uporabi za več strukture, fokusa in bolj realen naslednji korak.</li></ul>',
            [
                ['question' => 'Kaj lahko C9 Detox realno ponudi?', 'answer' => 'Običajno več strukture, jasnosti in bolj discipliniran začetek okoli obrokov in dnevnega ritma.'],
                ['question' => 'Ali gre tukaj za dobesedni “detoks telesa”?', 'answer' => 'Ne v dramatičnem smislu, kot ga pogosto uporablja marketing. Veliko bolj uporabno ga je gledati kot reset navad.'],
                ['question' => 'Kdaj ima program več smisla?', 'answer' => 'Ko ga obravnavate kot začetek daljšega procesa in ne kot dokončno rešitev.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Končati reset brez nadaljevanja in se takoj vrniti v isti kaos.'],
            ],
            'C9 Forever Detox: kako ga uporabiti kot reset navad in ne kot mit o detoksu',
            'Poglejte, kako C9 Forever Detox bolj realno razumeti kot okvir za nove navade in jasnejši začetek.',
            'C9 Detox',
            [
                ['heading' => 'Zakaj detox jezik pogosto zamegli resnično korist', 'html' => '<p>Ljudje si pogosto predstavljamo dramatičen proces čiščenja, vendar je praktična vrednost takšnega programa običajno predvsem v bolj jasni strukturi in začetnem ritmu. Ta okvir veliko bolje zaščiti pričakovanja kot mitološki jezik.</p>'],
                ['heading' => 'Zakaj naslednja faza odloča o pravi vrednosti', 'html' => '<p>Kratek reset je pomemben le, če izboljša tisto, kar pride za njim. Zato je pravi smisel takšnega programa pogosto v navadah, ki jih zgradi po koncu.</p>'],
            ]
        ),
        $entry(
            540,
            'Active Pro-B probiotik: kdaj res pomaga prebavi in kdaj je le še ena kapsula',
            'active-pro-b-probiotic-your-true-ally-for-healthy-digestion',
            'Probiotiki imajo smisel šele, ko natančno vemo, zakaj jih uvajamo. Tukaj je, kako Active Pro-B pogledati skozi vsakodnevno prebavo, ritem hrane in realno vlogo, ki jo lahko ima en probiotik znotraj širšega načrta.',
            '<ul><li>Probiotik je najbolj smiseln, ko obstaja jasen prebavni cilj ali potreba po podpori ravnovesju črevesja.</li><li>Največja napaka je jemati ga na slepo, medtem ko ostajajo obroki, stres in prebavni sprožilci enaki.</li><li>Pametnejši pristop ga poveže z boljšo prehrano in bolj jasnim razumevanjem simptomov.</li></ul>',
            [
                ['question' => 'Kdaj ima probiotični izdelek smisla?', 'answer' => 'Ko obstaja jasen cilj, kot so podpora prebavi, okrevanje ali boljši ritem črevesja.'],
                ['question' => 'Ali lahko probiotik sam reši vse težave?', 'answer' => 'Ne vedno. Hrana, stres in dnevni vzorec pogosto vplivajo enako močno.'],
                ['question' => 'Kako veste, ali pomaga?', 'answer' => 'S spremljanjem simptomov skozi čas in opazovanjem sprememb ob rutini.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Naenkrat spremeniti preveč stvari in nato ne vedeti, kaj je naredilo razliko.'],
            ],
            'Active Pro-B: kdaj ima probiotik res smisla za prebavo in kako ga ne preceniti',
            'Spoznajte, kdaj lahko Active Pro-B pomaga prebavi in kako ga bolj pametno vključiti v širši načrt za črevesje.',
            'Active Pro-B',
            [
                ['heading' => 'Zakaj probiotik potrebuje jasno prebavno vlogo', 'html' => '<p>Takšni izdelki postanejo veliko bolj uporabni, ko so vezani na resničen prebavni cilj in ne le na splošno upanje. Čim bolj jasna je vloga, tem lažje je presoditi, ali podpora res nekaj prinaša.</p>'],
                ['heading' => 'Zakaj je prebava še vedno odvisna od širšega vzorca', 'html' => '<p>Noben probiotik ne deluje ločeno od hrane, stresa in ritma osebe. Zato najbolj koristni rezultati običajno pridejo iz kombinacije, ne iz kapsule same.</p>'],
            ]
        ),
        $entry(
            542,
            'Forever Nutra Q10: podpora energiji in srcu ali le še ena anti-age zgodba',
            'forever-nutra-q10-discover-the-source-of-energy-and-heart-health-support',
            'Q10 pogosto stoji na stičišču zgodb o energiji, staranju in podpori srcu. Tukaj je, kako Forever Nutra Q10 oceniti bolj mirno skozi resnične potrebe telesa in pogoje, kjer tak izdelek res lahko nekaj prispeva.',
            '<ul><li>Q10 ima lahko uporabno mesto v določenih kontekstih energije in vitalnosti, vendar ne kot univerzalni “boost”.</li><li>Največja napaka je od njega pričakovati, da bo nadomestil slab spanec, stres in pomanjkljivo okrevanje.</li><li>Pametnejši pristop vidi Q10 kot ciljno podporo, ne kot glavni motor energijskega ravnovesja.</li></ul>',
            [
                ['question' => 'Zakaj ljudje uporabljajo Q10?', 'answer' => 'Najpogosteje zaradi podpore energiji, vitalnosti in zanimanja za širšo podporo srcu.'],
                ['question' => 'Ali lahko Q10 sam odpravi utrujenost?', 'answer' => 'Ne. Če so glavni vzroki utrujenosti drugje, en dodatek ne more nositi celotne rešitve.'],
                ['question' => 'Kdaj ima več smisla?', 'answer' => 'Ko obstaja jasen cilj in ko se ujema s širšim načrtom podpore.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Od Q10 pričakovati energijo, ki je v resnici odvisna od spanca in okrevanja.'],
            ],
            'Forever Nutra Q10: kje ima smisel za energijo in srce ter kje ga ne gre preceniti',
            'Razumite, kdaj lahko Forever Nutra Q10 smiselno podpira energijo in vitalnost brez nerealnih pričakovanj.',
            'Nutra Q10',
            [
                ['heading' => 'Zakaj se energijski izdelki pogosto narobe razumejo', 'html' => '<p>Ljudje pogosto upamo, da bo izdelek za energijo hitro izbrisal posledice slabega okrevanja. V praksi imajo dodatki, kot je Q10, največ smisla takrat, ko je širši energijski sistem že bolje podprt.</p>'],
                ['heading' => 'Zakaj jasen razlog vodi do pametnejše uporabe', 'html' => '<p>Vrednost izdelka je navadno precej jasnejša, kadar uporabnik ve, kakšno vrsto podpore sploh išče. Boljši razlog skoraj vedno pomeni tudi bolj realna pričakovanja in doslednejšo uporabo.</p>'],
            ]
        ),
        $entry(
            545,
            'Aloe First sprej: praktična podpora za kožo in lase brez pretiranih obljub',
            'aloe-first-spray-multi-purpose-protection-and-care-for-skin-and-hair',
            'Večnamenski izdelki se hitro prodajajo kot rešitev za vse, njihova prava vrednost pa se pokaže tam, kjer jih ljudje res uporabljajo in imajo radi. Tukaj je, kako Aloe First oceniti skozi kožo, lasišče in vsakodnevne situacije, kjer sprej format res pomaga.',
            '<ul><li>Aloe First sprej ima največ smisla tam, kjer je hitra, praktična in nežna podpora res uporabna.</li><li>Največja napaka je pričakovati, da bo en večnamenski sprej idealen odgovor za vse kožne ali lasne potrebe.</li><li>Pametnejši pristop pogleda, kje sprej rutino poenostavi in kje so še vedno boljši bolj ciljni izdelki.</li></ul>',
            [
                ['question' => 'Za kaj ljudje Aloe First najpogosteje uporabljajo?', 'answer' => 'Najpogosteje za občutek svežine, nego kože, po soncu ali kot lahkotno podporo lasišču in lasem.'],
                ['question' => 'Ali je idealen za vse situacije?', 'answer' => 'Ne. Dobro deluje v številnih manjših rutinah, ne pa kot popolna zamenjava za ciljno nego.'],
                ['question' => 'Kaj je prednost sprej oblike?', 'answer' => 'Hitrost, praktičnost in lažji ponovni nanos v običajnem dnevu.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Pričakovati, da bo en sprej v celoti nadomestil več različnih tipov izdelkov.'],
            ],
            'Aloe First sprej: kje res pomaga koži in lasem in kje ima svoje meje',
            'Poglejte, kje Aloe First sprej najbolje deluje za kožo in lase ter kako ga uporabljati brez pretiravanja.',
            'Aloe First',
            [
                ['heading' => 'Zakaj praktični format pogosto zmaga v vsakdanji uporabi', 'html' => '<p>Izdelki, po katerih je enostavno poseči, so pogosto tudi tisti, ki jih dejansko uporabljamo. Sprej oblika ima zato pravo vrednost prav v tem, da zmanjša napor in se naravno vključi v več situacij.</p>'],
                ['heading' => 'Zakaj večnamenskost še vedno potrebuje meje', 'html' => '<p>Vsestranski izdelki so lahko zelo uporabni, vendar niso brezmejna rešitev. Boljši rezultati običajno pridejo takrat, ko jasno vemo, kdaj praktičnost zadošča in kdaj še vedno potrebujemo bolj ciljno nego.</p>'],
            ]
        ),
        $entry(
            544,
            'Forever Lycium Plus: goji jagode, antioksidanti in bolj realen pogled na vitalnost',
            'forever-lycium-plus-goji-jagode-antioksidanti-in-bolj-realen-pogled-na-vitalnost',
            'Izdelki z antioksidativno zgodbo zvenijo najbolj privlačno takrat, ko se energija, imunost in anti-age obljube zlijejo v eno samo sporočilo. Tukaj je, kako Forever Lycium Plus oceniti bolj realno skozi prehrano, okrevanje in vsakodnevno vitalnost brez pretirane marketinške slike.',
            '<ul><li>Antioksidativna podpora ima največ smisla kot del širšega prehranskega in življenjskega vzorca, ne kot samostojna rešitev za nizko energijo.</li><li>Največja napaka je pričakovati, da bo en izdelek popravil slab spanec, šibko prehrano in kronični stres.</li><li>Pametnejši pristop vidi Lycium Plus kot dodaten sloj podpore, ko so osnove že postavljene precej bolje.</li></ul>',
            [
                ['question' => 'Za kaj se Forever Lycium Plus najpogosteje uporablja?', 'answer' => 'Najpogosteje ga ljudje povezujejo z antioksidativno podporo, goji jagodami in občutkom vsakodnevne vitalnosti.'],
                ['question' => 'Ali lahko sam dvigne energijo?', 'answer' => 'Ne zanesljivo. Spanec, kakovost hrane, okrevanje in dnevni ritem še vedno vplivajo veliko močneje.'],
                ['question' => 'Kdaj ima največ smisla?', 'answer' => 'Ko želite dopolniti že kar dobro urejeno rutino in ne pričakujete, da bo en izdelek naredil vse namesto vas.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kupiti izdelek zaradi zgodbe o antioksidantih brez jasnega cilja in brez izboljšanja širšega življenjskega vzorca.'],
            ],
            'Forever Lycium Plus: kje imajo goji jagode in antioksidanti res smisel za vitalnost',
            'Spoznajte, kdaj ima Forever Lycium Plus smisel kot antioksidativna podpora in kako ga vključiti bolj realno.',
            'Forever Lycium Plus',
            [
                ['heading' => 'Zakaj antioksidativni jezik hitro zveni večji od resničnosti', 'html' => '<p>Izdelki, zgrajeni okoli antioksidantov, pogosto zvenijo tako, kot da podpirajo skoraj vse hkrati. V praksi je njihovo pravo mesto veliko lažje razumeti, ko jih postavimo v širšo sliko prehrane, stresa in okrevanja.</p>'],
                ['heading' => 'Zakaj vitalnost še vedno stoji na osnovah', 'html' => '<p>Tudi najbolj privlačen podporni izdelek ne more nadomestiti spanja, kakovostne hrane in dosledne rutine. Zato je najpametnejša uporaba skoraj vedno takrat, ko so temelji že solidno urejeni.</p>'],
            ]
        ),
    ],
];
