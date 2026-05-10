<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class AnalyticsRepository
{
    public function __construct(private array $config)
    {
    }

    public function getDashboardSnapshot(): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [
                'content_total' => 0,
                'route_total' => 0,
                'click_total' => 0,
                'lead_total' => 0,
                'top_countries' => [],
                'top_content' => [],
                'recent_clicks' => [],
            ];
        }

        return [
            'content_total' => $this->getCount($connection, 'content_translations'),
            'route_total' => $this->getCount($connection, 'content_routes'),
            'click_total' => $this->getCount($connection, 'outbound_clicks'),
            'lead_total' => $this->getCount($connection, 'ai_leads'),
            'product_recommendation_total' => $this->getCount($connection, 'product_recommendations'),
            'product_market_ready_total' => $this->getScalar(
                $connection,
                "SELECT COUNT(*) AS total
                 FROM product_recommendations pr
                 WHERE (pr.market_urls_json IS NOT NULL AND pr.market_urls_json != '')
                    OR EXISTS (
                        SELECT 1
                        FROM product_market_overrides pmo
                        WHERE pmo.translation_group_id = pr.translation_group_id
                          AND pmo.is_active = 1
                    )"
            ),
            'manual_market_override_total' => $this->getScalar(
                $connection,
                "SELECT COUNT(*) AS total
                 FROM product_market_overrides
                 WHERE is_active = 1"
            ),
            'manual_market_override_group_total' => $this->getScalar(
                $connection,
                "SELECT COUNT(DISTINCT translation_group_id) AS total
                 FROM product_market_overrides
                 WHERE is_active = 1"
            ),
            'top_countries' => $this->fetchAll($connection, "SELECT country_code, COUNT(*) AS total FROM outbound_clicks WHERE country_code IS NOT NULL AND country_code != '' GROUP BY country_code ORDER BY total DESC, country_code ASC LIMIT 8"),
            'top_content' => $this->fetchAll($connection, "SELECT source_path, COUNT(*) AS total FROM outbound_clicks GROUP BY source_path ORDER BY total DESC, source_path ASC LIMIT 8"),
            'clicks_by_source' => $this->fetchAll($connection, "SELECT click_source, COUNT(*) AS total FROM outbound_clicks GROUP BY click_source ORDER BY total DESC, click_source ASC LIMIT 12"),
            'clicks_by_cta_position' => $this->fetchAll(
                $connection,
                "SELECT COALESCE(NULLIF(cta_position, ''), NULLIF(click_source, ''), 'content_cta') AS cta_position,
                        COUNT(*) AS total
                 FROM outbound_clicks
                 GROUP BY cta_position
                 ORDER BY total DESC, cta_position ASC
                 LIMIT 12"
            ),
            'clicks_by_cta_variant' => $this->fetchAll(
                $connection,
                "SELECT COALESCE(NULLIF(cta_position, ''), NULLIF(click_source, ''), 'content_cta') AS cta_position,
                        COALESCE(NULLIF(cta_variant, ''), NULLIF(click_source, ''), 'unknown') AS cta_variant,
                        COALESCE(NULLIF(cta_label, ''), NULLIF(cta_variant, ''), NULLIF(click_source, ''), 'Nepoznati CTA') AS cta_label,
                        COUNT(*) AS total
                 FROM outbound_clicks
                 GROUP BY cta_position, cta_variant, cta_label
                 ORDER BY total DESC, cta_position ASC, cta_variant ASC
                 LIMIT 16"
            ),
            'clicks_by_market' => $this->fetchAll($connection, "SELECT destination_market_code, COUNT(*) AS total FROM outbound_clicks WHERE destination_market_code IS NOT NULL AND destination_market_code != '' GROUP BY destination_market_code ORDER BY total DESC, destination_market_code ASC LIMIT 12"),
            'top_clicked_products' => $this->fetchAll(
                $connection,
                "SELECT oc.content_translation_id, ct.title, COUNT(*) AS total
                 FROM outbound_clicks oc
                 LEFT JOIN content_translations ct
                   ON ct.content_translation_id = oc.content_translation_id
                 LEFT JOIN content_items ci
                   ON ci.content_item_id = ct.content_item_id
                 WHERE oc.content_translation_id IS NOT NULL
                   AND (ci.content_type IS NULL OR ci.content_type = 'product_guide')
                 GROUP BY oc.content_translation_id, ct.title
                 ORDER BY total DESC, oc.content_translation_id ASC
                 LIMIT 10"
            ),
            'top_outbound_article_sources' => $this->fetchAll(
                $connection,
                "SELECT oc.source_path,
                        COALESCE(NULLIF(ct.title, ''), oc.source_path) AS title,
                        COUNT(*) AS total,
                        COUNT(DISTINCT NULLIF(oc.visitor_hash, '')) AS unique_visitors,
                        MAX(oc.created_at) AS latest_click_at
                 FROM outbound_clicks oc
                 INNER JOIN content_routes cr
                   ON cr.route_path = oc.source_path
                  AND cr.http_status_code = 200
                 INNER JOIN content_translations ct
                   ON ct.content_translation_id = cr.content_translation_id
                 INNER JOIN content_items ci
                   ON ci.content_item_id = ct.content_item_id
                  AND ci.content_type = 'article'
                 GROUP BY oc.source_path, ct.title
                 ORDER BY total DESC, latest_click_at DESC, oc.source_path ASC
                 LIMIT 10"
            ),
            'top_outbound_product_sources' => $this->fetchAll(
                $connection,
                "SELECT oc.source_path,
                        COALESCE(NULLIF(ct.title, ''), oc.source_path) AS title,
                        COUNT(*) AS total,
                        COUNT(DISTINCT NULLIF(oc.visitor_hash, '')) AS unique_visitors,
                        MAX(oc.created_at) AS latest_click_at
                 FROM outbound_clicks oc
                 INNER JOIN content_routes cr
                   ON cr.route_path = oc.source_path
                  AND cr.http_status_code = 200
                 INNER JOIN content_translations ct
                   ON ct.content_translation_id = cr.content_translation_id
                 INNER JOIN content_items ci
                   ON ci.content_item_id = ct.content_item_id
                  AND ci.content_type = 'product_guide'
                 GROUP BY oc.source_path, ct.title
                 ORDER BY total DESC, latest_click_at DESC, oc.source_path ASC
                 LIMIT 10"
            ),
            'recent_clicks' => $this->fetchAll($connection, "SELECT source_path, destination_market_code, country_code, click_source, cta_position, cta_variant, cta_label, created_at FROM outbound_clicks ORDER BY created_at DESC LIMIT 8"),
        ];
    }

    private function getCount(\mysqli $connection, string $table): int
    {
        return $this->getScalar($connection, "SELECT COUNT(*) AS total FROM {$table}");
    }

    private function fetchAll(\mysqli $connection, string $query): array
    {
        $result = $connection->query($query);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    private function getScalar(\mysqli $connection, string $query): int
    {
        $result = $connection->query($query);
        $row = $result ? $result->fetch_assoc() : null;

        return (int) ($row['total'] ?? 0);
    }
}
