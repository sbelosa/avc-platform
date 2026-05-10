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

$arguments = array_slice($argv, 1);
$apply = in_array('--apply', $arguments, true);
$targetRoute = optionValue($arguments, '--route=');
$limit = (int) (optionValue($arguments, '--limit=') ?? 0);

$config = require $rootPath . '/config/app.php';
$connection = Database::connection($config);

if ($connection === null) {
    fwrite(STDERR, 'Database connection failed.' . PHP_EOL);
    exit(1);
}

$baseUrl = rtrim((string) ($config['base_url'] ?? 'https://aloevera-centar.com'), '/');
$structuredContent = new StructuredContentService();
$exportContent = loadExportContent($rootPath . '/exports/wordpress/content.json');
$translationMap = loadTranslationMap($rootPath . '/exports/wordpress/translations.json');

if ($exportContent === []) {
    fwrite(STDERR, 'Missing or empty WordPress content export.' . PHP_EOL);
    exit(1);
}

$report = [
    'generated_at' => date('c'),
    'mode' => $apply ? 'apply' : 'dry-run',
    'target_route' => $targetRoute,
    'limit' => $limit,
    'checked' => 0,
    'export_matches' => 0,
    'would_update' => 0,
    'updated' => 0,
    'unchanged' => 0,
    'missing_export' => 0,
    'route_conflicts' => 0,
    'with_generated_current_body' => 0,
    'fixed_generated_current_body' => 0,
    'normalized_bodies' => 0,
    'examples_updated' => [],
    'examples_missing_export' => [],
    'errors' => [],
];

$rows = fetchArticleTranslations($connection, $targetRoute, $limit);

foreach ($rows as $row) {
    $report['checked']++;

    $languageCode = strtolower(trim((string) ($row['language_code'] ?? '')));
    $contentTranslationId = (int) ($row['content_translation_id'] ?? 0);
    $contentItemId = (int) ($row['content_item_id'] ?? 0);
    $translationGroupId = (int) ($row['translation_group_id'] ?? 0);
    $routePath = (string) ($row['route_path'] ?? '');
    $currentBody = (string) ($row['body_html'] ?? '');
    $currentLooksGenerated = looksGenerated($currentBody, (string) ($row['title'] ?? ''));

    if ($currentLooksGenerated) {
        $report['with_generated_current_body']++;
    }

    $sourceWpPostId = (int) ($row['source_wp_post_id'] ?? 0);
    if ($sourceWpPostId <= 0 && isset($translationMap[$translationGroupId][$languageCode]['wp_post_id'])) {
        $sourceWpPostId = (int) $translationMap[$translationGroupId][$languageCode]['wp_post_id'];
    }

    $export = $sourceWpPostId > 0 ? ($exportContent[$sourceWpPostId] ?? null) : null;
    if (!is_array($export) || strtolower((string) ($export['language_code'] ?? '')) !== $languageCode) {
        $report['missing_export']++;
        rememberExample($report['examples_missing_export'], [
            'content_translation_id' => $contentTranslationId,
            'language_code' => $languageCode,
            'route_path' => $routePath,
            'title' => (string) ($row['title'] ?? ''),
            'source_wp_post_id' => $sourceWpPostId,
            'current_body_is_generated' => $currentLooksGenerated,
        ]);
        continue;
    }

    $report['export_matches']++;

    $payload = buildPayload($row, $export, $translationMap[$translationGroupId][$languageCode] ?? [], $baseUrl, $structuredContent);
    $changes = detectChanges($row, $payload);

    if ($changes === []) {
        $report['unchanged']++;
        continue;
    }

    if (in_array('body_html', $changes, true)) {
        $report['normalized_bodies']++;
    }

    if ($currentLooksGenerated && in_array('body_html', $changes, true)) {
        $report['fixed_generated_current_body']++;
    }

    $report['would_update']++;
    rememberExample($report['examples_updated'], [
        'content_translation_id' => $contentTranslationId,
        'language_code' => $languageCode,
        'route_path_before' => $routePath,
        'route_path_after' => $payload['route_path'],
        'title_before' => (string) ($row['title'] ?? ''),
        'title_after' => $payload['title'],
        'current_text_len' => textLength($currentBody),
        'export_text_len' => textLength($payload['body_html']),
        'changes' => $changes,
    ]);

    if (!$apply) {
        continue;
    }

    $connection->begin_transaction();

    try {
        saveRevision($connection, $row, 'wordpress_export_restore');
        updateTranslation($connection, $contentTranslationId, $sourceWpPostId, $payload);
        upsertRoute($connection, $contentTranslationId, $languageCode, $payload['route_path']);
        upsertSeo($connection, $contentTranslationId, $payload);
        $connection->commit();
        $report['updated']++;
    } catch (mysqli_sql_exception $exception) {
        $connection->rollback();

        if (str_contains($exception->getMessage(), 'Duplicate entry')) {
            $report['route_conflicts']++;
        }

        $report['errors'][] = [
            'content_translation_id' => $contentTranslationId,
            'language_code' => $languageCode,
            'route_path' => $payload['route_path'],
            'message' => $exception->getMessage(),
        ];
    } catch (Throwable $throwable) {
        $connection->rollback();
        $report['errors'][] = [
            'content_translation_id' => $contentTranslationId,
            'language_code' => $languageCode,
            'route_path' => $payload['route_path'],
            'message' => $throwable->getMessage(),
        ];
    }
}

