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

$config = require $rootPath . '/config/app.php';
$connection = Database::connection($config);

if ($connection === null) {
    fwrite(STDERR, 'Database connection failed.' . PHP_EOL);
    exit(1);
}

$baseUrl = rtrim((string) ($config['base_url'] ?? 'https://aloavera-centar.com'), '/');
$structuredContent = new StructuredContentService();
$overrides = titleOverrides();
$report = [
    'generated_at' => date('c'),
    'updated' => [],
    'skipped' => [],
];

foreach ($overrides as $contentTranslationId => $title) {
    $row = findGeneratedTranslation($connection, (int) $contentTranslationId);
    if ($row === null) {
        $report['skipped'][] = [
            'content_translation_id' => (int) $contentTranslationId,
            'reason' => 'missing_generated_translation',
        ];
        continue;
    }

    $languageCode = (string) ($row['language_code'] ?? '');
    $slug = slugify($title);
    $routePath = uniqueRoutePath($connection, '/' . $languageCode . '/' . $slug . '/', (int) $contentTranslationId);
    $excerpt = localizedExcerpt($languageCode, $title);
    $bodyHtml = localizedBodyHtml($languageCode, $title, $excerpt);
    $summaryHtml = summaryHtml($languageCode, $title);
    $faqJson = $structuredContent->encodeFaqItems(defaultFaq($languageCode, $title));
    $metaTitle = normalizeMetaTitle($title);
    $metaDescription = metaDescription($languageCode, $title);
    $breadcrumbTitle = breadcrumbFromTitle($title);

    $connection->begin_transaction();

    try {
        $statement = $connection->prepare(
            'UPDATE content_translations
             SET title = ?, slug = ?, excerpt = ?, body_html = ?, summary_html = ?, faq_json = ?
             WHERE content_translation_id = ?'
        );
        $statement->bind_param('ssssssi', $title, $slug, $excerpt, $bodyHtml, $summaryHtml, $faqJson, $contentTranslationId);
        $statement->execute();
        $statement->close();

        $statement = $connection->prepare(
            'UPDATE content_routes
             SET route_path = ?, source_system = ?
             WHERE content_translation_id = ?
               AND is_primary = 1'
        );
        $sourceSystem = 'avc_seo_title_polish';
        $statement->bind_param('ssi', $routePath, $sourceSystem, $contentTranslationId);
        $statement->execute();
        $statement->close();

        $canonicalUrl = $baseUrl . $routePath;
        $robotsIndex = 1;
        $robotsFollow = 1;
        $statement = $connection->prepare(
            'INSERT INTO seo_metadata (
                content_translation_id,
                meta_title,
                meta_description,
                canonical_url,
                robots_index,
                robots_follow,
                breadcrumb_title
             ) VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                meta_title = VALUES(meta_title),
                meta_description = VALUES(meta_description),
                canonical_url = VALUES(canonical_url),
                robots_index = VALUES(robots_index),
                robots_follow = VALUES(robots_follow),
                breadcrumb_title = VALUES(breadcrumb_title)'
        );
        $statement->bind_param('isssiis', $contentTranslationId, $metaTitle, $metaDescription, $canonicalUrl, $robotsIndex, $robotsFollow, $breadcrumbTitle);
        $statement->execute();
        $statement->close();

        $connection->commit();
        $report['updated'][] = [
            'content_translation_id' => (int) $contentTranslationId,
            'language_code' => $languageCode,
            'route_path' => $routePath,
            'title' => $title,
        ];
    } catch (Throwable $throwable) {
        $connection->rollback();
        $report['skipped'][] = [
            'content_translation_id' => (int) $contentTranslationId,
            'reason' => 'exception',
            'message' => $throwable->getMessage(),
        ];
    }
}

