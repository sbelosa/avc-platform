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
    'key' => 'legacy-mindset-aloe-lifestyle-sl-wave-1',
    'name' => 'Legacy mindset, aloe in lifestyle (SL) - prvi val',
    'notes' => 'Ročno lokaliziran premium val za starejše mindset URL-je, aloe lifestyle vsebine in evergreen wellness članke.',
    'entries' => [
        $entry(
            92,
            'Prepričanja: kako sprememba razmišljanja preoblikuje odločitve, navade in smer življenja',
            'prepricanja-spremenite-svoje-misljenje-in-spremenili-boste-svoje-zivljenje',
            'Prepričanja vplivajo na to, kako razumemo trud, omejitve in priložnosti. Ta vodnik pokaže, kako prepoznati omejujoče miselne vzorce in jih zamenjati z bolj koristnimi odločitvami.',
            '<ul><li>Prepričanja vplivajo na samozavest, odločitve in na to, kaj si sploh dovolimo poskusiti.</li><li>Največja napaka je pričakovati spremembo prepričanj brez resničnih dejanj in novih navad.</li><li>Pametnejši pristop preverja stare domneve in jih podpira z novim vedenjem.</li></ul>',
            $faq(
                'Kaj so prepričanja v praksi?',
                'To so globoki miselni vzorci, ki vplivajo na to, kako razumemo sebe, druge in svet okoli sebe.',
                'Zakaj so prepričanja tako pomembna?',
                'Ker vplivajo na samozavest, odločitve in meje, ki si jih postavimo.',
                'Ali se prepričanja res lahko spremenijo?',
                'Da, vendar sprememba običajno zahteva zavedanje, ponavljanje in nova dejanja.',
                'Katera je pogosta napaka?',
                'Poskušati spremeniti rezultate v življenju brez spremembe prepričanj, ki vodijo ista ravnanja.'
            ),
            'Prepričanja: kako spremeniti miselne vzorce in napredovati',
            'Spoznajte, kako prepričanja vplivajo na navade in odločitve ter kako jih zamenjati z bolj podpornimi vzorci.',
            'Prepričanja',
            $sections(
                'Zakaj prepričanja oblikujejo več kot le počutje',
                'Prepričanja tiho določajo, kaj ljudje poskusijo, kako razlagajo neuspeh in koliko možnosti sploh vidijo pred seboj.',
                'Zakaj nova miselnost še vedno potrebuje dejanja',
                'Prava sprememba se zgodi takrat, ko drugačno razmišljanje podprejo nove odločitve, boljše navade in konkretna dejanja.'
            )
        ),
        $entry(
            94,
            'Zavrnitev: zakaj boli, kaj sproži in kako jo spremeniti v osebno rast',
            'zavrnitev-zakaj-do-nje-pride-in-kako-jo-premagati',
            'Zavrnitev boli ne le zaradi dogodka, temveč tudi zaradi pomena, ki mu ga pripišemo. Tukaj je, kako jo razumeti mirneje in jo uporabiti kot povratno informacijo namesto kot trajno rano ega.',
            '<ul><li>Zavrnitev pogosto zadene občutek pripadnosti, vrednosti in identitete bolj kot sam dogodek.</li><li>Največja napaka je iz ene zavrnitve narediti dokaz trajne nevrednosti.</li><li>Pametnejši pristop loči čustvo od identitete in zavrnitev pretvori v perspektivo in učenje.</li></ul>',
            $faq(
                'Zakaj zavrnitev tako boli?',
                'Ker pogosto zadene potrebo po pripadnosti, sprejetosti in osebni vrednosti.',
                'Ali zavrnitev pomeni, da je z nami nekaj narobe?',
                'Ne nujno. Pogosto govori bolj o času, ujemanju in kontekstu kot o naši vrednosti.',
                'Kako se z zavrnitvijo soočiti bolj zdravo?',
                'Tako, da dovolimo čustvo, vendar iz njega ne naredimo trajne samopodobe.',
                'Katera je pogosta napaka?',
                'Iz enega ne ustvariti celo zgodbo o osebnem neuspehu.'
            ),
            'Zavrnitev: kako jo razumeti bolje in jo uporabiti za rast',
            'Razumite, zakaj zavrnitev tako boli in kako se z njo soočiti brez izgube samozavesti in smeri.',
            'Zavrnitev',
            $sections(
                'Zakaj je zavrnitev večja od samega trenutka',
                'Ljudje ne reagirajo le na dogodek, ampak tudi na to, kaj mislijo, da ta dogodek pove o njihovi vrednosti.',
                'Zakaj perspektiva pospeši okrevanje',
                'Zavrnitev postane manj uničujoča, ko jo razumemo kot informacijo, ujemanje ali časovno neustreznost, ne pa kot osebno sodbo.'
            )
        ),
        $entry(
            95,
            'Osebnost ali značaj: kaj se res spremeni, ko želiš postati močnejša oseba',
            'osebnost-ali-znacaj-odkrijte-kaj-prinasa-resnicno-spremembo',
            'Osebnost oblikuje slog, značaj pa vedenje pod pritiskom, odgovornostjo in v tihih trenutkih. Ta članek pokaže, zakaj prava osebna rast temelji predvsem na značaju.',
            '<ul><li>Osebnost je pomembna, vendar značaj nosi integriteto, disciplino in dolgoročno zaupanje.</li><li>Največja napaka je delati samo na vtisu in zanemariti odgovornost in doslednost.</li><li>Pametnejši pristop gradi značaj skozi ponavljajoče se dobre odločitve.</li></ul>',
            $faq(
                'Kakšna je razlika med osebnostjo in značajem?',
                'Osebnost kaže, kako se izražamo, značaj pa, kako ravnamo, ko so preizkušene vrednote in odgovornost.',
                'Zakaj je značaj pomembnejši za spremembo?',
                'Ker vpliva na doslednost, zanesljivost in na to, kakšna oseba ostanemo v težjih okoliščinah.',
                'Ali se značaj lahko razvija?',
                'Da, skozi poštenost, disciplino, odgovornost in ponavljanje boljših odločitev.',
                'Katera je pogosta napaka?',
                'Poskušati delovati prepričljivo, namesto da bi postali bolj zanesljivi in zreli.'
            ),
            'Osebnost ali značaj: kaj prinaša trajno osebno spremembo',
            'Odkrijte, zakaj je značaj pomembnejši od vtisa in kako se prava rast gradi skozi navade in odgovornost.',
            'Osebnost ali značaj',
            $sections(
                'Zakaj ima vtis svoje meje',
                'Močan vtis lahko odpre vrata, vendar dolgoročno zaupanje skoraj vedno temelji na tem, kako se nekdo vede v resničnem življenju.',
                'Zakaj značaj raste skozi ponavljanje',
                'Majhne poštene odločitve, ponavljane skozi čas, oblikujejo človeka, ki mu lahko drugi zaupajo.'
            )
        ),
        $entry(
            96,
            'Zemljevid želja in Feng Shui: kako uporabljati vizualna orodja brez nadomeščanja dejanj',
            'zemljevid-zelja-feng-shui-nasveti-za-uresnicitev-sanj',
            'Zemljevid želja je lahko uporaben, kadar pomaga usmeriti pozornost, ne pa kadar nadomesti načrtovanje in delo. Tukaj je, kako takšna orodja uporabljati bolj prizemljeno.',
            '<ul><li>Zemljevid želja najbolje deluje kot vizualni opomnik, ne kot bližnjica mimo truda in discipline.</li><li>Največja napaka je zamenjati konkretne korake s simboliko in navdušenjem.</li><li>Pametnejši pristop poveže domišljijo, prioritete in vsakodnevno delovanje.</li></ul>',
            $faq(
                'Čemu v resnici služi zemljevid želja?',
                'Pomaga ohranjati cilje vidne, jasne in čustveno bolj konkretne.',
                'Ali lahko sam spremeni življenje?',
                'Ne. Vreden je toliko, kolikor vodi v resnične odločitve in dejanja.',
                'Zakaj so takšna orodja privlačna?',
                'Ker združujejo domišljijo, čustva in bolj jasno sliko želenega življenja.',
                'Katera je pogosta napaka?',
                'Ostati pri vizualizaciji in nikoli ne preiti v načrt.'
            ),
            'Zemljevid želja in Feng Shui: kako ju uporabiti kot orodji fokusa',
            'Spoznajte, kako uporabljati zemljevid želja in podobna orodja za več jasnosti, ne za prazno fantaziranje.',
            'Zemljevid želja',
            $sections(
                'Zakaj je vizualni fokus lahko koristen',
                'Ljudje se pogosto lažje premikajo proti cilju, ko je ta viden, jasen in čustveno smiseln.',
                'Zakaj simboli še vedno potrebujejo strukturo',
                'Ritual ali tabla postaneta koristna šele, ko vodita v koledar, prioritete in dejansko delo.'
            )
        ),
        $entry(
            97,
            'Dober odnos se začne pri sebi: meje, spoštovanje in čustvena stabilnost',
            'dober-odnos-gradite-zdrave-odnose-zacensi-pri-sebi',
            'Odnosi se izboljšajo, ko človek najprej razvije bolj zdrav odnos do sebe. Ta vodnik pokaže, zakaj samospoštovanje, meje in čustvena jasnost vplivajo na vse pomembne odnose.',
            '<ul><li>Odnos do samega sebe močno vpliva na meje, komunikacijo in čustveno varnost z drugimi.</li><li>Največja napaka je iskati mir in vrednost samo zunaj sebe.</li><li>Pametnejši pristop razvija notranjo stabilnost, jasnejše potrebe in bolj pošteno komunikacijo.</li></ul>',
            $faq(
                'Zakaj odnos do sebe vpliva na vse druge odnose?',
                'Ker določa meje, standarde in to, kako drugim dovolimo, da ravnajo z nami.',
                'Kaj pomeni zdrav odnos do sebe?',
                'Pomeni spoj samospoštovanja, iskrenosti, čustvene jasnosti in odgovornosti za lastne potrebe.',
                'Ali se tega lahko naučimo?',
                'Da, skozi samorefleksijo, meje in doslednejše samospoštovanje.',
                'Katera je pogosta napaka?',
                'Vso potrditev in mir iskati le v drugih ljudeh.'
            ),
            'Dober odnos do sebe: pravi temelj boljših odnosov',
            'Odkrijte, kako samospoštovanje in notranja stabilnost izboljšujeta komunikacijo, meje in bližino.',
            'Dober odnos',
            $sections(
                'Zakaj samospoštovanje spreminja odnose',
                'Ljudje z močnejšim samospoštovanjem navadno jasneje komunicirajo, manj prenašajo nezdravih vzorcev in bolje okrevajo po konfliktih.',
                'Zakaj čustvena jasnost podpira bližino',
                'Prava bližina je lažja, ko zna človek poimenovati potrebe, držati meje in ostati prizemljen brez stalnega potrjevanja.'
            )
        ),
        $entry(
            99,
            'Tveganje v življenju: kako ga sprejeti pametno brez nepremišljenosti',
            'tveganje-v-zivljenju-kako-ga-sprejeti-in-rasti',
            'Rast vedno vključuje negotovost, vendar tveganje ne pomeni kaosa ali impulzivnosti. Ta članek pojasni, kako ločiti zdravo tveganje od slabe presoje.',
            '<ul><li>Zdravo tveganje odpira rast, učenje in nove možnosti, ki bi jih strah sicer zaprl.</li><li>Največja napaka je ali bežati pred vsakim tveganjem ali ga romantizirati kot dokaz poguma.</li><li>Pametnejši pristop razlikuje premišljen izziv od impulzivnega skoka.</li></ul>',
            $faq(
                'Zakaj je tveganje pomembno za rast?',
                'Ker razvoj skoraj vedno zahteva izhod iz znanega in sprejemanje negotovosti.',
                'Ali sprejemanje tveganja pomeni neprevidnost?',
                'Ne. Zdravo tveganje še vedno vključuje razmislek, odgovornost in pripravljenost na posledice.',
                'Kako vedeti, ali je tveganje vredno?',
                'Pomaga oceniti, kaj lahko pridobite, kaj lahko izgubite in kaj ste pripravljeni nositi.',
                'Katera je pogosta napaka?',
                'Odločati se iz ega ali strahu pred zamujenim namesto iz pravega razloga.'
            ),
            'Tveganje v življenju: kako rasti brez nepremišljenih odločitev',
            'Naučite se gledati na tveganje bolj zrelo, z več presoje in manj paralizirajočega strahu.',
            'Tveganje v življenju',
            $sections(
                'Zakaj varnost ni vedno razvoj',
                'Prevelika navezanost na udobje lahko varuje občutek nadzora, hkrati pa tiho omejuje izkušnje in priložnosti.',
                'Zakaj pametno tveganje potrebuje meje',
                'Dobro tveganje navadno vključuje pripravo, samopoznavanje in dovolj stabilnosti za posledice.'
            )
        ),
        $entry(
            100,
            'MLM trženje s Forever Living: kako zgraditi delo od doma kot sistem',
            'mlm-trzenje-s-forever-living-delo-od-doma',
            'Posel od doma s Foreverjem najbolje deluje takrat, ko temelji na zaupanju, koristni vsebini in ponovljivih dnevnih dejavnostih. Tukaj je, kako do MLM dela pristopiti kot k sistemu.',
            '<ul><li>Rast MLM posla od doma je najbolj odvisna od zaupanja, podpore kupcem in ponovljivih dnevnih korakov.</li><li>Največja napaka je pričakovati resen rezultat brez vsebine, ritma in jasne poti za kupca.</li><li>Pametnejši pristop gradi občinstvo, odnos in priporočilo korak za korakom.</li></ul>',
            $faq(
                'Ali je mogoče Forever posel res graditi od doma?',
                'Da, vendar le, če obstaja sistem, dnevna aktivnost in resnična vrednost za ljudi.',
                'Kaj najbolj vpliva na uspeh?',
                'Zaupanje, doslednost, podpora kupcem in jasna komunikacija.',
                'Zakaj mnogi obstanejo?',
                'Ker iščejo hiter zaslužek brez gradnje občinstva in verodostojnosti.',
                'Katera je pogosta napaka?',
                'Začeti samo z navdušenjem in brez procesa, ki se lahko ponavlja.'
            ),
            'MLM trženje s Forever Living: kako zgraditi boljši posel od doma',
            'Odkrijte, kako graditi Forever Living posel od doma skozi zaupanje, vsebino in ponovljive vsakodnevne korake.',
            'MLM trženje',
            $sections(
                'Zakaj je vsebina pomembnejša od hypea',
                'Ljudje bolj zaupajo poslovnim modelom, ki jih učijo, vodijo in podpirajo, kot pa tistim, ki samo pritiskajo na priložnost.',
                'Zakaj sistemi prekašajo kratke valove motivacije',
                'Majhna vsakodnevna dejanja, ponavljana dosledno, navadno ustvarijo več kot občasno navdušenje brez nadaljevanja.'
            )
        ),
        $entry(
            109,
            'Stres na delovnem mestu: naravni načini sprostitve in boljši fokus brez bega pred vzroki',
            'stres-na-delovnem-mestu-naravne-metode-za-sprostitev-in-boljso-osredotocenost',
            'Stres pri delu se redko zmanjša samo z dihalnimi vajami. Običajno zahteva tudi boljše meje, okrevanje in navade fokusa. Tukaj je, kako združiti naravno podporo in boljšo strukturo dela.',
            '<ul><li>Naravna podpora pri stresu deluje najbolje, ko se hkrati izboljšata način dela in okrevanje.</li><li>Največja napaka je umirjati simptome, medtem ko resnični vzroki preobremenjenosti ostajajo enaki.</li><li>Pametnejši pristop poveže meje, preproste rituale in zaščito fokusa.</li></ul>',
            $faq(
                'Zakaj je stres pri delu tako izčrpavajoč?',
                'Ker pogosto združuje pritisk, stalno dosegljivost, odgovornost in premalo pravega okrevanja.',
                'Ali lahko naravne metode vseeno pomagajo?',
                'Da, vendar najbolj pomagajo takrat, ko podpirajo tudi boljšo organizacijo dela in počitka.',
                'Kaj pomaga fokusu pod stresom?',
                'Jasnejše prioritete, manj motenj, kratki odmori in boljše meje okoli pozornosti.',
                'Katera je pogosta napaka?',
                'Iskati sprostitev brez spremembe pogojev, ki stres vsak dan ustvarjajo.'
            ),
            'Stres na delovnem mestu: boljši fokus, okrevanje in meje',
            'Naučite se zmanjšati delovni stres s pomočjo boljših rutin, okrevanja in bolj trajnostnega fokusa.',
            'Stres na delovnem mestu',
            $sections(
                'Zakaj se obvladovanje stresa začne pri strukturi',
                'Ljudje si lažje opomorejo, ko so sestanki, motnje in pričakovanja bolj zavestno organizirani.',
                'Zakaj mora biti okrevanje aktivno',
                'Pravo okrevanje običajno prihaja iz namernih pavz, meja in življenjskih navad, ne iz upanja, da bo izčrpanost sama minila.'
            )
        ),
        $entry(
            112,
            'Nosečnost in prehranska dopolnila: varnejše izbire, več konteksta in manj ugibanja',
            'nosecnost-in-prehranska-dopolnila-varne-izbire-in-cemu-se-je-treba-izogibati',
            'Nosečnost zahteva previdnejši odnos do dopolnil, saj naravno ne pomeni samodejno tudi primerno. Ta vodič pojasni, zakaj so kontekst, namen in širša prehranska slika pomembnejši od trendov.',
            '<ul><li>Dopolnila v nosečnosti je smiselno izbirati z več previdnosti, konteksta in jasnega razloga.</li><li>Največja napaka je uvajati izdelke na slepo samo zato, ker so priljubljeni ali naravni.</li><li>Pametnejši pristop daje prednost varnosti, potrebi in celotni prehranski sliki.</li></ul>',
            $faq(
                'Zakaj so dopolnila v nosečnosti občutljiva tema?',
                'Ker sta varnost in kontekst uporabe še pomembnejša kot v mnogih drugih obdobjih življenja.',
                'Ali naravno vedno pomeni varno?',
                'Ne. Naravne sestavine niso avtomatsko primerne za nosečnost.',
                'Kako se tega lotiti bolj varno?',
                'S pogledom na dejansko potrebo, celotno prehrano in specifično situacijo, ne na trend.',
                'Katera je pogosta napaka?',
                'Domnevati, da mora biti nekaj primerno samo zato, ker to uporablja nekdo drug.'
            ),
            'Nosečnost in prehranska dopolnila: kako razmišljati bolj previdno',
            'Razumite, kako se prehranskih dopolnil v nosečnosti lotiti z več varnosti, konteksta in trezne presoje.',
            'Nosečnost in dopolnila',
            $sections(
                'Zakaj kontekst spremeni vse',
                'V nosečnosti postanejo varnost, primernost in premišljeno odločanje pomembnejši od spontanega eksperimentiranja.',
                'Zakaj trend ni dovolj dober vodnik',
                'Kar je priljubljeno na spletu, ni nujno usklajeno z individualnimi potrebami ali okoliščinami nosečnosti.'
            )
        ),
        $entry(
            113,
            'Glavoboli: pogosti vzroki in naravna podpora, ki ima več smisla',
            'najpogostejsi-vzroki-glavobolov-in-naravni-pristopi-k-njihovemu-resevanju',
            'Glavobol je lahko povezan z napetostjo, dehidracijo, stresom, spanjem ali prehranskim ritmom, zato je prvi korak pogosto razumevanje vzorca. Tukaj je, kako do glavobola pristopiti bolj mirno.',
            '<ul><li>Glavoboli imajo več mogočih sprožilcev, zato je opazovanje pogosto koristnejše od hitrega ugibanja.</li><li>Največja napaka je vsak glavobol obravnavati enako, ne glede na življenjski vzorec.</li><li>Pametnejši pristop poveže hidracijo, stres, spanec in prehranski ritem.</li></ul>',
            $faq(
                'Kateri so pogosti vzroki glavobola?',
                'Napetost, dehidracija, stres, neredno spanje, preobremenjenost in prehranski kaos.',
                'Ali lahko naravni pristopi pomagajo?',
                'Lahko, predvsem kot del širše rutine, ki zmanjšuje sprožilce in podpira okrevanje.',
                'Zakaj je koristno spremljati vzorce glavobola?',
                'Ker ponavljajoči sprožilci pogosto pokažejo, kaj težavo dejansko povzroča.',
                'Katera je pogosta napaka?',
                'Gasiti simptom brez razumevanja sprožilcev, ki se nenehno ponavljajo.'
            ),
            'Glavoboli: kako bolje razumeti sprožilce in poiskati pametnejšo pomoč',
            'Naučite se prepoznati pogoste sprožilce glavobola in razumeti, kje lahko mirnejši pristop res pomaga.',
            'Glavoboli',
            $sections(
                'Zakaj je prepoznavanje vzorca pomembno',
                'Glavobole je pogosto lažje obvladovati, ko človek prepozna, ali jih sprožajo napetost, ritem življenja ali drugi ponavljajoči se dejavniki.',
                'Zakaj osnovne navade še vedno štejejo',
                'Hidracija, počitek, čas obrokov in obremenitev s stresom lahko vplivajo na pogostost in intenzivnost glavobolov.'
            )
        ),
        $entry(
            127,
            'Smoothie ali stisnjen sok: ključne razlike in kje se aloe bolj naravno vključi',
            'smoothie-ali-stisnjen-sok-prednosti-slabosti-in-kje-se-aloja-prilega',
            'Smoothieji in sokovi niso prehransko enaki, čeprav oba zvenita zdravo. Ta članek pojasni praktične razlike in kje se aloe lahko bolj smiselno vključi v dnevno rutino.',
            '<ul><li>Smoothieji in sokovi se razlikujejo po vlakninah, sitosti in vlogi v obroku.</li><li>Največja napaka je obravnavati ju kot isto stvar samo zato, ker oba vsebujeta sadje ali zelenjavo.</li><li>Pametnejši pristop izbira glede na cilj: več sitosti, lažji vnos ali podpora rutini.</li></ul>',
            $faq(
                'Kakšna je glavna razlika med smoothijem in sokom?',
                'Smoothie običajno ohrani več vlaknin, sok pa je lažji in bolj filtriran tekoči napitek.',
                'Kdaj ima smoothie več smisla?',
                'Takrat, ko želite več sitosti in bolj obrokast občutek.',
                'Kje se aloe bolje vključi?',
                'Odvisno od cilja, vendar ima največ smisla, ko napitek ostane jasen po namenu in ni pretirano obremenjen.',
                'Katera je pogosta napaka?',
                'Piti zelo kalorične napitke in jih še vedno doživljati kot lahkotno zdravo izbiro.'
            ),
            'Smoothie ali sok: kako izbirati bolj pametno in aloe uporabiti smiselno',
            'Odkrijte razliko med smoothijem in sokom ter kako aloe vključiti v rutino bolj premišljeno.',
            'Smoothie ali sok',
            $sections(
                'Zakaj format napitka spremeni učinek',
                'Količina vlaknin, občutek sitosti in hitrost pitja lahko pomembno vplivajo na to, kako se napitek vključi v vsakdan.',
                'Zakaj mora odločati cilj',
                'Odločitev je boljša, ko se vprašamo, ali želimo sitost, praktičnost, hidracijo ali le več raznolikosti.'
            )
        ),
        $entry(
            131,
            'Čustvena lakota: kako ločiti pravo lakoto od stresa, dolgčasa in iskanja tolažbe',
            'kaj-je-custvena-lakota-kako-razlikovati-med-lakoto-in-dolgocasjem',
            'Čustvena lakota je lahko zelo intenzivna, čeprav telo fizično ni lačno. Ta vodič pojasni, kako ločiti čustvene sprožilce od resnične potrebe po hrani.',
            '<ul><li>Čustvena lakota je pogosto povezana z dolgčasom, stresom, utrujenostjo in potrebo po tolažbi.</li><li>Največja napaka je vsako željo po hrani brati kot fizično potrebo telesa.</li><li>Pametnejši pristop se uči razlikovati telesne signale od čustvenih avtomatizmov.</li></ul>',
            $faq(
                'Kaj je čustvena lakota?',
                'To je želja po hrani, ko glavni sprožilec ni telesna potreba, temveč čustveno stanje ali navada.',
                'Kako jo ločiti od prave lakote?',
                'Prava lakota navadno raste postopno, čustvena pa je pogosto nenadna in zelo specifična.',
                'Zakaj je to pomembno?',
                'Ker pomaga preprečiti prehranjevanje, ki ne reši pravega problema in za seboj pusti frustracijo.',
                'Katera je pogosta napaka?',
                'Poskušati čustveno lakoto utišati samo z disciplino brez razumevanja sprožilca.'
            ),
            'Čustvena lakota: kako jo prepoznati in nanjo odgovoriti bolj pametno',
            'Naučite se prepoznati čustveno lakoto in zgraditi bolj miren, zavesten odnos do hrane.',
            'Čustvena lakota',
            $sections(
                'Zakaj želja po hrani ni vedno fizična',
                'Ljudje pogosto posežejo po hrani, ker iščejo olajšanje, nagrado, prekinitev ali tolažbo, ne pa goriva.',
                'Zakaj zavedanje izboljša prehranski vzorec',
                'Ko človek natančneje poimenuje sprožilec, lažje izbere odziv, ki res ustreza potrebi.'
            )
        ),
        $entry(
            135,
            'Naravno beljenje zob: kaj aloe vera in soda bikarbona lahko in česa ne moreta narediti',
            'naravno-beljenje-zob-aloe-vera-in-soda-bikarbona-za-sijoc-nasmeh',
            'Naravno beljenje zveni privlačno, vendar zobje in sklenina potrebujejo več previdnosti kot navdušenja. Tukaj je, kako bolj realistično gledati na aloe, sodo bikarbono in rutino za svetlejši nasmeh.',
            '<ul><li>Naravno beljenje ima smisel le, če ne škodi dolgoročnemu udobju in zaščiti sklenine.</li><li>Največja napaka je prepogosto in pregrobo uporabljati domače metode.</li><li>Pametnejši pristop ceni nežnost, zmernost in celovito ustno nego.</li></ul>',
            $faq(
                'Ali lahko naravno beljenje izboljša videz nasmeha?',
                'Lahko podpre občutek svežine in čistosti, vendar ga ni smiselno razumeti kot čudež čez noč.',
                'Zakaj ljudje kombinirajo sodo bikarbono in aloe vero?',
                'Ker kombinacija deluje preprosto, naravno in lahko dostopno za domačo uporabo.',
                'Zakaj je previdnost pomembna?',
                'Ker sklenina in občutljivi zobje slabo prenašajo pogosto agresivno eksperimentiranje.',
                'Katera je pogosta napaka?',
                'Ponavljati grobe domače metode prepogosto in brez razmišljanja o dolgoročni zaščiti.'
            ),
            'Naravno beljenje zob: bolj realen pogled na aloe vero in sodo bikarbono',
            'Odkrijte, kaj lahko naravne metode beljenja realno ponudijo in zakaj je nežnost pomembnejša od hitrega učinka.',
            'Naravno beljenje zob',
            $sections(
                'Zakaj bolj belo ne pomeni vedno bolj zdravo',
                'Ideja o beljenju je lahko privlačna, vendar je dolgoročno stanje sklenine in udobje zob še vedno pomembnejše.',
                'Zakaj naj ustna nega ostane uravnotežena',
                'Ljudje navadno dosežejo več, ko ideje o beljenju ostanejo del širše in nežnejše rutine.'
            )
        ),
        $entry(
            137,
            'Prehranski načrt za zdravo prebavo: boljši ritem, več vlaknin in manj napihnjenosti',
            'prehranski-nacrt-za-zdravo-prebavo-koraki-do-ravnega-trebuha',
            'Boljša prebava redko pride iz enega popolnega načrta. Običajno nastane iz stalnega ritma obrokov, dovolj vlaknin, vode in manj prehranskega kaosa. Tukaj je, kako to zgraditi bolj trajnostno.',
            '<ul><li>Načrt za boljšo prebavo najbolje deluje, ko temelji na ritmu, vlakninah, vodi in preprostosti.</li><li>Največja napaka je loviti raven trebuh s kratkotrajnimi omejitvami, medtem ko osnovne navade ostajajo slabe.</li><li>Pametnejši pristop prebavo vidi kot dnevni vzorec, ne le kot estetsko vprašanje.</li></ul>',
            $faq(
                'Kaj najbolj podpira bolj zdravo prebavo?',
                'Bolj reden ritem obrokov, dovolj vlaknin, dovolj vode in manj prehranskega kaosa.',
                'Zakaj se ljudje osredotočijo samo na trebuh?',
                'Ker napihnjenost najprej opazijo vizualno, čeprav so vzroki pogosto vedenjski.',
                'Ali lahko preprost načrt res pomaga?',
                'Da, še posebej če je realen in dovolj enostaven za ponavljanje.',
                'Katera je pogosta napaka?',
                'Poskušati rešiti prebavo s prepovedmi namesto z bolj stabilno dnevno rutino.'
            ),
            'Prehranski načrt za zdravo prebavo: ravnovesje brez skrajnosti',
            'Naučite se sestaviti trajnosten prehranski načrt za manj napihnjenosti in bolj stabilen občutek v telesu.',
            'Načrt za prebavo',
            $sections(
                'Zakaj prebava rada sledi ritmu',
                'Čas obrokov, hitrost prehranjevanja, doslednost in hidracija lahko pri mnogih vplivajo na prebavo skoraj toliko kot sama živila.',
                'Zakaj preprosti načrti trajajo dlje',
                'Rutina ni nujno ekstremna, da bi pomagala. Biti mora jasna, ponovljiva in dovolj prijetna, da ostane v življenju.'
            )
        ),
        $entry(
            138,
            'Občutljiva koža in atopijski dermatitis: kje lahko aloe vera pomaga in kje je potrebna previdnost',
            'obcutljiva-koza-in-atopijski-dermatitis-kako-pomaga-aloe-vera',
            'Občutljiva koža praviloma potrebuje manj poskusov, manj draženja in več predvidljivosti. Ta članek pojasni, kje aloe vera lahko nudi nežno podporo in zakaj preproste rutine pogosto delujejo bolje.',
            '<ul><li>Aloe vera je lahko koristen pomirjujoč korak za kožo, ki potrebuje manj draženja in več udobja.</li><li>Največja napaka je občutljivo kožo preobremeniti s preveč naravnimi reševalnimi idejami.</li><li>Pametnejši pristop daje prednost zaščiti kožne bariere in enostavnejši rutini.</li></ul>',
            $faq(
                'Ali lahko aloe vera pomaga občutljivi koži?',
                'Lahko pomaga kot nežen pomirjujoč korak, zlasti kadar koža hitro reagira.',
                'Ali je to dovolj za atopijski dermatitis?',
                'Ne vedno. Pogosto ima največ smisla kot del širšega in previdnega pristopa.',
                'Kaj je pri takšni koži najpomembnejše?',
                'Zaščita bariere, manj draženja, več predvidljivosti in manj agresivnih poskusov.',
                'Katera je pogosta napaka?',
                'Preizkušati preveč izdelkov in nenehno menjavati rutino.'
            ),
            'Občutljiva koža in aloe vera: nežna podpora brez pretiravanja',
            'Razumite, kje se aloe vera lahko smiselno vključi v nego občutljive kože in zakaj so preproste rutine pogosto najboljše.',
            'Občutljiva koža in aloe',
            $sections(
                'Zakaj občutljiva koža želi stabilnost',
                'Koža, ki hitro reagira, se pogosto bolje odzove na manj dražljajev in več doslednosti kot na stalno novost.',
                'Zakaj ima bariera prednost',
                'Udobje, toleranca in zadrževanje vlage se pogosto izboljšajo, ko rutino gradimo okoli kožne bariere.'
            )
        ),
        $entry(
            143,
            'Aloe vera v gospodinjstvu: pet praktičnih načinov za bolj pametno uporabo',
            'aloe-vera-v-gospodinjstvu-5-presenetljivih-nacinov-uporabe-aloe-vere-v-gospodinjstvu',
            'Aloe vera je v domu privlačna zato, ker deluje vsestransko in preprosto. Ta članek pokaže, kako jo uporabljati praktično, ne da bi eno rastlino spremenili v rešitev za vse.',
            '<ul><li>Aloe vera v gospodinjstvu najbolje deluje, kadar jo uporabljamo v nekaj praktičnih načinih, ki res prinesejo vrednost.</li><li>Največja napaka je od ene rastline pričakovati odgovor na vsako majhno težavo doma.</li><li>Pametnejši pristop se osredotoča na nekaj uporabnih in realnih načinov uporabe.</li></ul>',
            $faq(
                'Zakaj je aloe vera tako priljubljena v gospodinjstvu?',
                'Ker deluje naravno, dostopno in jo je enostavno vključiti v vsakdan.',
                'Ali potrebujete veliko različnih načinov uporabe?',
                'Ne. Nekaj preverjenih in praktičnih uporab je navadno veliko bolj koristnih.',
                'Kaj aloe vero naredi zanimivo za dom?',
                'Preprostost, dostopnost in občutek naravne pomoči pri vsakodnevnih opravilih.',
                'Katera je pogosta napaka?',
                'Aloe vero obravnavati kot univerzalni odgovor za celotno gospodinjstvo.'
            ),
            'Aloe vera v gospodinjstvu: praktična uporaba brez pretiravanja',
            'Odkrijte, kako aloe vero uporabljati doma na nekaj realnih in uporabnih načinov brez nepotrebnih pretiravanj.',
            'Aloe vera doma',
            $sections(
                'Zakaj preprosta uporaba deluje najbolje',
                'Ljudje imajo največ koristi od nekaj ponovljivih načinov uporabe, ne od dolgih seznamov idej, ki jih nikoli ne uporabijo.',
                'Zakaj realizem varuje zaupanje',
                'Aloe vera ostane najbolj uporabna, ko jo obravnavamo kot praktičnega pomočnika in ne kot čudež za vse.'
            )
        ),
        $entry(
            144,
            'Zdrav krompir z aloe marinadami: lažji recepti, ki še vedno ostanejo okusni',
            'zdrav-krompir-recepti-z-marinadami-iz-aloje-za-manj-kalorij',
            'Bolj zdravi recepti delujejo dolgoročno samo takrat, ko so še vedno okusni in enostavni za ponavljanje. Tukaj je, kako lahko lažja priprava krompirja in aloe marinade pomagajo pri bolj prijetni kuhinji.',
            '<ul><li>Bolj zdravi recepti delujejo najbolje, ko ostanejo okusni, praktični in dovolj enostavni za ponavljanje.</li><li>Največja napaka je zdravo kuhanje spremeniti v kazen brez užitka.</li><li>Pametnejši pristop uporablja manjše spremembe v pripravi za manj težek občutek obroka.</li></ul>',
            $faq(
                'Ali so lahko bolj zdravi recepti še vedno nasitni in okusni?',
                'Da, in prav to jih naredi dolgoročno uporabne.',
                'Zakaj so majhne spremembe pri pripravi pomembne?',
                'Ker pogosto izboljšajo hranilni občutek obroka brez velikih odrekanj.',
                'Kje imajo aloe marinade smisel?',
                'Tam, kjer dodajo zanimivost, raznolikost in še vedno ohranijo recept praktičen.',
                'Katera je pogosta napaka?',
                'Pripraviti zdrav obrok, ki ga nihče ne želi ponovno jesti.'
            ),
            'Zdrav krompir in aloe marinade: boljše ravnovesje brez izgube okusa',
            'Spoznajte, kako pripraviti lažje krompirjeve jedi, ki ostanejo okusne in realno uporabne v vsakdanji prehrani.',
            'Zdrav krompir',
            $sections(
                'Zakaj trajnostna kuhinja potrebuje užitek',
                'Ljudje ponavljajo obroke, ki jim teknejo, ne pa tistih, ki so le navidezno zdravi in hkrati neokusni.',
                'Zakaj so majhni premiki pogosto dovolj',
                'Bolj zdravo kuhanje pogosto nastane iz boljših metod, začimb in zmernosti, ne iz popolne revolucije.'
            )
        ),
        $entry(
            147,
            'Aloe vera in krvni sladkor: kako o tej temi razmišljati bolj previdno',
            'aloe-vera-in-krvni-sladkor-ali-lahko-pomaga-uravnavati-glukozo',
            'Aloe vera in krvni sladkor je tema, ki pritegne veliko zanimanja, zato potrebuje še več konteksta in previdnosti. Tukaj je, kako o njej razmišljati brez pretiravanja in nerealnih pričakovanj.',
            '<ul><li>Tema aloe vere in krvnega sladkorja zahteva jasno razlikovanje med zanimanjem in močnimi trditvami.</li><li>Največja napaka je en aloe izdelek videti kot popoln odgovor na kompleksno presnovno zgodbo.</li><li>Pametnejši pristop ohranja v središču prehrano, gibanje, spanec in rutino.</li></ul>',
            $faq(
                'Zakaj ljudi zanima aloe vera in krvni sladkor?',
                'Ker mnogi iščejo bolj naravne načine za podporo bolj stabilni glukozi in presnovnim navadam.',
                'Ali to pomeni, da aloe vera rešuje uravnavanje glukoze?',
                'Ne. To bi bilo preveč poenostavljeno za temo, ki je veliko širša od enega izdelka.',
                'Kaj je tukaj pomembnejše od hypea?',
                'Prehrana, gibanje, doslednost in razumevanje širše presnovne slike.',
                'Katera je pogosta napaka?',
                'Od enega izdelka pričakovati to, kar običajno zahteva širšo spremembo življenjskega sloga.'
            ),
            'Aloe vera in krvni sladkor: bolj previden in uravnotežen pogled',
            'Raziščite, kako o aloe veri in uravnavanju glukoze razmišljati brez hypea in z več spoštovanja do širše slike.',
            'Aloe vera in krvni sladkor',
            $sections(
                'Zakaj presnova nikoli ni zgodba enega izdelka',
                'Vzorce krvnega sladkorja oblikujejo prehrana, gibanje, spanec, stres in mnoge ponavljajoče se odločitve.',
                'Zakaj je previden jezik pomemben',
                'Ljudje sprejemajo boljše odločitve, ko so podporne ideje predstavljene pošteno in brez pretiranih obljub.'
            )
        ),
        $entry(
            148,
            'Naravni piling za telo: kako lahko ovseni kosmiči, sladkor in aloe nežno podprejo nego kože',
            'naravni-piling-za-telo-iz-ovsenih-kosmicev-sladkorja-in-aloje',
            'Domači piling za telo ima največ smisla takrat, ko koži pusti občutek svežine in udobja, ne pa zategnjenosti in draženja. Tukaj je, kako ga uporabljati bolj nežno in pametno.',
            '<ul><li>Naravni piling za telo ima več smisla, ko osveži kožo brez dodatnega draženja.</li><li>Največja napaka je piling spremeniti v grob ritual, ki kožo bolj obremenjuje kot podpira.</li><li>Pametnejši pristop izbira nežnejšo teksturo, razumno pogostost in boljši občutek po negi.</li></ul>',
            $faq(
                'Zakaj imajo ljudje radi naravne pilinge za telo?',
                'Ker delujejo preprosto, sveže in jih je enostavno pripraviti doma.',
                'Ali lahko ovseni kosmiči, sladkor in aloe dobro delujejo skupaj?',
                'Da, kadar mešanico uporabljamo nežno in ne prepogosto.',
                'Kako pomembna je pogostost?',
                'Zelo, saj lahko pretirano luščenje zmanjša udobje in ravnovesje kože.',
                'Katera je pogosta napaka?',
                'Misliti, da močnejši in pogostejši piling vedno pomeni lepšo kožo.'
            ),
            'Naravni piling za telo: kako ga uporabljati bolj nežno in učinkovito',
            'Naučite se, kako uporabljati piling iz ovsenih kosmičev, sladkorja in aloe brez nepotrebnega pretiravanja.',
            'Naravni piling za telo',
            $sections(
                'Zakaj nežno luščenje pogosto deluje bolje',
                'Koža se pogosto bolje odzove na zmerno in udobno nego kot na agresivno drgnjenje in prepogosto odstranjevanje.',
                'Zakaj je občutek po negi pravi test',
                'Če koža po pilingu deluje uravnoteženo in prijetno, je rutina običajno primernejša kot takrat, ko peče ali zateguje.'
            )
        ),
        $entry(
            152,
            'Domača maska iz aloe vere za lase, nagnjene k prhljaju: kdaj naravni recept res pomaga',
            'domaca-maska-iz-aloe-vere-za-lase-nagnjene-k-prhljaju-naravni-recept-in-nasveti',
            'Naravne maske za lase lahko izboljšajo udobje lasišča, vendar prhljaj pogosto zahteva širši pogled na navade in sprožilce. Tukaj je, kako masko z aloe vero uporabljati bolj realno.',
            '<ul><li>Domača aloe maska ima največ smisla kot nežna podpora udobju lasišča, ne kot popolna rešitev za vse vrste prhljaja.</li><li>Največja napaka je pričakovati, da bo en domač recept dolgoročno rešil vsako težavo lasišča.</li><li>Pametnejši pristop upošteva stanje lasišča, pogostost nege in širše vzroke luščenja.</li></ul>',
            $faq(
                'Ali lahko domača aloe maska pomaga lasem, nagnjenim k prhljaju?',
                'Lahko pomaga pri občutku nege in bolj pomirjenem lasišču kot nežen dodatek rutini.',
                'Ali je to dovolj za vsak tip prhljaja?',
                'Ne. Nekateri vzroki prhljaja potrebujejo širši in bolj previden pristop.',
                'Zakaj so takšni recepti ljudem všeč?',
                'Ker delujejo naravno, preprosto in jih je mogoče pripraviti doma.',
                'Katera je pogosta napaka?',
                'Pričakovati, da bo ena maska rešila težavo brez spremembe preostale rutine nege lasišča.'
            ),
            'Aloe vera maska za lase: bolj pameten naravni pristop pri lasišču, nagnjenem k prhljaju',
            'Odkrijte, kako domačo aloe vera masko uporabljati bolj premišljeno in kje se naravna podpora smiselno vključi v nego lasišča.',
            'Aloe maska za lase',
            $sections(
                'Zakaj nega lasišča potrebuje kontekst',
                'Luščenje in nelagodje imata lahko več sprožilcev, zato en recept redko predstavlja celoten odgovor.',
                'Zakaj nežna rutina še vedno šteje',
                'Tudi kadar je vzrok širši, lahko bolj umirjena in podpirajoča rutina izboljša udobje in doslednost nege.'
            )
        ),
    ],
];
