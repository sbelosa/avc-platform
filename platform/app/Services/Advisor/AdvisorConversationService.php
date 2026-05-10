<?php

declare(strict_types=1);

namespace Avc\Services\Advisor;

use Avc\Core\Request;
use Avc\Repositories\AdvisorConversationRepository;
use Avc\Repositories\AdvisorMessageRepository;

final class AdvisorConversationService
{
    private AdvisorConversationRepository $conversationRepository;
    private AdvisorMessageRepository $messageRepository;

    public function __construct(
        private array $config,
        private Request $request
    ) {
        $this->conversationRepository = new AdvisorConversationRepository($this->config);
        $this->messageRepository = new AdvisorMessageRepository($this->config);
    }

    public function createOrResume(array $context): ?array
    {
        $conversationPublicId = trim((string) ($context['conversation_public_id'] ?? ''));
        if ($conversationPublicId !== '') {
            $existing = $this->conversationRepository->findByPublicId($conversationPublicId);
            if ($existing !== null) {
                return $existing;
            }
        }

        $visitorKey = $this->resolveVisitorKey();
        $sourcePath = $this->normalizePath((string) ($context['source_path'] ?? '/'));
        $existing = $this->conversationRepository->findLatestByVisitorAndSource($visitorKey, $sourcePath);

        if ($existing !== null) {
            return $existing;
        }

        return $this->conversationRepository->create([
            'conversation_public_id' => $this->buildPublicId('avcconv'),
            'visitor_key' => $visitorKey,
            'content_translation_id' => (int) ($context['content_translation_id'] ?? 0) ?: null,
            'language_code' => $this->normalizeLanguage((string) ($context['language_code'] ?? 'hr')),
            'country_code' => $this->normalizeCountryCode((string) ($context['country_code'] ?? '')),
            'source_path' => $sourcePath,
            'source_type' => trim((string) ($context['source_type'] ?? 'page')) ?: 'page',
            'source_title' => trim((string) ($context['source_title'] ?? '')) ?: null,
            'preferred_market_code' => strtolower(trim((string) ($context['preferred_market_code'] ?? ''))) ?: null,
            'lead_status' => 'open',
        ]);
    }

    public function findConversation(string $conversationPublicId): ?array
    {
        return $this->conversationRepository->findByPublicId(trim($conversationPublicId));
    }

    public function listMessages(array $conversation): array
    {
        return $this->messageRepository->listForConversation((int) ($conversation['advisor_conversation_id'] ?? 0));
    }

    public function ensureWelcomeMessage(array $conversation, string $welcomeBody, array $payload = []): ?array
    {
        $messages = $this->listMessages($conversation);
        if ($messages !== []) {
            return $messages[0] ?? null;
        }

        return $this->createAssistantMessage($conversation, $welcomeBody, $payload);
    }

    public function createUserMessage(array $conversation, string $body): ?array
    {
        $message = $this->messageRepository->create([
            'advisor_conversation_id' => (int) ($conversation['advisor_conversation_id'] ?? 0),
            'message_public_id' => $this->buildPublicId('avcmsg'),
            'role' => 'user',
            'body_text' => trim($body),
            'payload' => null,
        ]);

        if ($message !== null) {
            $this->conversationRepository->touchUserMessage((int) ($conversation['advisor_conversation_id'] ?? 0));
        }

        return $message;
    }

    public function createAssistantMessage(array $conversation, string $body, array $payload = []): ?array
    {
        $message = $this->messageRepository->create([
            'advisor_conversation_id' => (int) ($conversation['advisor_conversation_id'] ?? 0),
            'message_public_id' => $this->buildPublicId('avcmsg'),
            'role' => 'assistant',
            'body_text' => trim($body),
            'payload' => $payload,
        ]);

        if ($message !== null) {
            $this->conversationRepository->touchAssistantMessage((int) ($conversation['advisor_conversation_id'] ?? 0));
        }

        return $message;
    }

    public function saveFeedback(string $messagePublicId, string $feedbackValue, ?string $feedbackNote = null): bool
    {
        return $this->messageRepository->saveFeedback($messagePublicId, $feedbackValue, $feedbackNote);
    }

    public function markLeadCaptured(array $conversation): void
    {
        $this->conversationRepository->markLeadCaptured((int) ($conversation['advisor_conversation_id'] ?? 0));
    }

    public function serializeMessages(array $messages): array
    {
        return array_values(array_map(static function (array $message): array {
            return [
                'message_public_id' => (string) ($message['message_public_id'] ?? ''),
                'role' => (string) ($message['role'] ?? 'assistant'),
                'body' => (string) ($message['body_text'] ?? ''),
                'payload' => is_array($message['payload'] ?? null) ? $message['payload'] : [],
                'feedback_value' => (string) ($message['feedback_value'] ?? ''),
                'created_at' => (string) ($message['created_at'] ?? ''),
            ];
        }, $messages));
    }

    private function resolveVisitorKey(): string
    {
        $existing = trim((string) $this->request->session('advisor_visitor_key', ''));
        if ($existing !== '') {
            return $existing;
        }

        $seed = trim((string) $this->request->server('REMOTE_ADDR', ''))
            . '|'
            . trim((string) $this->request->header('User-Agent', ''))
            . '|'
            . uniqid('avc-visitor-', true);
        $visitorKey = substr(hash('sha256', $seed), 0, 40);
        $this->request->setSession('advisor_visitor_key', $visitorKey);

        return $visitorKey;
    }

    private function buildPublicId(string $prefix): string
    {
        return $prefix . '_' . substr(hash('sha256', uniqid($prefix . '-', true) . '|' . mt_rand()), 0, 20);
    }

    private function normalizeLanguage(string $language): string
    {
        $language = strtolower(trim($language));

        return in_array($language, ['hr', 'en', 'sl'], true) ? $language : 'hr';
    }

    private function normalizeCountryCode(string $countryCode): ?string
    {
        $countryCode = strtoupper(substr(trim($countryCode), 0, 2));

        return $countryCode !== '' ? $countryCode : null;
    }

    private function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parsedPath = parse_url($path, PHP_URL_PATH);

            return is_string($parsedPath) && $parsedPath !== '' ? $parsedPath : '/';
        }

        return str_starts_with($path, '/') ? $path : '/';
    }
}
