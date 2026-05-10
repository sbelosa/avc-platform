<?php

declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

const AVC_PRODUCTION_URL = 'https://aloevera-centar.com';

$mode = $argv[1] ?? null;

if ($mode === null) {
    fwrite(STDERR, "Usage: php /dev/stdin <mode>\n");
    exit(1);
}

$dbHost = getenv('WORDPRESS_DB_HOST') ?: 'db';
$dbName = getenv('WORDPRESS_DB_NAME') ?: 'wp902';
$dbUser = getenv('WORDPRESS_DB_USER') ?: 'wordpress';
$dbPassword = getenv('WORDPRESS_DB_PASSWORD') ?: 'wordpress';

$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
$mysqli->set_charset('utf8mb4');

$sitepressSettings = getSerializedOption($mysqli, 'wpru_options', 'icl_sitepress_settings') ?? [];
$permalinkMap = getSerializedOption($mysqli, 'wpru_options', 'permalink-manager-uris') ?? [];

$defaultLanguage = resolveDefaultLanguage($sitepressSettings);
$languagePrefixes = buildLanguagePrefixes($sitepressSettings, $defaultLanguage);

switch ($mode) {
    case 'settings':
        $showOnFront = getSimpleOption($mysqli, 'wpru_options', 'show_on_front');
        $pageOnFront = getSimpleOption($mysqli, 'wpru_options', 'page_on_front');
        $pageForPosts = getSimpleOption($mysqli, 'wpru_options', 'page_for_posts');

        outputJson([
            'generated_at' => gmdate(DATE_ATOM),
            'production_url' => AVC_PRODUCTION_URL,
            'default_language' => $defaultLanguage,
            'language_prefixes' => $languagePrefixes,
            'show_on_front' => $showOnFront,
            'page_on_front_wp_post_id' => $pageOnFront !== null ? (int) $pageOnFront : null,
            'page_for_posts_wp_post_id' => $pageForPosts !== null ? (int) $pageForPosts : null,
            'sitepress_settings' => $sitepressSettings,
            'permalink_map_count' => count($permalinkMap),
        ]);
        break;

    case 'inventory':
        outputJson(buildInventory($mysqli, $defaultLanguage));
        break;

    case 'permalink-map':
        outputJson([
            'generated_at' => gmdate(DATE_ATOM),
            'count' => count($permalinkMap),
            'map' => $permalinkMap,
        ]);
        break;

    case 'content':
        outputJson(buildContentMap($mysqli));
        break;

    case 'products':
        outputJson(buildProductMap($mysqli));
        break;

    case 'routes':
        outputJson(buildRoutes($mysqli, $permalinkMap, $defaultLanguage, $languagePrefixes));
        break;

    case 'translations':
        outputJson(buildTranslations($mysqli, $permalinkMap, $defaultLanguage, $languagePrefixes));
        break;

    case 'seo':
        outputJson(buildSeoMap($mysqli, $permalinkMap, $defaultLanguage, $languagePrefixes));
        break;

    default:
        fwrite(STDERR, "Unknown mode: {$mode}\n");
        exit(1);
}

function buildInventory(mysqli $mysqli, string $defaultLanguage): array
{
    $summary = fetchAllAssoc(
        $mysqli,
        "SELECT post_type, post_status, COUNT(*) AS count
         FROM wpru_posts
         GROUP BY post_type, post_status
         ORDER BY count DESC, post_type ASC, post_status ASC"
    );

    $publishedByLanguage = fetchAllAssoc(
        $mysqli,
        "SELECT t.language_code, p.post_type, COUNT(*) AS count
         FROM wpru_posts p
         LEFT JOIN wpru_icl_translations t
           ON t.element_id = p.ID
          AND t.element_type = CONCAT('post_', p.post_type)
         WHERE p.post_status = 'publish'
           AND p.post_type IN ('post', 'page', 'product')
         GROUP BY t.language_code, p.post_type
         ORDER BY t.language_code ASC, p.post_type ASC"
    );

    $activeLanguages = fetchAllAssoc(
        $mysqli,
        "SELECT code, english_name, default_locale, active
         FROM wpru_icl_languages
         WHERE active = 1
         ORDER BY code ASC"
    );

    return [
        'generated_at' => gmdate(DATE_ATOM),
        'production_url' => AVC_PRODUCTION_URL,
        'default_language' => $defaultLanguage,
        'summary' => $summary,
        'published_by_language' => $publishedByLanguage,
        'active_languages' => $activeLanguages,
    ];
}

