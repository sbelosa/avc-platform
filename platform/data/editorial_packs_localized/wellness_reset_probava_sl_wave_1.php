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
    'key' => 'wellness-reset-probava-sl-wave-1',
    'name' => 'Wellness reset in prebavni vodiči (SL) - prvi val',
    'notes' => 'Večji lokalizirani uredniški val za burnout, jogo, hujšanje ter ciljno usmerjene prebavne in respiratorne simptom vodiče.',
    'entries' => [
        $entry(
            142,
            'sl',
            'Razstrupljanje težkih kovin: kje imajo kelatorji in aloe sploh smisel',
            'razstrupljanje-tezkih-kovin-kje-imajo-kelatorji-in-aloe-smisel',
            'Težke kovine so tema, ki zelo hitro zdrsne v paniko in spletno pretiravanje. Tukaj je, kako o kelatorjih, podpori telesu in razstrupljanju razmišljati bolj odgovorno, brez ustvarjanja novih težav s slabo vodenimi “čistkami”.',
            '<ul><li>Razstrupljanje težkih kovin ni domači eksperiment, ampak tema, ki potrebuje veliko več konteksta in previdnosti.</li><li>Največja napaka je poseči po agresivnih protokolih brez jasne potrebe in ustreznega okvira.</li><li>Pametnejši pristop loči splošno podporo telesu od resničnih medicinskih situacij, ki zahtevajo poseben pristop.</li></ul>',
            [
                ['question' => 'Ali bi nas to moralo skrbeti vse?', 'answer' => 'Ne enako. Pomembno je ločiti splošno skrb od dejansko potrjene izpostavljenosti ali tveganja.'],
                ['question' => 'Ali so kelatorji lahko tvegani?', 'answer' => 'Da, posebno če se uporabljajo brez strokovnega vodstva in brez jasnega razloga.'],
                ['question' => 'Kje ima aloe vera mesto?', 'answer' => 'Bolj kot del nežnejše podpore telesu, ne pa kot glavno orodje za specifične medicinske intervencije.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Začeti agresiven “detoks” iz strahu namesto na podlagi resničnih podatkov in potrebe.'],
            ],
            'Težke kovine: kako ločiti podporo telesu od nevarnega eksperimenta',
            'Spoznajte, kako bolj odgovorno razmišljati o težkih kovinah, kelatorjih in detoks rutini brez spletne panike.',
            'Težke kovine',
            [
                ['heading' => 'Zakaj se govor o težkih kovinah hitro izkrivlja', 'html' => '<p>Ta tema pogosto privlači močna čustva in dramatične trditve. Prav zato potrebuje veliko več konteksta, dokazov in zadržanosti, kot jih običajno ponuja splet.</p>'],
                ['heading' => 'Zakaj podpora in zdravljenje nista isto', 'html' => '<p>Splošna skrb za telo je lahko koristna, vendar to ni isto kot dejanski detoks poseg. Jasnejša meja med tema pojmoma ljudi bolje zaščiti pred zmedo in tveganjem.</p>'],
            ]
        ),
        $entry(
            171,
            'sl',
            'Kdaj je čas za odmor? Znaki izgorelosti, ki jih ni dobro ignorirati',
            'kdaj-je-cas-za-odmor-znaki-izgorelosti-ki-jih-ne-gre-spregledati',
            'Izgorelost redko pride čez noč. Pogosteje se gradi skozi mesece slabega okrevanja, notranjega pritiska in življenja, ki ne pušča več prostora za dih. Tukaj je, kako zgodaj prepoznati znake, preden utrujenost prevzame ves sistem.',
            '<ul><li>Izgorelost se pogosto začne dolgo preden si priznamo, da normalno delovanje že drsi navzdol.</li><li>Največja napaka je, da počitek razumemo kot slabost ali razkošje namesto kot zaščito sposobnosti.</li><li>Pametnejši pristop prej opazi opozorila in vrne okrevanje še preden sistem resno popusti.</li></ul>',
            [
                ['question' => 'Kako prepoznamo zgodnjo izgorelost?', 'answer' => 'Po stalni utrujenosti, padcu motivacije, razdražljivosti in občutku, da počitek več ne pomaga kot nekoč.'],
                ['question' => 'Ali zadostuje en prost vikend?', 'answer' => 'Včasih ne, saj je težava pogosto v celotni strukturi življenja in obremenitve.'],
                ['question' => 'Zakaj ljudje reagiramo prepozno?', 'answer' => 'Ker dolgo delujemo iz dolžnosti in občutka, da bomo “še malo zdržali”.'],
                ['question' => 'Kaj pomaga najbolj?', 'answer' => 'Zgodnejše meje, spanec, manj preobremenjenosti in majhni redni prostori za resničen odklop.'],
            ],
            'Izgorelost: kdaj je čas za odmor in katere znake je treba vzeti resno',
            'Prepoznajte zgodnje znake izgorelosti in se naučite vrniti več prostora za okrevanje, preden pride do večjega zloma.',
            'Izgorelost',
            [
                ['heading' => 'Zakaj izgorelost običajno raste počasi', 'html' => '<p>Mnogi mislimo, da izgorelost nastane v enem samem trenutku, ko “poči”. V resnici se običajno kopiči skozi dolga obdobja, ko okrevanje ne dohaja več obremenitve.</p>'],
                ['heading' => 'Zakaj mora počitek priti prej, ne šele v sili', 'html' => '<p>Čakanje na popoln zlom naredi spremembo veliko težjo. Veliko bolj zdravo je, da počitek razumemo kot redno vzdrževalno prakso in ne kot zadnji ukrep.</p>'],
            ]
        ),
        $entry(
            172,
            'sl',
            'Vodnik za začetnike v jogi: kako začeti brez pritiska in lažnih pričakovanj',
            'vodnik-za-zacetnike-v-jogi-kako-zaceti-brez-pritiska',
            'Joga za začetnike ne rabi biti estetska predstava ali test gibljivosti. Največ koristi prinese takrat, ko v vsakdan vnese dih, prisotnost in pametnejše gibanje brez pritiska, da mora biti vse popolno že prvi dan.',
            '<ul><li>Začetek joge ima največ smisla, ko zmanjša napetost in okrepi stik s telesom.</li><li>Največja napaka je verjeti, da morate biti pred začetkom že gibčni, mirni ali “pripravljeni”.</li><li>Pametnejši pristop izbere preprostejšo prakso in dovolj nežen tempo, da navada ostane.</li></ul>',
            [
                ['question' => 'Ali začetniki potrebujemo posebno opremo?', 'answer' => 'Ne veliko. Podloga, udobna oblačila in nekaj prostora so običajno dovolj.'],
                ['question' => 'Ali moram biti že gibčen?', 'answer' => 'Ne. Veliko ljudi začne prav zato, da postopno pridobi več gibljivosti in lahkotnosti.'],
                ['question' => 'Kako pogosto vaditi?', 'answer' => 'Kratko in redno je praviloma bolje kot redko in preveč ambiciozno.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Primerjati svoj začetek z naprednimi prikazi na družbenih omrežjih.'],
            ],
            'Joga za začetnike: mirnejši, preprostejši in bolj vzdržen začetek',
            'Začnite jogo skozi dihanje, osnovne gibe in rutino, ki je res izvedljiva tudi za začetnike.',
            'Joga za začetnike',
            [
                ['heading' => 'Zakaj začetnikom koristi manj nastopa in več miru', 'html' => '<p>Joga pogosto postane veliko bolj uporabna, ko jo obravnavamo kot podporno prakso in ne kot vizualni standard, ki ga moramo doseči. Tak pogled navado precej olajša.</p>'],
                ['heading' => 'Zakaj nežna ponovitev ustvarja močnejši napredek', 'html' => '<p>Telo se navadno več nauči iz mirnega rednega stika kot iz dramatičnega napora. Prav zato mehkejši začetek pogosto zgradi več zaupanja kot agresiven.</p>'],
            ]
        ),
        $entry(
            468,
            'sl',
            'Ozempic in hujšanje: kaj potrebuje več konteksta, preden nastane hype',
            'ozempic-in-hujsanje-kaj-potrebuje-vec-konteksta',
            'Ozempic je postal osrednja tema pogovorov o hujšanju, vendar je resnična slika veliko bolj zapletena od viralnih fotografij prej in potem. Tukaj je, kako o njem razmišljati z več medicinskega konteksta, previdnosti in manj po logiki trenda.',
            '<ul><li>Ozempic in podobna zdravila so resna terapevtska orodja, ne trendovski pripomočki.</li><li>Največja napaka je, da jih obravnavamo kot preprost bližnjico brez razumevanja, komu so namenjeni in v kakšnih okoliščinah.</li><li>Pametnejši pristop gleda zdravstveni kontekst, navade in dolgoročno vzdržnost, ne samo hitro spremembo teže.</li></ul>',
            [
                ['question' => 'Ali je Ozempic univerzalna rešitev za hujšanje?', 'answer' => 'Ne. Gre za zdravilo s specifičnimi indikacijami, zato ga je treba gledati v medicinskem okviru.'],
                ['question' => 'Zakaj je okoli njega toliko hypea?', 'answer' => 'Ker vidni hitri rezultati v javnosti naredijo kompleksno temo videti enostavno.'],
                ['question' => 'Kaj ljudje pogosto spregledamo?', 'answer' => 'Neželene učinke, spremljanje, zdravstveni kontekst in vprašanje dolgoročnosti.'],
                ['question' => 'Kaj je pametnejši okvir?', 'answer' => 'O njem razmišljati kot o terapevtski možnosti, ne kot o instant wellness trendu.'],
            ],
            'Ozempic in hujšanje: koristi, omejitve in zakaj ni le internetni trend',
            'Razumite, kako o Ozempicu in hujšanju razmišljati bolj odgovorno skozi medicinski kontekst namesto hypea.',
            'Ozempic',
            [
                ['heading' => 'Zakaj družbena omrežja sploščijo celotno zgodbo', 'html' => '<p>Ko ljudje vidimo le hitro vidno spremembo, hitro pozabimo na medicinski in življenjski kontekst. Ravno zato je pri tej vrsti zdravila še posebej pomembno jasnejše razumevanje celote.</p>'],
                ['heading' => 'Zakaj je dolgoročno razmišljanje pomembnejše od novosti', 'html' => '<p>Orodja za hujšanje je smiselno presojati ne le po tem, kaj se zgodi hitro, ampak tudi po tem, kaj ostane vzdržno. Prav ta daljša perspektiva običajno vodi do pametnejših vprašanj.</p>'],
            ]
        ),
        $entry(
            469,
            'sl',
            'Najboljša dieta za hujšanje: kako primerjati načrte brez nove frustracije',
            'najboljsa-dieta-za-hujsanje-kako-primerjati-nacrte-brez-frustracije',
            'Najboljša dieta je redko tista, ki zveni najstrožje. Pogosteje je to tista, ki jo lahko izvajate dovolj dolgo, da naredi razliko. Tukaj je, kako primerjati priljubljene načrte brez novega kroga frustracije in odstopanja.',
            '<ul><li>Najboljša dieta je tista, ki ustreza vašemu življenju, zdravju in zmožnosti, da jo res ohranite.</li><li>Največja napaka je izbirati načrt po obljubljeni hitrosti namesto po vzdržnosti.</li><li>Pametnejši pristop primerja stopnjo omejitev, sitost, fleksibilnost in psihološko ceno načrta.</li></ul>',
            [
                ['question' => 'Ali obstaja ena najboljša dieta za vse?', 'answer' => 'Ne. Ljudje se razlikujemo po navadah, zdravju in tem, kaj lahko realno vzdržujemo.'],
                ['question' => 'Zakaj strožji načrti pogosto ne uspejo?', 'answer' => 'Ker kratkoročno delujejo, dolgoročno pa od vsakdana zahtevajo preveč.'],
                ['question' => 'Kaj je pomembnejše od imena diete?', 'answer' => 'Koliko je načrt vzdržen, kako vpliva na lakoto in kakšen odnos do hrane spodbuja.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Prehitro menjavati diete, ne da bi katerikoli strukturi dali dovolj časa.'],
            ],
            'Najboljša dieta za hujšanje: kako izbrati načrt, s katerim lahko res živite',
            'Primerjajte diete za hujšanje skozi vzdržnost, sitost in resnično življenje, ne samo skozi hitre obljube.',
            'Najboljša dieta',
            [
                ['heading' => 'Zakaj je najboljša dieta navadno tista, s katero lahko ostanemo', 'html' => '<p>Uspeh pri hujšanju je pogosto manj odvisen od imena diete kot od tega, kako dobro se njen okvir prilega vsakdanjemu življenju. Dober načrt običajno podpira realnost, namesto da se z njo bori.</p>'],
                ['heading' => 'Zakaj fleksibilnost pogosto bolje ščiti rezultat', 'html' => '<p>Načrti, ki dopuščajo nekaj prostora in prilagoditev, so pogosto lažji za dolgoročno vzdrževanje. Prav ta fleksibilnost lahko pomeni več kot sama strogost.</p>'],
            ]
        ),
        $entry(
            484,
            'sl',
            'Naravna pomoč pri zgagi: aloe vera in navade, ki res umirijo vzorec',
            'naravna-pomoc-pri-zgagi-aloe-vera-in-navade-ki-umirijo-vzorec',
            'Pri zgagi običajno največ koristi prinese razumevanje vzorca, ne pa nenehno gašenje istega požara. Tukaj je, kako hrana, čas obrokov in podpora, kot je aloe vera, lahko pomagajo ustvariti bolj stabilno olajšanje.',
            '<ul><li>Zgaga pogosto zahteva spremembo navad, ne le občasnega blaženja simptomov.</li><li>Največja napaka je ignorirati sprožilce in pričakovati, da bo en izdelek rešil ponavljajoč se vzorec.</li><li>Pametnejši pristop gleda obroke, čas hranjenja, količino hrane in položaj telesa po jedi.</li></ul>',
            [
                ['question' => 'Ali aloe vera lahko pomaga pri zgagi?', 'answer' => 'Pri nekaterih ljudeh je lahko del nežnejše podpore, vendar ne nadomesti razumevanja vzorca.'],
                ['question' => 'Kaj najpogosteje poslabša zgago?', 'answer' => 'Veliki obroki, pozno hranjenje, težja hrana, stres in ležanje kmalu po jedi.'],
                ['question' => 'Kdaj je potreben večji oprez?', 'answer' => 'Ko se simptomi pojavljajo pogosto, ponoči ali skupaj z resnejšimi opozorilnimi znaki.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Blažiti simptome brez spremembe prehranskih in časovnih navad, ki jih stalno vračajo.'],
            ],
            'Zgaga: aloe vera, čas obrokov in navade, ki prinašajo bolj smiselno olajšanje',
            'Spoznajte, kako pri zgagi pomagajo boljši obroki, pravi čas hranjenja in nežnejša podpora, kot je aloe vera.',
            'Zgaga',
            [
                ['heading' => 'Zakaj je zgaga pogosto vzorec, ne naključen dogodek', 'html' => '<p>Mnogi na zgago pomislimo šele, ko pečenje že pride. V resnici pa telo pogosto odgovarja na ponavljajoče se prehranske in dnevne vzorce, zato dolgotrajna pomoč običajno zahteva več kot en izdelek.</p>'],
                ['heading' => 'Zakaj preprostejši obroki podpirajo celoten sistem', 'html' => '<p>Velikost obrokov, čas in položaj telesa po jedi lahko močno spremenijo izkušnjo. Najbolj uporabna podpora običajno deluje najbolje, ko se spremenijo tudi te navade.</p>'],
            ]
        ),
        $entry(
            485,
            'sl',
            'Naravni pristopi proti driski: aloe vera, probiotiki in pametnejše okrevanje',
            'naravni-pristopi-proti-driski-aloe-vera-probiotiki-in-okrevanje',
            'Ko se pojavi driska, telo običajno potrebuje mir, tekočino in obnovo prebave veliko bolj kot paniko in naključna domača sredstva. Tukaj je, kaj naravna podpora res lahko ponudi in kje meja previdnosti ostaja pomembna.',
            '<ul><li>Driska zahteva hidracijo, previdnost in razumevanje vzroka, ne le hitro utišanje simptoma.</li><li>Največja napaka je podceniti izgubo tekočine ali predolgo čakati, ko se stanje slabša.</li><li>Pametnejši pristop poveže nežen prebavni počitek, ciljno podporo in pozornost do opozorilnih znakov.</li></ul>',
            [
                ['question' => 'Ali so probiotiki koristni?', 'answer' => 'Lahko, odvisno od vzroka in od tega, kdaj jih uvedemo kot del okrevanja.'],
                ['question' => 'Kaj je najpomembnejše najprej?', 'answer' => 'Tekočina, počitek prebave in spremljanje znakov dehidracije.'],
                ['question' => 'Ali aloe vera lahko pomaga?', 'answer' => 'Včasih kot del nežnejše podpore, vendar ni glavni odgovor za vsak vzrok.'],
                ['question' => 'Kdaj razmisliti o dodatni pomoči?', 'answer' => 'Ko se simptomi slabšajo, trajajo dlje ali jih spremljajo močnejši opozorilni znaki.'],
            ],
            'Driska: naravna podpora skozi tekočino, probiotike in previdno okrevanje',
            'Razumite, kako pri driski pomagajo hidracija, probiotiki in mirnejši pristop k okrevanju prebave.',
            'Driska',
            [
                ['heading' => 'Zakaj hidracija ostaja glavni prioriteti', 'html' => '<p>Prebavne težave so lahko zelo neprijetne, vendar je pogosto največja skrb izguba tekočine in splošne stabilnosti telesa. Zato se okrevanje vedno začne pri podpori, ne pri paniki.</p>'],
                ['heading' => 'Zakaj prebava najbolje okreva ob več miru', 'html' => '<p>Prebavni sistem se praviloma bolje odzove na manj pritiska, ne na več intervencij. Nežnejši pristop pogosto zaščiti telo bolje kot preveč različnih sredstev naenkrat.</p>'],
            ]
        ),
        $entry(
            486,
            'sl',
            'Kako naravno umiriti bruhanje: ingver, rehidracija in manj ugibanja',
            'kako-naravno-umiriti-bruhanje-z-ingverjem-in-rehidracijo',
            'Bruhanje telo hitro izčrpa, zato je zaščita tekočine praviloma pomembnejša od agresivnih trikov. Tukaj je, kako okrevanje nežneje podpreti z rehidracijo, ingverjem in počasnejšim vračanjem hrane.',
            '<ul><li>Ob bruhanju je prvi cilj zaščititi tekočino in prebavnemu sistemu dati čas, da se postopno umiri.</li><li>Največja napaka je prehitro vrniti težjo hrano ali spregledati znake dehidracije.</li><li>Pametnejši pristop uporablja male požirke, mir in previden tempo.</li></ul>',
            [
                ['question' => 'Kaj je najpomembnejše po bruhanju?', 'answer' => 'Postopen povratek tekočine in to, da želodca ne obremenimo prehitro.'],
                ['question' => 'Ali lahko ingver pomaga?', 'answer' => 'Pri nekaterih ljudeh lahko pomaga ublažiti slabost, če je uporabljen nežno in primerno.'],
                ['question' => 'Kdaj je potreben večji oprez?', 'answer' => 'Ko tekočina ne ostane v telesu ali ko se slabost in oslabelost povečujeta.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Prehitro se vrniti k normalnemu in težjemu obroku.'],
            ],
            'Bruhanje: rehidracija, ingver in nežnejša pot nazaj do hrane',
            'Preverite, kako podpreti okrevanje po bruhanju s tekočino, ingverjem in varnejšim povratkom hrane.',
            'Bruhanje',
            [
                ['heading' => 'Zakaj telo po bruhanju potrebuje počasnejši ritem', 'html' => '<p>Bruhanje želodec in celoten sistem za nekaj časa naredi bolj občutljiv. Prav zato največ pomeni preprost pristop: manjši požirki, več potrpežljivosti in manj pritiska.</p>'],
                ['heading' => 'Zakaj je čas uvedbe pomemben skoraj toliko kot sredstvo', 'html' => '<p>Tudi koristna podpora lahko postane neprimerna, če jo uvedemo prehitro. Okrevanje je zato močno odvisno od tempa in ne le od izbrane pomoči.</p>'],
            ]
        ),
        $entry(
            487,
            'sl',
            'Naravna pomoč pri kašlju: aloe vera, med in bolj pametna podpora grlu',
            'naravna-pomoc-pri-kaslju-aloe-vera-med-in-pametna-podpora-grlu',
            'Ni vsak kašelj enak problem, zato tudi vsako “naravno sredstvo” nima enakega smisla. Tukaj je, kako ločiti pomiritev grla od resničnega okrevanja in kje aloe vera, med in zelišča lahko bolj smiselno pomagajo.',
            '<ul><li>Naravna podpora kašlju najbolje deluje, ko ustreza vrsti draženja in širši sliki simptomov.</li><li>Največja napaka je vsak kašelj obravnavati enako, ne glede na trajanje in vzrok.</li><li>Pametnejši pristop pomiri grlo, zaščiti spanec in spremlja, kako se vzorec spreminja.</li></ul>',
            [
                ['question' => 'Ali med lahko pomaga pri kašlju?', 'answer' => 'Pri razdraženem grlu pogosto lahko prinese občutek pomiritve in manj draženja.'],
                ['question' => 'Kje ima aloe vera mesto?', 'answer' => 'Bolj kot del pomirjujoče podpore grlu kot pa kot glavni odgovor za vsak kašelj.'],
                ['question' => 'Zakaj ni vsak kašelj enak?', 'answer' => 'Ker je lahko posledica različnih sprožilcev in zato različno reagira na podporo.'],
                ['question' => 'Kdaj je potreben dodaten oprez?', 'answer' => 'Če kašelj traja dolgo, se slabša ali je povezan z močnejšimi simptomi.'],
            ],
            'Kašelj: med, aloe vera in bolj smiselna naravna podpora za grlo',
            'Odkrijte, kako kašlju pristopiti bolj naravno skozi med, zelišča in nežnejšo podporo grlu.',
            'Kašelj',
            [
                ['heading' => 'Zakaj oskrba kašlja začne pri razumevanju vzorca', 'html' => '<p>Ljudje pogosto iščemo eno sredstvo za vse, vendar podpora kašlju najbolje deluje, ko bolje razumemo vrsto draženja in celoten vzorec simptomov. Tak kontekst naredi tudi preprosta sredstva veliko bolj uporabna.</p>'],
                ['heading' => 'Zakaj je zaščita spanja pomembnejša, kot si mislimo', 'html' => '<p>Ko kašelj krade spanec, okrevanje celotnega telesa postane težje. Podpora grlu, ki pomaga tudi počitku, zato pogosto pomeni več kot le trenutna pomiritev.</p>'],
            ]
        ),
        $entry(
            488,
            'sl',
            'Čaj proti driski: katere zeliščne mešanice imajo smisla in kako jih uporabljati previdno',
            'caj-proti-driski-katere-zeliscne-mesanice-imajo-smisla',
            'Zeliščni čaji so lahko del mirnejšega okrevanja prebave, vendar le, če jih uporabljamo kot podporo in ne kot zamenjavo za tekočino in osnovno previdnost. Tukaj je, kako jih vključiti v nežnejši načrt okrevanja brez pretiravanja.',
            '<ul><li>Čaj lahko pomaga občutku ugodja in umiritvi prebave, ni pa glavno orodje, ko telo izgublja tekočino.</li><li>Največja napaka je zanašati se samo na čaj in prezreti hidracijo ter opozorilne znake.</li><li>Pametnejši pristop uporablja zelišča kot del nežnega okrevanja, ne kot čudežno rešitev.</li></ul>',
            [
                ['question' => 'Ali ima čaj pri driski smisel?', 'answer' => 'Lahko, kot del nežnejšega okrevanja in občutka ugodja, ne pa kot zamenjava za tekočino in previdnost.'],
                ['question' => 'Kaj je pomembnejše od čaja?', 'answer' => 'Hidracija, počitek prebave in spremljanje, kako se simptomi spreminjajo.'],
                ['question' => 'Ali je lahko aloe vera del podpore?', 'answer' => 'Včasih, vendar le previdno in ob pozornosti na to, kaj telo dejansko dobro prenaša.'],
                ['question' => 'Katera je pogosta napaka?', 'answer' => 'Misliti, da “naravno” samodejno pomeni dovolj za vsak prebavni problem.'],
            ],
            'Čaj proti driski: kdaj pomaga in kdaj je širša slika veliko pomembnejša',
            'Spoznajte, kako lahko zeliščni čaj pomaga pri okrevanju po driski in zakaj sta tekočina ter previdnost še vedno ključni.',
            'Čaj proti driski',
            [
                ['heading' => 'Zakaj je lahko zeliščni čaj koristen, ne pa osrednji', 'html' => '<p>Topla zeliščna podpora lahko okrevanje naredi bolj mirno in prijetno, vendar telo še vedno najprej potrebuje dovolj tekočine in nežnost. Čaj najbolje deluje takrat, ko spoštuje svojo vlogo in ne poskuša nadomestiti pomembnejših osnov.</p>'],
                ['heading' => 'Zakaj prebavi koristi več preprostosti', 'html' => '<p>Črevesje se praviloma bolje odzove na mir kot na preveč eksperimentiranja. Zato pri okrevanju pogosto največ naredi manjši in nežnejši nabor podpore.</p>'],
            ]
        ),
    ],
];