$reportPath = $rootPath . '/storage/reports/generated_translation_title_polish_' . date('Ymd_His') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}
file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report + ['report_path' => $reportPath], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function titleOverrides(): array
{
    return [
        1464 => 'How Probiotics and Healthy Diets Can Work Together',
        1465 => 'C9 vs. F15: Which Forever FIT Program Should You Choose?',
        1466 => 'Clean 9 Program: The First Step Toward Vitality',
        1467 => 'Clean 9 and Health Conditions: When to Start and How to Adapt',
        1468 => 'Forever Vitolize for Men and Women: Benefits and How to Take It',
        1469 => 'Herbal Teas: When to Drink Them and How to Pair Them With Aloe Gel',
        1470 => 'DMO Routine for Success: A Daily System That Brings Results',
        1471 => 'Zumba, Pilates or CrossFit: How to Choose the Best Group Workout',
        1472 => 'Zero-Waste Cooking: How to Use Roots, Leaves and Stems',
        1473 => 'Antibacterial and Antifungal Effects of Aloe',
        1474 => 'Perfectionism and Procrastination: How to Break the Pattern in 7 Steps',
        1475 => 'Forever Arctic Sea Omega-3 za zdravje srca in možganov',
        1476 => 'Kako probiotiki in zdrave diete delujejo z roko v roki',
        1477 => 'C9 vs. F15: kateri Forever FIT program izbrati',
        1478 => 'Simptomi celiakije pri odraslih: prepoznajte zgodnje znake',
        1479 => 'Clean 9 program: prvi korak k vitalnosti',
        1480 => 'Forever Alpha-E Factor: skrivnost globinske hidracije',
        1481 => 'Forever Lean: praktična pot do zdravega uravnavanja telesne teže',
        1482 => 'C9 Forever Detox: kako začeti prehranski reset',
        1483 => 'R3 Factor: podpora za mladostnejši videz kože',
        1484 => 'Active Pro-B probiotik: zaveznik za zdravo prebavo',
        1485 => 'Forever Nutra Q10: podpora energiji in zdravju srca',
        1486 => 'Forever Marine Collagen: podpora koži in sklepom',
        1487 => 'Forever Lycium Plus: moč goji jagod za vitalnost',
        1488 => 'Aloe First sprej: večnamenska nega kože in las',
        1489 => 'Mrežni marketing in spletno poslovanje s Forever sistemom',
        1490 => 'Trdovraten kašelj: kako ga umiriti pri odraslih',
        1491 => 'Čaj proti kašlju: najboljše zeliščne mešanice',
        1492 => 'Psoriaza: naravne strategije, vzroki in podpora',
        1493 => 'Naravno lajšanje težkih nog: hidromasaža in aloe krema',
        1494 => 'Medicinska dieta: 15-dnevni načrt hujšanja',
        1495 => 'Parkinsonova bolezen in prehrana: antioksidanti kot podpora',
        1496 => 'Forever Living za otroke: varnost in prednosti izdelkov',
        1497 => 'Vpliv glasbe na zmogljivost: ustvarite energijsko playlisto',
        1498 => 'Gojenje aloe vere doma ali na vrtu: ključni pogoji',
        1499 => 'Reishi in shiitake: kako zdravilne gobe podpirajo imunost',
        1500 => 'Otroci z ADHD: umirjanje, prehrana in podpora',
        1501 => 'Sezona gripe: podpora imunosti s cinkom, vitaminom D in probiotiki',
        1502 => 'Kako ponovno aktivirati telo po premoru',
        1503 => 'Hipervitaminoza D: kako prepoznati prevelik vnos',
        1504 => 'Aloe vera vs. agava: kakšna je razlika',
        1505 => 'Najboljši čas za multivitamine: zjutraj ali zvečer',
        1506 => 'Naravna podpora plodnosti: maca, vitex in akupunktura',
        1507 => 'Čustvena inteligenca: razvijte samozavedanje',
        1508 => 'Stres pri otrocih: prepoznajte znake in jih umirite',
        1509 => 'Jetra in alkohol: koraki za okrevanje in vloga aloe vere',
        1510 => 'Zgaga v nosečnosti: naravne rešitve z aloe vero',
        1511 => 'Omega-3 maščobne kisline: 7 ključnih koristi',
        1512 => 'Vitamin D: zakaj je pomemben in kako ga optimalno vnesti',
        1513 => 'Škodljivci in bolezni aloe: vodnik za preprečevanje',
        1514 => 'Spomladanski detoks z juhami, smutiji in vadbo',
        1515 => 'C9 recepti: 5 hitrih jedi za uspeh programa Clean 9',
        1516 => 'Kuhanje pri nizkih temperaturah: ohranite hranila',
        1517 => 'Ajurvedski pristop k prehrani skozi doše in letne čase',
        1518 => 'Vitamin D: močne kosti, boljša imunost in razpoloženje',
        1519 => 'Naravna odvajala: psilij, aloe gel in suhe slive',
        1520 => 'Alergije na hrano: prehrana brez mleka, glutena in arašidov',
        1521 => 'Avtoimunsko vnetje črevesja: probiotiki in prehrana',
        1522 => 'Kako izbrati zanesljive proizvajalce superhrane',
        1523 => 'Sirup iz čebule in medu za kašelj',
        1524 => 'Težave s cirkulacijo: masaža in naravni geli',
        1525 => 'Antioksidativna moč aloe vere: vitamini in polifenoli',
        1526 => 'Sladki koren: naravni zaveznik za prebavo in pljuča',
        1527 => 'Kalcij in vitamin D: sinergija za močne kosti in zobe',
        1528 => 'Alkohol in kava: kako vplivata na prebavo in imunost',
        1529 => 'Stres pri mladostnikih: 7 korakov za starše',
        1530 => 'Clean 9 in zdravstvene težave: kdaj začeti in kako prilagoditi',
        1531 => 'Forever Vitolize za moške in ženske: koristi in uporaba',
        1532 => 'Zeliščni čaji: kdaj jih piti in kako jih povezati z aloe gelom',
        1533 => 'DMO rutina za uspeh: dnevni sistem za rezultate',
        1534 => 'Zumba, pilates ali crossfit: izberite najboljši skupinski trening',
        1535 => 'Forever Vitamin C in bakuchiol: set za sijočo kožo',
        1536 => 'Kuhanje brez odpadkov: uporabite korenine, liste in stebla',
        1537 => 'Antibakterijsko in protiglivično delovanje aloe',
        1538 => 'Perfekcionizem in odlašanje: iz rutine v 7 korakih',
    ];
}