$reportPath = $rootPath . '/storage/reports/article_translation_restore_' . date('Ymd_His') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}

file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report + ['report_path' => $reportPath], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function optionValue(array $arguments, string $prefix): ?string
{
    foreach ($arguments as $argument) {
        if (str_starts_with($argument, $prefix)) {
            return substr($argument, strlen($prefix));
        }
    }

    return null;
}

function fetchArticleTranslations(mysqli $connection, ?string $targetRoute, int $limit): array
{
    $sql = "SELECT
            ci.content_item_id,
            ci.translation_group_id,
            ct.content_translation_id,
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
            cr.route_path,
            sm.meta_title,
            sm.meta_description,
            sm.canonical_url,
            sm.robots_index,
            sm.robots_follow,
            sm.breadcrumb_title
        FROM content_items ci
        INNER JOIN content_translations ct ON ct.content_item_id = ci.content_item_id
        LEFT JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
        LEFT JOIN seo_metadata sm ON sm.content_translation_id = ct.content_translation_id
        WHERE ci.content_type = 'article'
          AND ci.lifecycle_status = 'published'";

    if ($targetRoute !== null && trim($targetRoute) !== '') {
        $statement = $connection->prepare($sql . ' AND cr.route_path = ? ORDER BY ci.content_item_id, ct.language_code');
        $statement->bind_param('s', $targetRoute);
        $statement->execute();
        $rows = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $statement->close();

        return $rows;
    }

    $sql .= ' ORDER BY ci.content_item_id, FIELD(ct.language_code, \'hr\', \'en\', \'sl\'), ct.language_code';
    if ($limit > 0) {
        $sql .= ' LIMIT ' . $limit;
    }

    $result = $connection->query($sql);

    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function loadExportContent(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $items = is_array($decoded['content'] ?? null) ? $decoded['content'] : (is_array($decoded) ? $decoded : []);
    $map = [];

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $wpPostId = (int) ($item['wp_post_id'] ?? 0);
        $postType = strtolower((string) ($item['post_type'] ?? ''));
        $bodyHtml = trim((string) ($item['body_html'] ?? ''));

        if ($wpPostId <= 0 || $postType !== 'post' || $bodyHtml === '') {
            continue;
        }

        $map[$wpPostId] = $item;
    }

    return $map;
}

function loadTranslationMap(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $groups = is_array($decoded['groups'] ?? null) ? $decoded['groups'] : (is_array($decoded) ? $decoded : []);
    $map = [];

    foreach ($groups as $group) {
        if (!is_array($group)) {
            continue;
        }

        $groupId = (int) ($group['translation_group_id'] ?? 0);
        if ($groupId <= 0) {
            continue;
        }

        foreach ((array) ($group['items'] ?? []) as $item) {
            if (!is_array($item)) {
                continue;
            }

            $languageCode = strtolower((string) ($item['language_code'] ?? ''));
            if ($languageCode === '') {
                continue;
            }

            $map[$groupId][$languageCode] = $item;
        }
    }

    return $map;
}

