<?php

declare(strict_types=1);

namespace Avc\Controllers\Ops;

use Avc\Core\Database;
use Avc\Core\Request;
use Avc\Core\Response;
use mysqli;

final class ReadonlyController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        if (!$this->isAuthorized()) {
            $this->response->json(['ok' => false, 'error' => 'Not found.'], 404);
        }

        $scope = strtolower(trim((string) $this->request->input('scope', 'health')));
        $connection = Database::connection($this->config);

        $payload = [
            'ok' => true,
            'scope' => $scope,
            'generated_at' => gmdate('c'),
            'app' => [
                'name' => (string) ($this->config['app_name'] ?? 'Aloe Vera Centar'),
                'base_url' => (string) ($this->config['base_url'] ?? ''),
                'php_version' => PHP_VERSION,
            ],
            'database' => [
                'connected' => $connection instanceof mysqli,
            ],
        ];

        if ($scope === 'overview') {
            $payload['overview'] = $this->overview($connection);
        } else {
            $payload['health'] = $this->health($connection);
        }

        $this->response->json($payload);
    }

    private function isAuthorized(): bool
    {
        $settings = (array) ($this->config['ops_readonly'] ?? []);
        $enabled = (bool) ($settings['enabled'] ?? false);
        $expectedKey = trim((string) ($settings['key'] ?? ''));

        if (!$enabled || $expectedKey === '') {
            return false;
        }

        $providedKey = trim((string) $this->request->input('key', ''));
        if ($providedKey === '') {
            $providedKey = trim((string) $this->request->header('X-AVC-Ops-Key', ''));
        }

        return $providedKey !== '' && hash_equals($expectedKey, $providedKey);
    }

    private function health(?mysqli $connection): array
    {
        if (!$connection instanceof mysqli) {
            return [
                'status' => 'degraded',
                'checks' => [
                    'database' => false,
                ],
            ];
        }

        return [
            'status' => 'ok',
            'checks' => [
                'database' => true,
                'content_routes' => $this->count($connection, 'content_routes') > 0,
                'content_translations' => $this->count($connection, 'content_translations') > 0,
                'product_recommendations' => $this->count($connection, 'product_recommendations') > 0,
            ],
            'counts' => [
                'content_items' => $this->count($connection, 'content_items'),
                'content_translations' => $this->count($connection, 'content_translations'),
                'content_routes' => $this->count($connection, 'content_routes'),
                'product_recommendations' => $this->count($connection, 'product_recommendations'),
            ],
        ];
    }

    private function overview(?mysqli $connection): array
    {
        if (!$connection instanceof mysqli) {
            return [
                'status' => 'degraded',
                'reason' => 'Database is not connected.',
            ];
        }

        return [
            'status' => 'ok',
            'content_by_type' => $this->groupCount($connection, 'content_items', 'content_type'),
            'translations_by_language' => $this->groupCount($connection, 'content_translations', 'language_code'),
            'routes_by_status' => $this->groupCount($connection, 'content_routes', 'http_status_code'),
            'lead_counts' => [
                'ai_leads' => $this->count($connection, 'ai_leads'),
                'discount_leads' => $this->count($connection, 'discount_leads'),
                'outbound_clicks' => $this->count($connection, 'outbound_clicks'),
            ],
            'commerce' => [
                'product_recommendations' => $this->count($connection, 'product_recommendations'),
                'product_market_overrides' => $this->count($connection, 'product_market_overrides'),
            ],
        ];
    }

    private function count(mysqli $connection, string $table): int
    {
        if (!$this->tableExists($connection, $table)) {
            return 0;
        }

        $result = $connection->query('SELECT COUNT(*) AS total FROM `' . $table . '`');
        $row = $result ? $result->fetch_assoc() : null;

        return (int) ($row['total'] ?? 0);
    }

    private function groupCount(mysqli $connection, string $table, string $column): array
    {
        if (!$this->tableExists($connection, $table)) {
            return [];
        }

        $allowedColumns = [
            'content_type',
            'language_code',
            'http_status_code',
        ];

        if (!in_array($column, $allowedColumns, true)) {
            return [];
        }

        $rows = [];
        $result = $connection->query(
            'SELECT `' . $column . '` AS label, COUNT(*) AS total FROM `' . $table . '` GROUP BY `' . $column . '` ORDER BY total DESC'
        );

        while ($result && ($row = $result->fetch_assoc())) {
            $rows[(string) $row['label']] = (int) $row['total'];
        }

        return $rows;
    }

    private function tableExists(mysqli $connection, string $table): bool
    {
        static $allowedTables = [
            'ai_leads',
            'content_items',
            'content_routes',
            'content_translations',
            'discount_leads',
            'outbound_clicks',
            'product_market_overrides',
            'product_recommendations',
        ];

        if (!in_array($table, $allowedTables, true)) {
            return false;
        }

        $statement = $connection->prepare('SELECT COUNT(*) AS total FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
        $statement->bind_param('s', $table);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();
        $exists = (int) ($row['total'] ?? 0) > 0;
        $statement->close();

        return $exists;
    }
}