function findGeneratedTranslation(mysqli $connection, int $contentTranslationId): ?array
{
    $statement = $connection->prepare(
        "SELECT ct.content_translation_id, ct.language_code, cr.route_path
         FROM content_translations ct
         INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
         WHERE ct.content_translation_id = ?
           AND cr.source_system IN ('avc_seo_hardening', 'avc_seo_title_polish')
         LIMIT 1"
    );
    $statement->bind_param('i', $contentTranslationId);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();

    return $row ?: null;
}

function localizedExcerpt(string $languageCode, string $title): string
{
    if ($languageCode === 'sl') {
        return 'Praktičen slovenski vodnik za temo ' . $title . ', z jasnimi koraki, notranjimi povezavami, podporo pri izbiri Forever izdelkov in realnimi pričakovanji.';
    }

    return 'A practical English guide to ' . $title . ', with clear next steps, internal links, Forever product support and realistic expectations before choosing a routine.';
}

function localizedBodyHtml(string $languageCode, string $title, string $excerpt): string
{
    if ($languageCode === 'sl') {
        return '<p>' . e($excerpt) . '</p>'
            . '<h2>Zakaj je ta tema pomembna</h2><p>Ta vodnik obravnava temo <strong>' . e($title) . '</strong> na praktičen način: kaj je smiselno vedeti, kako razmišljati o rutini in kje lahko Forever izdelki dopolnijo širšo podporo brez pretiranih obljub.</p>'
            . '<h2>Kako uporabiti priporočila v praksi</h2><p>Najboljši pristop je začeti z jasnim ciljem, preveriti obstoječe navade in šele nato izbrati izdelek ali članek za nadaljnje branje. Pri zdravju, zdravilih, nosečnosti ali kroničnih težavah je smiselno vključiti tudi strokovni nasvet.</p>'
            . '<p>Takšna struktura pomaga obiskovalcu hitro razumeti, ali je tema povezana z vsakodnevno rutino, prehrano, nego kože, energijo ali izbiro izdelka.</p>'
            . '<ul><li>Najprej določite cilj: prebava, energija, koža, imunska podpora ali splošna rutina.</li><li>Primerjajte izdelek z obstoječimi navadami, ne samo z obljubo na embalaži.</li><li>Uporabite notranje povezave za nadaljnje branje in lokalno priporočilo za naročanje.</li></ul>'
            . '<section data-avc-seo-hardening="v1"><h2>Notranje povezave in naslednji korak</h2><p>Za širši kontekst si oglejte <a href="/sl/">Aloe Vera Centar</a>, primerjajte <a href="/sl/proizvod/aloe-vera-gel/">Aloe Vera Gel</a> in uporabite <a href="/sl/#ai-advisor">AI svetovalca</a>, ko želite bolj oseben predlog. Takšna povezava članka, izdelka in svetovanja pomaga tudi iskalnikom razumeti, kako tema sodi v celotno platformo.</p></section>';
    }

    return '<p>' . e($excerpt) . '</p>'
        . '<h2>Why this topic matters</h2><p>This guide looks at <strong>' . e($title) . '</strong> in a practical way: what is useful to understand, how to think about the routine and where Forever products may support a broader plan without exaggerated promises.</p>'
        . '<h2>How to use the recommendations</h2><p>The strongest approach starts with a clear goal, a look at current habits and then a product or article that fits the real situation. When health conditions, medication, pregnancy or chronic symptoms are involved, professional advice should stay part of the decision.</p>'
        . '<p>This structure helps the visitor understand whether the topic connects with daily routine, nutrition, skin care, energy or a more informed product decision.</p>'
        . '<ul><li>Start with the goal: digestion, energy, skin, immune support or a simpler daily routine.</li><li>Compare the product with existing habits, not only with the promise on the label.</li><li>Use internal links for deeper reading and the local referral path for ordering.</li></ul>'
        . '<section data-avc-seo-hardening="v1"><h2>Internal links and next step</h2><p>For broader context, visit <a href="/en/">Aloe Vera Centar</a>, compare <a href="/en/proizvod/aloe-vera-gel/">Aloe Vera Gel</a> and use the <a href="/en/#ai-advisor">AI advisor</a> when you want a more personal suggestion. Connecting the article, product guide and advisor also helps search engines understand how the topic fits into the full platform.</p></section>';
}

