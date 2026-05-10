<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class ContentRevisionRepository
{
    public function __construct(private array $config)
    {
    }

    public function createFromRecord(array $record, ?int $adminUserId = null, string $revisionType = 'manual_save'): void
    {
        $connection = Database::connection($this->config);
        $contentTranslationId = (int) ($record['content_translation_id'] ?? 0);
        if ($connection === null || $contentTranslationId <= 0) {
            return;
        }

        $this->ensureTable();

        $title = trim((string) ($record['title'] ?? ''));
        $snapshotJson = json_encode($this->snapshotFromRecord($record), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($snapshotJson === false) {
            return;
        }

        $statement = $connection->prepare(
            'INSERT INTO content_revisions (
                content_translation_id,
                title,
                revision_type,
                snapshot_json,
                changed_by_admin_user_id
             ) VALUES (?, ?, ?, ?, ?)'
        );
        $statement->bind_param('isssi', $contentTranslationId, $title, $revisionType, $snapshotJson, $adminUserId);
        $statement->execute();
        $statement->close();
    }

    public function listForContentTranslationId(int $contentTranslationId, int $limit = 6): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $contentTranslationId <= 0) {
            return [];
        }

        $this->ensureTable();
        $limit = max(1, min(20, $limit));
        $statement = $connection->prepare(
            'SELECT content_revision_id, content_translation_id, title, revision_type, snapshot_json, created_at
             FROM content_revisions
             WHERE content_translation_id = ?
             ORDER BY created_at DESC, content_revision_id DESC
             LIMIT ?'
        );
        $statement->bind_param('ii', $contentTranslationId, $limit);
        $statement->execute();
        $rows = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $statement->close();

        return array_map([$this, 'hydrateRevision'], $rows);
    }

    public function findForContentTranslationId(int $contentRevisionId, int $contentTranslationId): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $contentRevisionId <= 0 || $contentTranslationId <= 0) {
            return null;
        }

        $this->ensureTable();
        $statement = $connection->prepare(
            'SELECT content_revision_id, content_translation_id, title, revision_type, snapshot_json, created_at
             FROM content_revisions
             WHERE content_revision_id = ?
               AND content_translation_id = ?
             LIMIT 1'
        );
        $statement->bind_param('ii', $contentRevisionId, $contentTranslationId);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $row ? $this->hydrateRevision($row) : null;
    }

    private function ensureTable(): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return;
        }

        $connection->query(
            'CREATE TABLE IF NOT EXISTS content_revisions (
                content_revision_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                content_translation_id BIGINT UNSIGNED NOT NULL,
                title VARCHAR(255) NULL,
                revision_type VARCHAR(40) NOT NULL DEFAULT "manual_save",
                snapshot_json LONGTEXT NOT NULL,
                changed_by_admin_user_id BIGINT UNSIGNED NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_content_revisions_translation_created (content_translation_id, created_at),
                CONSTRAINT fk_content_revisions_translation
                    FOREIGN KEY (content_translation_id) REFERENCES content_translations(content_translation_id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_content_revisions_admin
                    FOREIGN KEY (changed_by_admin_user_id) REFERENCES admin_users(admin_user_id)
                    ON DELETE SET NULL
            )'
        );
    }

    private function hydrateRevision(array $row): array
    {
        $snapshot = json_decode((string) ($row['snapshot_json'] ?? ''), true);
        $row['snapshot'] = is_array($snapshot) ? $snapshot : [];

        return $row;
    }

    private function snapshotFromRecord(array $record): array
    {
        return [
            'title' => (string) ($record['title'] ?? ''),
            'excerpt' => (string) ($record['excerpt'] ?? ''),
            'summary_html' => (string) ($record['summary_html'] ?? ''),
            'faq_json' => (string) ($record['faq_json'] ?? ''),
            'body_html' => (string) ($record['body_html'] ?? ''),
            'featured_image_path' => (string) ($record['featured_image_path'] ?? ''),
            'meta_title' => (string) ($record['meta_title'] ?? ''),
            'meta_description' => (string) ($record['meta_description'] ?? ''),
            'canonical_url' => (string) ($record['canonical_url'] ?? ''),
            'breadcrumb_title' => (string) ($record['breadcrumb_title'] ?? ''),
            'robots_index' => (int) ($record['robots_index'] ?? 1),
            'robots_follow' => (int) ($record['robots_follow'] ?? 1),
        ];
    }
}
