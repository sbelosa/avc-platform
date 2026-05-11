<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class OutboundClickRepository
{
    public function __construct(private array $config)
    {
    }

    public function hasRecentDuplicate(array $payload, int $windowSeconds = 120): bool
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return false;
        }

        $windowSeconds = max(10, min(3600, $windowSeconds));
        $contentTranslationId = (int) ($payload['content_translation_id'] ?? 0);
        $sourcePath = (string) ($payload['source_path'] ?? '');
        $ctaPosition = (string) ($payload['cta_position'] ?? '');
        $ctaVariant = (string) ($payload['cta_variant'] ?? '');
        $destinationMarketCode = (string) ($payload['destination_market_code'] ?? '');
        $visitorHash = (string) ($payload['visitor_hash'] ?? '');

        $statement = $connection->prepare(
            "SELECT outbound_click_id
             FROM outbound_clicks
             WHERE COALESCE(content_translation_id, 0) = ?
               AND source_path = ?
               AND COALESCE(cta_position, '') = ?
               AND COALESCE(cta_variant, '') = ?
               AND COALESCE(destination_market_code, '') = ?
               AND COALESCE(visitor_hash, '') = ?
               AND created_at >= DATE_SUB(NOW(), INTERVAL ? SECOND)
             LIMIT 1"
        );

        $statement->bind_param(
            'isssssi',
            $contentTranslationId,
            $sourcePath,
            $ctaPosition,
            $ctaVariant,
            $destinationMarketCode,
            $visitorHash,
            $windowSeconds
        );
        $statement->execute();
        $result = $statement->get_result();
        $exists = $result !== false && $result->num_rows > 0;
        $statement->close();

        return $exists;
    }

    public function create(array $payload): int
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return 0;
        }

        $statement = $connection->prepare(
            'INSERT INTO outbound_clicks (
                content_translation_id, source_path, destination_url, destination_market_code, forever_id_used, language_code,
                country_code, city_name, browser_language, click_source, cta_position, cta_variant, cta_label,
                utm_source, utm_medium, utm_campaign, visitor_hash
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $statement->bind_param(
            'issssssssssssssss',
            $payload['content_translation_id'],
            $payload['source_path'],
            $payload['destination_url'],
            $payload['destination_market_code'],
            $payload['forever_id_used'],
            $payload['language_code'],
            $payload['country_code'],
            $payload['city_name'],
            $payload['browser_language'],
            $payload['click_source'],
            $payload['cta_position'],
            $payload['cta_variant'],
            $payload['cta_label'],
            $payload['utm_source'],
            $payload['utm_medium'],
            $payload['utm_campaign'],
            $payload['visitor_hash']
        );
        $statement->execute();
        $insertId = (int) $connection->insert_id;
        $statement->close();

        return $insertId;
    }
}
