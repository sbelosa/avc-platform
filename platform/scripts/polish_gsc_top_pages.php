<?php

declare(strict_types=1);

use Avc\Core\Database;
use Avc\Services\Content\StructuredContentService;

$rootPath = dirname(__DIR__);

spl_autoload_register(static function (string $class) use ($rootPath): void {
    $prefix = 'Avc\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $path = $rootPath . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($path)) {
        require_once $path;
    }
});

$options = parseArguments($argv);
$pagesPath = (string) ($options['pages'] ?? ($rootPath . '/storage/imports/gsc_pages_20260510.csv'));
$queriesPath = (string) ($options['queries'] ?? ($rootPath . '/storage/imports/gsc_queries_20260510.csv'));
$limit = max(1, (int) ($options['limit'] ?? 100));
$dryRun = (bool) ($options['dry-run'] ?? false);

$config = require $rootPath . '/config/app.php';
$connection = Database::connection($config);

if ($connection === null) {
    fwrite(STDERR, 'Database connection failed.' . PHP_EOL);
    exit(1);
}

if (!is_file($pagesPath)) {
    fwrite(STDERR, 'Pages CSV not found: ' . $pagesPath . PHP_EOL);
    exit(1);
}

$structuredContent = new StructuredContentService();
$baseUrl = rtrim((string) ($config['base_url'] ?? 'https://aloavera-centar.com'), '/');
$pages = loadGscPages($pagesPath, $limit);
$queries = is_file($queriesPath) ? loadGscQueries($queriesPath, 250) : [];

$report = [
    'generated_at' => date('c'),
    'dry_run' => $dryRun,
    'pages_csv' => $pagesPath,
    'queries_csv' => is_file($queriesPath) ? $queriesPath : null,
    'requested_limit' => $limit,
    'loaded_pages' => count($pages),
    'checked' => 0,
    'updated' => 0,
    'would_update' => 0,
    'skipped' => [],
    'intent_counts' => [],
    'query_intent_clusters' => summarizeQueryIntents($queries),
    'examples' => [],
];

foreach ($pages as $page) {
    $report['checked']++;
    $routePath = (string) ($page['route_path'] ?? '');
    $row = fetchContentByRoutePath($connection, $routePath);

    if ($row === null) {
        $report['skipped'][] = [
            'route_path' => $routePath,
            'reason' => 'route_not_found',
            'rank' => $page['rank'],
        ];
        continue;
    }

    if ((int) ($row['http_status_code'] ?? 200) !== 200 || (int) ($row['content_translation_id'] ?? 0) <= 0) {
        $report['skipped'][] = [
            'route_path' => $routePath,
            'reason' => 'not_public_content_route',
            'rank' => $page['rank'],
            'http_status_code' => (int) ($row['http_status_code'] ?? 0),
        ];
        continue;
    }

    if ((string) ($row['content_type'] ?? '') !== 'article') {
        $report['skipped'][] = [
            'route_path' => $routePath,
            'reason' => 'not_article',
            'rank' => $page['rank'],
            'content_type' => (string) ($row['content_type'] ?? ''),
        ];
        continue;
    }

    if ((string) ($row['lifecycle_status'] ?? '') !== 'published') {
        $report['skipped'][] = [
            'route_path' => $routePath,
            'reason' => 'not_published',
            'rank' => $page['rank'],
        ];
        continue;
    }

    $payload = buildPolishPayload($row, $page, $structuredContent, $baseUrl);
    $intent = (string) $payload['intent'];
    $report['intent_counts'][$intent] = ($report['intent_counts'][$intent] ?? 0) + 1;

    if (!hasChanges($row, $payload)) {
        $report['skipped'][] = [
            'route_path' => $routePath,
            'reason' => 'already_polished',
            'rank' => $page['rank'],
            'intent' => $intent,
        ];
        continue;
    }

    if ($dryRun) {
        $report['would_update']++;
    } else {
        $connection->begin_transaction();

        try {
            saveRevision($connection, $row, 'gsc_top_pages_polish');
            updateTranslation($connection, (int) ($row['content_translation_id'] ?? 0), $payload);
            upsertSeo($connection, (int) ($row['content_translation_id'] ?? 0), $payload);
            $connection->commit();
            $report['updated']++;
        } catch (Throwable $exception) {
            $connection->rollback();
            throw $exception;
        }
    }

    if (count($report['examples']) < 20) {
        $report['examples'][] = [
            'rank' => $page['rank'],
            'route_path' => $routePath,
            'language_code' => (string) ($row['language_code'] ?? ''),
            'intent' => $intent,
            'clicks' => $page['clicks'],
            'impressions' => $page['impressions'],
            'ctr' => $page['ctr'],
            'position' => $page['position'],
            'title' => (string) ($row['title'] ?? ''),
            'meta_description' => $payload['meta_description'],
        ];
    }
}

ksort($report['intent_counts']);

$reportPath = $rootPath . '/storage/reports/gsc_top_pages_polish_' . date('Ymd_His') . ($dryRun ? '_dry_run' : '') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}

file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report + ['report_path' => $reportPath], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function parseArguments(array $argv): array
{
    $options = [];

    foreach (array_slice($argv, 1) as $argument) {
        if ($argument === '--dry-run') {
            $options['dry-run'] = true;
            continue;
        }

        if (str_starts_with($argument, '--') && str_contains($argument, '=')) {
            [$key, $value] = explode('=', substr($argument, 2), 2);
            $options[$key] = $value;
        }
    }

    return $options;
}

function loadGscPages(string $path, int $limit): array
{
    $handle = fopen($path, 'rb');
    if ($handle === false) {
        return [];
    }

    $header = fgetcsv($handle);
    $pages = [];
    $rank = 0;

    while (($row = fgetcsv($handle)) !== false) {
        if ($row === [] || trim((string) ($row[0] ?? '')) === '') {
            continue;
        }

        $rank++;
        $url = trim((string) ($row[0] ?? ''));
        $pages[] = [
            'rank' => $rank,
            'url' => $url,
            'route_path' => normalizeRoutePath($url),
            'clicks' => parseIntValue((string) ($row[1] ?? '0')),
            'impressions' => parseIntValue((string) ($row[2] ?? '0')),
            'ctr' => parsePercentValue((string) ($row[3] ?? '0')),
            'position' => parseFloatValue((string) ($row[4] ?? '0')),
        ];

        if (count($pages) >= $limit) {
            break;
        }
    }

    fclose($handle);

    return $pages;
}

function loadGscQueries(string $path, int $limit): array
{
    $handle = fopen($path, 'rb');
    if ($handle === false) {
        return [];
    }

    fgetcsv($handle);
    $queries = [];
    $rank = 0;

    while (($row = fgetcsv($handle)) !== false) {
        if ($row === [] || trim((string) ($row[0] ?? '')) === '') {
            continue;
        }

        $rank++;
        $queries[] = [
            'rank' => $rank,
            'query' => trim((string) ($row[0] ?? '')),
            'clicks' => parseIntValue((string) ($row[1] ?? '0')),
            'impressions' => parseIntValue((string) ($row[2] ?? '0')),
            'ctr' => parsePercentValue((string) ($row[3] ?? '0')),
            'position' => parseFloatValue((string) ($row[4] ?? '0')),
        ];

        if (count($queries) >= $limit) {
            break;
        }
    }

    fclose($handle);

    return $queries;
}

function normalizeRoutePath(string $url): string
{
    $path = parse_url($url, PHP_URL_PATH);
    $path = is_string($path) && $path !== '' ? $path : $url;
    $path = '/' . ltrim(rawurldecode($path), '/');
    $path = preg_replace('#/+#', '/', $path) ?? $path;

    if ($path !== '/' && !str_ends_with($path, '/')) {
        $path .= '/';
    }

    return $path;
}

function parseIntValue(string $value): int
{
    $value = preg_replace('/[^\d-]/', '', $value) ?? '0';

    return (int) $value;
}

function parseFloatValue(string $value): float
{
    $value = str_replace(',', '.', trim($value));
    $value = preg_replace('/[^0-9.\-]/', '', $value) ?? '0';

    return (float) $value;
}

function parsePercentValue(string $value): float
{
    return parseFloatValue(str_replace('%', '', $value));
}

function fetchContentByRoutePath(mysqli $connection, string $routePath): ?array
{
    $paths = array_values(array_unique([
        $routePath,
        rtrim($routePath, '/'),
        rtrim($routePath, '/') . '/',
    ]));

    $statement = $connection->prepare(
        "SELECT
            cr.content_route_id,
            cr.route_path,
            cr.route_type,
            cr.http_status_code,
            cr.redirect_target_path,
            ct.content_translation_id,
            ct.content_item_id,
            ct.source_wp_post_id,
            ct.language_code,
            ct.title,
            ct.slug,
            ct.excerpt,
            ct.body_html,
            ct.summary_html,
            ct.faq_json,
            ct.featured_image_path,
            ct.published_at,
            ci.translation_group_id,
            ci.content_type,
            ci.lifecycle_status,
            sm.meta_title,
            sm.meta_description,
            sm.canonical_url,
            sm.robots_index,
            sm.robots_follow,
            sm.breadcrumb_title,
            sm.focus_keyword,
            sm.open_graph_title,
            sm.open_graph_description,
            sm.open_graph_image_path,
            sm.schema_json
         FROM content_routes cr
         LEFT JOIN content_translations ct ON ct.content_translation_id = cr.content_translation_id
         LEFT JOIN content_items ci ON ci.content_item_id = ct.content_item_id
         LEFT JOIN seo_metadata sm ON sm.content_translation_id = ct.content_translation_id
         WHERE cr.route_path = ?
         LIMIT 1"
    );

    foreach ($paths as $path) {
        $statement->bind_param('s', $path);
        $statement->execute();
        $result = $statement->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        if (is_array($row)) {
            $statement->close();

            return $row;
        }
    }

    $statement->close();

    return null;
}

