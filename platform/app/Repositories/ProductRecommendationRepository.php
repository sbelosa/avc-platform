<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class ProductRecommendationRepository
{
    public function __construct(private array $config)
    {
    }

    public function findByContentTranslationId(int $contentTranslationId): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        $statement = $connection->prepare(
            'SELECT pr.*
             FROM product_recommendations pr
             WHERE pr.content_translation_id = ?
             LIMIT 1'
        );
        $statement->bind_param('i', $contentTranslationId);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ? $this->hydrateRecommendation($result) : null;
    }

    public function findByProductRecommendationId(int $productRecommendationId): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        $statement = $connection->prepare(
            'SELECT pr.*
             FROM product_recommendations pr
             WHERE pr.product_recommendation_id = ?
             LIMIT 1'
        );
        $statement->bind_param('i', $productRecommendationId);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ? $this->hydrateRecommendation($result) : null;
    }

    public function listRoutingGroups(bool $unmatchedOnly = false, bool $manualOnly = false, int $limit = 50): array
    {
        $groups = array_values($this->loadRoutingGroups());

        if ($unmatchedOnly) {
            $groups = array_values(array_filter($groups, static fn (array $group): bool => (int) ($group['resolved_market_count'] ?? 0) === 0));
        }

        if ($manualOnly) {
            $groups = array_values(array_filter($groups, static fn (array $group): bool => (int) ($group['manual_override_count'] ?? 0) > 0));
        }

        usort($groups, static function (array $left, array $right): int {
            return strcmp((string) ($left['title'] ?? ''), (string) ($right['title'] ?? ''));
        });

        return array_slice($groups, 0, max(1, $limit));
    }

    public function findRoutingGroupByRecommendationId(int $productRecommendationId): ?array
    {
        if ($productRecommendationId <= 0) {
            return null;
        }

        foreach ($this->loadRoutingGroups() as $group) {
            if ((int) ($group['product_recommendation_id'] ?? 0) === $productRecommendationId) {
                return $group;
            }

            foreach ((array) ($group['translations'] ?? []) as $translation) {
                if ((int) ($translation['product_recommendation_id'] ?? 0) === $productRecommendationId) {
                    return $group;
                }
            }
        }

        return null;
    }

    public function replaceManualMarketOverrides(int $translationGroupId, array $marketUrls, ?int $adminUserId = null): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $translationGroupId <= 0) {
            return;
        }

        $connection->begin_transaction();

        try {
            $deleteStatement = $connection->prepare('DELETE FROM product_market_overrides WHERE translation_group_id = ?');
            $deleteStatement->bind_param('i', $translationGroupId);
            $deleteStatement->execute();
            $deleteStatement->close();

            if ($marketUrls !== []) {
                $insertStatement = $connection->prepare(
                    'INSERT INTO product_market_overrides (
                        translation_group_id,
                        market_code,
                        destination_url,
                        updated_by_admin_user_id,
                        is_active
                    ) VALUES (?, ?, ?, ?, 1)'
                );

                foreach ($marketUrls as $marketCode => $destinationUrl) {
                    $normalizedMarketCode = $this->normalizeMarketCode((string) $marketCode);
                    $normalizedDestinationUrl = trim((string) $destinationUrl);

                    if ($normalizedMarketCode === '' || $normalizedDestinationUrl === '') {
                        continue;
                    }

                    $insertStatement->bind_param('issi', $translationGroupId, $normalizedMarketCode, $normalizedDestinationUrl, $adminUserId);
                    $insertStatement->execute();
                }

                $insertStatement->close();
            }

            $connection->commit();
        } catch (\Throwable $throwable) {
            $connection->rollback();
            throw $throwable;
        }
    }

    public function getManualOverrideCount(): int
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return 0;
        }

        $result = $connection->query('SELECT COUNT(*) AS total FROM product_market_overrides WHERE is_active = 1');
        $row = $result ? $result->fetch_assoc() : null;

        return (int) ($row['total'] ?? 0);
    }

    private function loadRoutingGroups(): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $result = $connection->query(
            "SELECT
                pr.product_recommendation_id,
                pr.content_translation_id,
                pr.translation_group_id,
                pr.external_url,
                pr.sku,
                pr.destination_strategy,
                pr.market_urls_json,
                ct.language_code,
                ct.title,
                ct.slug,
                cr.route_path
             FROM product_recommendations pr
             INNER JOIN content_translations ct
                ON ct.content_translation_id = pr.content_translation_id
             INNER JOIN content_items ci
                ON ci.content_item_id = ct.content_item_id
             LEFT JOIN content_routes cr
                ON cr.content_translation_id = ct.content_translation_id
               AND cr.is_primary = 1
             WHERE ci.content_type = 'product_guide'
             ORDER BY pr.translation_group_id ASC,
                CASE ct.language_code
                    WHEN 'hr' THEN 0
                    WHEN 'en' THEN 1
                    WHEN 'sl' THEN 2
                    ELSE 9
                END ASC,
                ct.title ASC"
        );

        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        if ($rows === []) {
            return [];
        }

        $groupIds = array_values(array_unique(array_map(static fn (array $row): int => (int) ($row['translation_group_id'] ?? 0), $rows)));
        $manualOverrideMap = $this->fetchManualMarketUrlsByGroupIds($groupIds);
        $groups = [];

        foreach ($rows as $row) {
            $translationGroupId = (int) ($row['translation_group_id'] ?? 0);
            if ($translationGroupId <= 0) {
                continue;
            }

            if (!isset($groups[$translationGroupId])) {
                $groups[$translationGroupId] = [
                    'translation_group_id' => $translationGroupId,
                    'product_recommendation_id' => (int) ($row['product_recommendation_id'] ?? 0),
                    'content_translation_id' => (int) ($row['content_translation_id'] ?? 0),
                    'title' => (string) ($row['title'] ?? ''),
                    'route_path' => (string) ($row['route_path'] ?? ''),
                    'sku' => trim((string) ($row['sku'] ?? '')),
                    'external_url' => trim((string) ($row['external_url'] ?? '')),
                    'destination_strategy' => trim((string) ($row['destination_strategy'] ?? 'passthrough')),
                    'synced_market_urls' => [],
                    'manual_market_urls' => [],
                    'market_urls' => [],
                    'translations' => [],
                ];
            }

            $languageCode = (string) ($row['language_code'] ?? '');
            if ($languageCode === 'hr' || $groups[$translationGroupId]['route_path'] === '') {
                $groups[$translationGroupId]['product_recommendation_id'] = (int) ($row['product_recommendation_id'] ?? 0);
                $groups[$translationGroupId]['content_translation_id'] = (int) ($row['content_translation_id'] ?? 0);
                $groups[$translationGroupId]['title'] = (string) ($row['title'] ?? '');
                $groups[$translationGroupId]['route_path'] = (string) ($row['route_path'] ?? '');
                $groups[$translationGroupId]['external_url'] = trim((string) ($row['external_url'] ?? ''));
                $groups[$translationGroupId]['destination_strategy'] = trim((string) ($row['destination_strategy'] ?? 'passthrough'));
                if (trim((string) ($row['sku'] ?? '')) !== '') {
                    $groups[$translationGroupId]['sku'] = trim((string) ($row['sku'] ?? ''));
                }
            }

            foreach ($this->decodeMarketUrls((string) ($row['market_urls_json'] ?? '')) as $marketCode => $destinationUrl) {
                $groups[$translationGroupId]['synced_market_urls'][$marketCode] = $destinationUrl;
            }

            $groups[$translationGroupId]['translations'][] = [
                'product_recommendation_id' => (int) ($row['product_recommendation_id'] ?? 0),
                'content_translation_id' => (int) ($row['content_translation_id'] ?? 0),
                'language_code' => $languageCode,
                'title' => (string) ($row['title'] ?? ''),
                'slug' => (string) ($row['slug'] ?? ''),
                'route_path' => (string) ($row['route_path'] ?? ''),
            ];
        }

        foreach ($groups as $translationGroupId => &$group) {
            $manualMarketUrls = $manualOverrideMap[$translationGroupId] ?? [];
            ksort($group['synced_market_urls']);
            ksort($manualMarketUrls);

            $group['manual_market_urls'] = $manualMarketUrls;
            $group['market_urls'] = array_replace($group['synced_market_urls'], $manualMarketUrls);
            ksort($group['market_urls']);
            $group['synced_market_count'] = count($group['synced_market_urls']);
            $group['manual_override_count'] = count($manualMarketUrls);
            $group['resolved_market_count'] = count($group['market_urls']);
            $group['translation_count'] = count($group['translations']);
        }
        unset($group);

        return $groups;
    }

    private function hydrateRecommendation(array $row): array
    {
        $translationGroupId = (int) ($row['translation_group_id'] ?? 0);
        $manualMarketUrls = $translationGroupId > 0
            ? ($this->fetchManualMarketUrlsByGroupIds([$translationGroupId])[$translationGroupId] ?? [])
            : [];
        $syncedMarketUrls = $this->decodeMarketUrls((string) ($row['market_urls_json'] ?? ''));

        $row['synced_market_urls'] = $syncedMarketUrls;
        $row['manual_market_urls'] = $manualMarketUrls;
        $row['market_urls'] = array_replace($syncedMarketUrls, $manualMarketUrls);

        return $row;
    }

    private function fetchManualMarketUrlsByGroupIds(array $translationGroupIds): array
    {
        $normalizedIds = array_values(array_filter(array_map(static fn (mixed $value): int => (int) $value, $translationGroupIds), static fn (int $value): bool => $value > 0));
        if ($normalizedIds === []) {
            return [];
        }

        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($normalizedIds), '?'));
        $types = str_repeat('i', count($normalizedIds));
        $statement = $connection->prepare(
            "SELECT translation_group_id, market_code, destination_url
             FROM product_market_overrides
             WHERE is_active = 1
               AND translation_group_id IN ({$placeholders})
             ORDER BY translation_group_id ASC, market_code ASC"
        );
        $statement->bind_param($types, ...$normalizedIds);
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        $grouped = [];

        foreach ($rows as $row) {
            $translationGroupId = (int) ($row['translation_group_id'] ?? 0);
            $marketCode = $this->normalizeMarketCode((string) ($row['market_code'] ?? ''));
            $destinationUrl = trim((string) ($row['destination_url'] ?? ''));

            if ($translationGroupId <= 0 || $marketCode === '' || $destinationUrl === '') {
                continue;
            }

            $grouped[$translationGroupId][$marketCode] = $destinationUrl;
        }

        return $grouped;
    }

    private function decodeMarketUrls(string $json): array
    {
        if (trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }

        $normalized = [];

        foreach ($decoded as $marketCode => $destinationUrl) {
            $normalizedMarketCode = $this->normalizeMarketCode((string) $marketCode);
            $normalizedDestinationUrl = trim((string) $destinationUrl);

            if ($normalizedMarketCode === '' || $normalizedDestinationUrl === '') {
                continue;
            }

            $normalized[$normalizedMarketCode] = $normalizedDestinationUrl;
        }

        return $normalized;
    }

    private function normalizeMarketCode(string $marketCode): string
    {
        $marketCode = strtolower(trim($marketCode));
        $marketCode = preg_replace('/[^a-z0-9_-]/', '', $marketCode) ?? '';

        return $marketCode;
    }
}