function buildRoutes(mysqli $mysqli, array $permalinkMap, string $defaultLanguage, array $languagePrefixes): array
{
    $rows = fetchAllAssoc(
        $mysqli,
        "SELECT
            p.ID AS wp_post_id,
            p.post_type,
            p.post_status,
            p.post_name,
            p.post_title,
            p.post_modified_gmt,
            t.language_code,
            t.trid,
            t.source_language_code,
            yi.permalink
         FROM wpru_posts p
         LEFT JOIN wpru_icl_translations t
           ON t.element_id = p.ID
          AND t.element_type = CONCAT('post_', p.post_type)
         LEFT JOIN wpru_yoast_indexable yi
           ON yi.object_type = 'post'
          AND yi.object_id = p.ID
         WHERE p.post_status = 'publish'
           AND p.post_type IN ('post', 'page', 'product')
         ORDER BY p.post_type ASC, p.ID ASC"
    );

    $routes = [];

    foreach ($rows as $row) {
        $wpPostId = (int) $row['wp_post_id'];
        $languageCode = (string) ($row['language_code'] ?: $defaultLanguage);
        $customUri = isset($permalinkMap[$wpPostId]) ? trim((string) $permalinkMap[$wpPostId], '/') : '';

        $resolvedPath = buildResolvedPath(
            (string) $row['post_type'],
            (string) $row['post_name'],
            $languageCode,
            $defaultLanguage,
            $languagePrefixes,
            $customUri
        );
        $productionUrl = normalizeProductionUrl(
            $row['permalink'] ?? null,
            AVC_PRODUCTION_URL . $resolvedPath,
            $languageCode,
            $defaultLanguage,
            $languagePrefixes,
            (string) $row['post_name'],
            $customUri
        );
        $resolvedPath = parse_url($productionUrl, PHP_URL_PATH) ?: $resolvedPath;

        $routes[] = [
            'wp_post_id' => $wpPostId,
            'translation_group_id' => isset($row['trid']) ? (int) $row['trid'] : null,
            'post_type' => $row['post_type'],
            'post_status' => $row['post_status'],
            'language_code' => $languageCode,
            'source_language_code' => $row['source_language_code'] ?: null,
            'native_slug' => $row['post_name'],
            'custom_uri' => $customUri !== '' ? $customUri : null,
            'resolved_path' => $resolvedPath,
            'production_url' => $productionUrl,
            'title' => $row['post_title'],
            'post_modified_gmt' => $row['post_modified_gmt'],
        ];
    }

    return [
        'generated_at' => gmdate(DATE_ATOM),
        'production_url' => AVC_PRODUCTION_URL,
        'default_language' => $defaultLanguage,
        'count' => count($routes),
        'routes' => $routes,
    ];
}

function buildContentMap(mysqli $mysqli): array
{
    $rows = fetchAllAssoc(
        $mysqli,
        "SELECT
            p.ID AS wp_post_id,
            p.post_type,
            p.post_status,
            p.post_name,
            p.post_title,
            p.post_excerpt,
            p.post_content,
            p.post_date_gmt,
            p.post_modified_gmt,
            t.language_code,
            t.trid,
            t.source_language_code,
            thumbnail_attachment.guid AS featured_image_url
         FROM wpru_posts p
         LEFT JOIN wpru_icl_translations t
           ON t.element_id = p.ID
          AND t.element_type = CONCAT('post_', p.post_type)
         LEFT JOIN wpru_postmeta thumbnail_meta
           ON thumbnail_meta.post_id = p.ID
          AND thumbnail_meta.meta_key = '_thumbnail_id'
         LEFT JOIN wpru_posts thumbnail_attachment
           ON thumbnail_attachment.ID = CAST(thumbnail_meta.meta_value AS UNSIGNED)
         WHERE p.post_status = 'publish'
           AND p.post_type IN ('post', 'page', 'product')
         ORDER BY p.post_type ASC, p.ID ASC"
    );

    return [
        'generated_at' => gmdate(DATE_ATOM),
        'count' => count($rows),
        'content' => array_map(static function (array $row): array {
            return [
                'wp_post_id' => (int) $row['wp_post_id'],
                'translation_group_id' => isset($row['trid']) ? (int) $row['trid'] : null,
                'post_type' => $row['post_type'],
                'post_status' => $row['post_status'],
                'language_code' => $row['language_code'],
                'source_language_code' => $row['source_language_code'] ?: null,
                'slug' => $row['post_name'],
                'title' => $row['post_title'],
                'excerpt' => $row['post_excerpt'],
                'body_html' => $row['post_content'],
                'published_at_gmt' => $row['post_date_gmt'],
                'modified_at_gmt' => $row['post_modified_gmt'],
                'featured_image_url' => $row['featured_image_url'] ?: null,
            ];
        }, $rows),
    ];
}