function buildPolishPayload(array $row, array $page, StructuredContentService $structuredContent, string $baseUrl): array
{
    $languageCode = normalizeLanguage((string) ($row['language_code'] ?? 'hr'));
    $title = cleanText((string) ($row['title'] ?? ''));
    $bodyHtml = (string) ($row['body_html'] ?? '');
    $haystack = implode(' ', [
        (string) ($page['route_path'] ?? ''),
        $title,
    ]);
    $intent = classifyIntent($haystack);
    $copy = intentCopy($intent, $languageCode, $title);
    $bridgeHtml = buildBridgeHtml($copy);
    $polishedBody = injectBridge(removeExistingBridge($bodyHtml), $bridgeHtml);
    $summaryHtml = buildSummaryHtml($copy);
    $faqJson = $structuredContent->encodeFaqItems($copy['faq_items']);
    $metaDescription = buildMetaDescription($copy, $title, (string) ($row['meta_description'] ?? ''));
    $canonicalUrl = $baseUrl . (string) ($row['route_path'] ?? $page['route_path']);

    return [
        'intent' => $intent,
        'title' => $title,
        'excerpt' => buildExcerpt($copy),
        'body_html' => $polishedBody,
        'summary_html' => $summaryHtml,
        'faq_json' => $faqJson,
        'featured_image_path' => (string) ($row['featured_image_path'] ?? ''),
        'meta_title' => buildMetaTitle($row, $title, $languageCode),
        'meta_description' => $metaDescription,
        'canonical_url' => $canonicalUrl,
        'robots_index' => 1,
        'robots_follow' => 1,
        'breadcrumb_title' => $title,
        'focus_keyword' => focusKeyword($intent, $languageCode),
        'open_graph_title' => buildOpenGraphTitle($row, $title),
        'open_graph_description' => $metaDescription,
        'open_graph_image_path' => (string) ($row['open_graph_image_path'] ?? $row['featured_image_path'] ?? ''),
        'schema_json' => (string) ($row['schema_json'] ?? ''),
    ];
}

function normalizeLanguage(string $languageCode): string
{
    $languageCode = strtolower(trim($languageCode));

    return in_array($languageCode, ['hr', 'en', 'sl'], true) ? $languageCode : 'hr';
}

function classifyIntent(string $text): string
{
    $haystack = normalizeSearchText($text);
    $intents = [
        'aloe_purchase' => ['gdje kupiti', 'where to buy', 'kje kupiti', 'kupiti aloe', 'aloe u stanu', 'aloe vera u stanu', 'aloe vera gdje kupiti', 'biljka aloe', 'aloe plant', 'rezati list', 'presaditi aloe'],
        'thyroid' => ['nizak tsh', 'tsh', 'stitnjaca', 'thyroid', 'hormon', 'jod', 'iodine', 'selen', 'selenium'],
        'weight' => ['keto', 'ketogena', 'ketogenic', 'dieta', 'diet', 'jedilnik', 'mrsav', 'mršav', 'mrsat', 'huj', 'weight', 'garcinia', 'clean 9', 'c9', 'detox'],
        'skin' => ['dermatitis', 'ekcem', 'eczema', 'koza', 'skin', 'pristici', 'pristici', 'crvene fleke', 'osip', 'acne', 'akne', 'psorijaza', 'opekline', 'burns', 'lice', 'face', 'maska', 'mask', 'njega', 'nega'],
        'digestion' => ['probava', 'prebava', 'digestion', 'crijeva', 'gut', 'zeludac', 'stomach', 'zgaravica', 'proljev', 'diarrhea', 'hemoroidi', 'vlakna', 'fiber', 'kefir', 'povracanje', 'povraćanje', 'mucnina', 'mučnina', 'aloe berry', 'berry nectar', 'aloe nectar'],
        'inflammation' => ['upala', 'inflammation', 'kurkuma', 'turmeric', 'dumbir', 'ginger', 'cimet', 'cinnamon', 'detoks', 'jetra', 'liver', 'oregano'],
        'energy' => ['maca', 'energija', 'energy', 'umor', 'fatigue', 'fokus', 'focus', 'stres', 'libido', 'vitalnost'],
        'immunity' => ['imunitet', 'immunity', 'immune', 'otpornost', 'vitamin', 'cink', 'zinc', 'alergija', 'allergy'],
        'respiratory' => ['sirup od luka', 'luk i med', 'kasalj', 'kašalj', 'cough', 'grlo', 'throat', 'sirup', 'luk', 'med', 'honey', 'propolis', 'timijan', 'thyme', 'prehlada'],
    ];
    $scores = [];

    foreach ($intents as $intent => $needles) {
        foreach ($needles as $needle) {
            $score = keywordScore($haystack, $needle);
            if ($score > 0) {
                $scores[$intent] = ($scores[$intent] ?? 0) + $score;
            }
        }
    }

    if ($scores === []) {
        return 'general';
    }

    arsort($scores);

    return (string) array_key_first($scores);
}

function keywordScore(string $haystack, string $needle): int
{
    $needle = normalizeSearchText($needle);
    if ($needle === '') {
        return 0;
    }

    $hasPhrase = str_contains($needle, ' ');
    if ($hasPhrase && str_contains($haystack, $needle)) {
        return 3;
    }

    if (!$hasPhrase && mb_strlen($needle, 'UTF-8') >= 5 && str_contains($haystack, $needle)) {
        return 2;
    }

    $pattern = '/(?:^|[^\p{L}\p{N}])' . preg_quote($needle, '/') . '(?:$|[^\p{L}\p{N}])/u';

    return preg_match($pattern, $haystack) === 1 ? 1 : 0;
}