function buildPayload(array $row, array $export, array $translationEntry, string $baseUrl, StructuredContentService $structuredContent): array
{
    $languageCode = strtolower((string) ($row['language_code'] ?? 'hr')) ?: 'hr';
    $title = decodeText((string) ($export['title'] ?? $row['title'] ?? ''));
    $slug = trim((string) ($translationEntry['slug'] ?? $export['slug'] ?? $row['slug'] ?? ''));
    $routePath = trim((string) ($translationEntry['resolved_path'] ?? ''));
    $bodyHtml = normalizeArticleBodyHtml((string) ($export['body_html'] ?? ''));
    $plainText = articlePlainText($bodyHtml);
    $excerpt = cleanText((string) ($export['excerpt'] ?? ''));

    if ($excerpt === '') {
        $excerpt = buildExcerpt($plainText, $languageCode, $title);
    }

    if ($routePath === '') {
        $routePath = buildRoutePath($languageCode, $slug);
    }

    $routePath = normalizeRoutePath($routePath);
    $featuredImagePath = trim((string) ($row['featured_image_path'] ?? ''));
    if ($featuredImagePath === '') {
        $featuredImagePath = normalizeFeaturedImagePath((string) ($export['featured_image_url'] ?? ''));
    }

    $summaryHtml = buildSummaryHtml($languageCode, $title, $plainText);
    $faqJson = $structuredContent->encodeFaqItems(buildFaqItems($languageCode, $title));
    $metaDescription = buildMetaDescription($excerpt, $plainText, $languageCode, $title);

    return [
        'title' => $title,
        'slug' => $slug !== '' ? $slug : slugify($title),
        'excerpt' => $excerpt,
        'body_html' => $bodyHtml,
        'summary_html' => $summaryHtml,
        'faq_json' => $faqJson,
        'featured_image_path' => $featuredImagePath,
        'published_at' => (string) ($row['published_at'] ?? ''),
        'route_path' => $routePath,
        'meta_title' => truncateText($title, 68),
        'meta_description' => $metaDescription,
        'canonical_url' => $baseUrl . $routePath,
        'robots_index' => 1,
        'robots_follow' => 1,
        'breadcrumb_title' => truncateText($title, 80),
    ];
}

function normalizeArticleBodyHtml(string $html): string
{
    $html = decodeText($html);
    $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html) ?? $html;
    $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html) ?? $html;
    $html = preg_replace('/<!--.*?-->/s', '', $html) ?? $html;
    $html = preg_replace('/\[(\/?)(fusion|avada|woocommerce|contact-form-7|caption|gallery)[^\]]*\]/i', '', $html) ?? $html;
    $html = trim($html);

    if ($html === '') {
        return '';
    }

    $document = new DOMDocument('1.0', 'UTF-8');
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="UTF-8"><div id="avc-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    $root = $document->getElementById('avc-root');
    if (!$root instanceof DOMElement) {
        return $html;
    }

    $output = '';
    $paragraph = '';

    foreach (iterator_to_array($root->childNodes) as $node) {
        if ($node instanceof DOMText) {
            appendTextParagraphs($paragraph, $output, $node->nodeValue ?? '');
            continue;
        }

        if (!$node instanceof DOMElement) {
            continue;
        }

        $tagName = strtolower($node->tagName);
        if (!isBlockTag($tagName)) {
            $paragraph .= outerHtml($document, $node);
            continue;
        }

        flushParagraph($paragraph, $output);

        if ($tagName === 'h1') {
            $output .= headingHtml($document, $node, 'h2');
            continue;
        }

        $output .= outerHtml($document, $node);
    }

    flushParagraph($paragraph, $output);

    $output = preg_replace('/(?:\s*<p>\s*<\/p>\s*)+/i', '', $output) ?? $output;
    $output = preg_replace('/\s{2,}/u', ' ', $output) ?? $output;
    $output = preg_replace('/>\s+</u', ">\n<", $output) ?? $output;

    return trim($output);
}

function appendTextParagraphs(string &$paragraph, string &$output, string $text): void
{
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    $parts = preg_split('/\n{2,}/u', $text) ?: [$text];

    foreach ($parts as $index => $part) {
        $part = preg_replace('/\s+/u', ' ', $part) ?? $part;
        $part = trim($part);

        if ($part !== '') {
            $paragraph .= ($paragraph !== '' ? ' ' : '') . htmlspecialchars($part, ENT_QUOTES, 'UTF-8');
        }

        if ($index < count($parts) - 1) {
            flushParagraph($paragraph, $output);
        }
    }
}

