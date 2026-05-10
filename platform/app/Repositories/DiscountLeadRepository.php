<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class DiscountLeadRepository
{
    private const ALLOWED_STATUSES = ['new', 'sent', 'contacted', 'qualified', 'closed'];

    public function __construct(private array $config)
    {
    }

    public function ensureTable(): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return;
        }

        $connection->query(
            "CREATE TABLE IF NOT EXISTS discount_leads (
                discount_lead_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                content_translation_id BIGINT UNSIGNED NULL,
                discount_token VARCHAR(80) NOT NULL,
                language_code VARCHAR(10) NOT NULL,
                country_code VARCHAR(10) NULL,
                market_code VARCHAR(10) NULL,
                name VARCHAR(190) NULL,
                email VARCHAR(190) NULL,
                phone VARCHAR(80) NULL,
                consent_contact TINYINT(1) NOT NULL DEFAULT 0,
                product_title VARCHAR(255) NULL,
                source_path VARCHAR(500) NOT NULL,
                destination_url VARCHAR(1200) NOT NULL,
                fallback_url VARCHAR(1200) NULL,
                lead_status VARCHAR(50) NOT NULL DEFAULT 'new',
                admin_notified_at DATETIME NULL,
                customer_notified_at DATETIME NULL,
                visitor_hash VARCHAR(128) NULL,
                browser_language VARCHAR(120) NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_discount_leads_token (discount_token),
                INDEX idx_discount_leads_status_created (lead_status, created_at),
                INDEX idx_discount_leads_language_country (language_code, country_code),
                INDEX idx_discount_leads_contact (email, phone),
                INDEX idx_discount_leads_translation (content_translation_id)
            )"
        );

        $columnResult = $connection->query("SHOW COLUMNS FROM discount_leads LIKE 'customer_notified_at'");
        $hasCustomerNotifiedAt = $columnResult instanceof \mysqli_result && $columnResult->num_rows > 0;
        $columnResult?->close();

        if (!$hasCustomerNotifiedAt) {
            $connection->query('ALTER TABLE discount_leads ADD COLUMN customer_notified_at DATETIME NULL AFTER admin_notified_at');
        }
    }

    public function create(array $payload): int
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return 0;
        }

        $this->ensureTable();

        $contentTranslationId = $payload['content_translation_id'];
        $discountToken = (string) $payload['discount_token'];
        $languageCode = (string) $payload['language_code'];
        $countryCode = $payload['country_code'];
        $marketCode = $payload['market_code'];
        $name = $payload['name'];
        $email = $payload['email'];
        $phone = $payload['phone'];
        $consentContact = (int) ($payload['consent_contact'] ?? 0);
        $productTitle = $payload['product_title'];
        $sourcePath = (string) $payload['source_path'];
        $destinationUrl = (string) $payload['destination_url'];
        $fallbackUrl = $payload['fallback_url'];
        $leadStatus = (string) ($payload['lead_status'] ?? 'new');
        $visitorHash = $payload['visitor_hash'];
        $browserLanguage = $payload['browser_language'];

        $statement = $connection->prepare(
            'INSERT INTO discount_leads (
                content_translation_id, discount_token, language_code, country_code, market_code, name, email, phone,
                consent_contact, product_title, source_path, destination_url, fallback_url, lead_status, visitor_hash, browser_language
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->bind_param(
            'isssssssisssssss',
            $contentTranslationId,
            $discountToken,
            $languageCode,
            $countryCode,
            $marketCode,
            $name,
            $email,
            $phone,
            $consentContact,
            $productTitle,
            $sourcePath,
            $destinationUrl,
            $fallbackUrl,
            $leadStatus,
            $visitorHash,
            $browserLanguage
        );
        $statement->execute();
        $insertId = (int) $connection->insert_id;
        $statement->close();

        return $insertId;
    }

    public function findByToken(string $token): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null || trim($token) === '') {
            return null;
        }

        $this->ensureTable();

        $statement = $connection->prepare(
            'SELECT *
             FROM discount_leads
             WHERE discount_token = ?
             LIMIT 1'
        );
        $statement->bind_param('s', $token);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $row ?: null;
    }

    public function latest(int $limit = 50): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $this->ensureTable();
        $limit = max(1, min($limit, 200));
        $result = $connection->query(
            "SELECT discount_lead_id, content_translation_id, language_code, country_code, market_code, name, email, phone,
                    consent_contact, product_title, source_path, destination_url, fallback_url, lead_status, admin_notified_at, customer_notified_at, created_at
             FROM discount_leads
             ORDER BY created_at DESC
             LIMIT {$limit}"
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function pendingCustomerNotifications(int $limit = 50): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $this->ensureTable();
        $limit = max(1, min($limit, 200));
        $result = $connection->query(
            "SELECT discount_lead_id, discount_token, language_code, country_code, market_code, name, email, phone,
                    product_title, source_path, destination_url, fallback_url, lead_status, customer_notified_at, created_at
             FROM discount_leads
             WHERE email IS NOT NULL
                AND email <> ''
                AND customer_notified_at IS NULL
             ORDER BY created_at ASC
             LIMIT {$limit}"
        );

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findForCustomerNotification(int $discountLeadId): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $discountLeadId <= 0) {
            return null;
        }

        $this->ensureTable();
        $statement = $connection->prepare(
            'SELECT discount_lead_id, discount_token, language_code, country_code, market_code, name, email, phone,
                    product_title, source_path, destination_url, fallback_url, lead_status, customer_notified_at, created_at
             FROM discount_leads
             WHERE discount_lead_id = ?
             LIMIT 1'
        );
        $statement->bind_param('i', $discountLeadId);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $row ?: null;
    }

    public function listForAdmin(?string $status = null, ?string $languageCode = null, ?string $countryCode = null, string $query = '', int $limit = 160): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return [];
        }

        $this->ensureTable();
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
            $whereClauses[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR product_title LIKE ? OR source_path LIKE ?)';
            $types .= 'sssss';
            array_push($params, $like, $like, $like, $like, $like);
        }

        $sql = 'SELECT discount_lead_id, content_translation_id, discount_token, language_code, country_code, market_code,
                    name, email, phone, consent_contact, product_title, source_path, destination_url, fallback_url,
                    lead_status, admin_notified_at, customer_notified_at, created_at, updated_at
                FROM discount_leads
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
            return $this->emptySummary();
        }

        $this->ensureTable();

        return [
            'total' => $this->getScalar($connection, 'SELECT COUNT(*) AS total FROM discount_leads'),
            'new_total' => $this->getScalar($connection, "SELECT COUNT(*) AS total FROM discount_leads WHERE lead_status = 'new'"),
            'sent_total' => $this->getScalar($connection, "SELECT COUNT(*) AS total FROM discount_leads WHERE lead_status = 'sent'"),
            'contacted_total' => $this->getScalar($connection, "SELECT COUNT(*) AS total FROM discount_leads WHERE lead_status = 'contacted'"),
            'qualified_total' => $this->getScalar($connection, "SELECT COUNT(*) AS total FROM discount_leads WHERE lead_status = 'qualified'"),
            'closed_total' => $this->getScalar($connection, "SELECT COUNT(*) AS total FROM discount_leads WHERE lead_status = 'closed'"),
            'top_products' => $this->fetchAll($connection, "SELECT COALESCE(NULLIF(product_title, ''), '—') AS label, COUNT(*) AS total FROM discount_leads GROUP BY label ORDER BY total DESC, label ASC LIMIT 8"),
            'top_countries' => $this->fetchAll($connection, "SELECT COALESCE(NULLIF(country_code, ''), '—') AS label, COUNT(*) AS total FROM discount_leads GROUP BY label ORDER BY total DESC, label ASC LIMIT 8"),
        ];
    }

    public function markAdminNotified(int $discountLeadId): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $discountLeadId <= 0) {
            return;
        }

        $this->ensureTable();
        $statement = $connection->prepare('UPDATE discount_leads SET admin_notified_at = NOW() WHERE discount_lead_id = ?');
        $statement->bind_param('i', $discountLeadId);
        $statement->execute();
        $statement->close();
    }

    public function markCustomerNotified(int $discountLeadId): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $discountLeadId <= 0) {
            return;
        }

        $this->ensureTable();
        $statement = $connection->prepare('UPDATE discount_leads SET customer_notified_at = NOW() WHERE discount_lead_id = ?');
        $statement->bind_param('i', $discountLeadId);
        $statement->execute();
        $statement->close();
    }

    public function updateStatus(int $discountLeadId, string $status): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $discountLeadId <= 0) {
            return;
        }

        $this->ensureTable();
        $normalizedStatus = $this->normalizeStatus($status) ?? 'new';
        $statement = $connection->prepare('UPDATE discount_leads SET lead_status = ? WHERE discount_lead_id = ?');
        $statement->bind_param('si', $normalizedStatus, $discountLeadId);
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

    private function emptySummary(): array
    {
        return [
            'total' => 0,
            'new_total' => 0,
            'sent_total' => 0,
            'contacted_total' => 0,
            'qualified_total' => 0,
            'closed_total' => 0,
            'top_products' => [],
            'top_countries' => [],
        ];
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