function buildProductMap(mysqli $mysqli): array
{
    $rows = fetchAllAssoc(
        $mysqli,
        "SELECT
            p.ID AS wp_post_id,
            p.post_name,
            p.post_title,
            p.post_status,
            t.language_code,
            t.trid,
            product_url.meta_value AS external_url,
            button_text.meta_value AS button_label,
            price.meta_value AS price,
            regular_price.meta_value AS regular_price,
            sale_price.meta_value AS sale_price,
            stock_status.meta_value AS stock_status,
            sku.meta_value AS sku
         FROM wpru_posts p
         LEFT JOIN wpru_icl_translations t
           ON t.element_id = p.ID
          AND t.element_type = 'post_product'
         LEFT JOIN wpru_postmeta product_url
           ON product_url.post_id = p.ID
          AND product_url.meta_key = '_product_url'
         LEFT JOIN wpru_postmeta button_text
           ON button_text.post_id = p.ID
          AND button_text.meta_key = '_button_text'
         LEFT JOIN wpru_postmeta price
           ON price.post_id = p.ID
          AND price.meta_key = '_price'
         LEFT JOIN wpru_postmeta regular_price
           ON regular_price.post_id = p.ID
          AND regular_price.meta_key = '_regular_price'
         LEFT JOIN wpru_postmeta sale_price
           ON sale_price.post_id = p.ID
          AND sale_price.meta_key = '_sale_price'
         LEFT JOIN wpru_postmeta stock_status
           ON stock_status.post_id = p.ID
          AND stock_status.meta_key = '_stock_status'
         LEFT JOIN wpru_postmeta sku
           ON sku.post_id = p.ID
          AND sku.meta_key = '_sku'
         WHERE p.post_status = 'publish'
           AND p.post_type = 'product'
         ORDER BY p.ID ASC"
    );

    return [
        'generated_at' => gmdate(DATE_ATOM),
        'count' => count($rows),
        'products' => array_map(static function (array $row): array {
            $externalUrl = trim((string) ($row['external_url'] ?? ''));

            return [
                'wp_post_id' => (int) $row['wp_post_id'],
                'translation_group_id' => isset($row['trid']) ? (int) $row['trid'] : null,
                'language_code' => $row['language_code'] ?: null,
                'slug' => $row['post_name'],
                'title' => $row['post_title'],
                'external_url' => $externalUrl !== '' ? $externalUrl : null,
                'source_host' => extractHostFromUrl($externalUrl),
                'button_label' => normalizeNullableString($row['button_label'] ?? null),
                'price' => normalizeNullableDecimal($row['price'] ?? null),
                'regular_price' => normalizeNullableDecimal($row['regular_price'] ?? null),
                'sale_price' => normalizeNullableDecimal($row['sale_price'] ?? null),
                'stock_status' => normalizeNullableString($row['stock_status'] ?? null),
                'sku' => normalizeNullableString($row['sku'] ?? null),
                'destination_strategy' => inferDestinationStrategy($externalUrl),
            ];
        }, $rows),
    ];
}