function flushParagraph(string &$paragraph, string &$output): void
{
    $text = trim($paragraph);
    if ($text !== '') {
        $output .= '<p>' . $text . '</p>';
    }

    $paragraph = '';
}

function outerHtml(DOMDocument $document, DOMNode $node): string
{
    return (string) $document->saveHTML($node);
}

function headingHtml(DOMDocument $document, DOMElement $node, string $newTag): string
{
    $inner = '';
    foreach ($node->childNodes as $child) {
        $inner .= outerHtml($document, $child);
    }

    return '<' . $newTag . '>' . trim($inner) . '</' . $newTag . '>';
}

function isBlockTag(string $tagName): bool
{
    return in_array($tagName, [
        'address', 'article', 'aside', 'blockquote', 'div', 'dl', 'fieldset', 'figcaption', 'figure',
        'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hr', 'img', 'li', 'main',
        'nav', 'ol', 'p', 'pre', 'section', 'table', 'ul',
    ], true);
}

function detectChanges(array $row, array $payload): array
{
    $fields = [
        'title',
        'slug',
        'excerpt',
        'body_html',
        'summary_html',
        'faq_json',
        'featured_image_path',
        'route_path',
        'meta_title',
        'meta_description',
        'canonical_url',
        'robots_index',
        'robots_follow',
        'breadcrumb_title',
    ];
    $changes = [];

    foreach ($fields as $field) {
        $current = array_key_exists($field, $row) ? (string) ($row[$field] ?? '') : '';
        $next = (string) ($payload[$field] ?? '');

        if (normalizeForCompare($current) !== normalizeForCompare($next)) {
            $changes[] = $field;
        }
    }

    return $changes;
}

function normalizeForCompare(string $value): string
{
    return trim(str_replace(["\r\n", "\r"], "\n", $value));
}