function intentCopy(string $intent, string $languageCode, string $title): array
{
    $topic = trim($title) !== '' ? $title : topicFallback($languageCode);

    $copies = [
        'hr' => [
            'respiratory' => [
                'heading' => 'Kako ovu temu pretvoriti u dobar sljedeći korak',
                'intro' => 'Ako čitaš o kašlju, grlu ili sezonskoj otpornosti, najkorisnije je prvo razjasniti što želiš podržati: mirniju rutinu, ugodniji osjećaj u grlu ili svakodnevnu prehrambenu podršku.',
                'bullets' => [
                    'Kreni od simptoma i trajanja, a kod jačih ili dugih tegoba obavezno uključi liječnika.',
                    'Usporedi proizvode koji se najčešće vežu uz med, propolis, timijan i vitaminsku podršku.',
                    'Ako nisi siguran što ima smisla za tebe, pošalji pitanje i dobit ćeš jednostavniji prijedlog.',
                ],
                'summary' => [
                    'Razumjet ćeš gdje kućna rutina može pomoći, a gdje je bolje potražiti stručni savjet.',
                    'Dobit ćeš jasniji put prema proizvodima koji se prirodno vežu uz ovu temu.',
                    'Cilj nije brzinska kupnja, nego izbor koji ima smisla za tvoju situaciju.',
                ],
                'faq_items' => [
                    ['question' => 'Koji je najpametniji prvi korak?', 'answer' => 'Prvo razjasni koliko tegoba traje, što već koristiš i trebaš li stručni savjet. Nakon toga je lakše usporediti proizvode koji mogu podržati svakodnevnu rutinu.'],
                    ['question' => 'Mogu li odmah naručiti preporučeni proizvod?', 'answer' => 'Možeš, ali preporučujem da prije kupnje pogledaš opis proizvoda i provjeriš odgovara li tvojoj dobi, navikama i eventualnim terapijama.'],
                    ['question' => 'Što ako nisam siguran što odabrati?', 'answer' => 'Pošalji kratko pitanje kroz preporuku. Dovoljno je napisati cilj, što već koristiš i oko čega se dvoumiš.'],
                ],
                'meta' => 'Razjasni temu, usporedi prirodnu dnevnu podršku i pronađi smislen sljedeći korak prema Forever proizvodima.',
                'focus_keyword' => 'prirodna podrška kašlju i imunitetu',
            ],
            'weight' => [
                'heading' => 'Prije plana prehrane, odredi što želiš održati svaki dan',
                'intro' => 'Kod keto prehrane, mršavljenja ili detoksa najviše pomaže rutina koju stvarno možeš ponavljati. Zato ovaj članak gledaj kao orijentir za izbor jednostavnijeg plana, obroka ili dodatne podrške.',
                'bullets' => [
                    'Prvo provjeri želiš li bolju strukturu obroka, kontrolu apetita ili lakši početak.',
                    'Usporedi proizvode prema načinu korištenja, a ne samo prema obećanju rezultata.',
                    'Ako već imaš zdravstveno stanje ili terapiju, prije veće promjene prehrane pitaj stručnu osobu.',
                ],
                'summary' => [
                    'Dobit ćeš jasniju sliku što je korisno za prehranu, a što za naviku i organizaciju.',
                    'Preporuke proizvoda vode prema praktičnijem izboru za početak.',
                    'Najbolji izbor je onaj koji možeš uklopiti bez previše komplikacija.',
                ],
                'faq_items' => [
                    ['question' => 'Što odabrati ako tek počinjem?', 'answer' => 'Ako ti treba struktura, gledaj proizvode i programe koji olakšavaju prve dane. Ako već imaš plan, fokusiraj se na ono što ti nedostaje u rutini.'],
                    ['question' => 'Je li dovoljno kupiti jedan proizvod?', 'answer' => 'Proizvod može pomoći kao dio rutine, ali rezultat najviše ovisi o prehrani, kretanju, spavanju i dosljednosti.'],
                    ['question' => 'Kada trebam dodatno pitati za preporuku?', 'answer' => 'Ako ne znaš trebaš li program, proteinsku podršku ili nešto za apetit, pošalji kratko pitanje i opiši cilj.'],
                ],
                'meta' => 'Usporedi prehrambenu rutinu, keto ili detoks pristup i pronađi Forever proizvod koji se realno uklapa u tvoj cilj.',
                'focus_keyword' => 'Forever proizvodi za prehranu i kontrolu težine',
            ],
            'thyroid' => [
                'heading' => 'Kod hormona je važno birati mirno i informirano',
                'intro' => 'Teme poput TSH-a, štitnjače i energije ne treba rješavati napamet. Članak ti pomaže razumjeti kontekst, a preporuke proizvoda služe samo kao podrška svakodnevnim navikama, ne kao zamjena za dijagnozu ili terapiju.',
                'bullets' => [
                    'Kod nalaza i simptoma prvo se vodi liječničkim savjetom.',
                    'Za rutinu možeš usporediti proizvode vezane uz minerale, vitamine i dnevnu vitalnost.',
                    'Ako koristiš terapiju, prije dodataka provjeri moguće interakcije sa stručnom osobom.',
                ],
                'summary' => [
                    'Razdvojit ćeš informativni dio od odluka koje traže liječnički savjet.',
                    'Lakše ćeš vidjeti koja dnevna podrška ima smisla uz tvoje navike.',
                    'Preporuka proizvoda ostaje praktična, oprezna i vezana uz stvarnu potrebu.',
                ],
                'faq_items' => [
                    ['question' => 'Mogu li proizvodi zamijeniti terapiju za štitnjaču?', 'answer' => 'Ne. Kod štitnjače i hormonskih nalaza vodi se liječničkom preporukom. Proizvodi mogu biti samo dio opće prehrambene i dnevne podrške.'],
                    ['question' => 'Na što trebam paziti prije dodataka?', 'answer' => 'Važni su nalazi, terapija, trudnoća, dojenje i druga stanja. Ako postoji terapija, provjeri dodatke sa stručnom osobom.'],
                    ['question' => 'Kako dobiti smislen prijedlog?', 'answer' => 'Napiši što želiš podržati, koristiš li terapiju i tražiš li energiju, minerale ili opću dnevnu rutinu.'],
                ],
                'meta' => 'Saznaj kako čitati temu TSH-a, štitnjače i dnevne vitalnosti te kada ima smisla usporediti Forever podršku.',
                'focus_keyword' => 'štitnjača TSH dnevna podrška',
            ],
            'skin' => [
                'heading' => 'Kožu je najbolje gledati kroz nježnu rutinu, ne kroz brzinsko rješenje',
                'intro' => 'Kod osjetljive kože, dermatitisa, osipa ili akni najviše pomaže smiren pristup: što izbjegavati, što njegovati i koji proizvod ima smisla kao dio svakodnevne njege.',
                'bullets' => [
                    'Prvo razjasni je li koža suha, nadražena, oštećena ili sklona reakcijama.',
                    'Usporedi proizvode za njegu prema namjeni: umirivanje, hidratacija, zaštita ili kolagen.',
                    'Ako se stanje širi, boli ili dugo traje, potraži stručni savjet.',
                ],
                'summary' => [
                    'Dobit ćeš mirniji okvir za odluku što koristiti na koži.',
                    'Preporuke su povezane s njegom, hidratacijom i svakodnevnom zaštitom.',
                    'Članak pomaže prije kupnje, posebno kada uspoređuješ više opcija.',
                ],
                'faq_items' => [
                    ['question' => 'Kako odabrati proizvod za osjetljivu kožu?', 'answer' => 'Kreni od potrebe: hidratacija, umirivanje, zaštita ili njega nakon iritacije. Zatim provjeri sastav i način korištenja.'],
                    ['question' => 'Što ako koža jako reagira?', 'answer' => 'Ako je reakcija jaka, bolna, mokri, širi se ili traje, bolje je pitati dermatologa prije eksperimentiranja s proizvodima.'],
                    ['question' => 'Mogu li pitati za preporuku?', 'answer' => 'Da. Napiši gdje je problem, kakav je osjećaj na koži i što si već koristio.'],
                ],
                'meta' => 'Praktičan vodič za osjetljivu kožu, dermatitis ili njegu uz jasniji izbor Forever proizvoda za rutinu.',
                'focus_keyword' => 'Forever njega osjetljive kože',
            ],
            'aloe_purchase' => [
                'heading' => 'Ako tražiš aloe, prvo odvoji biljku od gotove rutine',
                'intro' => 'Aloe vera može značiti kućnu biljku, gel za piće, gel za kožu ili praktičan proizvod za svakodnevnu rutinu. Zato je najvažnije odrediti što želiš koristiti i kako želiš da ti to stvarno pomogne.',
                'bullets' => [
                    'Za piće gledaj proizvode namijenjene unutarnjoj upotrebi, a za kožu proizvode za njegu.',
                    'Ako želiš kupiti sigurno, koristi službeni Forever shop za svoju zemlju.',
                    'Ako nisi siguran trebaš li gel za piće ili njegu kože, pitaj za preporuku prije kupnje.',
                ],
                'summary' => [
                    'Razjasnit ćeš razliku između biljke, gela za piće i proizvoda za kožu.',
                    'Lakše ćeš odlučiti koji Forever proizvod odgovara tvojoj potrebi.',
                    'Linkovi vode prema službenom shopu, da ne tražiš lokalne opcije ručno.',
                ],
                'faq_items' => [
                    ['question' => 'Je li aloe vera biljka isto što i Forever proizvod?', 'answer' => 'Ne. Biljka i gotovi proizvodi nemaju istu namjenu ni način korištenja. Zato je važno znati želiš li piće, njegu kože ili opću rutinu.'],
                    ['question' => 'Gdje je najsigurnije kupiti?', 'answer' => 'Najsigurnije je nastaviti prema službenom Forever Living Products shopu u tvojoj zemlji.'],
                    ['question' => 'Koji aloe proizvod je najbolji početak?', 'answer' => 'Za svakodnevnu aloe rutinu često se uspoređuje Aloe Vera Gel, a za kožu Aloe Vera Gelly, Aloe First ili Aloe Propolis Creme.'],
                ],
                'meta' => 'Saznaj gdje kupiti aloe veru, koja je razlika između biljke i Forever proizvoda te kako odabrati pravi početak.',
                'focus_keyword' => 'gdje kupiti aloe vera Forever',
            ],
            'digestion' => [
                'heading' => 'Za probavu je najvažnije razumjeti vlastitu rutinu',
                'intro' => 'Probava se rijetko popravlja jednim potezom. Najbolje je vidjeti što ti smeta, kakva je prehrana i trebaš li podršku kroz aloe napitak, vlakna ili probiotike.',
                'bullets' => [
                    'Prati što se ponavlja nakon obroka: nadutost, težina, neredovitost ili žgaravica.',
                    'Usporedi proizvode prema navici koju možeš održati svaki dan.',
                    'Kod jakih, naglih ili dugotrajnih tegoba potraži liječnički savjet.',
                ],
                'summary' => [
                    'Dobit ćeš praktičniji okvir za razumijevanje probavnih navika.',
                    'Preporuke su usmjerene na aloe rutinu, vlakna i dnevnu podršku crijevima.',
                    'Cilj je izbor koji se može održati, a ne kratkotrajni pokušaj.',
                ],
                'faq_items' => [
                    ['question' => 'Što je dobar početak za probavu?', 'answer' => 'Najprije razjasni prehranu, unos tekućine, vlakna i ritam obroka. Nakon toga ima više smisla usporediti aloe napitak, vlakna ili probiotik.'],
                    ['question' => 'Kada se ne treba oslanjati samo na dodatke?', 'answer' => 'Ako imaš jake bolove, krv, nagli gubitak težine, povraćanje ili dugotrajne tegobe, javi se liječniku.'],
                    ['question' => 'Kako dobiti preporuku?', 'answer' => 'Napiši što se događa nakon obroka, koliko traje i što već koristiš u rutini.'],
                ],
                'meta' => 'Praktičan vodič za probavu, aloe rutinu, vlakna i probiotike uz jasniji izbor Forever proizvoda.',
                'focus_keyword' => 'Forever proizvodi za probavu',
            ],
            'inflammation' => [
                'heading' => 'Prirodna podrška ima smisla kada znaš cilj',
                'intro' => 'Kod tema poput kurkume, origana, detoksa ili upalnih procesa važno je ne obećavati čuda. Bolji pristup je vidjeti što želiš podržati u rutini: prehranu, imunitet, zglobove ili opću vitalnost.',
                'bullets' => [
                    'Prvo razdvoji informaciju iz članka od konkretne zdravstvene odluke.',
                    'Usporedi proizvode koji se vežu uz prirodnu dnevnu podršku i jednostavniji ritual.',
                    'Kod dijagnoze, terapije ili jakih simptoma odluku provjeri sa stručnom osobom.',
                ],
                'summary' => [
                    'Članak pomaže razumjeti temu bez pretjeranih obećanja.',
                    'Preporuke proizvoda vode prema smirenoj, svakodnevnoj podršci.',
                    'Najbolji izbor ovisi o cilju, navikama i eventualnoj terapiji.',
                ],
                'faq_items' => [
                    ['question' => 'Je li prirodni proizvod dovoljan?', 'answer' => 'Ovisi o situaciji. Kod bolesti ili terapije proizvod ne zamjenjuje stručni savjet, nego može biti dio šire rutine ako je prikladan.'],
                    ['question' => 'Kako znati što usporediti?', 'answer' => 'Kreni od cilja: imunitet, probava, zglobovi, energija ili opća prehrana. Tada je izbor puno jasniji.'],
                    ['question' => 'Mogu li pitati za osobniji prijedlog?', 'answer' => 'Da. Opiši cilj, što već koristiš i imaš li nešto na što treba paziti.'],
                ],
                'meta' => 'Razumij temu prirodne podrške, kurkume, origana ili detoksa i usporedi Forever proizvode bez pretjeranih obećanja.',
                'focus_keyword' => 'prirodna dnevna podrška Forever',
            ],
            'energy' => [
                'heading' => 'Energiju je najbolje graditi kroz rutinu koju možeš ponavljati',
                'intro' => 'Ako te tema dovela ovdje zbog umora, fokusa, mace ili vitalnosti, korisno je prvo razjasniti tražiš li dnevnu prehrambenu podršku, bolji ritam ili konkretnu pomoć oko navike.',
                'bullets' => [
                    'Pogledaj što najviše nedostaje: san, obroci, minerali, proteini ili dnevna organizacija.',
                    'Usporedi proizvode za energiju i vitalnost prema načinu korištenja.',
                    'Ako se umor naglo pogoršao ili traje dugo, potraži stručni savjet.',
                ],
                'summary' => [
                    'Dobit ćeš jasniji okvir za energiju, fokus i vitalnost.',
                    'Preporučeni proizvodi pomažu usporediti dnevnu podršku bez kompliciranja.',
                    'Najbolja odluka je ona koju možeš uklopiti u stvaran dan.',
                ],
                'faq_items' => [
                    ['question' => 'Što prvo provjeriti kod manjka energije?', 'answer' => 'San, obroke, unos tekućine, stres i osnovne navike. Tek onda ima smisla birati proizvod kao dodatnu podršku.'],
                    ['question' => 'Kada maca ili sličan proizvod ima smisla?', 'answer' => 'Ima smisla kada tražiš prehrambenu podršku za vitalnost, ali treba paziti na osobno stanje, terapiju i očekivanja.'],
                    ['question' => 'Kako odabrati između više proizvoda?', 'answer' => 'Napiši cilj: energija, fokus, hormonska ravnoteža ili opća vitalnost. Tako preporuka može biti preciznija.'],
                ],
                'meta' => 'Saznaj kako pristupiti energiji, fokusu, maci i vitalnosti te usporedi Forever proizvode za dnevnu rutinu.',
                'focus_keyword' => 'Forever energija i vitalnost',
            ],
            'immunity' => [
                'heading' => 'Za otpornost najviše vrijedi dosljedna dnevna podrška',
                'intro' => 'Kod imuniteta, vitamina, alergija ili sezonskih tema korisno je razmišljati praktično: što možeš raditi svaki dan i koji proizvod se uklapa bez nepotrebnog kompliciranja.',
                'bullets' => [
                    'Kreni od prehrane, sna, kretanja i onoga što već koristiš.',
                    'Usporedi proizvode za vitamine, aloe rutinu i sezonsku podršku.',
                    'Kod jačih simptoma, alergija ili terapije odluku provjeri sa stručnom osobom.',
                ],
                'summary' => [
                    'Dobit ćeš smiren okvir za dnevnu podršku otpornosti.',
                    'Preporuke proizvoda prate temu vitamina, aloe i sezonskih navika.',
                    'Cilj je lakša odluka, bez previše izbora odjednom.',
                ],
                'faq_items' => [
                    ['question' => 'Što je najvažnije kod imuniteta?', 'answer' => 'Dosljednost u osnovama: san, prehrana, tekućina, kretanje i dnevne navike. Dodaci imaju više smisla kada se uklapaju u taj okvir.'],
                    ['question' => 'Mogu li kombinirati više proizvoda?', 'answer' => 'Ponekad da, ali prvo treba vidjeti što već koristiš i postoji li terapija ili stanje zbog kojeg treba oprez.'],
                    ['question' => 'Kako dobiti jednostavniji izbor?', 'answer' => 'Pošalji cilj i napiši želiš li aloe rutinu, vitamine ili sezonsku podršku.'],
                ],
                'meta' => 'Praktičan vodič za imunitet, vitamine i sezonsku podršku uz jasniji izbor Forever proizvoda.',
                'focus_keyword' => 'Forever proizvodi za imunitet',
            ],
            'general' => [
                'heading' => 'Što napraviti nakon čitanja',
                'intro' => 'Ako ti je tema korisna, sljedeći korak nije nasumična kupnja, nego jasniji izbor: što želiš podržati, što već koristiš i koji proizvod se uklapa u tvoju rutinu.',
                'bullets' => [
                    'Zabilježi glavni cilj koji želiš riješiti ili podržati.',
                    'Pogledaj preporučene proizvode ispod članka i usporedi njihovu namjenu.',
                    'Ako se dvoumiš, pitaj za preporuku prije odlaska u shop.',
                ],
                'summary' => [
                    'Članak ti pomaže razumjeti temu i donijeti mirniju odluku.',
                    'Preporuke vode prema proizvodima koji imaju najviše veze s temom.',
                    'Ako treba, možeš zatražiti jednostavniji osobni prijedlog.',
                ],
                'faq_items' => [
                    ['question' => 'Kako znam koji proizvod ima smisla?', 'answer' => 'Kreni od cilja i usporedi proizvode po namjeni, načinu korištenja i tome koliko se uklapaju u tvoju rutinu.'],
                    ['question' => 'Moram li odmah kupiti?', 'answer' => 'Ne. Prvo razumij temu i opcije, a u shop idi tek kada ti izbor ima smisla.'],
                    ['question' => 'Mogu li dobiti preporuku?', 'answer' => 'Da. Napiši što želiš podržati, što već koristiš i oko čega nisi siguran.'],
                ],
                'meta' => 'Pročitaj jasan vodič, usporedi preporučene Forever proizvode i pronađi sljedeći korak koji ima smisla za tvoju rutinu.',
                'focus_keyword' => 'Forever proizvodi preporuka',
            ],
        ],
    ];

    $copies['en'] = translateCopyToEnglish($copies['hr']);
    $copies['sl'] = translateCopyToSlovenian($copies['hr']);

    $copy = $copies[$languageCode][$intent] ?? $copies[$languageCode]['general'];
    $copy['topic'] = $topic;

    return $copy;
}

