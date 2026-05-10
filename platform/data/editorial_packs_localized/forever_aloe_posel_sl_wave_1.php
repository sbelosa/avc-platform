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
    'key' => 'forever-aloe-posel-sl-wave-1',
    'name' => 'Forever, aloe in poslovni sistem (SL) - prvi val',
    'notes' => 'Ročno lokaliziran premium val za Forever izdelke, aloe nego, gojenje aloe in poslovni del priporočilnega sistema.',
    'entries' => [
        $entry(
            539,
            'R3 Factor: kdaj bogatejša nega res pomaga koži in kdaj le zveni luksuzno',
            'r3-factor-kdaj-bogatejsa-nega-res-pomaga-kozi',
            'R3 Factor ima smisel le, ko ga gledamo skozi udobje kože, podporo kožni barieri in dosledno rutino, ne pa skozi nejasno obljubo mladostnega videza. Tukaj je, kje lahko bogatejša formula res pomaga in kje pričakovanja običajno prehitro zrastejo.',
            '<ul><li>Bogatejša anti-age nega ima več smisla, ko koža potrebuje več udobja, zaščite bariere in hranljivosti.</li><li>Največja napaka je pričakovati, da bo en izdelek nadomestil zaščito pred soncem, spanec in celotno rutino.</li><li>Pametnejši pristop gleda tip kože, doslednost in dejansko ujemanje formule z vsakdanjo uporabo.</li></ul>',
            [
                ['question' => 'Komu R3 Factor običajno najbolj ustreza?', 'answer' => 'Najpogosteje bolj suhi, zrelejši ali bolj izčrpani koži, ki potrebuje bogatejši profil nege.'],
                ['question' => 'Ali lahko reši vse znake staranja?', 'answer' => 'Ne. Najbolje deluje kot del širše rutine nege kože.'],
                ['question' => 'Kdaj ima največ smisla?', 'answer' => 'Ko koža potrebuje več udobja in je uporabnik pripravljen na dosledno uporabo.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kupiti izdelek z luksuznim občutkom brez preverjanja, ali res ustreza koži.'],
            ],
            'R3 Factor: kdaj ima bogatejša formula res smisel za kožo',
            'Spoznajte, komu lahko R3 Factor najbolj koristi in kako ga uporabljati z bolj realnimi pričakovanji.',
            'R3 Factor',
            [
                ['heading' => 'Zakaj bogatejša nega potrebuje boljše ujemanje', 'html' => '<p>Vsaka koža ne potrebuje težje in bogatejše teksture. Izdelki, kot je R3 Factor, imajo največ smisla takrat, ko je potreba po udobju, mehkobi in podpori bariere res prisotna.</p>'],
                ['heading' => 'Zakaj je rutina še vedno pomembnejša od enega izdelka', 'html' => '<p>Tudi dobro izbran izdelek ne more nadomestiti zaščite pred soncem, spanja in doslednosti. Najboljši učinek običajno pride, ko bogatejša nega podpira rutino, ki je že smiselno zastavljena.</p>'],
            ]
        ),
        $entry(
            543,
            'Forever Marine Collagen: realna pričakovanja za kožo, sklepe in vsakodnevno uporabo',
            'forever-marine-collagen-realna-pricakovanja-za-kozo-in-sklepe',
            'Kolagenski izdelki zvenijo najbolj privlačno takrat, ko obljubijo preveč hkrati. Tukaj je, kako Forever Marine Collagen oceniti skozi doslednost, prehrano in realne cilje namesto skozi čudežno zgodbo.',
            '<ul><li>Kolagen ima največ smisla kot del širšega načrta za kožo, prehrano in okrevanje.</li><li>Največja napaka je pričakovati dramatične spremembe brez potrpežljivosti in širše podpore telesu.</li><li>Pametnejši pristop gleda kontinuiteto, realne cilje in vsakdanji kontekst.</li></ul>',
            [
                ['question' => 'Zakaj ljudje uporabljajo kolagen?', 'answer' => 'Najpogosteje zaradi zanimanja za kožo, sklepe in širši občutek vitalnosti.'],
                ['question' => 'Ali lahko Marine Collagen sam reši težave s kožo?', 'answer' => 'Ne. Največ smisla ima ob boljši prehrani in dosledni rutini.'],
                ['question' => 'Kdaj ima več smisla?', 'answer' => 'Ko so pričakovanja realna in ko uporabnik da izdelku dovolj časa.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Presojati izdelek prehitro ali od njega pričakovati vse naenkrat.'],
            ],
            'Marine Collagen: kaj lahko za kožo in sklepe realno pričakujete',
            'Preverite, kdaj ima Forever Marine Collagen smisel in kako ga uporabljati z bolj realnimi pričakovanji.',
            'Marine Collagen',
            [
                ['heading' => 'Zakaj kolagen deluje najbolje znotraj širše rutine', 'html' => '<p>Mnogi si želimo, da bi kolagen nosil celoten rezultat, vendar dodatki delujejo jasneje, ko so spanec, prehrana in nega že bolje urejeni. Prav ta širši okvir pogosto odloči, kako uporaben se izdelek v resnici zdi.</p>'],
                ['heading' => 'Zakaj je potrpežljivost pomembnejša od hypea', 'html' => '<p>Kolagen ni hitra kozmetična prevara. Bolj ko so pričakovanja mirna in realna, lažje je presoditi, ali izdelek res zasluži mesto v rutini.</p>'],
            ]
        ),
        $entry(
            552,
            'Mrežni marketing in spletno poslovanje: kako graditi Forever sistem brez iluzij o lahkem zaslužku',
            'mrezni-marketing-in-spletno-poslovanje-kako-graditi-forever-sistem',
            'Forever posel ni bližnjica do pasivnega dohodka, temveč sistem, ki potrebuje zaupanje, uporaben vsebinski tok in ponovljive dnevne aktivnosti. Tukaj je, kako mrežni marketing gledati bolj realno in graditi nekaj bolj stabilnega skozi čas.',
            '<ul><li>Spletni mrežni marketing je najbolj odvisen od zaupanja, doslednosti in dovolj preprostega sistema, da ga je mogoče ponavljati.</li><li>Največja napaka je pričakovati resen prihodek brez vsebine, odnosov in dnevne aktivnosti.</li><li>Pametnejši pristop občinstvo, priporočila in podporo gradi korak za korakom.</li></ul>',
            [
                ['question' => 'Ali lahko Forever posel postane resen spletni sistem?', 'answer' => 'Lahko, vendar le ob jasnem toku vsebine, priporočil in vsakodnevne izvedbe.'],
                ['question' => 'Kaj je največja iluzija?', 'answer' => 'Da pasivni dohodek nastane brez aktivne gradnje zaupanja in doslednega dela.'],
                ['question' => 'Na čem naj sistem stoji?', 'answer' => 'Na vsebini, odnosih, koristni podpori in preprostem dnevnem procesu.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Iskati bližnjice namesto graditi ponovljiv proces, ki mu ljudje zaupajo.'],
            ],
            'Forever mrežni marketing: kako graditi resen sistem namesto lovljenja hypea',
            'Spoznajte, kako Forever spletno poslovanje graditi skozi zaupanje, vsebino in ponovljive dnevne aktivnosti.',
            'Forever poslovni sistem',
            [
                ['heading' => 'Zakaj je zaupanje prava poslovna valuta', 'html' => '<p>Ljudje redko ostanejo samo zaradi hypea. V priporočilnih poslih zaupanje raste takrat, ko so vsebina, nasvet in dnevna prisotnost dovolj stabilni, da jih je mogoče jemati resno.</p>'],
                ['heading' => 'Zakaj vsakodnevna preprostost premaga občasno intenzivnost', 'html' => '<p>Najmočnejše poslovne rutine običajno niso dramatične. So dovolj preproste, da jih lahko ponavljamo tudi takrat, ko energija, čas in motivacija niso idealni.</p>'],
            ]
        ),
        $entry(
            568,
            'Forever Living za otroke: varnost, previdnost in bolj premišljena izbira izdelkov',
            'forever-living-za-otroke-varnost-in-smiselna-izbira',
            'Ko gre za otroke, se koristna podpora vedno začne s previdnostjo. Tukaj je, kako Forever izdelke za otroke gledati skozi varnost, starost, rutino in resnične potrebe družine, ne pa skozi pretirana pričakovanja.',
            '<ul><li>Izdelke za otroke je treba presojati bolj previdno in bolj zadržano kot izdelke za odrasle.</li><li>Največja napaka je uvesti izdelek samo zato, ker zveni naravno ali priljubljeno.</li><li>Pametnejši pristop začne pri varnosti, preprostosti in resničnem kontekstu otroka.</li></ul>',
            [
                ['question' => 'Ali so vsi Forever izdelki primerni za otroke?', 'answer' => 'Ne. Starost, namen in celoten kontekst so vedno pomembni.'],
                ['question' => 'Kaj naj starši gledajo najprej?', 'answer' => 'Varnost, preprostost in to, ali otrok takšno podporo res potrebuje.'],
                ['question' => 'Kdaj ima dodatna podpora več smisla?', 'answer' => 'Ko je premišljena, zmerna in smiselno vključena v širšo družinsko skrb.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Prenesti logiko dodatkov za odrasle neposredno na otroške potrebe.'],
            ],
            'Forever izdelki za otroke: kako izbirati z več varnosti in realizma',
            'Razumite, kako Forever izdelke za otroke presojati skozi varnost, previdnost in resnične potrebe družine.',
            'Forever za otroke',
            [
                ['heading' => 'Zakaj otroška podpora potrebuje bolj miren pogled', 'html' => '<p>Starši si naravno želimo pomagati, kar lahko hitro ustvari pritisk, da naredimo preveč. Boljša pot je običajno počasnejša, preprostejša in bolj usmerjena v to, kar je res potrebno.</p>'],
                ['heading' => 'Zakaj mora varnost ostati pred navdušenjem', 'html' => '<p>Naraven jezik še ne pomeni samodejno otrokom prijazne uporabe. Najodgovornejše odločitve običajno izhajajo iz vprašanja, ali je izdelek sploh potreben.</p>'],
            ]
        ),
        $entry(
            571,
            'Gojenje aloe vere doma ali na vrtu: ključni pogoji za dolgoročen uspeh',
            'gojenje-aloe-vere-doma-ali-na-vrtu-kljucni-pogoji-za-uspeh',
            'Aloe vera je hvaležna rastlina samo takrat, ko ji ne poskušamo preveč pomagati. Tukaj je, kako domače ali vrtno gojenje postaviti bolj pametno, od svetlobe in drenaže do začetniških napak, ki rastlino najpogosteje oslabijo.',
            '<ul><li>Aloe vera običajno najbolje uspeva ob močni svetlobi, dobri drenaži in zmernem zalivanju.</li><li>Največja napaka je pretirano zalivanje in obremenjevanje korenin iz dobre namere.</li><li>Pametnejši pristop ustvari stabilne pogoje in rastlini pusti, da sama naredi več dela.</li></ul>',
            [
                ['question' => 'Kaj aloe vera najbolj potrebuje?', 'answer' => 'Veliko svetlobe, zračen substrat in razumno zalivanje.'],
                ['question' => 'Zakaj aloe doma pogosto propade?', 'answer' => 'Najpogosteje zaradi preveč vode, slabe drenaže ali premalo svetlobe.'],
                ['question' => 'Ali lahko raste tudi zunaj?', 'answer' => 'Da, če so podnebje in pogoji vlage dovolj primerni.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Obravnavati aloe kot rastlino, ki želi pogosto zalivanje in stalno poseganje.'],
            ],
            'Gojenje aloe vere: kako ji dati prave pogoje brez pretiravanja',
            'Spoznajte, kako aloe vero gojiti doma ali zunaj z več svetlobe, boljšo drenažo in manj začetniških napak.',
            'Gojenje aloe vere',
            [
                ['heading' => 'Zakaj aloe običajno želi manj poseganja', 'html' => '<p>Mnoge začetniške težave nastanejo prav zato, ker poskušamo narediti preveč. Aloe je pogosto najbolj zdrava takrat, ko so pogoji pravi in se lastnik upre želji po pretiranem zalivanju.</p>'],
                ['heading' => 'Zakaj so pogoji pomembnejši od poznega reševanja', 'html' => '<p>Zdrava aloe je običajno rezultat preventive, ne neprestanega popravljanja. Dobra svetloba in drenaža pogosto pomenita več kot kateri koli pozni poskus reševanja oslabljene rastline.</p>'],
            ]
        ),
        $entry(
            602,
            'Škodljivci in bolezni aloe: kako rastlino zaščititi, preden se težava razraste',
            'aloe-skodljivci-in-bolezni-kako-zascititi-rastlino',
            'Pri aloe veri največja grožnja pogosto ni eksotična bolezen, temveč spregledani zgodnji znaki stresa. Tukaj je, kako spremljati škodljivce, gnilobo in poškodbe rastline, še preden se celotna rast oslabi.',
            '<ul><li>Večino težav z aloe vero je veliko lažje reševati, ko jih opazimo zgodaj in brez panike.</li><li>Največja napaka je ignorirati mehke liste, znake gnilobe ali drobne škodljivce, dokler škoda ne postane večja.</li><li>Pametnejši pristop redno preverja rastlino, substrat in pogoje rasti.</li></ul>',
            [
                ['question' => 'Katere težave so pri aloe pogoste?', 'answer' => 'Preveč vlage, gniloba, mokaste uši in širši znaki stresa rastline.'],
                ['question' => 'Kaj je treba najprej preveriti?', 'answer' => 'Zalivanje, drenažo, svetlobo ter stanje listov in korenin.'],
                ['question' => 'Ali se lahko rastlina opomore?', 'answer' => 'Pogosto da, zlasti če težavo opazimo dovolj zgodaj.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Zdraviti posledico, ne da bi odpravili glavni vzrok v načinu gojenja.'],
            ],
            'Bolezni in škodljivci aloe: preventiva pomeni več kot pozno reševanje',
            'Preverite, kako težave z aloe vero prepoznati prej in rastlino zaščititi z boljšimi pogoji gojenja.',
            'Bolezni aloe',
            [
                ['heading' => 'Zakaj zgodnje opazovanje spremeni vse', 'html' => '<p>Rastline redko propadejo čez noč. Večina resnih težav z aloe se začne z drobnimi vidnimi znaki, zato je redno opazovanje pogosto najmočnejša oblika preventive.</p>'],
                ['heading' => 'Zakaj je način gojenja pogosto glavni vzrok', 'html' => '<p>Škodljivci in bolezenski znaki se običajno poslabšajo, ko je rastlina že oslabljena zaradi okolja. Popraviti pogoje je zato pogosto vrednejše kot zdraviti le vidni simptom.</p>'],
            ]
        ),
        $entry(
            825,
            'Forever Vitolize za moške in ženske: kdaj ima takšna podpora sploh smisel',
            'forever-vitolize-za-moske-in-zenske-kako-ga-smiselno-uporabljati',
            'Izdelki Vitolize zvenijo najbolj privlačno takrat, ko marketing združi hormone, energijo in vitalnost v eno samo obljubo. Tukaj je, kako jih oceniti bolj realno skozi jasen cilj in boljši razlog za uporabo.',
            '<ul><li>Dodatki za vitalnost imajo največ smisla, ko obstaja jasen razlog in realno pričakovanje.</li><li>Največja napaka je pričakovati, da bo en izdelek rešil utrujenost, stres in slabe navade hkrati.</li><li>Pametnejši pristop Vitolize vidi kot podporo, ne kot celoten odgovor.</li></ul>',
            [
                ['question' => 'Zakaj ljudje posegajo po Vitolize izdelkih?', 'answer' => 'Najpogosteje zaradi zanimanja za vitalnost, energijo in širšo hormonsko podporo.'],
                ['question' => 'Ali lahko sami rešijo težavo?', 'answer' => 'Ne. Spanec, okrevanje in stres še vedno odločajo o širšem izidu.'],
                ['question' => 'Kdaj imajo več smisla?', 'answer' => 'Ko obstaja jasen cilj in izdelek ni uporabljen kot bližnjica za vse.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Kupiti dodatek, še preden so pregledane dnevne osnove, ki pomenijo več.'],
            ],
            'Forever Vitolize: kje ima smisel za vitalnost in kje pričakovanja gredo predaleč',
            'Spoznajte, kdaj ima Forever Vitolize za moške in ženske smisel in kako ga gledati bolj realistično.',
            'Forever Vitolize',
            [
                ['heading' => 'Zakaj je jasnost pomembnejša od velikih obljub', 'html' => '<p>Dodatki za vitalnost hitro zvenijo univerzalno. Veliko bolj uporabno vprašanje je, kakšen točno cilj ima posameznik in ali izdelek temu cilju pošteno ustreza.</p>'],
                ['heading' => 'Zakaj osnove še vedno odločajo o rezultatu', 'html' => '<p>Noben izdelek za vitalnost ne more prevzeti vloge spanja, okrevanja in bolj mirne rutine. Najbolj uporaben položaj dodatka je skoraj vedno znotraj širšega načrta, ki že ima smisel.</p>'],
            ]
        ),
        $entry(
            835,
            'DMO rutina za uspeh: dnevni sistem, ki deluje le, če ga lahko res živite',
            'dmo-rutina-za-uspeh-dnevni-sistem-ki-prinasa-rezultate',
            'DMO rutina nima velike vrednosti, če postane popoln seznam nalog, ki ga v resnici nihče ne zmore živeti dolgo. Tukaj je, kako zgraditi dnevni sistem, ki ustvarja rezultate namesto občutka krivde in zaostajanja.',
            '<ul><li>Dober DMO mora biti dovolj preprost, da preživi tudi nepopolne dneve.</li><li>Največja napaka je prenapolniti načrt s preveč opravili namesto z nekaj aktivnostmi z največjim učinkom.</li><li>Pametnejši pristop dnevni ritem gradi okoli vsebine, stikov in dela, ki res premika posel naprej.</li></ul>',
            [
                ['question' => 'Kaj je DMO rutina?', 'answer' => 'To je osredotočen nabor dnevnih aktivnosti, ki dosledno premikajo posel naprej.'],
                ['question' => 'Zakaj z njo mnogi ne uspejo?', 'answer' => 'Ker ustvarijo sistem, ki deluje resno, a je pretežak za ponavljanje.'],
                ['question' => 'Kako izgleda dober DMO?', 'answer' => 'Preprosto, jasno in dovolj realno, da ga lahko ponavljate tudi povprečen dan.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Zamenjati zaposlenost s peščico aktivnosti, ki res ustvarjajo zagon.'],
            ],
            'DMO rutina: kako zgraditi dnevni sistem, ki ga lahko dejansko vzdržujete',
            'Odkrijte, kako DMO rutino pretvoriti v preprost dnevni sistem za bolj enakomeren napredek in fokus.',
            'DMO rutina',
            [
                ['heading' => 'Zakaj dnevni sistemi propadejo, ko so preveč ambiciozni', 'html' => '<p>Mnogi si rutine zgradimo za najboljši dan, ne pa za resničen teden. Uporaben DMO običajno uspe zato, ker je dovolj lahek, da preživi tudi povprečno energijo in običajne motnje.</p>'],
                ['heading' => 'Zakaj nekaj ključnih dejanj običajno zmaga', 'html' => '<p>Zagon v poslu pogosto prihaja iz majhnega števila ponovljenih aktivnosti, ne iz neskončnih seznamov. Bolj ko je sistem preprost, lažje mu zaupamo in ga držimo.</p>'],
            ]
        ),
        $entry(
            840,
            'Forever Vitamin C in bakuchiol: kdaj ima ta set smisel za gladko in sijočo kožo',
            'forever-vitamin-c-in-bakuchiol-za-gladko-in-sijoco-kozo',
            'Vitamin C in bakuchiol sta privlačna, ker obljubljata sijaj, lepšo teksturo in nežnejši anti-age občutek. Tukaj je, kako ta Forever set oceniti skozi tip kože, občutljivost in pričakovanja od rutine.',
            '<ul><li>Kombinacija vitamina C in bakuchiola je lahko smiselna za sijaj, teksturo in nežnejši anti-age pristop.</li><li>Največja napaka je uporabljati aktivne izdelke brez spremljanja tolerance kože in širše rutine.</li><li>Pametnejši pristop set uvaja postopoma in spremlja kožo skozi nekaj tednov.</li></ul>',
            [
                ['question' => 'Komu je ta set najbolj zanimiv?', 'answer' => 'Najpogosteje ljudem, ki želijo več sijaja, bolj enakomeren ton in mehkejši anti-age pristop.'],
                ['question' => 'Ali je primeren za vsako kožo?', 'answer' => 'Ne samodejno. Občutljiva koža pogosto potrebuje počasnejše uvajanje.'],
                ['question' => 'Kdaj ima največ smisla?', 'answer' => 'Ko že obstaja jasna rutina in realna pričakovanja o postopnem napredku.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Dodati preveč aktivnih izdelkov hkrati in nato ne vedeti, kaj koži ustreza.'],
            ],
            'Vitamin C in bakuchiol: kdaj ima Forever set res smisel za kožo',
            'Spoznajte, komu Forever Vitamin C in bakuchiol set lahko najbolj koristi in kako ga uvajati bolj previdno.',
            'Vitamin C in bakuchiol',
            [
                ['heading' => 'Zakaj aktivna nega kože potrebuje mirnejše uvajanje', 'html' => '<p>Izdelki za sijaj kože so lahko zelo privlačni, vendar koža običajno najbolje reagira, ko jih uvajamo potrpežljivo. Počasnejša uporaba pogosto zaščiti udobje in pomaga jasneje videti, kaj res deluje.</p>'],
                ['heading' => 'Zakaj tip kože še vedno oblikuje rezultat', 'html' => '<p>Obetavna formula ni samodejno univerzalna. Boljše ujemanje med izdelkom in profilom kože običajno pomeni več koristi in manj draženja.</p>'],
            ]
        ),
        $entry(
            863,
            'Antibakterijsko in protiglivično delovanje aloe: kje se dejstva končajo in marketing začne',
            'antibakterijsko-in-protiglivicno-delovanje-aloe',
            'Aloe vera je zanimiva prav zato, ker stoji med tradicionalno uporabo in sodobnim zanimanjem za njene lastnosti. Tukaj je, kako o njenem antibakterijskem in protiglivičnem delovanju govoriti brez nerealnih obljub.',
            '<ul><li>Aloe vera ima zanimive lastnosti, vendar znanstveno zanimanje ne bi smelo postati univerzalna marketinška trditev.</li><li>Največja napaka je zamenjati podporno nego kože z idejo, da aloe nadomesti vse drugo.</li><li>Pametnejši pristop razlikuje pomirjujočo nego, praktično podporo koži in realne meje izdelka.</li></ul>',
            [
                ['question' => 'Zakaj se aloe pogosto povezuje z antibakterijskim delovanjem?', 'answer' => 'Ker nekatere spojine vzbujajo zanimanje v raziskovalnem in praktičnem kontekstu nege kože.'],
                ['question' => 'Ali to pomeni, da aloe reši vsako težavo kože?', 'answer' => 'Ne. Lahko je koristna v negi, ni pa celotna rešitev za vse.'],
                ['question' => 'Kje ima največ smisla?', 'answer' => 'V pomirjujoči negi kože in praktičnih rutinah, kjer šteje udobje in podpora.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Napihniti znanstveno zanimivost v trditve, ki gredo precej dlje od resničnosti.'],
            ],
            'Aloe in zaščitna nega kože: kaj je vredno vedeti brez pretiravanja',
            'Razumite, kje ima aloe vera smisel v negi kože in kako bolj realistično brati trditve o njenih lastnostih.',
            'Aloe in podpora koži',
            [
                ['heading' => 'Zakaj aloe privlači večje trditve, kot jih lahko nosi', 'html' => '<p>Ljudje nas naravno privlačijo sestavine, ki zvenijo hkrati naravno in znanstveno zanimivo. Prav ta kombinacija pogosto vodi v pretiravanje, zato aloe zasluži bolj prizemljeno in uporabno razlago.</p>'],
                ['heading' => 'Zakaj koristna nega še vedno potrebuje meje', 'html' => '<p>Aloe si lahko povsem zasluži mesto v negi kože, vendar njena vrednost postane jasnejša, ko od nje ne pričakujemo dela vseh drugih kategorij izdelkov. Praktična nega običajno premaga napihnjene obljube.</p>'],
            ]
        ),
    ],
];