function saveRevision(mysqli $connection, array $row, string $revisionType): void
{
    $snapshot = [
        'content_translation' => [
            'content_translation_id' => (int) ($row['content_translation_id'] ?? 0),
            'source_wp_post_id' => isset($row['source_wp_post_id']) ? (int) $row['source_wp_post_id'] : null,
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

function updateTranslation(mysqli $connection, int $contentTranslationId, int $sourceWpPostId, array $payload): void
{
    $sourceWpPostIdValue = $sourceWpPostId > 0 ? $sourceWpPostId : null;
    $statement = $connection->prepare(
        'UPDATE content_translations
         SET source_wp_post_id = ?,
             title = ?,
             slug = ?,
             excerpt = ?,
             body_html = ?,
             summary_html = ?,
             faq_json = ?,
             featured_image_path = ?
         WHERE content_translation_id = ?'
    );
    $statement->bind_param(
        'isssssssi',
        $sourceWpPostIdValue,
        $payload['title'],
        $payload['slug'],
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

function upsertRoute(mysqli $connection, int $contentTranslationId, string $languageCode, string $routePath): void
{
    $existing = $connection->prepare('SELECT content_route_id FROM content_routes WHERE content_translation_id = ? AND is_primary = 1 LIMIT 1');
    $existing->bind_param('i', $contentTranslationId);
    $existing->execute();
    $row = $existing->get_result()->fetch_assoc();
    $existing->close();

    if ($row !== null) {
        $contentRouteId = (int) ($row['content_route_id'] ?? 0);
        $statement = $connection->prepare(
            'UPDATE content_routes
             SET language_code = ?,
                 route_path = ?,
                 route_type = \'content\',
                 http_status_code = 200,
                 redirect_target_path = NULL,
                 source_system = \'wordpress_restore\',
                 is_primary = 1
             WHERE content_route_id = ?'
        );
        $statement->bind_param('ssi', $languageCode, $routePath, $contentRouteId);
        $statement->execute();
        $statement->close();

        return;
    }

    $statement = $connection->prepare(
        'INSERT INTO content_routes (
            language_code,
            route_path,
            content_translation_id,
            route_type,
            http_status_code,
            redirect_target_path,
            source_system,
            is_primary
        ) VALUES (?, ?, ?, \'content\', 200, NULL, \'wordpress_restore\', 1)'
    );
    $statement->bind_param('ssi', $languageCode, $routePath, $contentTranslationId);
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
    $statement->bind_param(
        'isssiis',
        $contentTranslationId,
        $payload['meta_title'],
        $payload['meta_description'],
        $payload['canonical_url'],
        $payload['robots_index'],
        $payload['robots_follow'],
        $payload['breadcrumb_title']
    );
    $statement->execute();
    $statement->close();
}

function buildSummaryHtml(string $languageCode, string $title, string $plainText): string
{
    $sentences = extractSentences($plainText, 2);
    $first = $sentences[0] ?? '';
    $second = $sentences[1] ?? '';

    if ($languageCode === 'en') {
        $items = [
            $first !== '' ? $first : 'The article gives context for the topic before choosing a product or routine.',
            $second !== '' ? $second : 'Use it as a practical starting point, especially when comparing several options.',
            'For personal questions, continue with a recommendation so the next step fits your situation.',
        ];
    } elseif ($languageCode === 'sl') {
        $items = [
            $first !== '' ? $first : 'Članek daje kontekst pred izbiro izdelka ali rutine.',
            $second !== '' ? $second : 'Uporabi ga kot praktičen začetek, posebej ko primerjaš več možnosti.',
            'Za osebna vprašanja nadaljuj s priporočilom, da bo naslednji korak ustrezal tvoji situaciji.',
        ];
    } else {
        $items = [
            $first !== '' ? $first : 'Članak daje kontekst prije odabira proizvoda ili rutine.',
            $second !== '' ? $second : 'Koristi ga kao praktičan početak, posebno kad uspoređuješ više opcija.',
            'Za osobna pitanja nastavi s preporukom kako bi sljedeći korak imao smisla za tvoju situaciju.',
        ];
    }

    $items = array_map(static fn (string $item): string => '<li>' . htmlspecialchars(truncateText($item, 170), ENT_QUOTES, 'UTF-8') . '</li>', $items);

    return '<ul>' . implode('', $items) . '</ul>';
}

function buildFaqItems(string $languageCode, string $title): array
{
    $shortTitle = truncateText($title, 90);

    if ($languageCode === 'en') {
        return [
            ['question' => 'What is useful to take from this article?', 'answer' => 'Use it to understand the topic more clearly and decide whether it connects with your current routine, habits or product comparison.'],
            ['question' => 'When should I ask for a recommendation?', 'answer' => 'Ask when you recognize the topic but are not sure which Forever product or next step fits your situation.'],
            ['question' => 'Does this replace professional advice?', 'answer' => $shortTitle . ' is educational content. For medical conditions, medication, pregnancy or persistent symptoms, include qualified professional advice.'],
        ];
    }

    if ($languageCode === 'sl') {
        return [
            ['question' => 'Kaj je koristno odnesti iz tega članka?', 'answer' => 'Uporabi ga za jasnejše razumevanje teme in za odločitev, ali se povezuje s tvojo rutino, navadami ali primerjavo izdelkov.'],
            ['question' => 'Kdaj je smiselno vprašati za priporočilo?', 'answer' => 'Vprašaj, ko se prepoznaš v temi, vendar nisi prepričan, kateri Forever izdelek ali naslednji korak ustreza tvoji situaciji.'],
            ['question' => 'Ali članek nadomešča strokovni nasvet?', 'answer' => $shortTitle . ' je izobraževalna vsebina. Pri zdravstvenih težavah, zdravilih, nosečnosti ali trdovratnih simptomih vključi strokovni nasvet.'],
        ];
    }

    return [
        ['question' => 'Što je korisno uzeti iz ovog članka?', 'answer' => 'Koristi ga da jasnije razumiješ temu i procijeniš povezuje li se s tvojom rutinom, navikama ili usporedbom proizvoda.'],
        ['question' => 'Kada ima smisla pitati za preporuku?', 'answer' => 'Pitaj kada se prepoznaješ u temi, ali nisi siguran koji Forever proizvod ili sljedeći korak najbolje odgovara tvojoj situaciji.'],
        ['question' => 'Zamjenjuje li članak stručni savjet?', 'answer' => $shortTitle . ' je edukativan sadržaj. Kod zdravstvenih tegoba, lijekova, trudnoće ili dugotrajnih simptoma uključi stručni savjet.'],
    ];
}

function buildExcerpt(string $plainText, string $languageCode, string $title): string
{
    $sentences = extractSentences($plainText, 2);
    $excerpt = trim(implode(' ', $sentences));

    if ($excerpt === '') {
        $excerpt = match ($languageCode) {
            'en' => 'Read a practical Aloe Vera Centar guide about ' . $title . ' before choosing the next step.',
            'sl' => 'Preberi praktičen vodič Aloe Vera Centra o temi ' . $title . ' pred naslednjim korakom.',
            default => 'Pročitaj praktičan vodič Aloe Vera Centra o temi ' . $title . ' prije sljedećeg koraka.',
        };
    }

    return truncateText($excerpt, 180);
}

function buildMetaDescription(string $excerpt, string $plainText, string $languageCode, string $title): string
{
    $description = trim($excerpt);
    if (mb_strlen($description) < 110) {
        $description = buildExcerpt($plainText, $languageCode, $title);
    }

    return truncateText($description, 168);
}

function extractSentences(string $text, int $limit): array
{
    $text = cleanText($text);
    $sentences = preg_split('/(?<=[.!?])\s+/u', $text) ?: [];
    $result = [];

    foreach ($sentences as $sentence) {
        $sentence = trim($sentence);
        if ($sentence === '' || mb_strlen($sentence) < 35) {
            continue;
        }

        $result[] = $sentence;
        if (count($result) >= $limit) {
            break;
        }
    }

    return $result;
}

function articlePlainText(string $html): string
{
    $html = preg_replace('/^\s*<h[1-3]\b[^>]*>.*?<\/h[1-3]>\s*/isu', '', $html, 1) ?? $html;

    return cleanText($html);
}

function looksGenerated(string $bodyHtml, string $title): bool
{
    $haystack = mb_strtolower($bodyHtml . ' ' . $title);

    return str_contains($haystack, 'data-avc-seo-hardening')
        || str_contains($haystack, 'this guide looks at')
        || str_contains($haystack, 'ta vodnik obravnava')
        || str_contains($haystack, 'practical guide:')
        || str_contains($haystack, 'praktični vodnik:')
        || str_contains($haystack, 'how to connect this guide with the next step')
        || str_contains($haystack, 'kako ta vodnik povezati z naslednjim korakom');
}

function normalizeFeaturedImagePath(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }

    $path = (string) parse_url($url, PHP_URL_PATH);

    return $path !== '' ? $path : $url;
}

function cleanText(string $value): string
{
    $value = preg_replace('/<br\s*\/?>/iu', ' ', $value) ?? $value;
    $value = preg_replace('/<\/(?:p|h1|h2|h3|h4|h5|h6|li|blockquote|div|section|article|tr|td|th)>/iu', '$0 ', $value) ?? $value;
    $value = decodeText(strip_tags($value));
    $value = preg_replace('/\[[^\]]+\]/u', ' ', $value) ?? $value;
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

    return trim($value);
}

function textLength(string $html): int
{
    return mb_strlen(cleanText($html));
}

function decodeText(string $value): string
{
    return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function truncateText(string $value, int $limit): string
{
    $value = cleanText($value);
    if (mb_strlen($value) <= $limit) {
        return $value;
    }

    return rtrim(mb_substr($value, 0, max(1, $limit - 3)), " \t\n\r\0\x0B.,;:-") . '...';
}

function buildRoutePath(string $languageCode, string $slug): string
{
    $slug = trim($slug !== '' ? $slug : 'clanak', '/');
    $prefix = match ($languageCode) {
        'en' => 'en',
        'sl' => 'sl',
        default => '',
    };

    return normalizeRoutePath(($prefix !== '' ? '/' . $prefix : '') . '/' . $slug . '/');
}

function normalizeRoutePath(string $routePath): string
{
    $routePath = '/' . trim($routePath, '/') . '/';

    return $routePath === '//' ? '/' : $routePath;
}

function slugify(string $value): string
{
    $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', decodeText($value));
    $value = strtolower($converted !== false ? $converted : $value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;
    $value = trim($value, '-');

    return $value !== '' ? $value : 'clanak';
}

function rememberExample(array &$examples, array $example): void
{
    if (count($examples) >= 12) {
        return;
    }

    $examples[] = $example;
}
