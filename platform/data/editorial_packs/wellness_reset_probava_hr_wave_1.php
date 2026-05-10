<?php

declare(strict_types=1);

$entry = static fn (
    int $id,
    string $title,
    string $excerpt,
    string $summaryHtml,
    array $faqItems,
    string $metaTitle,
    string $metaDescription,
    string $breadcrumbTitle
): array => [
    'content_translation_id' => $id,
    'title' => $title,
    'excerpt' => $excerpt,
    'summary_html' => $summaryHtml,
    'faq_items' => $faqItems,
    'meta_title' => $metaTitle,
    'meta_description' => $metaDescription,
    'breadcrumb_title' => $breadcrumbTitle,
];

return [
    'key' => 'wellness-reset-probava-hr-wave-1',
    'name' => 'Wellness, reset i probavni vodiči (HR) - prvi val',
    'notes' => 'Veći ručni premium editorial pack za burnout, jogu, mršavljenje i ciljane simptom vodiče za probavu i respiratorne tegobe.',
    'entries' => [
        $entry(
            142,
            'Detoksikacija teških metala: gdje kelatori i aloe vera imaju smisla, a gdje ne',
            'Teški metali su tema koja lako sklizne u paniku i pretjerane internetske tvrdnje. Ovdje je kako o kelatorima, podršci organizmu i detoksikaciji razmišljati odgovornije, bez stvaranja novih problema kroz loše vođene pokušaje čišćenja.',
            '<ul><li>Detoksikacija teških metala nije kućni eksperiment nego tema koja traži više opreza i konteksta.</li><li>Najveća pogreška je samostalno posezati za agresivnim protokolima bez jasne potrebe i stručnog okvira.</li><li>Pametniji pristup razlikuje opću brigu o organizmu od stvarnih medicinskih situacija koje traže poseban tretman.</li></ul>',
            [
                ['question' => 'Jesu li teški metali tema za svakoga?', 'answer' => 'Ne na isti način. Važno je razlikovati opću zabrinutost od stvarno potvrđene izloženosti ili problema.'],
                ['question' => 'Mogu li kelatori biti riskantni?', 'answer' => 'Da, posebno ako se koriste bez stručnog nadzora i bez razumijevanja što se zapravo radi.'],
                ['question' => 'Gdje aloe vera ima mjesto?', 'answer' => 'Više kao dio opće rutine nježnije podrške organizmu, a ne kao glavni alat za specifične medicinske intervencije.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Ući u agresivan “detoks” na temelju straha, a ne na temelju stvarnih podataka i potrebe.'],
            ],
            'Detoksikacija teških metala: kako razlikovati podršku organizmu od opasnog eksperimenta',
            'Saznajte kako odgovornije gledati na teške metale, kelatore i detoks protokole bez internetske panike.',
            'Teški metali'
        ),
        $entry(
            171,
            'Kada je vrijeme za odmor? Pregorijevanje i signali koje ne treba ignorirati',
            'Pregorijevanje rijetko dolazi preko noći. Češće se nakuplja kroz tjedne i mjesece kad tijelo i glava više nemaju prostora za oporavak. Ovdje je kako prepoznati rane znakove i zaustaviti klizanje prema dubljoj iscrpljenosti.',
            '<ul><li>Burnout najčešće počinje puno prije nego što ljudi sebi priznaju da više ne mogu normalno funkcionirati.</li><li>Najveća pogreška je odmor promatrati kao slabost ili luksuz umjesto kao nužnu zaštitu kapaciteta.</li><li>Pametniji pristup prepoznaje signale ranije i vraća male prostore oporavka prije potpunog sloma sustava.</li></ul>',
            [
                ['question' => 'Kako prepoznati rani burnout?', 'answer' => 'Kroz stalnu iscrpljenost, pad motivacije, razdražljivost i osjećaj da odmor više ne puni baterije kao prije.'],
                ['question' => 'Je li dovoljno samo uzeti vikend?', 'answer' => 'Nekad nije, jer problem često leži u cijelom ritmu života i opterećenja, a ne samo u manjku slobodnih dana.'],
                ['question' => 'Zašto ljudi prekasno reagiraju?', 'answer' => 'Jer dugo funkcioniraju na inerciji, osjećaju obveze i uvjerenju da će “još malo izdržati”.'],
                ['question' => 'Što najviše pomaže?', 'answer' => 'Rani znakovi, granice, san, manje preopterećenja i vraćanje osnovnog osjećaja kontrole nad danom.'],
            ],
            'Pregorijevanje: kada je vrijeme za odmor i koje znakove ne treba ignorirati',
            'Prepoznajte rane znakove pregorijevanja i saznajte kako vratiti prostor za oporavak prije većeg sloma.',
            'Pregorijevanje'
        ),
        $entry(
            172,
            'Vodič za početnike u jogi: kako započeti bez pritiska i bez krive opreme',
            'Joga za početnike ne treba biti estetski projekt ni test fleksibilnosti. Najviše koristi daje kad vas uvede u disanje, prisutnost i pametnije kretanje bez nepotrebnog pritiska da odmah sve radite “savršeno”.',
            '<ul><li>Početak joge ima najviše smisla kad smanjuje napetost i vraća kontakt s tijelom, a ne kad stvara novi pritisak na izvedbu.</li><li>Najveća pogreška je misliti da morate biti fleksibilni, smireni i “spremni” prije prvog koraka.</li><li>Pametniji pristup bira jednostavnu praksu, dobar ritam i dovoljno nježan ulaz u naviku.</li></ul>',
            [
                ['question' => 'Treba li posebna oprema?', 'answer' => 'Ne puno. Za početak su dovoljni prostirka, udobna odjeća i malo prostora.'],
                ['question' => 'Moram li biti fleksibilan za jogu?', 'answer' => 'Ne. Upravo mnogi ljudi kreću u jogu kako bi postupno vratili više mobilnosti i mira.'],
                ['question' => 'Koliko često vježbati?', 'answer' => 'Bolje kratko i redovito nego rijetko i preambiciozno.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Uspoređivati svoj početak s naprednim prikazima na društvenim mrežama.'],
            ],
            'Joga za početnike: kako krenuti mirnije, jednostavnije i bez nepotrebnog pritiska',
            'Krenite u jogu na jednostavan način kroz disanje, osnove pokreta i rutinu koju je lako održati.',
            'Početnici u jogi'
        ),
        $entry(
            468,
            'Ozempic i mršavljenje: što treba razumjeti prije nego što ga netko idealizira',
            'Ozempic je postao centralna tema rasprava o mršavljenju, ali stvarna slika je složenija od viralnih rezultata “prije i poslije”. Ovdje je kako o tom lijeku razmišljati odgovornije, s više razumijevanja koristi, ograničenja i konteksta.',
            '<ul><li>Ozempic i slični lijekovi nisu trend alat nego ozbiljna terapijska tema koja traži kontekst i medicinsko vođenje.</li><li>Najveća pogreška je promatrati ih kao jednostavnu prečicu za mršavljenje bez razumijevanja kome su namijenjeni i pod kojim uvjetima.</li><li>Pametniji pristup gleda zdravstvenu sliku, navike i dugoročnu održivost, a ne samo brz rezultat na vagi.</li></ul>',
            [
                ['question' => 'Je li Ozempic univerzalno rješenje za mršavljenje?', 'answer' => 'Ne. Riječ je o lijeku koji ima specifične indikacije i treba ga promatrati u medicinskom kontekstu.'],
                ['question' => 'Zašto je oko njega toliko hypea?', 'answer' => 'Zbog brzih vidljivih rezultata u javnosti i dojma da postoji jednostavan odgovor na kompleksan problem.'],
                ['question' => 'Što ljudi često zanemare?', 'answer' => 'Nuspojave, praćenje, zdravstveni kontekst i pitanje što se događa dugoročno.'],
                ['question' => 'Što je pametniji okvir?', 'answer' => 'O tome razgovarati kao o terapijskoj opciji, a ne kao o instant wellness trendu.'],
            ],
            'Ozempic i mršavljenje: koristi, ograničenja i zašto nije samo internetski trend',
            'Razumijte kako odgovornije gledati na Ozempic i mršavljenje kroz medicinski kontekst, a ne samo hype.',
            'Ozempic'
        ),
        $entry(
            469,
            'Najbolja dijeta za mršavljenje: kako usporediti planove bez da izgubite živce',
            'Najbolja dijeta nije ona koja zvuči najstrože nego ona koju možete pratiti dovoljno dugo da napravi razliku. Ovdje je kako usporediti popularne planove mršavljenja bez ulaska u još jedan krug frustracije i odustajanja.',
            '<ul><li>Najbolja dijeta je ona koja odgovara vašem ritmu, zdravlju i stvarnoj mogućnosti da je održite.</li><li>Najveća pogreška je birati plan prema brzini obećanog rezultata umjesto prema održivosti.</li><li>Pametniji pristup uspoređuje razinu restrikcije, sitost, fleksibilnost i psihološku cijenu plana.</li></ul>',
            [
                ['question' => 'Postoji li jedna najbolja dijeta za sve?', 'answer' => 'Ne. Ljudi se razlikuju po navikama, zdravlju, ritmu i tome što realno mogu održavati.'],
                ['question' => 'Zašto stroži planovi često ne uspiju?', 'answer' => 'Jer su kratkoročno izvedivi, ali dugoročno prezahtjevni za svakodnevni život.'],
                ['question' => 'Što je važnije od naziva dijete?', 'answer' => 'Koliko je plan održiv, koliko vas drži sitima i kako utječe na odnos prema hrani.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Mijenjati planove prečesto bez da se ijednom da dovoljno vremena.'],
            ],
            'Najbolja dijeta za mršavljenje: kako izabrati plan koji stvarno možete živjeti',
            'Usporedite planove mršavljenja kroz održivost, sitost i stvarni život umjesto kroz brza obećanja.',
            'Najbolja dijeta'
        ),
        $entry(
            484,
            'Prirodni lijek za žgaravicu: aloe vera i strategije koje stvarno smiruju',
            'Kod žgaravice više vrijedi razumjeti obrazac nego stalno gasiti vatru nasumičnim rješenjima. Ovdje je kako hrana, ritam obroka i odabrana podrška poput aloe vere mogu pomoći da olakšanje bude smislenije i stabilnije.',
            '<ul><li>Žgaravica najčešće traži promjenu navika, a ne samo povremeno olakšanje simptoma.</li><li>Najveća pogreška je ignorirati okidače i očekivati da jedan pripravak riješi obrazac koji se stalno vraća.</li><li>Pametniji pristup gleda obroke, vrijeme jedenja, količinu hrane i položaj tijela nakon jela.</li></ul>',
            [
                ['question' => 'Može li aloe vera pomoći kod žgaravice?', 'answer' => 'Kod nekih ljudi može biti dio blaže podrške, ali nije zamjena za razumijevanje okidača i obrazaca.'],
                ['question' => 'Što najčešće pogoršava žgaravicu?', 'answer' => 'Veliki obroci, kasno jedenje, masna hrana, stres i ležanje odmah nakon jela.'],
                ['question' => 'Kada treba biti oprezniji?', 'answer' => 'Kad se simptomi javljaju često, noću ili su praćeni drugim ozbiljnijim tegobama.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Gasiti simptome bez promjene ritma obroka i ponašanja koje ih stalno vraća.'],
            ],
            'Žgaravica: prirodna podrška, aloe vera i navike koje stvarno donose olakšanje',
            'Saznajte kako kod žgaravice pomoći kroz navike, prehranu i blažu podršku poput aloe vere.',
            'Žgaravica'
        ),
        $entry(
            485,
            'Prirodni lijekovi protiv proljeva: aloe vera, probiotici i pametniji oporavak',
            'Kad se pojavi proljev, tijelo najviše treba smirenje, tekućinu i oporavak probavnog sustava, a ne paniku i nasumično miješanje svega iz kućne ljekarne. Ovdje je što prirodna podrška stvarno može dati, a gdje treba stati.',
            '<ul><li>Proljev traži hidraciju, oprez i razumijevanje uzroka, ne samo brzo zaustavljanje simptoma pod svaku cijenu.</li><li>Najveća pogreška je zanemariti gubitak tekućine ili predugo čekati kad se stanje pogoršava.</li><li>Pametniji pristup kombinira lagan oporavak probave, ciljanu podršku i pažnju na znakove upozorenja.</li></ul>',
            [
                ['question' => 'Jesu li probiotici korisni?', 'answer' => 'Mogu biti, ovisno o uzroku i trenutku kada se uvode kao dio oporavka.'],
                ['question' => 'Što je prvo važno kod proljeva?', 'answer' => 'Tekućina, odmor probave i praćenje znakova dehidracije.'],
                ['question' => 'Može li aloe vera pomoći?', 'answer' => 'Ponekad kao dio blažeg pristupa oporavku, ali nije glavno rješenje za svaku situaciju.'],
                ['question' => 'Kada treba tražiti dodatnu procjenu?', 'answer' => 'Kad se simptomi pogoršavaju, traju dulje ili su praćeni alarmantnim znakovima.'],
            ],
            'Proljev: prirodna podrška kroz tekućinu, probiotike i oprezan oporavak',
            'Razumijte kako kod proljeva pomoći hidracijom, probioticima i smirenijim pristupom oporavku probave.',
            'Proljev'
        ),
        $entry(
            486,
            'Kako zaustaviti povraćanje prirodnim putem: đumbir, rehidracija i manje improvizacije',
            'Povraćanje brzo iscrpi tijelo i zato je važnije smiriti gubitak tekućine i oporaviti ritam nego tražiti agresivne trikove. Ovdje je kako prirodno pomoći bez dodatnog opterećivanja probavnog sustava.',
            '<ul><li>Kod povraćanja najveći prioritet je smiriti unos, zaštititi tekućinu i dati tijelu vremena da se stabilizira.</li><li>Najveća pogreška je prerano uvoditi tešku hranu ili ignorirati znakove dehidracije.</li><li>Pametniji pristup ide postupno: mali gutljaji, mir, đumbir kad ima smisla i pažljivo praćenje stanja.</li></ul>',
            [
                ['question' => 'Što je najvažnije nakon povraćanja?', 'answer' => 'Postupno vraćati tekućinu i ne opteretiti probavu prebrzo hranom ili velikim količinama pića.'],
                ['question' => 'Može li đumbir pomoći?', 'answer' => 'Kod nekih ljudi može smiriti osjećaj mučnine ako se koristi pažljivo i u prikladnom obliku.'],
                ['question' => 'Kada treba biti oprezniji?', 'answer' => 'Kad se stanje ne smiruje, kad ne možete zadržati tekućinu ili se javljaju znakovi jače slabosti.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Vrlo brzo pojesti normalan obrok i tako ponovno izazvati iritaciju probavnog sustava.'],
            ],
            'Povraćanje: prirodna podrška kroz rehidraciju, đumbir i oprezan povratak hrane',
            'Saznajte kako kod povraćanja pomoći kroz tekućinu, đumbir i nježniji oporavak probavnog sustava.',
            'Povraćanje'
        ),
        $entry(
            487,
            'Prirodni lijek za kašalj: aloe vera, med i biljke koje stvarno smiruju grlo',
            'Kašalj nije uvijek isti problem i zato mu ne pomaže uvijek isti pristup. Ovdje je kako razlikovati smirenje grla od stvarnog oporavka i gdje aloe vera, med i biljne strategije mogu imati smisla.',
            '<ul><li>Prirodna pomoć kašlju ima najviše smisla kad odgovara vrsti iritacije i ukupnoj slici simptoma.</li><li>Najveća pogreška je tretirati svaki kašalj isto, bez obzira na trajanje, uzrok i dodatne znakove.</li><li>Pametniji pristup smiruje grlo, štiti san i promatra kad se situacija mijenja ili pogoršava.</li></ul>',
            [
                ['question' => 'Može li med pomoći kod kašlja?', 'answer' => 'Kod nadraženog grla često može pružiti osjećaj smirenja i ublažiti iritaciju.'],
                ['question' => 'Gdje aloe vera ima smisla?', 'answer' => 'Više kao dio umirujuće potpore grlu i osjećaju nadraženosti nego kao glavno rješenje za sve uzroke kašlja.'],
                ['question' => 'Zašto nije svaki kašalj isti?', 'answer' => 'Jer može dolaziti iz različitih uzroka i zato različito reagirati na podršku.'],
                ['question' => 'Kada treba dodatni oprez?', 'answer' => 'Ako kašalj traje dugo, pogoršava se ili dolazi s težim simptomima.'],
            ],
            'Kašalj: prirodna podrška kroz med, aloe veru i smirenje grla bez pretjerivanja',
            'Otkrijte kako prirodnije pristupiti kašlju kroz med, biljke i blažu podršku za nadraženo grlo.',
            'Kašalj'
        ),
        $entry(
            488,
            'Čaj protiv proljeva: koje biljne mješavine imaju smisla i kako ih koristiti oprezno',
            'Biljni čajevi kod proljeva mogu biti dio smirenijeg oporavka, ali samo ako se koriste kao podrška, a ne kao zamjena za tekućinu i osnovni oprez. Ovdje je kako ih uklopiti u blaži plan oporavka bez pretjerivanja.',
            '<ul><li>Čaj može pomoći osjećaju ugode i smirenju probave, ali nije glavni alat kad tijelo gubi tekućinu.</li><li>Najveća pogreška je osloniti se samo na čaj i zanemariti hidraciju i znakove pogoršanja.</li><li>Pametniji pristup koristi biljke kao dio nježnog oporavka, ne kao čudesan popravak svega.</li></ul>',
            [
                ['question' => 'Ima li čaj smisla kod proljeva?', 'answer' => 'Može imati kao dio blažeg oporavka i osjećaja ugode, ali ne kao zamjena za tekućinu i pažnju.'],
                ['question' => 'Što je važnije od čaja?', 'answer' => 'Dovoljno tekućine, odmor probave i procjena kako se simptomi razvijaju.'],
                ['question' => 'Može li aloe vera biti dio podrške?', 'answer' => 'Ponekad može, ali u takvim situacijama treba posebno paziti što tijelo podnosi.'],
                ['question' => 'Koja je česta greška?', 'answer' => 'Misliti da je “prirodno” automatski dovoljno za svaku probavnu tegobu.'],
            ],
            'Čaj protiv proljeva: kada pomaže, a kada treba gledati puno širu sliku',
            'Saznajte kako biljni čajevi mogu pomoći kod proljeva i zašto hidracija i oprez ostaju najvažniji.',
            'Čaj protiv proljeva'
        ),
    ],
];