function translateCopyToEnglish(array $hrCopies): array
{
    return [
        'respiratory' => [
            'heading' => 'How to turn this topic into a useful next step',
            'intro' => 'If you are reading about cough, throat comfort or seasonal resilience, start by clarifying what you want to support: a calmer routine, a more comfortable throat or everyday nutritional support.',
            'bullets' => [
                'Start with the symptoms and how long they last, and involve a professional if they are strong or persistent.',
                'Compare products connected with honey, propolis, thyme and vitamin support.',
                'If you are not sure what makes sense, send a question and get a simpler suggestion.',
            ],
            'summary' => [
                'You will see where a home routine may help and where professional advice matters.',
                'You will get a clearer path toward products that naturally connect with this topic.',
                'The goal is not a rushed purchase, but a choice that fits your situation.',
            ],
            'faq_items' => [
                ['question' => 'What is the smartest first step?', 'answer' => 'First clarify how long the issue has lasted, what you already use and whether professional advice is needed. Then it is easier to compare products for everyday support.'],
                ['question' => 'Can I order a recommended product right away?', 'answer' => 'You can, but it is better to read the product page first and check whether it fits your age, habits and any therapy you use.'],
                ['question' => 'What if I am not sure what to choose?', 'answer' => 'Send a short recommendation request. Write your goal, what you already use and where you are unsure.'],
            ],
            'meta' => 'Clarify the topic, compare natural daily support and find a sensible next step toward Forever products.',
            'focus_keyword' => 'natural cough and immunity support',
        ],
        'weight' => [
            'heading' => 'Before choosing a nutrition plan, define what you can keep doing daily',
            'intro' => 'With keto, weight management or detox topics, the most useful routine is the one you can repeat. Use this article as a guide for choosing a simpler plan, meal option or extra support.',
            'bullets' => [
                'First decide whether you need better meal structure, appetite support or an easier start.',
                'Compare products by how they are used, not only by the result you hope for.',
                'If you have a health condition or use medication, ask a professional before a major nutrition change.',
            ],
            'summary' => [
                'You will separate what helps nutrition from what helps habit and structure.',
                'Product recommendations point toward a practical starting choice.',
                'The best option is the one you can fit in without too much friction.',
            ],
            'faq_items' => [
                ['question' => 'What should I choose if I am just starting?', 'answer' => 'If you need structure, compare products and programs that make the first days easier. If you already have a plan, focus on what is missing in your routine.'],
                ['question' => 'Is one product enough?', 'answer' => 'A product can support a routine, but results depend mostly on nutrition, movement, sleep and consistency.'],
                ['question' => 'When should I ask for a recommendation?', 'answer' => 'If you do not know whether you need a program, protein support or appetite support, send a short question and describe your goal.'],
            ],
            'meta' => 'Compare nutrition, keto or detox routines and find the Forever product that realistically fits your goal.',
            'focus_keyword' => 'Forever nutrition and weight management products',
        ],
        'thyroid' => [
            'heading' => 'With hormones, choose calmly and with good information',
            'intro' => 'Topics such as TSH, thyroid and energy should not be handled by guesswork. This article gives context, while product suggestions are only everyday support, not a replacement for diagnosis or therapy.',
            'bullets' => [
                'For lab results and symptoms, follow medical advice first.',
                'For routine support, compare products related to minerals, vitamins and daily vitality.',
                'If you use medication, check possible interactions with a professional before supplements.',
            ],
            'summary' => [
                'You will separate information from decisions that require medical advice.',
                'You will see which daily support could make sense with your habits.',
                'Recommendations stay practical, cautious and connected with real needs.',
            ],
            'faq_items' => [
                ['question' => 'Can products replace thyroid therapy?', 'answer' => 'No. With thyroid and hormone results, follow medical guidance. Products can only be part of general nutrition and daily support.'],
                ['question' => 'What should I check before supplements?', 'answer' => 'Lab results, medication, pregnancy, breastfeeding and other conditions matter. If medication is involved, check supplements with a professional.'],
                ['question' => 'How can I get a sensible suggestion?', 'answer' => 'Write what you want to support, whether you use therapy and whether you are looking for energy, minerals or a general daily routine.'],
            ],
            'meta' => 'Understand TSH, thyroid and daily vitality topics and see when it makes sense to compare Forever support.',
            'focus_keyword' => 'thyroid TSH daily support',
        ],
        'skin' => [
            'heading' => 'Skin is best approached through a gentle routine, not a quick fix',
            'intro' => 'For sensitive skin, dermatitis, rash or acne, a calm approach helps most: what to avoid, what to care for and which product makes sense as part of everyday care.',
            'bullets' => [
                'First clarify whether skin is dry, irritated, damaged or reactive.',
                'Compare care products by purpose: calming, hydration, protection or collagen support.',
                'If the condition spreads, hurts or lasts, ask for professional advice.',
            ],
            'summary' => [
                'You will get a calmer frame for deciding what to use on skin.',
                'Recommendations connect with care, hydration and daily protection.',
                'The article helps before purchase, especially when comparing several options.',
            ],
            'faq_items' => [
                ['question' => 'How do I choose for sensitive skin?', 'answer' => 'Start with the need: hydration, calming, protection or care after irritation. Then check ingredients and use instructions.'],
                ['question' => 'What if my skin reacts strongly?', 'answer' => 'If the reaction is strong, painful, spreading, oozing or persistent, it is better to ask a dermatologist before experimenting.'],
                ['question' => 'Can I ask for a recommendation?', 'answer' => 'Yes. Write where the issue is, how the skin feels and what you already used.'],
            ],
            'meta' => 'A practical guide to sensitive skin, dermatitis or care with a clearer choice of Forever products.',
            'focus_keyword' => 'Forever sensitive skin care',
        ],
        'aloe_purchase' => [
            'heading' => 'If you are looking for aloe, first separate the plant from a ready-made routine',
            'intro' => 'Aloe vera can mean a house plant, aloe drink, skin gel or a practical everyday product. The most important step is to define what you want to use and how you want it to help.',
            'bullets' => [
                'For drinking, look at products intended for internal use; for skin, choose care products.',
                'For safer buying, use the official Forever shop for your country.',
                'If you are not sure whether you need an aloe drink or skin care, ask before buying.',
            ],
            'summary' => [
                'You will clarify the difference between the plant, aloe drink and skin products.',
                'You will decide more easily which Forever product fits your need.',
                'Links lead to the official shop so you do not have to search local options manually.',
            ],
            'faq_items' => [
                ['question' => 'Is an aloe plant the same as a Forever product?', 'answer' => 'No. The plant and ready-made products do not have the same purpose or use instructions. First decide whether you want a drink, skin care or a general routine.'],
                ['question' => 'Where is the safest place to buy?', 'answer' => 'The safest path is the official Forever Living Products shop in your country.'],
                ['question' => 'Which aloe product is a good start?', 'answer' => 'For a daily aloe routine, Aloe Vera Gel is often compared first; for skin, Aloe Vera Gelly, Aloe First or Aloe Propolis Creme may be relevant.'],
            ],
            'meta' => 'Learn where to buy aloe vera, how the plant differs from Forever products and how to choose the right first step.',
            'focus_keyword' => 'where to buy Forever aloe vera',
        ],
        'digestion' => [
            'heading' => 'For digestion, start by understanding your own routine',
            'intro' => 'Digestion rarely improves with one move. It helps to see what bothers you, what your diet looks like and whether aloe drink, fiber or probiotics make sense.',
            'bullets' => [
                'Notice what repeats after meals: bloating, heaviness, irregularity or heartburn.',
                'Compare products by the habit you can keep daily.',
                'For strong, sudden or long-lasting issues, ask for medical advice.',
            ],
            'summary' => [
                'You will get a practical frame for understanding digestive habits.',
                'Recommendations focus on aloe routine, fiber and daily gut support.',
                'The goal is a choice you can maintain, not a short-lived attempt.',
            ],
            'faq_items' => [
                ['question' => 'What is a good start for digestion?', 'answer' => 'First check food, fluids, fiber and meal rhythm. Then it makes more sense to compare aloe drink, fiber or probiotics.'],
                ['question' => 'When should supplements not be the only answer?', 'answer' => 'If you have strong pain, blood, sudden weight loss, vomiting or persistent issues, contact a doctor.'],
                ['question' => 'How do I get a recommendation?', 'answer' => 'Write what happens after meals, how long it has lasted and what you already use.'],
            ],
            'meta' => 'A practical guide to digestion, aloe routine, fiber and probiotics with a clearer choice of Forever products.',
            'focus_keyword' => 'Forever digestion products',
        ],
        'inflammation' => [
            'heading' => 'Natural support makes sense when the goal is clear',
            'intro' => 'With turmeric, oregano, detox or inflammation topics, it is important not to promise miracles. A better approach is to define what you want to support: nutrition, immunity, joints or vitality.',
            'bullets' => [
                'Separate article information from an actual health decision.',
                'Compare products connected with natural daily support and a simpler ritual.',
                'If there is a diagnosis, medication or strong symptoms, check decisions with a professional.',
            ],
            'summary' => [
                'The article helps you understand the topic without exaggerated promises.',
                'Product suggestions point toward calm everyday support.',
                'The best choice depends on your goal, habits and possible therapy.',
            ],
            'faq_items' => [
                ['question' => 'Is a natural product enough?', 'answer' => 'It depends. With illness or therapy, a product does not replace professional advice; it may only be part of a broader routine if appropriate.'],
                ['question' => 'How do I know what to compare?', 'answer' => 'Start with the goal: immunity, digestion, joints, energy or general nutrition. Then the choice becomes clearer.'],
                ['question' => 'Can I ask for a more personal suggestion?', 'answer' => 'Yes. Describe your goal, what you already use and anything that requires caution.'],
            ],
            'meta' => 'Understand natural support, turmeric, oregano or detox topics and compare Forever products without exaggerated promises.',
            'focus_keyword' => 'natural daily Forever support',
        ],
        'energy' => [
            'heading' => 'Energy is best built through a routine you can repeat',
            'intro' => 'If you arrived here because of tiredness, focus, maca or vitality, first clarify whether you want daily nutrition support, a better rhythm or help with a specific habit.',
            'bullets' => [
                'Look at what is missing most: sleep, meals, minerals, protein or daily organization.',
                'Compare energy and vitality products by how they are used.',
                'If fatigue became sudden or lasts a long time, ask for professional advice.',
            ],
            'summary' => [
                'You will get a clearer frame for energy, focus and vitality.',
                'Recommended products help compare daily support without overcomplicating.',
                'The best decision is the one that fits a real day.',
            ],
            'faq_items' => [
                ['question' => 'What should I check first with low energy?', 'answer' => 'Sleep, meals, fluids, stress and basic habits. Then it makes sense to choose a product as extra support.'],
                ['question' => 'When does maca or a similar product make sense?', 'answer' => 'It can make sense when you want nutritional support for vitality, while still considering your condition, therapy and expectations.'],
                ['question' => 'How do I choose between products?', 'answer' => 'Write your goal: energy, focus, hormonal balance or general vitality. That makes recommendations more precise.'],
            ],
            'meta' => 'Learn how to approach energy, focus, maca and vitality and compare Forever products for a daily routine.',
            'focus_keyword' => 'Forever energy and vitality',
        ],
        'immunity' => [
            'heading' => 'For resilience, consistent daily support matters most',
            'intro' => 'With immunity, vitamins, allergies or seasonal topics, think practically: what can you do every day and which product fits without unnecessary complexity.',
            'bullets' => [
                'Start with nutrition, sleep, movement and what you already use.',
                'Compare products for vitamins, aloe routine and seasonal support.',
                'For stronger symptoms, allergies or medication, check decisions with a professional.',
            ],
            'summary' => [
                'You will get a calm frame for daily resilience support.',
                'Product recommendations follow vitamins, aloe and seasonal habits.',
                'The goal is an easier decision, without too many options at once.',
            ],
            'faq_items' => [
                ['question' => 'What matters most for immunity?', 'answer' => 'Consistency with the basics: sleep, nutrition, fluids, movement and daily habits. Supplements make more sense inside that frame.'],
                ['question' => 'Can I combine several products?', 'answer' => 'Sometimes yes, but first consider what you already use and whether therapy or a condition requires caution.'],
                ['question' => 'How can I get a simpler choice?', 'answer' => 'Send your goal and say whether you want an aloe routine, vitamins or seasonal support.'],
            ],
            'meta' => 'A practical guide to immunity, vitamins and seasonal support with a clearer choice of Forever products.',
            'focus_keyword' => 'Forever immunity products',
        ],
        'general' => [
            'heading' => 'What to do after reading',
            'intro' => 'If this topic is useful, the next step is not a random purchase. It is a clearer choice: what you want to support, what you already use and which product fits your routine.',
            'bullets' => [
                'Write down the main goal you want to solve or support.',
                'Look at the recommended products below the article and compare their purpose.',
                'If you are unsure, ask for a recommendation before going to the shop.',
            ],
            'summary' => [
                'The article helps you understand the topic and decide more calmly.',
                'Recommendations point to products most connected with the topic.',
                'If needed, you can ask for a simpler personal suggestion.',
            ],
            'faq_items' => [
                ['question' => 'How do I know which product makes sense?', 'answer' => 'Start with the goal and compare products by purpose, usage and how well they fit your routine.'],
                ['question' => 'Do I have to buy right away?', 'answer' => 'No. First understand the topic and options, then go to the shop when the choice makes sense.'],
                ['question' => 'Can I get a recommendation?', 'answer' => 'Yes. Write what you want to support, what you already use and where you are unsure.'],
            ],
            'meta' => 'Read a clear guide, compare recommended Forever products and find the next step that fits your routine.',
            'focus_keyword' => 'Forever product recommendation',
        ],
    ];
}