function summaryHtml(string $languageCode, string $title): string
{
    if ($languageCode === 'sl') {
        return '<ul><li>Vodnik razloži temo ' . e($title) . ' v praktičnem jeziku.</li><li>Povezuje članek z izdelki, rutino in lokalnim priporočilom.</li><li>FAQ in notranje povezave pomagajo uporabnikom, Googlu in AI sistemom.</li></ul>';
    }

    return '<ul><li>This guide explains ' . e($title) . ' in practical language.</li><li>It connects the article with products, routine and the local referral path.</li><li>FAQ and internal links help users, Google and AI answer engines understand the page.</li></ul>';
}

function defaultFaq(string $languageCode, string $title): array
{
    if ($languageCode === 'sl') {
        return [
            ['question' => 'Kaj je glavni namen tega vodnika?', 'answer' => 'Pomaga razumeti temo ' . $title . ' in jo povezati s praktično rutino ali izborom Forever izdelka.'],
            ['question' => 'Ali članek nadomešča strokovni nasvet?', 'answer' => 'Ne. Pri zdravstvenih težavah, zdravilih ali posebnih stanjih je pomemben posvet s strokovnjakom.'],
            ['question' => 'Kje lahko nadaljujem?', 'answer' => 'Uporabite notranje povezave, vodnike izdelkov in AI svetovalca na Aloe Vera Centru.'],
        ];
    }

    return [
        ['question' => 'What is the main purpose of this guide?', 'answer' => 'It helps explain ' . $title . ' and connect the topic with a practical routine or Forever product choice.'],
        ['question' => 'Does this article replace professional advice?', 'answer' => 'No. Health conditions, medication, pregnancy or chronic symptoms should be discussed with a qualified professional.'],
        ['question' => 'Where can I continue next?', 'answer' => 'Use the internal links, product guides and AI advisor on Aloe Vera Centar.'],
    ];
}

function metaDescription(string $languageCode, string $title): string
{
    if ($languageCode === 'sl') {
        return mb_substr('Spoznajte ' . $title . ' skozi jasen slovenski vodnik, praktične korake, notranje povezave in podporo pri izbiri Forever izdelkov.', 0, 168);
    }

    return mb_substr('Explore ' . $title . ' with a practical English guide, clear next steps, internal links and Forever product support.', 0, 168);
}

function normalizeMetaTitle(string $title): string
{
    return mb_strlen($title) > 68 ? rtrim(mb_substr($title, 0, 65), ' -,') . '...' : $title;
}

function breadcrumbFromTitle(string $title): string
{
    return mb_strlen($title) > 80 ? rtrim(mb_substr($title, 0, 77), ' -,') . '...' : $title;
}

function uniqueRoutePath(mysqli $connection, string $candidate, int $contentTranslationId): string
{
    $candidate = normalizeRoutePath($candidate);
    $base = rtrim($candidate, '/');
    $attempt = $candidate;
    $counter = 2;

    while (routeExistsForAnotherTranslation($connection, $attempt, $contentTranslationId)) {
        $attempt = $base . '-' . $counter . '/';
        $counter++;
    }

    return $attempt;
}

function routeExistsForAnotherTranslation(mysqli $connection, string $routePath, int $contentTranslationId): bool
{
    $statement = $connection->prepare('SELECT 1 FROM content_routes WHERE route_path = ? AND content_translation_id != ? LIMIT 1');
    $statement->bind_param('si', $routePath, $contentTranslationId);
    $statement->execute();
    $exists = $statement->get_result()->fetch_assoc() !== null;
    $statement->close();

    return $exists;
}

function normalizeRoutePath(string $routePath): string
{
    $routePath = '/' . trim($routePath, '/') . '/';
    return $routePath === '//' ? '/' : $routePath;
}

function slugify(string $value): string
{
    $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    $value = strtolower($converted !== false ? $converted : $value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;
    $value = trim($value, '-');

    return $value !== '' ? $value : 'guide';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
