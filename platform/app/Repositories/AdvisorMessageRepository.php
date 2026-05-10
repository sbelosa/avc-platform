<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class AdvisorMessageRepository
{
    public function __construct(private array $config)
    {
    }

    public function create(array $payload): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        $payloadJson = $payload['payload'] !== null
            ? json_encode($payload['payload'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : null;

        $statement = $connection->prepare(
            'INSERT INTO advisor_messages (
                advisor_conversation_id, message_public_id, role, body_text, payload_json
             ) VALUES (?, ?, ?, ?, ?)'
        );
        $statement->bind_param(
            'issss',
            $payload['advisor_conversation_id'],
            $payload['message_public_id'],
            $payload['role'],
            $payload['body_text'],
            $payloadJson
        );
        $statement->execute();
        $statement->close();

        return $this->findByPublicId((string) $payload['message_public_id']);
    }

    public function listForConversation(int $advisorConversationId, int $limit = 80): array
    {
        $connection = Database::connection($this->config);
        if ($connection === null || $advisorConversationId <= 0) {
            return [];
        }

        $limit = max(1, min($limit, 200));
        $statement = $connection->prepare(
            'SELECT advisor_message_id, advisor_conversation_id, message_public_id, role, body_text, payload_json,
                    feedback_value, feedback_note, feedback_created_at, created_at
             FROM advisor_messages
             WHERE advisor_conversation_id = ?
             ORDER BY advisor_message_id ASC
             LIMIT ?'
        );
        $statement->bind_param('ii', $advisorConversationId, $limit);
        $statement->execute();
        $result = $statement->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $statement->close();

        return array_map([$this, 'hydrateRow'], $rows);
    }

    public function findByPublicId(string $messagePublicId): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null || trim($messagePublicId) === '') {
            return null;
        }

        $statement = $connection->prepare(
            'SELECT advisor_message_id, advisor_conversation_id, message_public_id, role, body_text, payload_json,
                    feedback_value, feedback_note, feedback_created_at, created_at
             FROM advisor_messages
             WHERE message_public_id = ?
             LIMIT 1'
        );
        $statement->bind_param('s', $messagePublicId);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ? $this->hydrateRow($result) : null;
    }

    public function saveFeedback(string $messagePublicId, string $feedbackValue, ?string $feedbackNote = null): bool
    {
        $connection = Database::connection($this->config);
        if ($connection === null || trim($messagePublicId) === '') {
            return false;
        }

        $normalizedFeedback = $this->normalizeFeedbackValue($feedbackValue);
        if ($normalizedFeedback === null) {
            return false;
        }

        $feedbackNote = trim((string) $feedbackNote);
        $feedbackNote = $feedbackNote !== '' ? $feedbackNote : null;

        $statement = $connection->prepare(
            'UPDATE advisor_messages
             SET feedback_value = ?, feedback_note = ?, feedback_created_at = NOW()
             WHERE message_public_id = ?
               AND role = \'assistant\''
        );
        $statement->bind_param('sss', $normalizedFeedback, $feedbackNote, $messagePublicId);
        $statement->execute();
        $affectedRows = $statement->affected_rows;
        $statement->close();

        return $affectedRows > 0;
    }

    private function hydrateRow(array $row): array
    {
        $payload = [];
        if (!empty($row['payload_json'])) {
            $decoded = json_decode((string) $row['payload_json'], true);
            $payload = is_array($decoded) ? $decoded : [];
        }

        $row['payload'] = $payload;
        unset($row['payload_json']);

        return $row;
    }

    private function normalizeFeedbackValue(string $feedbackValue): ?string
    {
        $feedbackValue = strtolower(trim($feedbackValue));

        return match ($feedbackValue) {
            'up', 'down', 'helpful', 'not_helpful' => $feedbackValue,
            default => null,
        };
    }
}