function translateCopyToSlovenian(array $hrCopies): array
{
    return [
        'respiratory' => [
            'heading' => 'Kako to temo spremeniti v dober naslednji korak',
            'intro' => 'Če bereš o kašlju, grlu ali sezonski odpornosti, je najkoristneje najprej razjasniti, kaj želiš podpreti: mirnejšo rutino, prijetnejši občutek v grlu ali vsakodnevno prehransko podporo.',
            'bullets' => [
                'Začni pri simptomih in trajanju, pri močnejših ali dolgotrajnih težavah pa vključi strokovnjaka.',
                'Primerjaj izdelke, povezane z medom, propolisom, timijanom in vitaminsko podporo.',
                'Če nisi prepričan, kaj ima smisel, pošlji vprašanje in dobiš preprostejši predlog.',
            ],
            'summary' => [
                'Lažje boš videl, kje lahko pomaga domača rutina in kje je pomemben strokovni nasvet.',
                'Dobil boš jasnejšo pot do izdelkov, ki se naravno povezujejo s temo.',
                'Cilj ni hiter nakup, temveč izbira, ki ustreza tvoji situaciji.',
            ],
            'faq_items' => [
                ['question' => 'Kaj je najpametnejši prvi korak?', 'answer' => 'Najprej razjasni, kako dolgo težava traja, kaj že uporabljaš in ali potrebuješ strokovni nasvet. Nato je lažje primerjati izdelke za vsakodnevno podporo.'],
                ['question' => 'Ali lahko priporočeni izdelek naročim takoj?', 'answer' => 'Lahko, vendar je bolje pred nakupom prebrati opis izdelka in preveriti, ali ustreza tvoji starosti, navadam in morebitni terapiji.'],
                ['question' => 'Kaj če nisem prepričan, kaj izbrati?', 'answer' => 'Pošlji kratko vprašanje za priporočilo. Napiši cilj, kaj že uporabljaš in pri čem se odločaš.'],
            ],
            'meta' => 'Razjasni temo, primerjaj naravno dnevno podporo in poišči smiseln naslednji korak do Forever izdelkov.',
            'focus_keyword' => 'naravna podpora kašlju in odpornosti',
        ],
        'weight' => [
            'heading' => 'Pred prehranskim načrtom določi, kaj lahko vzdržuješ vsak dan',
            'intro' => 'Pri keto prehrani, uravnavanju teže ali detoksu najbolj pomaga rutina, ki jo lahko ponavljaš. Članek uporabi kot orientacijo za preprostejši načrt, obrok ali dodatno podporo.',
            'bullets' => [
                'Najprej preveri, ali želiš boljšo strukturo obrokov, podporo apetitu ali lažji začetek.',
                'Izdelke primerjaj po načinu uporabe, ne samo po obljubi rezultata.',
                'Če imaš zdravstveno stanje ali terapijo, se pred večjo spremembo prehrane posvetuj s strokovnjakom.',
            ],
            'summary' => [
                'Jasneje boš ločil, kaj pomaga prehrani in kaj navadi ter organizaciji.',
                'Priporočila izdelkov vodijo k praktičnejši začetni izbiri.',
                'Najboljša možnost je tista, ki jo lahko vključiš brez preveč zapletov.',
            ],
            'faq_items' => [
                ['question' => 'Kaj izbrati, če šele začenjam?', 'answer' => 'Če potrebuješ strukturo, primerjaj izdelke in programe, ki olajšajo prve dni. Če načrt že imaš, se osredotoči na to, kar manjka v rutini.'],
                ['question' => 'Je dovolj en izdelek?', 'answer' => 'Izdelek lahko podpre rutino, vendar so rezultati najbolj odvisni od prehrane, gibanja, spanja in doslednosti.'],
                ['question' => 'Kdaj vprašati za priporočilo?', 'answer' => 'Če ne veš, ali potrebuješ program, beljakovinsko podporo ali podporo apetitu, pošlji kratko vprašanje in opiši cilj.'],
            ],
            'meta' => 'Primerjaj prehransko rutino, keto ali detoks pristop in poišči Forever izdelek, ki se realno ujema s tvojim ciljem.',
            'focus_keyword' => 'Forever izdelki za prehrano in težo',
        ],
        'thyroid' => [
            'heading' => 'Pri hormonih je pomembno izbirati mirno in informirano',
            'intro' => 'Tem, kot so TSH, ščitnica in energija, ni dobro reševati na pamet. Članek pomaga razumeti kontekst, priporočila izdelkov pa so le podpora vsakodnevnim navadam, ne zamenjava za diagnozo ali terapijo.',
            'bullets' => [
                'Pri izvidih in simptomih se najprej ravnaj po zdravniškem nasvetu.',
                'Za rutino lahko primerjaš izdelke, povezane z minerali, vitamini in dnevno vitalnostjo.',
                'Če uporabljaš terapijo, pred dodatki preveri možne interakcije s strokovnjakom.',
            ],
            'summary' => [
                'Ločil boš informativni del od odločitev, ki zahtevajo zdravniški nasvet.',
                'Lažje boš videl, katera dnevna podpora ima smisel ob tvojih navadah.',
                'Priporočilo izdelkov ostane praktično, previdno in povezano z resnično potrebo.',
            ],
            'faq_items' => [
                ['question' => 'Ali lahko izdelki nadomestijo terapijo za ščitnico?', 'answer' => 'Ne. Pri ščitnici in hormonskih izvidih se ravnaj po zdravniških priporočilih. Izdelki so lahko le del splošne prehranske in dnevne podpore.'],
                ['question' => 'Na kaj paziti pred dodatki?', 'answer' => 'Pomembni so izvidi, terapija, nosečnost, dojenje in druga stanja. Če obstaja terapija, dodatke preveri s strokovnjakom.'],
                ['question' => 'Kako dobiti smiseln predlog?', 'answer' => 'Napiši, kaj želiš podpreti, ali uporabljaš terapijo in ali iščeš energijo, minerale ali splošno dnevno rutino.'],
            ],
            'meta' => 'Razumi temo TSH, ščitnice in dnevne vitalnosti ter kdaj ima smisel primerjati Forever podporo.',
            'focus_keyword' => 'ščitnica TSH dnevna podpora',
        ],
        'skin' => [
            'heading' => 'Kožo je najbolje gledati skozi nežno rutino, ne hitro rešitev',
            'intro' => 'Pri občutljivi koži, dermatitisu, izpuščaju ali aknah najbolj pomaga miren pristop: čemu se izogniti, kaj negovati in kateri izdelek ima smisel v vsakodnevni negi.',
            'bullets' => [
                'Najprej razjasni, ali je koža suha, razdražena, poškodovana ali reaktivna.',
                'Izdelke za nego primerjaj po namenu: pomiritev, hidracija, zaščita ali kolagen.',
                'Če se stanje širi, boli ali traja dolgo, poišči strokovni nasvet.',
            ],
            'summary' => [
                'Dobil boš mirnejši okvir za odločitev, kaj uporabljati na koži.',
                'Priporočila so povezana z nego, hidracijo in vsakodnevno zaščito.',
                'Članek pomaga pred nakupom, posebej ko primerjaš več možnosti.',
            ],
            'faq_items' => [
                ['question' => 'Kako izbrati izdelek za občutljivo kožo?', 'answer' => 'Začni pri potrebi: hidracija, pomiritev, zaščita ali nega po draženju. Nato preveri sestavo in način uporabe.'],
                ['question' => 'Kaj če koža močno reagira?', 'answer' => 'Če je reakcija močna, boleča, se širi, rosi ali traja, je bolje vprašati dermatologa pred preizkušanjem izdelkov.'],
                ['question' => 'Ali lahko vprašam za priporočilo?', 'answer' => 'Da. Napiši, kje je težava, kakšen je občutek na koži in kaj si že uporabljal.'],
            ],
            'meta' => 'Praktičen vodič za občutljivo kožo, dermatitis ali nego z jasnejšo izbiro Forever izdelkov.',
            'focus_keyword' => 'Forever nega občutljive kože',
        ],
        'aloe_purchase' => [
            'heading' => 'Če iščeš aloe, najprej loči rastlino od pripravljene rutine',
            'intro' => 'Aloe vera lahko pomeni sobno rastlino, napitek, gel za kožo ali praktičen vsakodnevni izdelek. Najpomembneje je določiti, kaj želiš uporabljati in kako naj ti to pomaga.',
            'bullets' => [
                'Za pitje glej izdelke za notranjo uporabo, za kožo pa izdelke za nego.',
                'Za varnejši nakup uporabi uradno Forever trgovino za svojo državo.',
                'Če nisi prepričan, ali potrebuješ napitek ali nego kože, vprašaj pred nakupom.',
            ],
            'summary' => [
                'Razjasnil boš razliko med rastlino, napitkom aloe in izdelki za kožo.',
                'Lažje se boš odločil, kateri Forever izdelek ustreza tvoji potrebi.',
                'Povezave vodijo v uradno trgovino, da lokalnih možnosti ne iščeš ročno.',
            ],
            'faq_items' => [
                ['question' => 'Je rastlina aloe isto kot Forever izdelek?', 'answer' => 'Ne. Rastlina in pripravljeni izdelki nimajo istega namena niti načina uporabe. Najprej določi, ali želiš napitek, nego kože ali splošno rutino.'],
                ['question' => 'Kje je najvarneje kupiti?', 'answer' => 'Najvarnejša pot je uradna Forever Living Products trgovina v tvoji državi.'],
                ['question' => 'Kateri aloe izdelek je dober začetek?', 'answer' => 'Za vsakodnevno aloe rutino se pogosto najprej primerja Aloe Vera Gel, za kožo pa Aloe Vera Gelly, Aloe First ali Aloe Propolis Creme.'],
            ],
            'meta' => 'Izvedi, kje kupiti aloe vero, kako se rastlina razlikuje od Forever izdelkov in kako izbrati pravi začetek.',
            'focus_keyword' => 'kje kupiti Forever aloe vera',
        ],
        'digestion' => [
            'heading' => 'Za prebavo je najpomembneje razumeti lastno rutino',
            'intro' => 'Prebava se redko izboljša z eno potezo. Najbolje je videti, kaj ti ne ustreza, kakšna je prehrana in ali ima smisel podpora z aloe napitkom, vlakninami ali probiotiki.',
            'bullets' => [
                'Spremljaj, kaj se ponavlja po obrokih: napihnjenost, teža, nerednost ali zgaga.',
                'Izdelke primerjaj po navadi, ki jo lahko ohranjaš vsak dan.',
                'Pri močnih, nenadnih ali dolgotrajnih težavah poišči zdravniški nasvet.',
            ],
            'summary' => [
                'Dobil boš praktičnejši okvir za razumevanje prebavnih navad.',
                'Priporočila so usmerjena v aloe rutino, vlaknine in dnevno podporo črevesju.',
                'Cilj je izbira, ki jo lahko ohranjaš, ne kratkotrajen poskus.',
            ],
            'faq_items' => [
                ['question' => 'Kaj je dober začetek za prebavo?', 'answer' => 'Najprej razjasni prehrano, tekočino, vlaknine in ritem obrokov. Nato je bolj smiselno primerjati aloe napitek, vlaknine ali probiotik.'],
                ['question' => 'Kdaj se ne opirati samo na dodatke?', 'answer' => 'Če imaš močne bolečine, kri, nenadno izgubo teže, bruhanje ali dolgotrajne težave, se obrni na zdravnika.'],
                ['question' => 'Kako dobiti priporočilo?', 'answer' => 'Napiši, kaj se dogaja po obroku, koliko časa traja in kaj že uporabljaš.'],
            ],
            'meta' => 'Praktičen vodič za prebavo, aloe rutino, vlaknine in probiotike z jasnejšo izbiro Forever izdelkov.',
            'focus_keyword' => 'Forever izdelki za prebavo',
        ],
        'inflammation' => [
            'heading' => 'Naravna podpora ima smisel, ko poznaš cilj',
            'intro' => 'Pri temah, kot so kurkuma, origano, detoks ali vnetni procesi, je pomembno ne obljubljati čudežev. Boljši pristop je videti, kaj želiš podpreti: prehrano, odpornost, sklepe ali vitalnost.',
            'bullets' => [
                'Najprej loči informacije iz članka od konkretne zdravstvene odločitve.',
                'Primerjaj izdelke, povezane z naravno dnevno podporo in preprostejšim ritualom.',
                'Pri diagnozi, terapiji ali močnih simptomih odločitev preveri s strokovnjakom.',
            ],
            'summary' => [
                'Članek pomaga razumeti temo brez pretiranih obljub.',
                'Priporočila izdelkov vodijo k mirni, vsakodnevni podpori.',
                'Najboljša izbira je odvisna od cilja, navad in morebitne terapije.',
            ],
            'faq_items' => [
                ['question' => 'Je naravni izdelek dovolj?', 'answer' => 'Odvisno od situacije. Pri bolezni ali terapiji izdelek ne nadomesti strokovnega nasveta, ampak je lahko del širše rutine, če je primeren.'],
                ['question' => 'Kako vedeti, kaj primerjati?', 'answer' => 'Začni pri cilju: odpornost, prebava, sklepi, energija ali splošna prehrana. Takrat je izbira jasnejša.'],
                ['question' => 'Ali lahko vprašam za osebnejši predlog?', 'answer' => 'Da. Opiši cilj, kaj že uporabljaš in ali je kaj, pri čemer je potrebna previdnost.'],
            ],
            'meta' => 'Razumi naravno podporo, kurkumo, origano ali detoks in primerjaj Forever izdelke brez pretiranih obljub.',
            'focus_keyword' => 'naravna dnevna podpora Forever',
        ],
        'energy' => [
            'heading' => 'Energijo je najbolje graditi skozi rutino, ki jo lahko ponavljaš',
            'intro' => 'Če si tukaj zaradi utrujenosti, fokusa, mace ali vitalnosti, najprej razjasni, ali iščeš dnevno prehransko podporo, boljši ritem ali pomoč pri konkretni navadi.',
            'bullets' => [
                'Poglej, kaj najbolj manjka: spanje, obroki, minerali, beljakovine ali dnevna organizacija.',
                'Izdelke za energijo in vitalnost primerjaj po načinu uporabe.',
                'Če se je utrujenost naglo poslabšala ali traja dolgo, poišči strokovni nasvet.',
            ],
            'summary' => [
                'Dobil boš jasnejši okvir za energijo, fokus in vitalnost.',
                'Priporočeni izdelki pomagajo primerjati dnevno podporo brez zapletanja.',
                'Najboljša odločitev je tista, ki se ujema z resničnim dnevom.',
            ],
            'faq_items' => [
                ['question' => 'Kaj najprej preveriti pri pomanjkanju energije?', 'answer' => 'Spanje, obroke, tekočino, stres in osnovne navade. Šele nato ima smisel izbrati izdelek kot dodatno podporo.'],
                ['question' => 'Kdaj ima maca ali podoben izdelek smisel?', 'answer' => 'Ima smisel, ko želiš prehransko podporo vitalnosti, vendar je treba upoštevati osebno stanje, terapijo in pričakovanja.'],
                ['question' => 'Kako izbrati med več izdelki?', 'answer' => 'Napiši cilj: energija, fokus, hormonsko ravnovesje ali splošna vitalnost. Tako je priporočilo natančnejše.'],
            ],
            'meta' => 'Spoznaj pristop k energiji, fokusu, maci in vitalnosti ter primerjaj Forever izdelke za dnevno rutino.',
            'focus_keyword' => 'Forever energija in vitalnost',
        ],
        'immunity' => [
            'heading' => 'Za odpornost največ velja dosledna dnevna podpora',
            'intro' => 'Pri odpornosti, vitaminih, alergijah ali sezonskih temah razmišljaj praktično: kaj lahko narediš vsak dan in kateri izdelek se vključi brez nepotrebnega zapletanja.',
            'bullets' => [
                'Začni pri prehrani, spanju, gibanju in tem, kar že uporabljaš.',
                'Primerjaj izdelke za vitamine, aloe rutino in sezonsko podporo.',
                'Pri močnejših simptomih, alergijah ali terapiji odločitev preveri s strokovnjakom.',
            ],
            'summary' => [
                'Dobil boš miren okvir za dnevno podporo odpornosti.',
                'Priporočila izdelkov sledijo temi vitaminov, aloe in sezonskih navad.',
                'Cilj je lažja odločitev brez preveč možnosti naenkrat.',
            ],
            'faq_items' => [
                ['question' => 'Kaj je najpomembnejše pri odpornosti?', 'answer' => 'Doslednost pri osnovah: spanje, prehrana, tekočina, gibanje in dnevne navade. Dodatki imajo več smisla znotraj tega okvira.'],
                ['question' => 'Ali lahko kombiniram več izdelkov?', 'answer' => 'Včasih da, vendar je najprej treba videti, kaj že uporabljaš in ali obstaja terapija ali stanje, ki zahteva previdnost.'],
                ['question' => 'Kako dobiti preprostejšo izbiro?', 'answer' => 'Pošlji cilj in napiši, ali želiš aloe rutino, vitamine ali sezonsko podporo.'],
            ],
            'meta' => 'Praktičen vodič za odpornost, vitamine in sezonsko podporo z jasnejšo izbiro Forever izdelkov.',
            'focus_keyword' => 'Forever izdelki za odpornost',
        ],
        'general' => [
            'heading' => 'Kaj narediti po branju',
            'intro' => 'Če ti je tema koristna, naslednji korak ni naključen nakup, ampak jasnejša izbira: kaj želiš podpreti, kaj že uporabljaš in kateri izdelek se ujema s tvojo rutino.',
            'bullets' => [
                'Zapiši glavni cilj, ki ga želiš rešiti ali podpreti.',
                'Poglej priporočene izdelke pod člankom in primerjaj njihov namen.',
                'Če se odločaš, vprašaj za priporočilo pred odhodom v trgovino.',
            ],
            'summary' => [
                'Članek ti pomaga razumeti temo in sprejeti mirnejšo odločitev.',
                'Priporočila vodijo k izdelkom, ki so najbolj povezani s temo.',
                'Po potrebi lahko zaprosiš za preprostejši osebni predlog.',
            ],
            'faq_items' => [
                ['question' => 'Kako vem, kateri izdelek ima smisel?', 'answer' => 'Začni pri cilju in primerjaj izdelke po namenu, načinu uporabe in tem, kako se vključijo v tvojo rutino.'],
                ['question' => 'Ali moram takoj kupiti?', 'answer' => 'Ne. Najprej razumi temo in možnosti, v trgovino pa pojdi, ko ima izbira smisel.'],
                ['question' => 'Ali lahko dobim priporočilo?', 'answer' => 'Da. Napiši, kaj želiš podpreti, kaj že uporabljaš in pri čem nisi prepričan.'],
            ],
            'meta' => 'Preberi jasen vodič, primerjaj priporočene Forever izdelke in najdi naslednji korak, ki ustreza tvoji rutini.',
            'focus_keyword' => 'Forever izdelki priporočilo',
        ],
    ];
}

