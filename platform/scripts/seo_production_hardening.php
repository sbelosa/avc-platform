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

$structuredContent = new StructuredContentService();
$baseUrl = rtrim((string) ($config['base_url'] ?? 'https://aloevera-centar.com'), '/');
$exportTranslations = loadExportTranslations($rootPath . '/exports/wordpress/translations.json');
$exportContent = loadExportContent($rootPath . '/exports/wordpress/content.json');

$report = [
    'generated_at' => date('c'),
    'legacy_pages_polished' => 0,
    'legacy_pages_noindexed' => 0,
    'localized_articles_expanded' => 0,
    'article_meta_descriptions_extended' => 0,
    'missing_translations_created' => 0,
    'missing_translations_from_export' => 0,
    'missing_translations_generated' => 0,
    'skipped' => [],
];

polishLegacyPages($connection, $structuredContent, $baseUrl, $report);
createMissingArticleTranslations($connection, $structuredContent, $baseUrl, $exportTranslations, $exportContent, $report);
// Disabled: this used to append generic SEO filler to localized articles.
// Original translations are restored by restore_wordpress_article_translations.php,
// and untranslated pages are tracked by article_translation_gap_report.php.
extendShortArticleDescriptions($connection, $report);

$reportPath = $rootPath . '/storage/reports/seo_production_hardening_' . date('Ymd_His') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}
file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo json_encode($report + ['report_path' => $reportPath], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

function polishLegacyPages(mysqli $connection, StructuredContentService $structuredContent, string $baseUrl, array &$report): void
{
    $polishedPages = legacyPagePayloads();
    $polishedRoutes = array_fill_keys(array_keys($polishedPages), true);
    $result = $connection->query(
        "SELECT ct.content_translation_id, ct.language_code, cr.route_path
         FROM content_translations ct
         INNER JOIN content_items ci ON ci.content_item_id = ct.content_item_id
         INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
         WHERE ci.content_type = 'page'
           AND ci.lifecycle_status = 'published'
           AND cr.http_status_code = 200"
    );

    while ($row = $result->fetch_assoc()) {
        $routePath = (string) ($row['route_path'] ?? '');
        $contentTranslationId = (int) ($row['content_translation_id'] ?? 0);
        $languageCode = (string) ($row['language_code'] ?? 'hr');

        if ($contentTranslationId <= 0 || $routePath === '') {
            continue;
        }

        if (isset($polishedRoutes[$routePath])) {
            $payload = $polishedPages[$routePath];
            updateTranslation($connection, $contentTranslationId, [
                'title' => $payload['title'],
                'excerpt' => $payload['excerpt'],
                'body_html' => $payload['body_html'],
                'summary_html' => $payload['summary_html'],
                'faq_json' => $structuredContent->encodeFaqItems($payload['faq_items']),
                'featured_image_path' => $payload['featured_image_path'],
            ]);
            upsertSeo($connection, $contentTranslationId, [
                'meta_title' => $payload['meta_title'],
                'meta_description' => $payload['meta_description'],
                'canonical_url' => $baseUrl . $routePath,
                'robots_index' => 1,
                'robots_follow' => 1,
                'breadcrumb_title' => $payload['breadcrumb_title'],
            ]);
            $report['legacy_pages_polished']++;
            continue;
        }

        $homePath = homePathForLanguage($languageCode);
        upsertSeo($connection, $contentTranslationId, [
            'meta_title' => noindexTitle($languageCode),
            'meta_description' => noindexDescription($languageCode),
            'canonical_url' => $baseUrl . $homePath,
            'robots_index' => 0,
            'robots_follow' => 1,
            'breadcrumb_title' => noindexBreadcrumb($languageCode),
        ]);
        $report['legacy_pages_noindexed']++;
    }
}

function createMissingArticleTranslations(
    mysqli $connection,
    StructuredContentService $structuredContent,
    string $baseUrl,
    array $exportTranslations,
    array $exportContent,
    array &$report
): void {
    foreach (['en', 'sl'] as $targetLanguage) {
        $statement = $connection->prepare(
            "SELECT
                ci.content_item_id,
                ci.translation_group_id,
                hr.content_translation_id AS source_translation_id,
                hr.source_wp_post_id,
                hr.title,
                hr.slug,
                hr.excerpt,
                hr.body_html,
                hr.summary_html,
                hr.faq_json,
                hr.featured_image_path,
                hr.published_at,
                hr_route.route_path
             FROM content_items ci
             INNER JOIN content_translations hr
                ON hr.content_item_id = ci.content_item_id
               AND hr.language_code = 'hr'
             INNER JOIN content_routes hr_route
                ON hr_route.content_translation_id = hr.content_translation_id
               AND hr_route.is_primary = 1
               AND hr_route.http_status_code = 200
             LEFT JOIN content_translations target
                ON target.content_item_id = ci.content_item_id
               AND target.language_code = ?
             WHERE ci.lifecycle_status = 'published'
               AND ci.content_type = 'article'
               AND target.content_translation_id IS NULL
             ORDER BY ci.content_item_id"
        );
        $statement->bind_param('s', $targetLanguage);
        $statement->execute();
        $rows = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $statement->close();

        foreach ($rows as $source) {
            $translationGroupId = (int) ($source['translation_group_id'] ?? 0);
            $exportItem = $exportTranslations[$translationGroupId][$targetLanguage] ?? null;
            $exportPost = is_array($exportItem) ? ($exportContent[(int) ($exportItem['wp_post_id'] ?? 0)] ?? null) : null;
            if (!is_array($exportItem) || !is_array($exportPost)) {
                $report['skipped'][] = [
                    'source_translation_id' => (int) ($source['source_translation_id'] ?? 0),
                    'content_item_id' => (int) ($source['content_item_id'] ?? 0),
                    'translation_group_id' => $translationGroupId,
                    'language_code' => $targetLanguage,
                    'reason' => 'missing_export_translation',
                ];
                continue;
            }

            $payload = localizedPayloadFromExport($targetLanguage, $source, $exportItem, $exportPost, $structuredContent);

            $routePath = is_array($exportItem) && !empty($exportItem['resolved_path'])
                ? normalizeRoutePath((string) $exportItem['resolved_path'])
                : uniqueRoutePath($connection, '/' . languagePrefix($targetLanguage) . '/' . trim((string) $payload['slug'], '/') . '/');

            $connection->begin_transaction();

            try {
                $contentTranslationId = insertTranslation($connection, (int) $source['content_item_id'], $targetLanguage, $payload);
                insertRoute($connection, $contentTranslationId, $targetLanguage, $routePath, 'avc_seo_hardening');
                upsertSeo($connection, $contentTranslationId, [
                    'meta_title' => $payload['meta_title'],
                    'meta_description' => $payload['meta_description'],
                    'canonical_url' => $baseUrl . $routePath,
                    'robots_index' => 1,
                    'robots_follow' => 1,
                    'breadcrumb_title' => $payload['breadcrumb_title'],
                ]);
                $connection->commit();

                $report['missing_translations_created']++;
                $report['missing_translations_from_export']++;
            } catch (Throwable $throwable) {
                $connection->rollback();
                $report['skipped'][] = [
                    'source_translation_id' => (int) ($source['source_translation_id'] ?? 0),
                    'language_code' => $targetLanguage,
                    'reason' => 'missing_translation_create_failed',
                    'message' => $throwable->getMessage(),
                ];
            }
        }
    }
}

function expandLocalizedArticles(mysqli $connection, StructuredContentService $structuredContent, string $baseUrl, array &$report): void
{
    $result = $connection->query(
        "SELECT
            ct.content_translation_id,
            ct.content_item_id,
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
         FROM content_translations ct
         INNER JOIN content_items ci ON ci.content_item_id = ct.content_item_id
         INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
         LEFT JOIN seo_metadata sm ON sm.content_translation_id = ct.content_translation_id
         WHERE ci.lifecycle_status = 'published'
           AND ci.content_type = 'article'
           AND ct.language_code IN ('en', 'sl')
           AND cr.http_status_code = 200"
    );

    while ($row = $result->fetch_assoc()) {
        $bodyHtml = (string) ($row['body_html'] ?? '');
        $bodyTextLength = mb_strlen(plainText($bodyHtml));
        $internalLinks = preg_match_all('/<a\s/i', $bodyHtml) ?: 0;

        if (str_contains($bodyHtml, 'data-avc-seo-hardening="v1"') || ($bodyTextLength >= 1200 && $internalLinks >= 2)) {
            continue;
        }

        $languageCode = (string) ($row['language_code'] ?? 'en');
        $bodyHtml .= "\n" . seoHardeningBlock($connection, $languageCode, $row);
        updateTranslation($connection, (int) $row['content_translation_id'], [
            'title' => (string) $row['title'],
            'excerpt' => (string) $row['excerpt'],
            'body_html' => $bodyHtml,
            'summary_html' => (string) $row['summary_html'],
            'faq_json' => ensureFaqJson($structuredContent, $languageCode, $row),
        ]);

        $metaDescription = normalizeMetaDescription($languageCode, (string) ($row['meta_description'] ?: $row['excerpt']), (string) $row['title']);
        upsertSeo($connection, (int) $row['content_translation_id'], [
            'meta_title' => normalizeMetaTitle((string) ($row['meta_title'] ?: $row['title'])),
            'meta_description' => $metaDescription,
            'canonical_url' => (string) ($row['canonical_url'] ?: $baseUrl . (string) $row['route_path']),
            'robots_index' => (int) ($row['robots_index'] ?? 1),
            'robots_follow' => (int) ($row['robots_follow'] ?? 1),
            'breadcrumb_title' => (string) ($row['breadcrumb_title'] ?: $row['title']),
        ]);

        $report['localized_articles_expanded']++;
    }
}

function extendShortArticleDescriptions(mysqli $connection, array &$report): void
{
    $result = $connection->query(
        "SELECT ct.content_translation_id, ct.language_code, ct.title, ct.excerpt, cr.route_path, sm.meta_title, sm.meta_description, sm.canonical_url, sm.robots_index, sm.robots_follow, sm.breadcrumb_title
         FROM content_translations ct
         INNER JOIN content_items ci ON ci.content_item_id = ct.content_item_id
         INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
         LEFT JOIN seo_metadata sm ON sm.content_translation_id = ct.content_translation_id
         WHERE ci.lifecycle_status = 'published'
           AND ci.content_type = 'article'
           AND cr.http_status_code = 200"
    );

    while ($row = $result->fetch_assoc()) {
        $current = trim((string) ($row['meta_description'] ?: $row['excerpt']));
        if ($current !== '' && mb_strlen($current) >= 110) {
            continue;
        }

        $metaDescription = normalizeMetaDescription((string) $row['language_code'], $current, (string) $row['title']);
        upsertSeo($connection, (int) $row['content_translation_id'], [
            'meta_title' => normalizeMetaTitle((string) ($row['meta_title'] ?: $row['title'])),
            'meta_description' => $metaDescription,
            'canonical_url' => (string) ($row['canonical_url'] ?: ''),
            'robots_index' => (int) ($row['robots_index'] ?? 1),
            'robots_follow' => (int) ($row['robots_follow'] ?? 1),
            'breadcrumb_title' => (string) ($row['breadcrumb_title'] ?: $row['title']),
        ]);
        $report['article_meta_descriptions_extended']++;
    }
}

function legacyPagePayloads(): array
{
    return [
        '/o-nama/' => pagePayload(
            'hr',
            'O nama',
            'Aloe Vera Centar je platforma za korisnike Forever proizvoda, preporuke, edukaciju i podršku pri odabiru proizvoda uz mogućnost 15% popusta.',
            'Aloe Vera Centar | O nama i podrška za Forever proizvode',
            'Saznajte kako Aloe Vera Centar pomaže korisnicima Forever proizvoda kroz članke, preporuke, AI savjetnika i podršku za kupnju uz 15% popusta.',
            'O nama'
        ),
        '/kontakt/' => pagePayload(
            'hr',
            'Kontakt',
            'Kontaktirajte Aloe Vera Centar za pomoć pri odabiru Forever proizvoda, korištenju preporuka i podršci prije kupnje.',
            'Kontakt | Aloe Vera Centar i Forever podrška',
            'Pošaljite upit Aloe Vera Centru i zatražite podršku oko Forever proizvoda, preporuka, popusta i odabira rutine.',
            'Kontakt'
        ),
        '/blog/' => pagePayload(
            'hr',
            'Blog',
            'Urednički vodiči Aloe Vera Centra povezuju Forever proizvode, wellness rutine, prehranu, njegu kože i korisne odgovore prije kupnje.',
            'Blog | Forever proizvodi, aloe vera i wellness vodiči',
            'Čitajte vodiče o Forever proizvodima, aloe veri, prehrani, njezi kože i pametnijem odabiru proizvoda uz podršku Aloe Vera Centra.',
            'Blog'
        ),
        '/forever-living-webshop-3/' => pagePayload(
            'hr',
            'Forever Living webshop',
            'Forever Living webshop kroz Aloe Vera Centar vodi vas prema originalnoj kupnji, lokalnom Forever shopu i preporuci s 15% popusta.',
            'Forever Living webshop | Kupnja uz preporuku i 15% popusta',
            'Naručite Forever proizvode putem lokalnog Forever shopa, uz preporuku Aloe Vera Centra, podršku pri odabiru i mogućnost 15% popusta.',
            'Forever webshop'
        ),
        '/en/about-us/' => pagePayload(
            'en',
            'About us',
            'Aloe Vera Centar helps Forever customers compare products, understand routines and choose with support before ordering.',
            'About Aloe Vera Centar | Forever product support',
            'Learn how Aloe Vera Centar supports Forever customers with product guides, useful articles, AI advice and referral-based ordering support.',
            'About us'
        ),
        '/en/contact/' => pagePayload(
            'en',
            'Contact',
            'Contact Aloe Vera Centar for help choosing Forever products, understanding recommendations and finding the right local ordering path.',
            'Contact | Aloe Vera Centar Forever support',
            'Send a request to Aloe Vera Centar and get support with Forever product choice, referral links, discounts and practical routines.',
            'Contact'
        ),
        '/en/blog-2/' => pagePayload(
            'en',
            'Blog',
            'The Aloe Vera Centar blog connects Forever products, aloe vera, wellness routines, nutrition and practical buyer education.',
            'Blog | Forever products, aloe vera and wellness guides',
            'Read guides about Forever products, aloe vera, nutrition, skin care and smarter product decisions with Aloe Vera Centar.',
            'Blog'
        ),
        '/en/forever-living-webshop/' => pagePayload(
            'en',
            'Forever Living webshop',
            'The Forever Living webshop experience through Aloe Vera Centar helps visitors reach the right local shop with referral support.',
            'Forever Living webshop | Referral ordering and support',
            'Order Forever products through the right local Forever shop with Aloe Vera Centar guidance, referral support and practical product advice.',
            'Forever webshop'
        ),
        '/sl/o-nas/' => pagePayload(
            'sl',
            'O nas',
            'Aloe Vera Centar pomaga uporabnikom Forever izdelkov razumeti izbiro, rutine, priporočila in lokalno naročanje.',
            'O Aloe Vera Centru | Podpora za Forever izdelke',
            'Spoznajte, kako Aloe Vera Centar podpira uporabnike Forever izdelkov z vodiči, članki, AI svetovalcem in pomočjo pri izbiri.',
            'O nas'
        ),
        '/sl/blog-2/' => pagePayload(
            'sl',
            'Blog',
            'Blog Aloe Vera Centra povezuje Forever izdelke, aloe vero, wellness rutine, prehrano, nego kože in praktično izobraževanje.',
            'Blog | Forever izdelki, aloe vera in wellness vodiči',
            'Berite vodiče o Forever izdelkih, aloe veri, prehrani, negi kože in pametnejši izbiri izdelkov z Aloe Vera Centrom.',
            'Blog'
        ),
        '/sl/spletna-trgovina-forever-living/' => pagePayload(
            'sl',
            'Spletna trgovina Forever Living',
            'Spletna trgovina Forever Living prek Aloe Vera Centra obiskovalce usmeri do lokalnega nakupa, priporočila in podpore pri izbiri.',
            'Spletna trgovina Forever Living | Naročanje s priporočilom',
            'Naročite Forever izdelke prek lokalne Forever trgovine z usmerjanjem Aloe Vera Centra, priporočilom in praktično podporo.',
            'Forever trgovina'
        ),
    ];
}

function pagePayload(string $languageCode, string $title, string $excerpt, string $metaTitle, string $metaDescription, string $breadcrumb): array
{
    $copy = pageCopy($languageCode);
    $home = homePathForLanguage($languageCode);
    $productPath = productPathForLanguage($languageCode);
    $advisorPath = $home . '#ai-advisor';
    $articlesPath = $home . '#latest-articles';
    $bodyHtml = '<p>' . e($excerpt) . '</p>'
        . '<h2>' . e($copy['why_heading']) . '</h2>'
        . '<p>' . e($copy['why_body']) . '</p>'
        . '<h2>' . e($copy['support_heading']) . '</h2>'
        . '<p>' . e($copy['support_body']) . '</p>'
        . '<ul><li>' . e($copy['point_1']) . '</li><li>' . e($copy['point_2']) . '</li><li>' . e($copy['point_3']) . '</li></ul>'
        . '<p><a href="' . e($productPath) . '">' . e($copy['product_link']) . '</a> ' . e($copy['link_joiner']) . ' <a href="' . e($articlesPath) . '">' . e($copy['article_link']) . '</a> ' . e($copy['link_joiner']) . ' <a href="' . e($advisorPath) . '">' . e($copy['advisor_link']) . '</a>.</p>';

    return [
        'title' => $title,
        'excerpt' => $excerpt,
        'body_html' => $bodyHtml,
        'summary_html' => '<ul><li>' . e($copy['summary_1']) . '</li><li>' . e($copy['summary_2']) . '</li><li>' . e($copy['summary_3']) . '</li></ul>',
        'faq_items' => [
            ['question' => $copy['faq_q1'], 'answer' => $copy['faq_a1']],
            ['question' => $copy['faq_q2'], 'answer' => $copy['faq_a2']],
            ['question' => $copy['faq_q3'], 'answer' => $copy['faq_a3']],
        ],
        'featured_image_path' => '/media/branding/avc-home-premium-og-wide.png',
        'meta_title' => $metaTitle,
        'meta_description' => $metaDescription,
        'breadcrumb_title' => $breadcrumb,
    ];
}

function pageCopy(string $languageCode): array
{
    return match ($languageCode) {
        'en' => [
            'why_heading' => 'Why this page matters for Forever customers',
            'why_body' => 'Aloe Vera Centar is being shaped as a practical support platform: product education, local referral routing, useful articles and help before the visitor decides what to order.',
            'support_heading' => 'How the platform supports the decision',
            'support_body' => 'The goal is to connect content with the right Forever product guide, a local ordering path and a clear explanation of what a routine can realistically do.',
            'point_1' => 'Clear product guides for comparing goals, ingredients and use cases.',
            'point_2' => 'Educational articles written for humans, Google and AI answer engines.',
            'point_3' => 'Referral paths that preserve the visitor journey toward the correct Forever shop.',
            'product_link' => 'Browse recommended Forever products',
            'article_link' => 'read helpful articles',
            'advisor_link' => 'ask the AI advisor',
            'link_joiner' => 'or',
            'summary_1' => 'A clean trust page replaces old WordPress shortcode content.',
            'summary_2' => 'Internal links point visitors toward products, articles and AI support.',
            'summary_3' => 'The page is indexable with a focused meta title and description.',
            'faq_q1' => 'Can Aloe Vera Centar help me choose a Forever product?',
            'faq_a1' => 'Yes. The platform connects product guides, articles and advisor support so the choice is easier and more informed.',
            'faq_q2' => 'Does ordering happen on Aloe Vera Centar?',
            'faq_a2' => 'No. The site guides visitors toward the appropriate official Forever ordering path through referral links.',
            'faq_q3' => 'Is this content written for search and AI discovery?',
            'faq_a3' => 'Yes. The structure uses clear headings, summaries, FAQ blocks and internal links to make the page easier to understand.',
        ],
        'sl' => [
            'why_heading' => 'Zakaj je ta stran pomembna za uporabnike Forever izdelkov',
            'why_body' => 'Aloe Vera Centar se razvija kot praktična podporna platforma: izobraževanje o izdelkih, lokalno usmerjanje, koristni članki in pomoč pred odločitvijo za naročilo.',
            'support_heading' => 'Kako platforma podpira odločitev',
            'support_body' => 'Cilj je povezati vsebino s pravim vodičem za Forever izdelek, lokalno potjo naročanja in jasnim razumevanjem realne rutine.',
            'point_1' => 'Jasni vodiči za primerjavo ciljev, sestavin in načinov uporabe.',
            'point_2' => 'Izobraževalni članki, napisani za ljudi, Google in AI iskalnike odgovorov.',
            'point_3' => 'Poti priporočil, ki obiskovalca vodijo do ustrezne Forever trgovine.',
            'product_link' => 'Oglejte si priporočene Forever izdelke',
            'article_link' => 'preberite koristne članke',
            'advisor_link' => 'vprašajte AI svetovalca',
            'link_joiner' => 'ali',
            'summary_1' => 'Čista stran zaupanja nadomešča staro WordPress shortcode vsebino.',
            'summary_2' => 'Notranje povezave vodijo do izdelkov, člankov in AI podpore.',
            'summary_3' => 'Stran ima urejen meta naslov, opis in jasen namen za indeksacijo.',
            'faq_q1' => 'Ali mi Aloe Vera Centar pomaga izbrati Forever izdelek?',
            'faq_a1' => 'Da. Platforma povezuje vodiče izdelkov, članke in svetovalno podporo za lažjo odločitev.',
            'faq_q2' => 'Ali naročilo poteka na Aloe Vera Centru?',
            'faq_a2' => 'Ne. Stran obiskovalca usmeri na ustrezno uradno Forever pot naročanja prek priporočilnih povezav.',
            'faq_q3' => 'Ali je vsebina pripravljena za Google in AI odkrivanje?',
            'faq_a3' => 'Da. Struktura uporablja jasne naslove, povzetke, FAQ bloke in notranje povezave.',
        ],
        default => [
            'why_heading' => 'Zašto je ova stranica važna za Forever korisnike',
            'why_body' => 'Aloe Vera Centar razvija se kao praktična platforma podrške: edukacija o proizvodima, lokalno usmjeravanje, korisni članci i pomoć prije odluke o naručivanju.',
            'support_heading' => 'Kako platforma pomaže pri odluci',
            'support_body' => 'Cilj je povezati sadržaj s pravim Forever vodičem, lokalnim putem naručivanja i jasnim objašnjenjem što rutina realno može donijeti.',
            'point_1' => 'Jasni vodiči za usporedbu ciljeva, sastojaka i načina korištenja.',
            'point_2' => 'Edukativni članci pisani za ljude, Google i AI sustave odgovora.',
            'point_3' => 'Preporučene putanje koje posjetitelja vode prema ispravnom Forever shopu.',
            'product_link' => 'Pregledaj preporučene Forever proizvode',
            'article_link' => 'čitaj korisne članke',
            'advisor_link' => 'pitaj AI savjetnika',
            'link_joiner' => 'ili',
            'summary_1' => 'Čista stranica povjerenja zamjenjuje stari WordPress shortcode sadržaj.',
            'summary_2' => 'Interni linkovi vode prema proizvodima, člancima i AI podršci.',
            'summary_3' => 'Stranica ima uređen meta naslov, opis i jasnu svrhu za indeksaciju.',
            'faq_q1' => 'Može li Aloe Vera Centar pomoći pri odabiru Forever proizvoda?',
            'faq_a1' => 'Da. Platforma povezuje vodiče proizvoda, članke i savjetničku podršku kako bi odluka bila jasnija.',
            'faq_q2' => 'Odvija li se narudžba na Aloe Vera Centru?',
            'faq_a2' => 'Ne. Stranica posjetitelja usmjerava prema odgovarajućem službenom Forever putu naručivanja preko preporuka.',
            'faq_q3' => 'Je li sadržaj pripremljen za Google i AI pronalaženje?',
            'faq_a3' => 'Da. Struktura koristi jasne naslove, sažetke, FAQ blokove i interne poveznice.',
        ],
    };
}

function localizedPayloadFromExport(string $languageCode, array $source, array $exportItem, array $exportPost, StructuredContentService $structuredContent): array
{
    $title = decodeText((string) ($exportPost['title'] ?? $exportItem['title'] ?? ''));
    $excerpt = cleanShortcodeText((string) ($exportPost['excerpt'] ?? ''));
    $bodyHtml = (string) ($exportPost['body_html'] ?? '');
    if (trim($excerpt) === '') {
        $excerpt = normalizeMetaDescription($languageCode, '', $title);
    }

    $bodyHtml = trim($bodyHtml) !== ''
        ? $bodyHtml . "\n" . generatedSupportBlock($languageCode, $title, $excerpt)
        : generatedArticleBody($languageCode, $title, (string) ($source['title'] ?? ''), $excerpt);

    return [
        'source_wp_post_id' => (int) ($exportItem['wp_post_id'] ?? 0),
        'title' => $title,
        'slug' => trim((string) ($exportItem['slug'] ?? slugify($title)), '/'),
        'excerpt' => $excerpt,
        'body_html' => $bodyHtml,
        'summary_html' => summaryHtml($languageCode, $title),
        'faq_json' => $structuredContent->encodeFaqItems(defaultFaq($languageCode, $title)),
        'featured_image_path' => (string) ($source['featured_image_path'] ?? ''),
        'published_at' => (string) ($source['published_at'] ?? ''),
        'meta_title' => normalizeMetaTitle($title),
        'meta_description' => normalizeMetaDescription($languageCode, $excerpt, $title),
        'breadcrumb_title' => breadcrumbFromTitle($title),
    ];
}

function generatedLocalizedPayload(string $languageCode, array $source, StructuredContentService $structuredContent): array
{
    $sourceTitle = decodeText((string) ($source['title'] ?? ''));
    $sourceExcerpt = cleanShortcodeText((string) ($source['excerpt'] ?? ''));
    $title = generatedLocalizedTitle($languageCode, $sourceTitle);
    $excerpt = generatedLocalizedExcerpt($languageCode, $sourceTitle, $sourceExcerpt);

    return [
        'source_wp_post_id' => null,
        'title' => $title,
        'slug' => slugify(($languageCode === 'en' ? 'guide ' : 'vodnik ') . (string) ($source['slug'] ?: $sourceTitle)),
        'excerpt' => $excerpt,
        'body_html' => generatedArticleBody($languageCode, $title, $sourceTitle, $excerpt),
        'summary_html' => summaryHtml($languageCode, $title),
        'faq_json' => $structuredContent->encodeFaqItems(defaultFaq($languageCode, $title)),
        'featured_image_path' => (string) ($source['featured_image_path'] ?? ''),
        'published_at' => (string) ($source['published_at'] ?? ''),
        'meta_title' => normalizeMetaTitle($title),
        'meta_description' => normalizeMetaDescription($languageCode, $excerpt, $title),
        'breadcrumb_title' => breadcrumbFromTitle($title),
    ];
}

function generatedArticleBody(string $languageCode, string $title, string $sourceTitle, string $excerpt): string
{
    if ($languageCode === 'sl') {
        return '<p>' . e($excerpt) . '</p>'
            . '<h2>Zakaj je ta tema pomembna</h2><p>Ta vodnik obravnava temo <strong>' . e($sourceTitle) . '</strong> na praktičen način: kaj je smiselno vedeti, kako razmišljati o rutini in kje lahko Forever izdelki dopolnijo širšo podporo brez pretiranih obljub.</p>'
            . '<h2>Kako uporabiti priporočila v praksi</h2><p>Najboljši pristop je začeti z jasnim ciljem, preveriti obstoječe navade in šele nato izbrati izdelek ali članek za nadaljnje branje. Pri zdravju, zdravilih, nosečnosti ali kroničnih težavah je smiselno vključiti tudi strokovni nasvet.</p>'
            . '<ul><li>Najprej določite cilj: prebava, energija, koža, imunska podpora ali splošna rutina.</li><li>Primerjajte izdelek z obstoječimi navadami, ne samo z obljubo na embalaži.</li><li>Uporabite notranje povezave za nadaljnje branje in lokalno priporočilo za naročanje.</li></ul>'
            . generatedSupportBlock($languageCode, $title, $excerpt);
    }

    return '<p>' . e($excerpt) . '</p>'
        . '<h2>Why this topic matters</h2><p>This guide looks at <strong>' . e($sourceTitle) . '</strong> in a practical way: what is useful to understand, how to think about the routine and where Forever products may support a broader plan without exaggerated promises.</p>'
        . '<h2>How to use the recommendations</h2><p>The strongest approach starts with a clear goal, a look at current habits and then a product or article that fits the real situation. When health conditions, medication, pregnancy or chronic symptoms are involved, professional advice should stay part of the decision.</p>'
        . '<ul><li>Start with the goal: digestion, energy, skin, immune support or a simpler daily routine.</li><li>Compare the product with existing habits, not only with the promise on the label.</li><li>Use internal links for deeper reading and the local referral path for ordering.</li></ul>'
        . generatedSupportBlock($languageCode, $title, $excerpt);
}

function seoHardeningBlock(mysqli $connection, string $languageCode, array $row): string
{
    $links = relatedLinks($connection, $languageCode, (int) ($row['content_item_id'] ?? 0));
    $title = decodeText((string) ($row['title'] ?? ''));
    $excerpt = cleanShortcodeText((string) ($row['excerpt'] ?? ''));

    if ($languageCode === 'sl') {
        return '<section data-avc-seo-hardening="v1"><h2>Kako ta vodnik povezati z naslednjim korakom</h2><p>Če raziskujete temo <strong>' . e($title) . '</strong>, je najkoristneje povezati informacije iz članka z realnim ciljem: boljša rutina, jasnejši izbor izdelka, manj zmede pred naročilom in varnejše razumevanje pričakovanj.</p><p>' . e($excerpt !== '' ? $excerpt : 'Ta vsebina je zasnovana kot praktičen vodnik za obiskovalce, ki želijo razumeti temo in jo povezati s smiselno podporo.') . '</p><ul><li>Preverite, kateri cilj želite podpreti in katere navade že imate.</li><li>Primerjajte povezane Forever vodiče, preden kliknete na lokalno naročanje.</li><li>Pri zdravstvenih vprašanjih uporabite članek kot podporo, ne kot zamenjavo za strokovni nasvet.</li></ul><p>Za nadaljnje branje odprite <a href="' . e($links[0]['href']) . '">' . e($links[0]['label']) . '</a>, primerjajte <a href="' . e($links[1]['href']) . '">' . e($links[1]['label']) . '</a> ali uporabite <a href="' . e($links[2]['href']) . '">' . e($links[2]['label']) . '</a> za osebnejše usmerjanje.</p></section>';
    }

    return '<section data-avc-seo-hardening="v1"><h2>How to connect this guide with the next step</h2><p>If you are researching <strong>' . e($title) . '</strong>, the most useful next step is to connect the article with a real goal: a clearer routine, a better product choice, less confusion before ordering and more realistic expectations.</p><p>' . e($excerpt !== '' ? $excerpt : 'This content is designed as a practical guide for visitors who want to understand the topic and connect it with sensible support.') . '</p><ul><li>Clarify which goal you want to support and which habits are already in place.</li><li>Compare related Forever guides before using a local ordering link.</li><li>For health questions, use the article as support, not as a replacement for professional advice.</li></ul><p>For deeper reading, open <a href="' . e($links[0]['href']) . '">' . e($links[0]['label']) . '</a>, compare <a href="' . e($links[1]['href']) . '">' . e($links[1]['label']) . '</a> or use the <a href="' . e($links[2]['href']) . '">' . e($links[2]['label']) . '</a> for more personal guidance.</p></section>';
}

function generatedSupportBlock(string $languageCode, string $title, string $excerpt): string
{
    if ($languageCode === 'sl') {
        return '<h2>Notranje povezave in priporočila</h2><p>Za širši kontekst si oglejte <a href="/sl/">Aloe Vera Centar</a>, primerjajte <a href="/sl/proizvod/aloe-vera-gel/">Aloe Vera Gel</a> in uporabite <a href="/sl/#ai-advisor">AI svetovalca</a>, ko želite bolj oseben predlog. Takšna povezava članka, izdelka in svetovanja pomaga tudi iskalnikom razumeti, kako tema sodi v celotno platformo.</p>';
    }

    return '<h2>Internal links and recommendations</h2><p>For broader context, visit <a href="/en/">Aloe Vera Centar</a>, compare <a href="/en/proizvod/aloe-vera-gel/">Aloe Vera Gel</a> and use the <a href="/en/#ai-advisor">AI advisor</a> when you want a more personal suggestion. Connecting the article, product guide and advisor also helps search engines understand how the topic fits into the full platform.</p>';
}

function relatedLinks(mysqli $connection, string $languageCode, int $excludeContentItemId): array
{
    $home = homePathForLanguage($languageCode);
    $fallback = $languageCode === 'sl'
        ? [
            ['href' => '/sl/', 'label' => 'Aloe Vera Centar'],
            ['href' => '/sl/proizvod/aloe-vera-gel/', 'label' => 'Aloe Vera Gel'],
            ['href' => '/sl/#ai-advisor', 'label' => 'AI svetovalec'],
        ]
        : [
            ['href' => '/en/', 'label' => 'Aloe Vera Centar'],
            ['href' => '/en/proizvod/aloe-vera-gel/', 'label' => 'Aloe Vera Gel'],
            ['href' => '/en/#ai-advisor', 'label' => 'AI advisor'],
        ];

    $statement = $connection->prepare(
        "SELECT cr.route_path, ct.title
         FROM content_translations ct
         INNER JOIN content_items ci ON ci.content_item_id = ct.content_item_id
         INNER JOIN content_routes cr ON cr.content_translation_id = ct.content_translation_id AND cr.is_primary = 1
         WHERE ct.language_code = ?
           AND ci.lifecycle_status = 'published'
           AND ci.content_type IN ('product_guide', 'article')
           AND ci.content_item_id != ?
         ORDER BY CASE ci.content_type WHEN 'product_guide' THEN 0 ELSE 1 END, COALESCE(ct.published_at, ct.updated_at) DESC
         LIMIT 2"
    );
    $statement->bind_param('si', $languageCode, $excludeContentItemId);
    $statement->execute();
    $rows = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    $statement->close();

    $links = [];
    foreach ($rows as $row) {
        $links[] = [
            'href' => (string) ($row['route_path'] ?? $home),
            'label' => breadcrumbFromTitle((string) ($row['title'] ?? 'Aloe Vera Centar')),
        ];
    }

    $links[] = $fallback[2];

    return array_slice(array_merge($links, $fallback), 0, 3);
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

function ensureFaqJson(StructuredContentService $structuredContent, string $languageCode, array $row): string
{
    $existing = trim((string) ($row['faq_json'] ?? ''));
    if ($existing !== '') {
        return $existing;
    }

    return $structuredContent->encodeFaqItems(defaultFaq($languageCode, (string) ($row['title'] ?? '')));
}

function generatedLocalizedTitle(string $languageCode, string $sourceTitle): string
{
    $sourceTitle = decodeText($sourceTitle);
    return $languageCode === 'sl'
        ? 'Praktični vodnik: ' . $sourceTitle
        : 'Practical Guide: ' . $sourceTitle;
}

function generatedLocalizedExcerpt(string $languageCode, string $sourceTitle, string $sourceExcerpt): string
{
    $sourceTitle = decodeText($sourceTitle);
    if ($languageCode === 'sl') {
        return 'Praktičen slovenski vodnik za temo ' . $sourceTitle . ', z jasnimi koraki, notranjimi povezavami, podporo pri izbiri Forever izdelkov in realnimi pričakovanji.';
    }

    return 'A practical English guide to ' . $sourceTitle . ', with clear next steps, internal links, Forever product support and realistic expectations before choosing a routine.';
}

function normalizeMetaDescription(string $languageCode, string $description, string $title): string
{
    $description = cleanShortcodeText($description);
    if (mb_strlen($description) >= 110 && mb_strlen($description) <= 170) {
        return $description;
    }

    if ($languageCode === 'sl') {
        $fallback = 'Spoznajte ' . breadcrumbFromTitle($title) . ' skozi jasen vodnik, praktične korake, notranje povezave in podporo pri izbiri Forever izdelkov.';
    } elseif ($languageCode === 'en') {
        $fallback = 'Explore ' . breadcrumbFromTitle($title) . ' with a practical guide, clear next steps, internal links and Forever product support.';
    } else {
        $fallback = 'Saznajte više o temi ' . breadcrumbFromTitle($title) . ' kroz praktičan vodič, jasne korake, interne linkove i Forever podršku.';
    }

    if ($description !== '') {
        $combined = rtrim($description, '. ') . '. ' . $fallback;
        return mb_substr($combined, 0, 168);
    }

    return mb_substr($fallback, 0, 168);
}

function normalizeMetaTitle(string $title): string
{
    $title = decodeText(trim($title));
    if (mb_strlen($title) > 68) {
        return rtrim(mb_substr($title, 0, 65), ' -,') . '...';
    }

    return $title;
}

function breadcrumbFromTitle(string $title): string
{
    $title = decodeText(strip_tags($title));
    return mb_strlen($title) > 80 ? rtrim(mb_substr($title, 0, 77), ' -,') . '...' : $title;
}

function noindexTitle(string $languageCode): string
{
    return match ($languageCode) {
        'en' => 'Utility page | Aloe Vera Centar',
        'sl' => 'Tehnična stran | Aloe Vera Centar',
        default => 'Tehnička stranica | Aloe Vera Centar',
    };
}

function noindexDescription(string $languageCode): string
{
    return match ($languageCode) {
        'en' => 'This legacy utility page is kept for compatibility and internal navigation, while search engines should follow links and skip indexing.',
        'sl' => 'Ta stara tehnična stran je ohranjena zaradi združljivosti in notranje navigacije, za iskalnike pa je označena kot noindex.',
        default => 'Ova stara tehnička stranica zadržana je zbog kompatibilnosti i interne navigacije, ali je označena kao noindex.',
    };
}

function noindexBreadcrumb(string $languageCode): string
{
    return match ($languageCode) {
        'en' => 'Utility page',
        'sl' => 'Tehnična stran',
        default => 'Tehnička stranica',
    };
}

function homePathForLanguage(string $languageCode): string
{
    return match ($languageCode) {
        'en' => '/en/',
        'sl' => '/sl/',
        default => '/',
    };
}

function productPathForLanguage(string $languageCode): string
{
    return match ($languageCode) {
        'en' => '/en/proizvod/aloe-vera-gel/',
        'sl' => '/sl/proizvod/aloe-vera-gel/',
        default => '/proizvod/aloe-vera-gel/',
    };
}

function languagePrefix(string $languageCode): string
{
    return match ($languageCode) {
        'en' => 'en',
        'sl' => 'sl',
        default => '',
    };
}

function insertTranslation(mysqli $connection, int $contentItemId, string $languageCode, array $payload): int
{
    $sourceWpPostId = isset($payload['source_wp_post_id']) && (int) $payload['source_wp_post_id'] > 0
        ? (int) $payload['source_wp_post_id']
        : null;
    $statement = $connection->prepare(
        'INSERT INTO content_translations (
            content_item_id,
            source_wp_post_id,
            language_code,
            title,
            slug,
            excerpt,
            body_html,
            body_json,
            summary_html,
            faq_json,
            featured_image_path,
            published_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?, ?)'
    );
    $statement->bind_param(
        'iisssssssss',
        $contentItemId,
        $sourceWpPostId,
        $languageCode,
        $payload['title'],
        $payload['slug'],
        $payload['excerpt'],
        $payload['body_html'],
        $payload['summary_html'],
        $payload['faq_json'],
        $payload['featured_image_path'],
        $payload['published_at']
    );
    $statement->execute();
    $insertId = (int) $connection->insert_id;
    $statement->close();

    return $insertId;
}

function updateTranslation(mysqli $connection, int $contentTranslationId, array $payload): void
{
    if (array_key_exists('featured_image_path', $payload)) {
        $statement = $connection->prepare(
            'UPDATE content_translations
             SET title = ?, excerpt = ?, body_html = ?, summary_html = ?, faq_json = ?, featured_image_path = ?
             WHERE content_translation_id = ?'
        );
        $statement->bind_param(
            'ssssssi',
            $payload['title'],
            $payload['excerpt'],
            $payload['body_html'],
            $payload['summary_html'],
            $payload['faq_json'],
            $payload['featured_image_path'],
            $contentTranslationId
        );
        $statement->execute();
        $statement->close();
        return;
    }

    $statement = $connection->prepare(
        'UPDATE content_translations
         SET title = ?, excerpt = ?, body_html = ?, summary_html = ?, faq_json = ?
         WHERE content_translation_id = ?'
    );
    $statement->bind_param(
        'sssssi',
        $payload['title'],
        $payload['excerpt'],
        $payload['body_html'],
        $payload['summary_html'],
        $payload['faq_json'],
        $contentTranslationId
    );
    $statement->execute();
    $statement->close();
}

function insertRoute(mysqli $connection, int $contentTranslationId, string $languageCode, string $routePath, string $sourceSystem): void
{
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
        ) VALUES (?, ?, ?, \'content\', 200, NULL, ?, 1)'
    );
    $statement->bind_param('ssis', $languageCode, $routePath, $contentTranslationId, $sourceSystem);
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

function loadExportTranslations(string $path): array
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
        if ($wpPostId > 0) {
            $map[$wpPostId] = $item;
        }
    }

    return $map;
}

function uniqueRoutePath(mysqli $connection, string $candidate): string
{
    $candidate = normalizeRoutePath($candidate);
    $base = rtrim($candidate, '/');
    $attempt = $candidate;
    $counter = 2;

    while (routeExists($connection, $attempt)) {
        $attempt = $base . '-' . $counter . '/';
        $counter++;
    }

    return $attempt;
}

function routeExists(mysqli $connection, string $routePath): bool
{
    $statement = $connection->prepare('SELECT 1 FROM content_routes WHERE route_path = ? LIMIT 1');
    $statement->bind_param('s', $routePath);
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
    $value = decodeText($value);
    $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    $value = strtolower($converted !== false ? $converted : $value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;
    $value = trim($value, '-');

    return $value !== '' ? $value : 'vodnik';
}

function cleanShortcodeText(string $value): string
{
    $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = preg_replace('/\[[^\]]+\]/u', ' ', $value) ?? $value;
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

    return trim($value);
}

function plainText(string $html): string
{
    $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

    return trim($text);
}

function decodeText(string $value): string
{
    return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
