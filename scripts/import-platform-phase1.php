<?php

declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$rootPath = getenv('AVC_APP_ROOT') ?: '/var/www/html';
$exportPath = $rootPath . '/exports/wordpress';

$dbHost = getenv('AVC_DB_HOST') ?: 'db';
$dbPort = (int) (getenv('AVC_DB_PORT') ?: 3306);
$dbName = getenv('AVC_DB_NAME') ?: 'avc_platform';
$dbUser = getenv('AVC_DB_USER') ?: 'avc_platform';
$dbPassword = getenv('AVC_DB_PASSWORD') ?: 'avc_platform';
$adminEmail = getenv('AVC_ADMIN_NOTIFICATION_EMAIL') ?: 'admin@example.com';
$activeForeverId = trim((string) (getenv('AVC_ACTIVE_FOREVER_ID') ?: ''));

$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
$mysqli->set_charset('utf8mb4');

$content = loadJsonFile($exportPath . '/content.json')['content'] ?? [];
$products = loadJsonFile($exportPath . '/products.json')['products'] ?? [];
$routes = loadJsonFile($exportPath . '/routes.json')['routes'] ?? [];
$seo = loadJsonFile($exportPath . '/seo.json')['seo'] ?? [];
$settings = loadJsonFile($exportPath . '/settings.json');
$frontPageWpPostId = (int) ($settings['page_on_front_wp_post_id'] ?? 0);

$mysqli->begin_transaction();

