<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class AdvisorConversationRepository
{
    public function __construct(private array $config)
    {
    }

    public function findByPublicId(string $conversationPublicId): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null || trim($conversationPublicId) === '') {
            return null;
        }

        $statement = $connection->prepare(
            'SELECT advisor_conversation_id, conversation_public_id, visitor_key, content_translation_id, language_code,
                    country_code, source_path, source_type, source_title, preferred_market_code, lead_status,
                    last_user_message_at, last_assistant_message_at, created_at, updated_at
             FROM advisor_conversations
             WHERE conversation_public_id = ?
             LIMIT 1'
        );
        $statement->bind_param('s', $conversationPublicId);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ?: null;
    }

    public function findLatestByVisitorAndSource(string $visitorKey, string $sourcePath): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null || trim($visitorKey) === '' || trim($sourcePath) === '') {
            return null;
        }

        $statement = $connection->prepare(
            'SELECT advisor_conversation_id, conversation_public_id, visitor_key, content_translation_id, language_code,
                    country_code, source_path, source_type, source_title, preferred_market_code, lead_status,
                    last_user_message_at, last_assistant_message_at, created_at, updated_at
             FROM advisor_conversations
             WHERE visitor_key = ?
               AND source_path = ?
             ORDER BY updated_at DESC
             LIMIT 1'
        );
        $statement->bind_param('ss', $visitorKey, $sourcePath);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ?: null;
    }

    public function create(array $payload): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        $statement = $connection->prepare(
            'INSERT INTO advisor_conversations (
                conversation_public_id, visitor_key, content_translation_id, language_code, country_code,
                source_path, source_type, source_title, preferred_market_code, lead_status
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->bind_param(
            'ssisssssss',
            $payload['conversation_public_id'],
            $payload['visitor_key'],
            $payload['content_translation_id'],
            $payload['language_code'],
            $payload['country_code'],
            $payload['source_path'],
            $payload['source_type'],
            $payload['source_title'],
            $payload['preferred_market_code'],
            $payload['lead_status']
        );
        $statement->execute();
        $statement->close();

        return $this->findByPublicId((string) $payload['conversation_public_id']);
    }

    public function touchUserMessage(int $advisorConversationId): void
    {
        $this->touch($advisorConversationId, 'last_user_message_at');
    }

    public function touchAssistantMessage(int $advisorConversationId): void
    {
        $this->touch($advisorConversationId, 'last_assistant_message_at');
    }

    public function markLeadCaptured(int $advisorConversationId): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $advisorConversationId <= 0) {
            return;
        }

        $leadStatus = 'captured';
        $statement = $connection->prepare(
            'UPDATE advisor_conversations
             SET lead_status = ?
             WHERE advisor_conversation_id = ?'
        );
        $statement->bind_param('si', $leadStatus, $advisorConversationId);
        $statement->execute();
        $statement->close();
    }

    private function touch(int $advisorConversationId, string $column): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $advisorConversationId <= 0) {
            return;
        }

        $allowedColumns = ['last_user_message_at', 'last_assistant_message_at'];
        if (!in_array($column, $allowedColumns, true)) {
            return;
        }

        $statement = $connection->prepare(
            "UPDATE advisor_conversations
             SET {$column} = NOW()
             WHERE advisor_conversation_id = ?"
        );
        $statement->bind_param('i', $advisorConversationId);
        $statement->execute();
        $statement->close();
    }
}