function topicFallback(string $languageCode): string
{
    return match ($languageCode) {
        'en' => 'this topic',
        'sl' => 'ta tema',
        default => 'ova tema',
    };
}

function buildBridgeHtml(array $copy): string
{
    $html = '<section data-avc-gsc-polish="v1">';
    $html .= '<h2>' . escapeHtml((string) $copy['heading']) . '</h2>';
    $html .= '<p>' . escapeHtml((string) $copy['intro']) . '</p>';
    $html .= '<ul>';

    foreach ((array) ($copy['bullets'] ?? []) as $bullet) {
        $html .= '<li>' . escapeHtml((string) $bullet) . '</li>';
    }

    $html .= '</ul>';
    $html .= '</section>';

    return $html;
}

function buildSummaryHtml(array $copy): string
{
    $html = '<ul data-avc-gsc-summary="v1">';

    foreach ((array) ($copy['summary'] ?? []) as $item) {
        $html .= '<li>' . escapeHtml((string) $item) . '</li>';
    }

    $html .= '</ul>';

    return $html;
}

function removeExistingBridge(string $bodyHtml): string
{
    return preg_replace('/\s*<section\b[^>]*data-avc-gsc-polish="v1"[^>]*>.*?<\/section>\s*/isu', "\n", $bodyHtml) ?? $bodyHtml;
}

