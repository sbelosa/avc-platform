<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class ContentRepository
{
    public function __construct(private array $config)
    {
    }

    public function findByRoutePath(string $routePath): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        $statement = $connection->prepare(
            'SELECT
                cr.route_path,
                cr.http_status_code,
                cr.redirect_target_path,
                cr.route_type,
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
                ci.content_type,
                ci.lifecycle_status,
                sm.meta_title,
                sm.meta_description,
                sm.canonical_url,
                sm.robots_index,
                sm.robots_follow,
                sm.breadcrumb_title,
                sm.open_graph_title,
                sm.open_graph_description,
                sm.open_graph_image_path
             FROM content_routes cr
             LEFT JOIN content_translations ct
               ON ct.content_translation_id = cr.content_translation_id
             LEFT JOIN content_items ci
               ON ci.content_item_id = ct.content_item_id
             LEFT JOIN seo_metadata sm
               ON sm.content_translation_id = ct.content_translation_id
             WHERE cr.route_path = ?
             LIMIT 1'
        );
        $statement->bind_param('s', $routePath);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ?: null;
    }

    public function findAlternatesForContentItem(int $contentItemId): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $statement = $connection->prepare(
            'SELECT ct.language_code, ct.title, cr.route_path
             FROM content_translations ct
             INNER JOIN content_routes cr
               ON cr.content_translation_id = ct.content_translation_id
              AND cr.is_primary = 1
             WHERE ct.content_item_id = ?
             ORDER BY ct.language_code ASC'
        );
        $statement->bind_param('i', $contentItemId);
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        return $rows;
    }

    public function findRouteBySourceWordPressId(int $sourceWpPostId, ?string $preferredLanguageCode = null): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        if ($preferredLanguageCode !== null && $preferredLanguageCode !== '') {
            $statement = $connection->prepare(
                'SELECT ct.language_code, cr.route_path
                 FROM content_translations ct
                 INNER JOIN content_routes cr
                   ON cr.content_translation_id = ct.content_translation_id
                  AND cr.is_primary = 1
                 WHERE ct.source_wp_post_id = ?
                   AND ct.language_code = ?
                 LIMIT 1'
            );
            $statement->bind_param('is', $sourceWpPostId, $preferredLanguageCode);
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $statement->close();

            if ($result) {
                return $result;
            }
        }

        $statement = $connection->prepare(
            'SELECT ct.language_code, cr.route_path
             FROM content_translations ct
             INNER JOIN content_routes cr
               ON cr.content_translation_id = ct.content_translation_id
              AND cr.is_primary = 1
             WHERE ct.source_wp_post_id = ?
             LIMIT 1'
        );
        $statement->bind_param('i', $sourceWpPostId);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ?: null;
    }

    public function findCardsByType(string $contentType, string $languageCode, int $limit = 6, ?int $excludeContentItemId = null, bool $marketReadyOnly = false): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $limit = max(1, min($limit, 24));
        $whereClauses = [
            "ci.content_type = ?",
            "ct.language_code = ?",
            "ci.lifecycle_status = 'published'",
            "cr.is_primary = 1",
        ];
        $types = 'ss';
        $params = [$contentType, $languageCode];

        if ($excludeContentItemId !== null && $excludeContentItemId > 0) {
            $whereClauses[] = 'ci.content_item_id != ?';
            $types .= 'i';
            $params[] = $excludeContentItemId;
        }

        if ($marketReadyOnly) {
            $whereClauses[] = "((
                pr.market_urls_json IS NOT NULL AND pr.market_urls_json != ''
            ) OR EXISTS (
                SELECT 1
                FROM product_market_overrides pmo
                WHERE pmo.translation_group_id = pr.translation_group_id
                  AND pmo.is_active = 1
            ))";
        }

        $sql = 'SELECT
                    ct.content_translation_id,
                    ct.content_item_id,
                    ct.language_code,
                    ct.title,
                    ct.slug,
                    ct.excerpt,
                    ct.featured_image_path,
                    ct.published_at,
                    cr.route_path,
                    sm.meta_description,
                    pr.button_label,
                    pr.currency_code,
                    pr.price,
                    pr.sale_price,
                    pr.translation_group_id,
                    pr.market_urls_json
                FROM content_translations ct
                INNER JOIN content_items ci
                    ON ci.content_item_id = ct.content_item_id
                INNER JOIN content_routes cr
                    ON cr.content_translation_id = ct.content_translation_id
                LEFT JOIN seo_metadata sm
                    ON sm.content_translation_id = ct.content_translation_id
                LEFT JOIN product_recommendations pr
                    ON pr.content_translation_id = ct.content_translation_id
                WHERE ' . implode(' AND ', $whereClauses) . '
                ORDER BY COALESCE(ct.published_at, ct.updated_at) DESC
                LIMIT ' . $limit;

        $statement = $connection->prepare($sql);
        $statement->bind_param($types, ...$params);
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        $rows = $this->hydrateMarketUrls($rows);

        return $rows;
    }

    public function findCardsByContentItemIds(array $contentItemIds, string $languageCode, bool $marketReadyOnly = false): array
    {
        $contentItemIds = array_values(array_filter(array_map(static fn (mixed $value): int => (int) $value, $contentItemIds), static fn (int $value): bool => $value > 0));
        if ($contentItemIds === []) {
            return [];
        }

        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($contentItemIds), '?'));
        $whereClauses = [
            "ct.language_code = ?",
            "ci.lifecycle_status = 'published'",
            "cr.is_primary = 1",
            'ci.content_item_id IN (' . $placeholders . ')',
        ];
        $types = 's' . str_repeat('i', count($contentItemIds));
        $params = array_merge([$languageCode], $contentItemIds);

        if ($marketReadyOnly) {
            $whereClauses[] = "((
                pr.market_urls_json IS NOT NULL AND pr.market_urls_json != ''
            ) OR EXISTS (
                SELECT 1
                FROM product_market_overrides pmo
                WHERE pmo.translation_group_id = pr.translation_group_id
                  AND pmo.is_active = 1
            ))";
        }

        $sql = 'SELECT
                    ct.content_translation_id,
                    ct.content_item_id,
                    ct.language_code,
                    ct.title,
                    ct.slug,
                    ct.excerpt,
                    ct.featured_image_path,
                    ct.published_at,
                    cr.route_path,
                    sm.meta_description,
                    ci.content_type,
                    pr.button_label,
                    pr.currency_code,
                    pr.price,
                    pr.sale_price,
                    pr.translation_group_id,
                    pr.market_urls_json
                FROM content_translations ct
                INNER JOIN content_items ci
                    ON ci.content_item_id = ct.content_item_id
                INNER JOIN content_routes cr
                    ON cr.content_translation_id = ct.content_translation_id
                LEFT JOIN seo_metadata sm
                    ON sm.content_translation_id = ct.content_translation_id
                LEFT JOIN product_recommendations pr
                    ON pr.content_translation_id = ct.content_translation_id
                WHERE ' . implode(' AND ', $whereClauses) . '
                ORDER BY FIELD(ci.content_item_id, ' . implode(',', $contentItemIds) . ')';

        $statement = $connection->prepare($sql);
        $statement->bind_param($types, ...$params);
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        return $this->hydrateMarketUrls($rows);
    }

    public function findProductCatalogCards(string $languageCode, int $limit = 120): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $limit = max(1, min($limit, 200));
        $statement = $connection->prepare(
            'SELECT
                ct.content_translation_id,
                ct.content_item_id,
                ct.language_code,
                ct.title,
                ct.slug,
                ct.excerpt,
                ct.featured_image_path,
                ct.published_at,
                cr.route_path,
                sm.meta_description,
                ci.content_type,
                pr.button_label,
                pr.currency_code,
                pr.price,
                pr.sale_price,
                pr.sku,
                pr.translation_group_id,
                pr.market_urls_json
             FROM content_translations ct
             INNER JOIN content_items ci
                ON ci.content_item_id = ct.content_item_id
             INNER JOIN content_routes cr
                ON cr.content_translation_id = ct.content_translation_id
               AND cr.is_primary = 1
             LEFT JOIN seo_metadata sm
                ON sm.content_translation_id = ct.content_translation_id
             LEFT JOIN product_recommendations pr
                ON pr.content_translation_id = ct.content_translation_id
             WHERE ci.content_type = \'product_guide\'
               AND ct.language_code = ?
               AND ci.lifecycle_status = \'published\'
             ORDER BY
                CASE
                    WHEN pr.market_urls_json IS NOT NULL AND pr.market_urls_json != \'\' THEN 0
                    ELSE 1
                END ASC,
                ct.title ASC
             LIMIT ' . $limit
        );
        $statement->bind_param('s', $languageCode);
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        return $this->hydrateMarketUrls($rows);
    }

    public function findArticleCatalogCards(string $languageCode, int $limit = 120): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $limit = max(1, min($limit, 240));
        $statement = $connection->prepare(
            'SELECT
                ct.content_translation_id,
                ct.content_item_id,
                ct.language_code,
                ct.title,
                ct.slug,
                ct.excerpt,
                ct.featured_image_path,
                ct.published_at,
                ct.updated_at,
                cr.route_path,
                sm.meta_description,
                ci.content_type
             FROM content_translations ct
             INNER JOIN content_items ci
                ON ci.content_item_id = ct.content_item_id
             INNER JOIN content_routes cr
                ON cr.content_translation_id = ct.content_translation_id
               AND cr.is_primary = 1
             LEFT JOIN seo_metadata sm
                ON sm.content_translation_id = ct.content_translation_id
             WHERE ci.content_type = \'article\'
               AND ct.language_code = ?
               AND ci.lifecycle_status = \'published\'
             ORDER BY COALESCE(ct.published_at, ct.updated_at) DESC, ct.title ASC
             LIMIT ' . $limit
        );
        $statement->bind_param('s', $languageCode);
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        return $rows;
    }

    public function findAdvisorCandidates(string $languageCode, int $limit = 240): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $limit = max(20, min($limit, 400));
        $statement = $connection->prepare(
            'SELECT
                ct.content_translation_id,
                ct.language_code,
                ct.title,
                ct.slug,
                ct.excerpt,
                ct.featured_image_path,
                cr.route_path,
                ci.content_type,
                sm.meta_description,
                pr.button_label,
                pr.sku,
                pr.translation_group_id,
                pr.market_urls_json
             FROM content_translations ct
             INNER JOIN content_items ci
                ON ci.content_item_id = ct.content_item_id
             INNER JOIN content_routes cr
                ON cr.content_translation_id = ct.content_translation_id
               AND cr.is_primary = 1
             LEFT JOIN seo_metadata sm
                ON sm.content_translation_id = ct.content_translation_id
             LEFT JOIN product_recommendations pr
                ON pr.content_translation_id = ct.content_translation_id
             WHERE ct.language_code = ?
               AND ci.lifecycle_status = \'published\'
               AND ci.content_type IN (\'article\', \'product_guide\')
             ORDER BY
                CASE ci.content_type
                    WHEN \'product_guide\' THEN 0
                    ELSE 1
                END ASC,
                COALESCE(ct.published_at, ct.updated_at) DESC
             LIMIT ' . $limit
        );
        $statement->bind_param('s', $languageCode);
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        return $this->hydrateMarketUrls($rows);
    }

    public function listForAdmin(?string $contentType = null, ?string $languageCode = null, string $query = '', int $limit = 100): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $limit = max(1, min($limit, 200));
        $whereClauses = ['1 = 1'];
        $types = '';
        $params = [];

        $contentType = trim((string) $contentType);
        if ($contentType !== '') {
            $whereClauses[] = 'ci.content_type = ?';
            $types .= 's';
            $params[] = $contentType;
        }

        $languageCode = trim((string) $languageCode);
        if ($languageCode !== '') {
            $whereClauses[] = 'ct.language_code = ?';
            $types .= 's';
            $params[] = $languageCode;
        }

        $query = trim($query);
        if ($query !== '') {
            $like = '%' . $query . '%';
            $whereClauses[] = '(ct.title LIKE ? OR ct.slug LIKE ? OR cr.route_path LIKE ? OR sm.meta_title LIKE ?)';
            $types .= 'ssss';
            array_push($params, $like, $like, $like, $like);
        }

        $sql = 'SELECT
                    ct.content_translation_id,
                    ct.content_item_id,
                    ct.language_code,
                    ct.title,
                    ct.slug,
                    ct.published_at,
                    ct.updated_at,
                    ci.content_type,
                    ci.lifecycle_status,
                    cr.route_path,
                    sm.meta_title,
                    sm.meta_description
                FROM content_translations ct
                INNER JOIN content_items ci
                    ON ci.content_item_id = ct.content_item_id
                LEFT JOIN content_routes cr
                    ON cr.content_translation_id = ct.content_translation_id
                   AND cr.is_primary = 1
                LEFT JOIN seo_metadata sm
                    ON sm.content_translation_id = ct.content_translation_id
                WHERE ' . implode(' AND ', $whereClauses) . '
                ORDER BY COALESCE(ct.updated_at, ct.published_at) DESC
                LIMIT ' . $limit;

        $statement = $connection->prepare($sql);
        if ($types !== '') {
            $statement->bind_param($types, ...$params);
        }
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        return $rows;
    }

    public function countByContentTypeForAdmin(): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $result = $connection->query(
            'SELECT ci.content_type, COUNT(*) AS total
             FROM content_translations ct
             INNER JOIN content_items ci
                ON ci.content_item_id = ct.content_item_id
             GROUP BY ci.content_type'
        );
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $counts = [];

        foreach ($rows as $row) {
            $counts[(string) ($row['content_type'] ?? '')] = (int) ($row['total'] ?? 0);
        }

        return $counts;
    }

    public function findForAdminEdit(int $contentTranslationId): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        $statement = $connection->prepare(
            'SELECT
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
                ct.updated_at,
                ci.content_type,
                ci.lifecycle_status,
                ci.editor_template,
                cr.route_path,
                sm.meta_title,
                sm.meta_description,
                sm.canonical_url,
                sm.robots_index,
                sm.robots_follow,
                sm.breadcrumb_title
             FROM content_translations ct
             INNER JOIN content_items ci
               ON ci.content_item_id = ct.content_item_id
             LEFT JOIN content_routes cr
               ON cr.content_translation_id = ct.content_translation_id
              AND cr.is_primary = 1
             LEFT JOIN seo_metadata sm
               ON sm.content_translation_id = ct.content_translation_id
             WHERE ct.content_translation_id = ?
             LIMIT 1'
        );
        $statement->bind_param('i', $contentTranslationId);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ?: null;
    }

    public function findAdminTranslationsForContentItem(int $contentItemId): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $statement = $connection->prepare(
            'SELECT
                ct.content_translation_id,
                ct.language_code,
                ct.title,
                cr.route_path
             FROM content_translations ct
             LEFT JOIN content_routes cr
               ON cr.content_translation_id = ct.content_translation_id
              AND cr.is_primary = 1
             WHERE ct.content_item_id = ?
             ORDER BY FIELD(ct.language_code, \'hr\', \'en\', \'sl\'), ct.language_code ASC'
        );
        $statement->bind_param('i', $contentItemId);
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        return $rows;
    }

    public function updateTranslationAndSeo(int $contentTranslationId, array $payload): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $contentTranslationId <= 0) {
            return;
        }

        $connection->begin_transaction();

        try {
            $title = trim((string) ($payload['title'] ?? ''));
            $excerpt = trim((string) ($payload['excerpt'] ?? ''));
            $bodyHtml = (string) ($payload['body_html'] ?? '');
            $summaryHtml = (string) ($payload['summary_html'] ?? '');
            $faqJson = (string) ($payload['faq_json'] ?? '');
            $featuredImagePath = trim((string) ($payload['featured_image_path'] ?? ''));
            $metaTitle = trim((string) ($payload['meta_title'] ?? ''));
            $metaDescription = trim((string) ($payload['meta_description'] ?? ''));
            $canonicalUrl = trim((string) ($payload['canonical_url'] ?? ''));
            $breadcrumbTitle = trim((string) ($payload['breadcrumb_title'] ?? ''));
            $robotsIndex = (int) ($payload['robots_index'] ?? 1);
            $robotsFollow = (int) ($payload['robots_follow'] ?? 1);

            $updateTranslation = $connection->prepare(
                'UPDATE content_translations
                 SET title = ?, excerpt = ?, body_html = ?, summary_html = ?, faq_json = ?, featured_image_path = ?
                 WHERE content_translation_id = ?'
            );
            $updateTranslation->bind_param('ssssssi', $title, $excerpt, $bodyHtml, $summaryHtml, $faqJson, $featuredImagePath, $contentTranslationId);
            $updateTranslation->execute();
            $updateTranslation->close();

            $upsertSeo = $connection->prepare(
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
            $upsertSeo->bind_param(
                'isssiis',
                $contentTranslationId,
                $metaTitle,
                $metaDescription,
                $canonicalUrl,
                $robotsIndex,
                $robotsFollow,
                $breadcrumbTitle
            );
            $upsertSeo->execute();
            $upsertSeo->close();

            $connection->commit();
        } catch (\Throwable $throwable) {
            $connection->rollback();
            throw $throwable;
        }
    }

    public function updateFeaturedImage(int $contentTranslationId, string $featuredImagePath): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $contentTranslationId <= 0) {
            return;
        }

        $featuredImagePath = trim($featuredImagePath);
        $statement = $connection->prepare(
            'UPDATE content_translations
             SET featured_image_path = ?
             WHERE content_translation_id = ?'
        );
        $statement->bind_param('si', $featuredImagePath, $contentTranslationId);
        $statement->execute();
        $statement->close();
    }

    private function hydrateMarketUrls(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $connection = Database::connection($this->config);
        if ($connection === null) {
            return $rows;
        }

        $groupIds = array_values(array_unique(array_filter(array_map(static fn (array $row): int => (int) ($row['translation_group_id'] ?? 0), $rows), static fn (int $value): bool => $value > 0)));
        $manualOverrides = [];

        if ($groupIds !== []) {
            $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
            $types = str_repeat('i', count($groupIds));
            $statement = $connection->prepare(
                "SELECT translation_group_id, market_code, destination_url
                 FROM product_market_overrides
                 WHERE is_active = 1
                   AND translation_group_id IN ({$placeholders})"
            );
            $statement->bind_param($types, ...$groupIds);
            $statement->execute();
            $result = $statement->get_result();
            $overrideRows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $statement->close();

            foreach ($overrideRows as $overrideRow) {
                $translationGroupId = (int) ($overrideRow['translation_group_id'] ?? 0);
                $marketCode = strtolower(trim((string) ($overrideRow['market_code'] ?? '')));
                $destinationUrl = trim((string) ($overrideRow['destination_url'] ?? ''));

                if ($translationGroupId <= 0 || $marketCode === '' || $destinationUrl === '') {
                    continue;
                }

                $manualOverrides[$translationGroupId][$marketCode] = $destinationUrl;
            }
        }

        foreach ($rows as &$row) {
            $syncedMarketUrls = !empty($row['market_urls_json'])
                ? (json_decode((string) $row['market_urls_json'], true) ?: [])
                : [];
            $normalizedSyncedMarketUrls = [];

            foreach ($syncedMarketUrls as $marketCode => $destinationUrl) {
                $normalizedMarketCode = strtolower(trim((string) $marketCode));
                $normalizedDestinationUrl = trim((string) $destinationUrl);

                if ($normalizedMarketCode === '' || $normalizedDestinationUrl === '') {
                    continue;
                }

                $normalizedSyncedMarketUrls[$normalizedMarketCode] = $normalizedDestinationUrl;
            }

            $row['market_urls'] = array_replace(
                $normalizedSyncedMarketUrls,
                $manualOverrides[(int) ($row['translation_group_id'] ?? 0)] ?? []
            );
        }
        unset($row);

        return $rows;
    }
}