function buildTranslations(mysqli $mysqli, array $permalinkMap, string $defaultLanguage, array $languagePrefixes): array
{
    $rows = fetchAllAssoc(
        $mysqli,
        "SELECT
            p.ID AS wp_post_id,
            p.post_type,
            p.post_name,
            p.post_title,
            p.post_status,
            t.language_code,
            t.trid,
            t.source_language_code,
            yi.permalink
         FROM wpru_posts p
         INNER JOIN wpru_icl_translations t
           ON t.element_id = p.ID
          AND t.element_type = CONCAT('post_', p.post_type)
         LEFT JOIN wpru_yoast_indexable yi
           ON yi.object_type = 'post'
          AND yi.object_id = p.ID
         WHERE p.post_status = 'publish'
           AND p.post_type IN ('post', 'page', 'product')
         ORDER BY t.trid ASC, t.language_code ASC, p.ID ASC"
    );

    $groups = [];

    foreach ($rows as $row) {
        $translationGroupId = (int) $row['trid'];
        $wpPostId = (int) $row['wp_post_id'];
        $languageCode = (string) ($row['language_code'] ?: $defaultLanguage);
        $customUri = isset($permalinkMap[$wpPostId]) ? trim((string) $permalinkMap[$wpPostId], '/') : '';
        $resolvedPath = buildResolvedPath(
            (string) $row['post_type'],
            (string) $row['post_name'],
            $languageCode,
            $defaultLanguage,
            $languagePrefixes,
            $customUri
        );
        $productionUrl = normalizeProductionUrl(
            $row['permalink'] ?? null,
            AVC_PRODUCTION_URL . $resolvedPath,
            $languageCode,
            $defaultLanguage,
            $languagePrefixes,
            (string) $row['post_name'],
            $customUri
        );
        $resolvedPath = parse_url($productionUrl, PHP_URL_PATH) ?: $resolvedPath;

        $groups[$translationGroupId]['translation_group_id'] = $translationGroupId;
        $groups[$translationGroupId]['post_type'] = $row['post_type'];
        $groups[$translationGroupId]['items'][] = [
            'wp_post_id' => $wpPostId,
            'language_code' => $languageCode,
            'source_language_code' => $row['source_language_code'] ?: null,
            'title' => $row['post_title'],
            'slug' => $row['post_name'],
            'custom_uri' => $customUri !== '' ? $customUri : null,
            'resolved_path' => $resolvedPath,
            'production_url' => $productionUrl,
        ];
    }

    return [
        'generated_at' => gmdate(DATE_ATOM),
        'default_language' => $defaultLanguage,
        'count' => count($groups),
        'groups' => array_values($groups),
    ];
}