function injectBridge(string $bodyHtml, string $bridgeHtml): string
{
    $bodyHtml = trim($bodyHtml);

    if ($bodyHtml === '') {
        return $bridgeHtml;
    }

    $updated = preg_replace('/(<p\b[^>]*>.*?<\/p>)/isu', '$1' . "\n" . $bridgeHtml, $bodyHtml, 1, $count);

    if ($count > 0 && is_string($updated)) {
        return $updated;
    }

    return $bridgeHtml . "\n" . $bodyHtml;
}

function buildExcerpt(array $copy): string
{
    return truncateText(cleanText((string) ($copy['intro'] ?? '')), 230);
}

function buildMetaDescription(array $copy, string $title, string $existingMetaDescription): string
{
    $base = trim($title . ': ' . (string) ($copy['meta'] ?? ''));
    $description = truncateText(cleanText($base), 165);

    $existing = cleanText($existingMetaDescription);
    if ($existing !== '' && !isGenericMeta($existing) && mb_strlen($existing) >= 135) {
        return truncateText($existing, 165);
    }

    return $description;
}

function buildMetaTitle(array $row, string $title, string $languageCode): string
{
    $existing = cleanText((string) ($row['meta_title'] ?? ''));
    if ($existing !== '' && mb_strlen($existing) >= 25) {
        return truncateText($existing, 68);
    }

    $suffix = match ($languageCode) {
        'en' => ' | Aloe Vera Centar',
        'sl' => ' | Aloe Vera Center',
        default => ' | Aloe Vera Centar',
    };

    return truncateText($title . $suffix, 68);
}