try {
    $mysqli->query('SET FOREIGN_KEY_CHECKS = 0');
    foreach (['product_recommendations', 'seo_metadata', 'content_routes', 'content_translations', 'content_items'] as $table) {
        $mysqli->query("TRUNCATE TABLE {$table}");
    }
    $mysqli->query('SET FOREIGN_KEY_CHECKS = 1');

    upsertSetting(
        $mysqli,
        'platform',
        [
            'app_name' => 'AVC Platform',
            'base_url' => $settings['production_url'] ?? 'https://aloevera-centar.com',
            'default_language' => $settings['default_language'] ?? 'hr',
            'supported_languages' => array_keys((array) ($settings['language_prefixes'] ?? ['hr' => '', 'en' => 'en/', 'sl' => 'sl/'])),
        ]
    );

    upsertSetting(
        $mysqli,
        'referral',
        [
            'active_forever_id' => $activeForeverId,
            'admin_notification_email' => $adminEmail,
        ]
    );

    $contentItemIds = [];
    $translationIdsByWpPost = [];

    $contentItemStatement = $mysqli->prepare(
        'INSERT INTO content_items (source_wp_post_id, translation_group_id, content_type, lifecycle_status, editor_template)
         VALUES (?, ?, ?, ?, ?)'
    );
    $translationStatement = $mysqli->prepare(
        'INSERT INTO content_translations (content_item_id, source_wp_post_id, language_code, title, slug, excerpt, body_html, featured_image_path, published_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    foreach ($content as $row) {
        $wpPostId = (int) $row['wp_post_id'];
        $contentType = mapContentType((string) $row['post_type']);
        $translationGroupId = (int) ($row['translation_group_id'] ?? 0);
        $groupKey = $translationGroupId > 0 ? $translationGroupId . ':' . $contentType : 'wp:' . $wpPostId . ':' . $contentType;

        if (!isset($contentItemIds[$groupKey])) {
            $sourceWpPostId = $wpPostId;
            $translationGroupIdNullable = $translationGroupId > 0 ? $translationGroupId : null;
            $lifecycleStatus = 'published';
            $editorTemplate = mapEditorTemplate($contentType);
            $contentItemStatement->bind_param(
                'iisss',
                $sourceWpPostId,
                $translationGroupIdNullable,
                $contentType,
                $lifecycleStatus,
                $editorTemplate
            );
            $contentItemStatement->execute();
            $contentItemIds[$groupKey] = (int) $mysqli->insert_id;
        }

        $contentItemId = $contentItemIds[$groupKey];
        $sourceWpPostId = $wpPostId;
        $languageCode = (string) ($row['language_code'] ?: 'hr');
        $title = (string) $row['title'];
        $slug = (string) $row['slug'];
        $excerpt = trim((string) ($row['excerpt'] ?? '')) ?: null;
        $bodyHtml = (string) ($row['body_html'] ?? '');
        $featuredImagePath = trim((string) ($row['featured_image_url'] ?? '')) ?: null;
        $publishedAt = normalizeDateTime($row['published_at_gmt'] ?? null);

        $translationStatement->bind_param(
            'iisssssss',
            $contentItemId,
            $sourceWpPostId,
            $languageCode,
            $title,
            $slug,
            $excerpt,
            $bodyHtml,
            $featuredImagePath,
            $publishedAt
        );
        $translationStatement->execute();
        $translationIdsByWpPost[$wpPostId] = (int) $mysqli->insert_id;
    }

    $productRecommendationStatement = $mysqli->prepare(
        'INSERT INTO product_recommendations (
            content_translation_id, source_wp_post_id, translation_group_id, external_url, source_host, button_label, sku,
            stock_status, price, regular_price, sale_price, currency_code, destination_strategy, market_urls_json, source_system
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $importedProductRecommendations = 0;

    foreach ($products as $row) {
        $wpPostId = (int) ($row['wp_post_id'] ?? 0);

        if ($wpPostId <= 0 || !isset($translationIdsByWpPost[$wpPostId])) {
            continue;
        }

        $externalUrl = sanitizeNullableString($row['external_url'] ?? null);
        if ($externalUrl === null) {
            continue;
        }

        $contentTranslationId = $translationIdsByWpPost[$wpPostId];
        $translationGroupId = (int) ($row['translation_group_id'] ?? 0);
        $translationGroupIdNullable = $translationGroupId > 0 ? $translationGroupId : null;
        $sourceHost = sanitizeNullableString($row['source_host'] ?? null);
        $buttonLabel = sanitizeNullableString($row['button_label'] ?? null);
        $sku = sanitizeNullableString($row['sku'] ?? null);
        $stockStatus = sanitizeNullableString($row['stock_status'] ?? null);
        $price = normalizeNullableDecimal($row['price'] ?? null);
        $regularPrice = normalizeNullableDecimal($row['regular_price'] ?? null);
        $salePrice = normalizeNullableDecimal($row['sale_price'] ?? null);
        $currencyCode = 'EUR';
        $destinationStrategy = sanitizeNullableString($row['destination_strategy'] ?? null) ?? 'passthrough';
        $marketUrlsJson = null;
        $sourceSystem = 'wordpress';

        $productRecommendationStatement->bind_param(
            'iiissssssssssss',
            $contentTranslationId,
            $wpPostId,
            $translationGroupIdNullable,
            $externalUrl,
            $sourceHost,
            $buttonLabel,
            $sku,
            $stockStatus,
            $price,
            $regularPrice,
            $salePrice,
            $currencyCode,
            $destinationStrategy,
            $marketUrlsJson,
            $sourceSystem
        );
        $productRecommendationStatement->execute();
        $importedProductRecommendations++;
    }

    $seoStatement = $mysqli->prepare(
        'INSERT INTO seo_metadata (
            content_translation_id, meta_title, meta_description, canonical_url, robots_index, robots_follow,
            breadcrumb_title, focus_keyword, open_graph_title, open_graph_description, open_graph_image_path, schema_json
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    foreach ($seo as $row) {
        $wpPostId = (int) $row['wp_post_id'];
        if (!isset($translationIdsByWpPost[$wpPostId])) {
            continue;
        }

        $contentTranslationId = $translationIdsByWpPost[$wpPostId];
        $metaTitle = sanitizeNullableString($row['meta_title'] ?? null);
        $metaDescription = sanitizeNullableString($row['meta_description'] ?? null);
        $canonicalUrl = sanitizeNullableString($row['meta_canonical'] ?? ($row['production_url'] ?? null));
        $robotsIndex = ((int) ($row['robots_noindex'] ?? 0) === 1) ? 0 : 1;
        $robotsFollow = ((int) ($row['robots_nofollow'] ?? 0) === 1) ? 0 : 1;
        $breadcrumbTitle = sanitizeNullableString($row['breadcrumb_title'] ?? null);
        $focusKeyword = sanitizeNullableString($row['focus_keyword'] ?? null);
        $openGraphTitle = sanitizeNullableString($row['title'] ?? null);
        $openGraphDescription = $metaDescription;
        $openGraphImagePath = null;
        $schemaJson = null;

        $seoStatement->bind_param(
            'isssiissssss',
            $contentTranslationId,
            $metaTitle,
            $metaDescription,
            $canonicalUrl,
            $robotsIndex,
            $robotsFollow,
            $breadcrumbTitle,
            $focusKeyword,
            $openGraphTitle,
            $openGraphDescription,
            $openGraphImagePath,
            $schemaJson
        );
        $seoStatement->execute();
    }

    $routeStatement = $mysqli->prepare(
        'INSERT INTO content_routes (
            language_code, route_path, content_translation_id, route_type, http_status_code, redirect_target_path, source_system, is_primary
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );

    foreach ($routes as $row) {
        $wpPostId = (int) $row['wp_post_id'];
        if (!isset($translationIdsByWpPost[$wpPostId])) {
            continue;
        }

        $languageCode = (string) ($row['language_code'] ?: 'hr');
        $routePath = (string) ($row['resolved_path'] ?? '/');
        $contentTranslationId = $translationIdsByWpPost[$wpPostId];
        $routeType = 'content';
        $httpStatusCode = 200;
        $redirectTargetPath = null;
        $sourceSystem = 'wordpress';
        $isPrimary = 1;

        $routeStatement->bind_param(
            'ssisiisi',
            $languageCode,
            $routePath,
            $contentTranslationId,
            $routeType,
            $httpStatusCode,
            $redirectTargetPath,
            $sourceSystem,
            $isPrimary
        );
        $routeStatement->execute();
    }

    if ($frontPageWpPostId > 0 && isset($translationIdsByWpPost[$frontPageWpPostId])) {
        $contentTranslationId = $translationIdsByWpPost[$frontPageWpPostId];
        $contentItemId = getContentItemIdForTranslation($mysqli, $contentTranslationId);

        if ($contentItemId !== null) {
            $frontPageTranslations = fetchAllContentTranslationsForItem($mysqli, $contentItemId);

            foreach ($frontPageTranslations as $frontPageTranslation) {
                $languageCode = (string) $frontPageTranslation['language_code'];
                $routePath = match ($languageCode) {
                    'en' => '/en/',
                    'sl' => '/sl/',
                    default => '/',
                };
                $contentTranslationId = (int) $frontPageTranslation['content_translation_id'];
                $routeType = 'content';
                $httpStatusCode = 200;
                $redirectTargetPath = null;
                $sourceSystem = 'wordpress_front_page_alias';
                $isPrimary = 0;

                $routeStatement->bind_param(
                    'ssisiisi',
                    $languageCode,
                    $routePath,
                    $contentTranslationId,
                    $routeType,
                    $httpStatusCode,
                    $redirectTargetPath,
                    $sourceSystem,
                    $isPrimary
                );
                $routeStatement->execute();
            }
        }
    }

    $mysqli->commit();

    echo json_encode([
        'status' => 'ok',
        'imported_content_rows' => count($content),
        'imported_routes' => count($routes),
        'imported_seo_rows' => count($seo),
        'content_items' => count($contentItemIds),
        'content_translations' => count($translationIdsByWpPost),
        'product_recommendations' => $importedProductRecommendations,
        'admin_notification_email' => $adminEmail,
        'active_forever_id' => $activeForeverId,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $exception) {
    $mysqli->rollback();
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}

function loadJsonFile(string $path): array
{
    if (!is_file($path)) {
        throw new RuntimeException("Missing JSON file: {$path}");
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    if (!is_array($decoded)) {
        throw new RuntimeException("Invalid JSON file: {$path}");
    }

    return $decoded;
}

function mapContentType(string $wordpressPostType): string
{
    return match ($wordpressPostType) {
        'post' => 'article',
        'page' => 'page',
        'product' => 'product_guide',
        default => 'page',
    };
}

function mapEditorTemplate(string $contentType): string
{
    return match ($contentType) {
        'article' => 'article',
        'product_guide' => 'product-guide',
        default => 'page',
    };
}

function normalizeDateTime(?string $value): ?string
{
    if (!is_string($value) || trim($value) === '' || $value === '0000-00-00 00:00:00') {
        return null;
    }

    return $value;
}

function sanitizeNullableString(mixed $value): ?string
{
    if (!is_string($value)) {
        return null;
    }

    $trimmed = trim($value);

    return $trimmed === '' ? null : $trimmed;
}

function normalizeNullableDecimal(mixed $value): ?float
{
    if ($value === null) {
        return null;
    }

    $normalized = trim((string) $value);

    if ($normalized === '' || !is_numeric($normalized)) {
        return null;
    }

    return (float) $normalized;
}

function upsertSetting(mysqli $mysqli, string $key, array $payload): void
{
    $statement = $mysqli->prepare(
        'INSERT INTO settings (setting_key, setting_value_json) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value_json = VALUES(setting_value_json)'
    );

    $value = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $statement->bind_param('ss', $key, $value);
    $statement->execute();
}

function getContentItemIdForTranslation(mysqli $mysqli, int $contentTranslationId): ?int
{
    $statement = $mysqli->prepare('SELECT content_item_id FROM content_translations WHERE content_translation_id = ? LIMIT 1');
    $statement->bind_param('i', $contentTranslationId);
    $statement->execute();
    $result = $statement->get_result()->fetch_assoc();
    $statement->close();

    return $result ? (int) $result['content_item_id'] : null;
}

function fetchAllContentTranslationsForItem(mysqli $mysqli, int $contentItemId): array
{
    $statement = $mysqli->prepare(
        'SELECT content_translation_id, language_code
         FROM content_translations
         WHERE content_item_id = ?
         ORDER BY content_translation_id ASC'
    );
    $statement->bind_param('i', $contentItemId);
    $statement->execute();
    $result = $statement->get_result();
    $rows = [];

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $statement->close();

    return $rows;
}