function buildSeoMap(mysqli $mysqli, array $permalinkMap, string $defaultLanguage, array $languagePrefixes): array
{
    $rows = fetchAllAssoc(
        $mysqli,
        "SELECT
            p.ID AS wp_post_id,
            p.post_type,
            p.post_name,
            p.post_title,
            p.post_excerpt,
            p.post_modified_gmt,
            t.language_code,
            t.trid,
            yi.permalink,
            yi.title AS yoast_indexable_title,
            yi.description AS yoast_indexable_description,
            yi.breadcrumb_title,
            yi.canonical AS yoast_indexable_canonical,
            yi.is_robots_noindex,
            yi.is_robots_nofollow,
            mt.meta_value AS yoast_meta_title,
            md.meta_value AS yoast_meta_description,
            mc.meta_value AS yoast_meta_canonical,
            mk.meta_value AS yoast_focus_keyword
         FROM wpru_posts p
         LEFT JOIN wpru_icl_translations t
           ON t.element_id = p.ID
          AND t.element_type = CONCAT('post_', p.post_type)
         LEFT JOIN wpru_yoast_indexable yi
           ON yi.object_type = 'post'
          AND yi.object_id = p.ID
         LEFT JOIN wpru_postmeta mt
           ON mt.post_id = p.ID
          AND mt.meta_key = '_yoast_wpseo_title'
         LEFT JOIN wpru_postmeta md
           ON md.post_id = p.ID
          AND md.meta_key = '_yoast_wpseo_metadesc'
         LEFT JOIN wpru_postmeta mc
           ON mc.post_id = p.ID
          AND mc.meta_key = '_yoast_wpseo_canonical'
         LEFT JOIN wpru_postmeta mk
           ON mk.post_id = p.ID
          AND mk.meta_key = '_yoast_wpseo_focuskw'
         WHERE p.post_status = 'publish'
           AND p.post_type IN ('post', 'page', 'product')
         ORDER BY p.post_type ASC, p.ID ASC"
    );

    $seoMap = [];

    foreach ($rows as $row) {
        $wpPostId = (int) $row['wp_post_id'];
        $languageCode = (string) ($row['language_code'] ?: $defaultLanguage);
        $customUri = isset($permalinkMap[$wpPostId]) ? trim((string) $permalinkMap[$wpPostId], '/') : '';

        $resolvedPath = buildResolvedPath(
            (string) $row['post_type'],
            (string) $row['post_name'],
            $languageCode,
            $defaultLanguage,
            $languagePrefixes,
            $customUri
        );

        $seoMap[] = [
            'wp_post_id' => $wpPostId,
            'translation_group_id' => isset($row['trid']) ? (int) $row['trid'] : null,
            'post_type' => $row['post_type'],
            'language_code' => $languageCode,
            'resolved_path' => $resolvedPath,
            'production_url' => AVC_PRODUCTION_URL . $resolvedPath,
            'title' => $row['post_title'],
            'excerpt' => $row['post_excerpt'],
            'meta_title' => firstNonEmpty($row['yoast_meta_title'], $row['yoast_indexable_title']),
            'meta_description' => firstNonEmpty($row['yoast_meta_description'], $row['yoast_indexable_description']),
            'meta_canonical' => firstNonEmpty($row['yoast_meta_canonical'], $row['yoast_indexable_canonical']),
            'breadcrumb_title' => $row['breadcrumb_title'] ?: null,
            'focus_keyword' => $row['yoast_focus_keyword'] ?: null,
            'robots_noindex' => isset($row['is_robots_noindex']) ? (int) $row['is_robots_noindex'] : null,
            'robots_nofollow' => isset($row['is_robots_nofollow']) ? (int) $row['is_robots_nofollow'] : null,
            'yoast_permalink' => $row['permalink'] ?: null,
            'post_modified_gmt' => $row['post_modified_gmt'],
        ];
    }

    return [
        'generated_at' => gmdate(DATE_ATOM),
        'count' => count($seoMap),
        'seo' => $seoMap,
    ];
}

function buildResolvedPath(
    string $postType,
    string $slug,
    string $languageCode,
    string $defaultLanguage,
    array $languagePrefixes,
    string $customUri = ''
): string {
    if ($customUri !== '') {
        $normalizedCustomUri = trim($customUri, '/');
        $prefix = trim((string) ($languagePrefixes[$languageCode] ?? ''), '/');

        if ($prefix !== '' && !str_starts_with($normalizedCustomUri . '/', $prefix . '/')) {
            $normalizedCustomUri = $prefix . '/' . $normalizedCustomUri;
        }

        $path = '/' . $normalizedCustomUri . '/';
    } else {
        $basePath = match ($postType) {
            'product' => '/proizvod/' . trim($slug, '/') . '/',
            default => '/' . trim($slug, '/') . '/',
        };

        $prefix = $languagePrefixes[$languageCode] ?? null;
        if ($prefix !== null && $prefix !== '' && $languageCode !== $defaultLanguage) {
            $path = '/' . trim($prefix, '/') . '/' . trim($basePath, '/') . '/';
            $path = '/' . trim($path, '/') . '/';
        } else {
            $path = $basePath;
        }
    }

    return '/' . trim($path, '/') . '/';
}

function normalizeProductionUrl(
    ?string $candidateUrl,
    string $fallbackUrl,
    string $languageCode,
    string $defaultLanguage,
    array $languagePrefixes,
    string $slug,
    string $customUri = ''
): string
{
    if (!is_string($candidateUrl) || trim($candidateUrl) === '') {
        return $fallbackUrl;
    }

    $path = parse_url($candidateUrl, PHP_URL_PATH);
    if (!is_string($path) || trim($path) === '') {
        return $fallbackUrl;
    }

    $normalizedPath = '/' . trim($path, '/') . '/';
    $expectedPrefix = trim((string) ($languagePrefixes[$languageCode] ?? ''), '/');
    $normalizedCandidate = trim($normalizedPath, '/');

    if ($languageCode !== $defaultLanguage && $expectedPrefix !== '') {
        if (!str_starts_with($normalizedCandidate . '/', $expectedPrefix . '/')) {
            return $fallbackUrl;
        }
    }

    $expectedFragment = trim($customUri !== '' ? $customUri : $slug, '/');
    if ($expectedFragment !== '' && !str_contains($normalizedCandidate, $expectedFragment)) {
        return $fallbackUrl;
    }

    return AVC_PRODUCTION_URL . $normalizedPath;
}