function buildOpenGraphTitle(array $row, string $title): string
{
    $existing = cleanText((string) ($row['open_graph_title'] ?? ''));

    return $existing !== '' ? truncateText($existing, 80) : truncateText($title, 80);
}

function focusKeyword(string $intent, string $languageCode): string
{
    $copy = intentCopy($intent, $languageCode, '');

    return (string) ($copy['focus_keyword'] ?? '');
}

function isGenericMeta(string $description): bool
{
    $normalized = normalizeSearchText($description);

    foreach ([
        'procitaj praktican clanak',
        'read a practical',
        'preberi prakticen',
        'jasan kontekst',
        'korisne napomene',
        'next step',
    ] as $pattern) {
        if (str_contains($normalized, normalizeSearchText($pattern))) {
            return true;
        }
    }

    return false;
}

function hasChanges(array $row, array $payload): bool
{
    $checks = [
        'excerpt',
        'body_html',
        'summary_html',
        'faq_json',
        'meta_title',
        'meta_description',
        'canonical_url',
        'breadcrumb_title',
        'focus_keyword',
        'open_graph_title',
        'open_graph_description',
        'open_graph_image_path',
    ];

    foreach ($checks as $field) {
        if ((string) ($row[$field] ?? '') !== (string) ($payload[$field] ?? '')) {
            return true;
        }
    }

    if ((int) ($row['robots_index'] ?? 1) !== 1 || (int) ($row['robots_follow'] ?? 1) !== 1) {
        return true;
    }

    return false;
}

function saveRevision(mysqli $connection, array $row, string $revisionType): void
{
    $snapshot = [
        'translation' => [
            'content_translation_id' => (int) ($row['content_translation_id'] ?? 0),
            'content_item_id' => (int) ($row['content_item_id'] ?? 0),
            'language_code' => (string) ($row['language_code'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'slug' => (string) ($row['slug'] ?? ''),
            'excerpt' => (string) ($row['excerpt'] ?? ''),
            'body_html' => (string) ($row['body_html'] ?? ''),
            'summary_html' => (string) ($row['summary_html'] ?? ''),
            'faq_json' => (string) ($row['faq_json'] ?? ''),
            'featured_image_path' => (string) ($row['featured_image_path'] ?? ''),
        ],
        'route_path' => (string) ($row['route_path'] ?? ''),
        'seo' => [
            'meta_title' => (string) ($row['meta_title'] ?? ''),
            'meta_description' => (string) ($row['meta_description'] ?? ''),
            'canonical_url' => (string) ($row['canonical_url'] ?? ''),
            'robots_index' => (int) ($row['robots_index'] ?? 1),
            'robots_follow' => (int) ($row['robots_follow'] ?? 1),
            'breadcrumb_title' => (string) ($row['breadcrumb_title'] ?? ''),
            'focus_keyword' => (string) ($row['focus_keyword'] ?? ''),
            'open_graph_title' => (string) ($row['open_graph_title'] ?? ''),
            'open_graph_description' => (string) ($row['open_graph_description'] ?? ''),
            'open_graph_image_path' => (string) ($row['open_graph_image_path'] ?? ''),
        ],
    ];
    $snapshotJson = (string) json_encode($snapshot, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $contentTranslationId = (int) ($row['content_translation_id'] ?? 0);
    $title = (string) ($row['title'] ?? '');

    $statement = $connection->prepare(
        'INSERT INTO content_revisions (
            content_translation_id,
            title,
            revision_type,
            snapshot_json,
            changed_by_admin_user_id
        ) VALUES (?, ?, ?, ?, NULL)'
    );
    $statement->bind_param('isss', $contentTranslationId, $title, $revisionType, $snapshotJson);
    $statement->execute();
    $statement->close();
}

function updateTranslation(mysqli $connection, int $contentTranslationId, array $payload): void
{
    $statement = $connection->prepare(
        'UPDATE content_translations
         SET excerpt = ?,
             body_html = ?,
             summary_html = ?,
             faq_json = ?,
             featured_image_path = ?
         WHERE content_translation_id = ?'
    );
    $statement->bind_param(
        'sssssi',
        $payload['excerpt'],
        $payload['body_html'],
        $payload['summary_html'],
        $payload['faq_json'],
        $payload['featured_image_path'],
        $contentTranslationId
    );
    $statement->execute();
    $statement->close();
}

function upsertSeo(mysqli $connection, int $contentTranslationId, array $payload): void
{
    $statement = $connection->prepare(
        'INSERT INTO seo_metadata (
            content_translation_id,
            meta_title,
            meta_description,
            canonical_url,
            robots_index,
            robots_follow,
            breadcrumb_title,
            focus_keyword,
            open_graph_title,
            open_graph_description,
            open_graph_image_path,
            schema_json
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            meta_title = VALUES(meta_title),
            meta_description = VALUES(meta_description),
            canonical_url = VALUES(canonical_url),
            robots_index = VALUES(robots_index),
            robots_follow = VALUES(robots_follow),
            breadcrumb_title = VALUES(breadcrumb_title),
            focus_keyword = VALUES(focus_keyword),
            open_graph_title = VALUES(open_graph_title),
            open_graph_description = VALUES(open_graph_description),
            open_graph_image_path = VALUES(open_graph_image_path),
            schema_json = VALUES(schema_json)'
    );
    $statement->bind_param(
        'isssiissssss',
        $contentTranslationId,
        $payload['meta_title'],
        $payload['meta_description'],
        $payload['canonical_url'],
        $payload['robots_index'],
        $payload['robots_follow'],
        $payload['breadcrumb_title'],
        $payload['focus_keyword'],
        $payload['open_graph_title'],
        $payload['open_graph_description'],
        $payload['open_graph_image_path'],
        $payload['schema_json']
    );
    $statement->execute();
    $statement->close();
}

function summarizeQueryIntents(array $queries): array
{
    $clusters = [];

    foreach ($queries as $query) {
        $intent = classifyIntent((string) ($query['query'] ?? ''));
        if (!isset($clusters[$intent])) {
            $clusters[$intent] = [
                'queries' => 0,
                'clicks' => 0,
                'impressions' => 0,
                'examples' => [],
            ];
        }

        $clusters[$intent]['queries']++;
        $clusters[$intent]['clicks'] += (int) ($query['clicks'] ?? 0);
        $clusters[$intent]['impressions'] += (int) ($query['impressions'] ?? 0);

        if (count($clusters[$intent]['examples']) < 8) {
            $clusters[$intent]['examples'][] = (string) ($query['query'] ?? '');
        }
    }

    ksort($clusters);

    return $clusters;
}

function cleanText(string $text): string
{
    $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

    return trim($text);
}

function normalizeSearchText(string $text): string
{
    $text = mb_strtolower(cleanText($text), 'UTF-8');
    $text = strtr($text, [
        'č' => 'c',
        'ć' => 'c',
        'đ' => 'd',
        'š' => 's',
        'ž' => 'z',
    ]);

    return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
}

function truncateText(string $text, int $limit): string
{
    $text = cleanText($text);

    if (mb_strlen($text, 'UTF-8') <= $limit) {
        return $text;
    }

    $cut = mb_substr($text, 0, max(0, $limit - 1), 'UTF-8');
    $space = mb_strrpos($cut, ' ', 0, 'UTF-8');

    if ($space !== false && $space > 60) {
        $cut = mb_substr($cut, 0, $space, 'UTF-8');
    }

    return rtrim($cut, " \t\n\r\0\x0B.,;:") . '…';
}

function escapeHtml(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
