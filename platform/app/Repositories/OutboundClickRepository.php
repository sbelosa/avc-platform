<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class OutboundClickRepository
{
    public function __construct(private array $config)
    {
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