function extractHostFromUrl(string $value): ?string
{
    if ($value === '' || !filter_var($value, FILTER_VALIDATE_URL)) {
        return null;
    }

    $host = parse_url($value, PHP_URL_HOST);
    if (!is_string($host) || $host === '') {
        return null;
    }

    return strtolower(preg_replace('/^www\./', '', $host) ?? $host);
}

function inferDestinationStrategy(string $value): string
{
    $host = extractHostFromUrl($value);

    if ($host === null) {
        return 'passthrough';
    }

    if (str_contains($host, 'foreverliving') || str_contains($host, 'foreverlivingproducts')) {
        return 'forever_official';
    }

    if (in_array($host, ['thealoeveraco.shop', 'forevercard.club'], true)) {
        return 'shortlink_passthrough';
    }

    return 'passthrough';
}

function normalizeNullableString(mixed $value): ?string
{
    if (!is_string($value)) {
        return null;
    }

    $trimmed = trim($value);

    return $trimmed === '' ? null : $trimmed;
}

function normalizeNullableDecimal(mixed $value): ?string
{
    if (!is_scalar($value)) {
        return null;
    }

    $trimmed = trim((string) $value);

    if ($trimmed === '' || !is_numeric($trimmed)) {
        return null;
    }

    return number_format((float) $trimmed, 2, '.', '');
}

function resolveDefaultLanguage(array $sitepressSettings): string
{
    $defaultLanguage = $sitepressSettings['default_language'] ?? null;

    if (is_string($defaultLanguage) && $defaultLanguage !== '') {
        return $defaultLanguage;
    }

    $activeLanguages = $sitepressSettings['active_languages'] ?? [];
    if (is_array($activeLanguages) && in_array('hr', $activeLanguages, true)) {
        return 'hr';
    }

    return is_array($activeLanguages) && $activeLanguages ? (string) $activeLanguages[0] : 'hr';
}

function buildLanguagePrefixes(array $sitepressSettings, string $defaultLanguage): array
{
    $activeLanguages = $sitepressSettings['active_languages'] ?? [];
    $prefixes = [];

    if (!is_array($activeLanguages)) {
        return [$defaultLanguage => ''];
    }

    foreach ($activeLanguages as $languageCode) {
        $languageCode = (string) $languageCode;
        $prefixes[$languageCode] = $languageCode === $defaultLanguage ? '' : $languageCode . '/';
    }

    return $prefixes;
}

function getSerializedOption(mysqli $mysqli, string $table, string $optionName): mixed
{
    $statement = $mysqli->prepare("SELECT option_value FROM {$table} WHERE option_name = ? LIMIT 1");
    $statement->bind_param('s', $optionName);
    $statement->execute();
    $result = $statement->get_result()->fetch_assoc();
    $statement->close();

    if (!$result) {
        return null;
    }

    return unserialize((string) $result['option_value']);
}

function getSimpleOption(mysqli $mysqli, string $table, string $optionName): ?string
{
    $statement = $mysqli->prepare("SELECT option_value FROM {$table} WHERE option_name = ? LIMIT 1");
    $statement->bind_param('s', $optionName);
    $statement->execute();
    $result = $statement->get_result()->fetch_assoc();
    $statement->close();

    if (!$result || !isset($result['option_value'])) {
        return null;
    }

    return (string) $result['option_value'];
}

function fetchAllAssoc(mysqli $mysqli, string $query): array
{
    $result = $mysqli->query($query);
    $rows = [];

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $result->free();

    return $rows;
}

function firstNonEmpty(?string ...$values): ?string
{
    foreach ($values as $value) {
        if ($value !== null && trim($value) !== '') {
            return $value;
        }
    }

    return null;
}

function outputJson(array $payload): void
{
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
