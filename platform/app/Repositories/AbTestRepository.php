<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class AbTestRepository
{
    public const DISCOUNT_MODAL_TEST_KEY = 'discount_modal_contact';

    private const DEFAULT_TESTS = [
        self::DISCOUNT_MODAL_TEST_KEY => [
            'title' => 'Popup za 15% popust: email ili mobitel',
            'description' => 'Testira daje li bolji rezultat popup koji traži samo email ili popup koji traži samo broj mobitela.',
            'status' => 'active',
            'variants' => [
                'email_only' => [
                    'label' => 'Samo email',
                    'description' => 'Posjetitelj vidi samo email polje i nakon prijave dobiva link s popustom emailom.',
                    'weight' => 50,
                    'is_control' => 1,
                ],
                'phone_only' => [
                    'label' => 'Samo mobitel',
                    'description' => 'Posjetitelj vidi samo polje za mobitel, a AVC sprema kontakt i odmah otvara shop.',
                    'weight' => 50,
                    'is_control' => 0,
                ],
            ],
        ],
    ];

    public function __construct(private array $config)
    {
    }

    public function ensureTables(): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return;
        }

        $connection->query(
            "CREATE TABLE IF NOT EXISTS ab_tests (
                test_key VARCHAR(80) NOT NULL PRIMARY KEY,
                title VARCHAR(190) NOT NULL,
                description TEXT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_ab_tests_status (status)
            )"
        );

        $connection->query(
            "CREATE TABLE IF NOT EXISTS ab_test_variants (
                ab_test_variant_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                test_key VARCHAR(80) NOT NULL,
                variant_key VARCHAR(80) NOT NULL,
                label VARCHAR(160) NOT NULL,
                description TEXT NULL,
                weight INT UNSIGNED NOT NULL DEFAULT 50,
                is_control TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_ab_test_variants_key (test_key, variant_key),
                INDEX idx_ab_test_variants_test (test_key)
            )"
        );

        $connection->query(
            "CREATE TABLE IF NOT EXISTS ab_test_events (
                ab_test_event_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                test_key VARCHAR(80) NOT NULL,
                variant_key VARCHAR(80) NOT NULL,
                event_type VARCHAR(40) NOT NULL,
                visitor_hash VARCHAR(128) NULL,
                source_path VARCHAR(500) NULL,
                content_translation_id BIGINT UNSIGNED NULL,
                language_code VARCHAR(10) NULL,
                metadata_json LONGTEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_ab_test_events_test_variant (test_key, variant_key, event_type),
                INDEX idx_ab_test_events_created (created_at),
                INDEX idx_ab_test_events_visitor (visitor_hash)
            )"
        );

        $this->seedDefaultTests($connection);
    }

    public function recordEvent(array $payload): bool
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return false;
        }

        $this->ensureTables();

        $testKey = $this->normalizeKey((string) ($payload['test_key'] ?? ''));
        $variantKey = $this->normalizeKey((string) ($payload['variant_key'] ?? ''));
        $eventType = $this->normalizeKey((string) ($payload['event_type'] ?? ''));
        if ($testKey === '' || $variantKey === '' || !in_array($eventType, ['impression', 'conversion', 'skip'], true)) {
            return false;
        }

        $visitorHash = trim((string) ($payload['visitor_hash'] ?? ''));
        $sourcePath = trim((string) ($payload['source_path'] ?? ''));
        $contentTranslationId = (int) ($payload['content_translation_id'] ?? 0) ?: null;
        $languageCode = mb_substr(strtolower(trim((string) ($payload['language_code'] ?? ''))), 0, 10);
        $metadataJson = json_encode((array) ($payload['metadata'] ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($metadataJson === false || $metadataJson === '[]') {
            $metadataJson = null;
        }

        $statement = $connection->prepare(
            'INSERT INTO ab_test_events (
                test_key, variant_key, event_type, visitor_hash, source_path, content_translation_id, language_code, metadata_json
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->bind_param(
            'sssssiss',
            $testKey,
            $variantKey,
            $eventType,
            $visitorHash,
            $sourcePath,
            $contentTranslationId,
            $languageCode,
            $metadataJson
        );
        $ok = $statement->execute();
        $statement->close();

        return $ok;
    }

    public function listSummaries(): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $this->ensureTables();
        $tests = $this->fetchTests($connection);
        $eventStats = $this->fetchEventStats($connection);
        $leadStats = $this->fetchLeadStats($connection);
        $summaries = [];

        foreach ($tests as $testKey => $test) {
            $variants = [];

            foreach ((array) ($test['variants'] ?? []) as $variantKey => $variant) {
                $eventKey = $testKey . '|' . $variantKey;
                $events = $eventStats[$eventKey] ?? [];
                $leads = $leadStats[$eventKey] ?? [];
                $impressions = (int) ($events['impressions'] ?? 0);
                $conversions = max((int) ($events['conversions'] ?? 0), (int) ($leads['leads'] ?? 0));
                $skips = (int) ($events['skips'] ?? 0);
                $conversionRate = $impressions > 0 ? round(($conversions / $impressions) * 100, 2) : 0.0;

                $variants[$variantKey] = array_merge($variant, [
                    'impressions' => $impressions,
                    'unique_visitors' => (int) ($events['unique_visitors'] ?? 0),
                    'conversions' => $conversions,
                    'email_leads' => (int) ($leads['email_leads'] ?? 0),
                    'phone_leads' => (int) ($leads['phone_leads'] ?? 0),
                    'skips' => $skips,
                    'conversion_rate' => $conversionRate,
                    'last_event_at' => (string) ($events['last_event_at'] ?? ''),
                ]);
            }

            $summaries[] = array_merge($test, [
                'variants' => $variants,
                'winner' => $this->pickWinner($variants),
            ]);
        }

        return $summaries;
    }

    private function seedDefaultTests(\mysqli $connection): void
    {
        foreach (self::DEFAULT_TESTS as $testKey => $test) {
            $title = (string) $test['title'];
            $description = (string) $test['description'];
            $status = (string) $test['status'];
            $statement = $connection->prepare(
                'INSERT INTO ab_tests (test_key, title, description, status)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), status = VALUES(status)'
            );
            $statement->bind_param('ssss', $testKey, $title, $description, $status);
            $statement->execute();
            $statement->close();

            foreach ((array) $test['variants'] as $variantKey => $variant) {
                $label = (string) $variant['label'];
                $variantDescription = (string) $variant['description'];
                $weight = (int) $variant['weight'];
                $isControl = (int) $variant['is_control'];
                $variantStatement = $connection->prepare(
                    'INSERT INTO ab_test_variants (test_key, variant_key, label, description, weight, is_control)
                     VALUES (?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE label = VALUES(label), description = VALUES(description), weight = VALUES(weight), is_control = VALUES(is_control)'
                );
                $variantStatement->bind_param('ssssii', $testKey, $variantKey, $label, $variantDescription, $weight, $isControl);
                $variantStatement->execute();
                $variantStatement->close();
            }
        }
    }

    private function fetchTests(\mysqli $connection): array
    {
        $tests = [];
        $result = $connection->query(
            'SELECT t.test_key, t.title, t.description, t.status,
                    v.variant_key, v.label, v.description AS variant_description, v.weight, v.is_control
             FROM ab_tests t
             LEFT JOIN ab_test_variants v
               ON v.test_key = t.test_key
             ORDER BY t.created_at ASC, v.is_control DESC, v.variant_key ASC'
        );

        while ($result && ($row = $result->fetch_assoc())) {
            $testKey = (string) ($row['test_key'] ?? '');
            if ($testKey === '') {
                continue;
            }

            $tests[$testKey] ??= [
                'test_key' => $testKey,
                'title' => (string) ($row['title'] ?? $testKey),
                'description' => (string) ($row['description'] ?? ''),
                'status' => (string) ($row['status'] ?? 'active'),
                'variants' => [],
            ];

            $variantKey = (string) ($row['variant_key'] ?? '');
            if ($variantKey !== '') {
                $tests[$testKey]['variants'][$variantKey] = [
                    'variant_key' => $variantKey,
                    'label' => (string) ($row['label'] ?? $variantKey),
                    'description' => (string) ($row['variant_description'] ?? ''),
                    'weight' => (int) ($row['weight'] ?? 0),
                    'is_control' => (int) ($row['is_control'] ?? 0) === 1,
                ];
            }
        }

        return $tests;
    }

    private function fetchEventStats(\mysqli $connection): array
    {
        $stats = [];
        $result = $connection->query(
            "SELECT test_key, variant_key,
                    SUM(CASE WHEN event_type = 'impression' THEN 1 ELSE 0 END) AS impressions,
                    SUM(CASE WHEN event_type = 'conversion' THEN 1 ELSE 0 END) AS conversions,
                    SUM(CASE WHEN event_type = 'skip' THEN 1 ELSE 0 END) AS skips,
                    COUNT(DISTINCT NULLIF(visitor_hash, '')) AS unique_visitors,
                    MAX(created_at) AS last_event_at
             FROM ab_test_events
             GROUP BY test_key, variant_key"
        );

        while ($result && ($row = $result->fetch_assoc())) {
            $stats[(string) $row['test_key'] . '|' . (string) $row['variant_key']] = $row;
        }

        return $stats;
    }

    private function fetchLeadStats(\mysqli $connection): array
    {
        $stats = [];
        if (!$this->tableHasColumn($connection, 'discount_leads', 'ab_test_key')
            || !$this->tableHasColumn($connection, 'discount_leads', 'ab_variant_key')
        ) {
            return $stats;
        }

        $result = $connection->query(
            "SELECT ab_test_key AS test_key, ab_variant_key AS variant_key,
                    COUNT(*) AS leads,
                    SUM(CASE WHEN email IS NOT NULL AND email != '' THEN 1 ELSE 0 END) AS email_leads,
                    SUM(CASE WHEN phone IS NOT NULL AND phone != '' THEN 1 ELSE 0 END) AS phone_leads
             FROM discount_leads
             WHERE ab_test_key IS NOT NULL
               AND ab_test_key != ''
               AND ab_variant_key IS NOT NULL
               AND ab_variant_key != ''
             GROUP BY ab_test_key, ab_variant_key"
        );

        while ($result && ($row = $result->fetch_assoc())) {
            $stats[(string) $row['test_key'] . '|' . (string) $row['variant_key']] = $row;
        }

        return $stats;
    }

    private function tableHasColumn(\mysqli $connection, string $table, string $column): bool
    {
        $statement = $connection->prepare(
            'SELECT COUNT(*) AS total
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?'
        );
        $statement->bind_param('ss', $table, $column);
        $statement->execute();
        $result = $statement->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $exists = (int) ($row['total'] ?? 0) > 0;
        $statement->close();

        return $exists;
    }

    private function pickWinner(array $variants): ?array
    {
        $winner = null;
        foreach ($variants as $variant) {
            if ((int) ($variant['impressions'] ?? 0) < 30) {
                continue;
            }

            if ($winner === null || (float) $variant['conversion_rate'] > (float) $winner['conversion_rate']) {
                $winner = $variant;
            }
        }

        return $winner;
    }

    private function normalizeKey(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]+/', '_', $value) ?? '';

        return mb_substr(trim($value, '_'), 0, 80);
    }
}
