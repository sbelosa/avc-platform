<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class AiLeadRepository
{
    private const ALLOWED_STATUSES = ['new', 'contacted', 'qualified', 'closed'];

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
            'INSERT INTO ai_leads (
                content_translation_id, advisor_session_key, conversation_public_id, language_code, country_code, name, email, phone, lead_question,
                source_path, recommended_content_path, recommended_market_code, lead_status
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->bind_param(
            'issssssssssss',
            $payload['content_translation_id'],
            $payload['advisor_session_key'],
            $payload['conversation_public_id'],
            $payload['language_code'],
            $payload['country_code'],
            $payload['name'],
            $payload['email'],
            $payload['phone'],
            $payload['lead_question'],
            $payload['source_path'],
            $payload['recommended_content_path'],
            $payload['recommended_market_code'],
            $payload['lead_status']
        );
        $statement->execute();
        $insertId = (int) $connection->insert_id;
        $statement->close();

        return $insertId;
    }

    public function markAdminNotified(int $aiLeadId): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return;
        }

        $statement = $connection->prepare('UPDATE ai_leads SET admin_notified_at = NOW() WHERE ai_lead_id = ?');
        $statement->bind_param('i', $aiLeadId);
        $statement->execute();
        $statement->close();
    }

    public function latest(int $limit = 50): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $limit = max(1, min($limit, 200));
        $result = $connection->query(
            "SELECT ai_lead_id, language_code, country_code, name, email, phone, lead_question, source_path,
                    recommended_content_path, recommended_market_code, lead_status, admin_notified_at, created_at
             FROM ai_leads
             ORDER BY created_at DESC
             LIMIT {$limit}"
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function listForAdmin(?string $status = null, ?string $languageCode = null, ?string $countryCode = null, string $query = '', int $limit = 100): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $limit = max(1, min($limit, 300));
        $whereClauses = ['1 = 1'];
        $types = '';
        $params = [];

        $status = $this->normalizeStatus($status);
        if ($status !== null) {
            $whereClauses[] = 'lead_status = ?';
            $types .= 's';
            $params[] = $status;
        }

        $languageCode = strtolower(trim((string) $languageCode));
        if ($languageCode !== '') {
            $whereClauses[] = 'language_code = ?';
            $types .= 's';
            $params[] = $languageCode;
        }

        $countryCode = strtoupper(trim((string) $countryCode));
        if ($countryCode !== '') {
            $whereClauses[] = 'country_code = ?';
            $types .= 's';
            $params[] = $countryCode;
        }

        $query = trim($query);
        if ($query !== '') {
            $like = '%' . $query . '%';
            $whereClauses[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR lead_question LIKE ? OR source_path LIKE ?)';
            $types .= 'sssss';
            array_push($params, $like, $like, $like, $like, $like);
        }

        $sql = 'SELECT ai_lead_id, language_code, country_code, name, email, phone, lead_question, source_path,
                    recommended_content_path, recommended_market_code, lead_status, admin_notified_at, created_at, updated_at
                FROM ai_leads
                WHERE ' . implode(' AND ', $whereClauses) . '
                ORDER BY created_at DESC
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

    public function summarizeForAdmin(): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [
                'total' => 0,
                'new_total' => 0,
                'contacted_total' => 0,
                'qualified_total' => 0,
                'closed_total' => 0,
                'top_countries' => [],
                'top_languages' => [],
            ];
        }

        return [
            'total' => $this->getScalar($connection, 'SELECT COUNT(*) AS total FROM ai_leads'),
            'new_total' => $this->getScalar($connection, "SELECT COUNT(*) AS total FROM ai_leads WHERE lead_status = 'new'"),
            'contacted_total' => $this->getScalar($connection, "SELECT COUNT(*) AS total FROM ai_leads WHERE lead_status = 'contacted'"),
            'qualified_total' => $this->getScalar($connection, "SELECT COUNT(*) AS total FROM ai_leads WHERE lead_status = 'qualified'"),
            'closed_total' => $this->getScalar($connection, "SELECT COUNT(*) AS total FROM ai_leads WHERE lead_status = 'closed'"),
            'top_countries' => $this->fetchAll($connection, "SELECT COALESCE(NULLIF(country_code, ''), '—') AS label, COUNT(*) AS total FROM ai_leads GROUP BY label ORDER BY total DESC, label ASC LIMIT 6"),
            'top_languages' => $this->fetchAll($connection, "SELECT COALESCE(NULLIF(language_code, ''), '—') AS label, COUNT(*) AS total FROM ai_leads GROUP BY label ORDER BY total DESC, label ASC LIMIT 6"),
        ];
    }

    public function updateStatus(int $aiLeadId, string $status): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $aiLeadId <= 0) {
            return;
        }

        $normalizedStatus = $this->normalizeStatus($status) ?? 'new';
        $statement = $connection->prepare('UPDATE ai_leads SET lead_status = ? WHERE ai_lead_id = ?');
        $statement->bind_param('si', $normalizedStatus, $aiLeadId);
        $statement->execute();
        $statement->close();
    }

    public function allowedStatuses(): array
    {
        return self::ALLOWED_STATUSES;
    }

    private function normalizeStatus(?string $status): ?string
    {
        $status = strtolower(trim((string) $status));
        if ($status === '' || !in_array($status, self::ALLOWED_STATUSES, true)) {
            return null;
        }

        return $status;
    }

    private function fetchAll(\mysqli $connection, string $sql): array
    {
        $result = $connection->query($sql);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    private function getScalar(\mysqli $connection, string $sql): int
    {
        $result = $connection->query($sql);
        $row = $result ? $result->fetch_assoc() : null;

        return (int) ($row['total'] ?? 0);
    }
}
