<?php

declare(strict_types=1);

namespace Avc\Controllers\Api;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\AiLeadRepository;
use Avc\Services\Advisor\AdvisorConversationService;
use Avc\Services\Advisor\FccStyleAdvisorBrain;
use Avc\Services\Geo\CountryDetector;
use Avc\Services\Geo\MarketResolver;
use Avc\Services\Notifications\AdminLeadNotificationService;
use Avc\Services\Referral\ActiveForeverIdService;

final class AdvisorController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function conversation(): never
    {
        $languageCode = $this->normalizeLanguage((string) $this->request->input('language_code', 'hr'));
        $countryCode = (new CountryDetector())->detect($this->request);
        $preferredMarketCode = (new MarketResolver())->resolvePreferredMarket(
            $countryCode,
            (string) $this->request->header('Accept-Language', ''),
            []
        );

        $service = new AdvisorConversationService($this->config, $this->request);
        $conversation = $service->createOrResume([
            'conversation_public_id' => (string) $this->request->input('conversation_public_id', ''),
            'content_translation_id' => (int) $this->request->input('content_translation_id', 0),
            'language_code' => $languageCode,
            'country_code' => $countryCode,
            'source_path' => $this->normalizePath((string) $this->request->input('source_path', '/')),
            'source_type' => (string) $this->request->input('source_type', 'page'),
            'source_title' => (string) $this->request->input('source_title', ''),
            'preferred_market_code' => $preferredMarketCode,
        ]);

        if ($conversation === null) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Conversation bootstrap failed.',
            ], 500);
        }

        $brain = new FccStyleAdvisorBrain($this->config);
        $service->ensureWelcomeMessage($conversation, $brain->welcomeMessage($languageCode), [
            'kind' => 'welcome',
            'recommendations' => [
                'products' => [],
                'articles' => [],
            ],
        ]);

        $messages = $service->listMessages($conversation);

        $this->response->json([
            'status' => 'ok',
            'conversation_public_id' => (string) ($conversation['conversation_public_id'] ?? ''),
            'language_code' => (string) ($conversation['language_code'] ?? $languageCode),
            'messages' => $service->serializeMessages($messages),
        ]);
    }

    public function message(): never
    {
        $conversationPublicId = trim((string) $this->request->input('conversation_public_id', ''));
        $body = trim((string) $this->request->input('message', ''));

        if ($conversationPublicId === '' || $body === '') {
            $this->response->json([
                'status' => 'error',
                'message' => 'Conversation and message are required.',
            ], 422);
        }

        $service = new AdvisorConversationService($this->config, $this->request);
        $conversation = $service->findConversation($conversationPublicId);

        if ($conversation === null) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Conversation not found.',
            ], 404);
        }

        $history = $service->listMessages($conversation);
        $service->createUserMessage($conversation, $body);

        $brain = new FccStyleAdvisorBrain($this->config);
        $reply = $brain->buildReply($body, [
            'language' => (string) ($conversation['language_code'] ?? 'hr'),
            'source_path' => (string) ($conversation['source_path'] ?? '/'),
            'content_translation_id' => (int) ($conversation['content_translation_id'] ?? 0),
            'history' => $history,
        ]);
        $assistantMessage = $service->createAssistantMessage($conversation, (string) ($reply['body'] ?? ''), is_array($reply['payload'] ?? null) ? $reply['payload'] : []);

        if ($assistantMessage === null) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Assistant reply failed.',
            ], 500);
        }

        $this->response->json([
            'status' => 'ok',
            'conversation_public_id' => $conversationPublicId,
            'message' => $service->serializeMessages([$assistantMessage])[0],
        ]);
    }

    public function lead(): never
    {
        $conversationPublicId = trim((string) $this->request->input('conversation_public_id', ''));
        $email = trim((string) $this->request->input('email', ''));
        $name = trim((string) $this->request->input('name', ''));
        $phone = trim((string) $this->request->input('phone', ''));

        if ($conversationPublicId === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Conversation and valid email are required.',
            ], 422);
        }

        $service = new AdvisorConversationService($this->config, $this->request);
        $conversation = $service->findConversation($conversationPublicId);
        if ($conversation === null) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Conversation not found.',
            ], 404);
        }

        $messages = $service->listMessages($conversation);
        $latestUserQuestion = '';
        foreach (array_reverse($messages) as $message) {
            if ((string) ($message['role'] ?? '') === 'user') {
                $latestUserQuestion = trim((string) ($message['body_text'] ?? ''));
                break;
            }
        }

        $leadQuestion = trim((string) $this->request->input('question', $latestUserQuestion));
        if ($leadQuestion === '') {
            $leadQuestion = trim((string) $this->request->input('lead_question', ''));
        }

        $countryCode = (new CountryDetector())->detect($this->request);
        $recommendedMarketCode = (new MarketResolver())->resolvePreferredMarket(
            $countryCode,
            (string) $this->request->header('Accept-Language', ''),
            []
        );
        $sourcePath = $this->normalizePath((string) ($conversation['source_path'] ?? '/'));

        $recommendations = (new FccStyleAdvisorBrain($this->config))->buildReply($leadQuestion, [
            'language' => (string) ($conversation['language_code'] ?? 'hr'),
            'source_path' => $sourcePath,
            'content_translation_id' => (int) ($conversation['content_translation_id'] ?? 0),
            'history' => $messages,
        ]);
        $recommendedContentPath = $this->normalizePath((string) (($recommendations['payload']['recommendations']['top_route_path'] ?? $sourcePath)));

        $repository = new AiLeadRepository($this->config);
        $aiLeadId = $repository->create([
            'content_translation_id' => (int) ($conversation['content_translation_id'] ?? 0) ?: null,
            'advisor_session_key' => (string) ($conversation['conversation_public_id'] ?? $conversationPublicId),
            'conversation_public_id' => $conversationPublicId,
            'language_code' => (string) ($conversation['language_code'] ?? 'hr'),
            'country_code' => $countryCode,
            'name' => $name !== '' ? $name : null,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'lead_question' => $leadQuestion !== '' ? $leadQuestion : 'Advisor lead capture',
            'source_path' => $sourcePath,
            'recommended_content_path' => $recommendedContentPath,
            'recommended_market_code' => $recommendedMarketCode,
            'lead_status' => 'new',
        ]);

        if ($aiLeadId <= 0) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Lead insert failed.',
            ], 500);
        }

        $activeForeverIdService = new ActiveForeverIdService($this->config);
        $notificationService = new AdminLeadNotificationService($this->config, $activeForeverIdService->getAdminNotificationEmail());
        $notificationSent = $notificationService->notify([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'language_code' => (string) ($conversation['language_code'] ?? 'hr'),
            'country_code' => $countryCode,
            'source_path' => $sourcePath,
            'recommended_content_path' => $recommendedContentPath,
            'recommended_market_code' => $recommendedMarketCode,
            'lead_question' => $leadQuestion,
        ]);

        if ($notificationSent) {
            $repository->markAdminNotified($aiLeadId);
        }

        $service->markLeadCaptured($conversation);
        $service->createAssistantMessage($conversation, $this->leadSavedMessage((string) ($conversation['language_code'] ?? 'hr')), [
            'kind' => 'lead_saved',
            'recommendations' => [
                'products' => [],
                'articles' => [],
            ],
        ]);

        $this->response->json([
            'status' => 'ok',
            'ai_lead_id' => $aiLeadId,
            'admin_notified' => $notificationSent,
            'message' => $this->leadSavedMessage((string) ($conversation['language_code'] ?? 'hr')),
        ], 201);
    }

    public function feedback(): never
    {
        $conversationPublicId = trim((string) $this->request->input('conversation_public_id', ''));
        $messagePublicId = trim((string) $this->request->input('message_public_id', ''));
        $feedbackValue = trim((string) $this->request->input('value', ''));
        $feedbackNote = trim((string) $this->request->input('note', ''));

        if ($conversationPublicId === '' || $messagePublicId === '' || $feedbackValue === '') {
            $this->response->json([
                'status' => 'error',
                'message' => 'Conversation, message and feedback value are required.',
            ], 422);
        }

        $service = new AdvisorConversationService($this->config, $this->request);
        $conversation = $service->findConversation($conversationPublicId);
        if ($conversation === null) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Conversation not found.',
            ], 404);
        }

        $saved = $service->saveFeedback($messagePublicId, $feedbackValue, $feedbackNote !== '' ? $feedbackNote : null);
        if (!$saved) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Feedback could not be saved.',
            ], 422);
        }

        $this->response->json([
            'status' => 'ok',
        ]);
    }

    private function normalizeLanguage(string $language): string
    {
        $language = strtolower(trim($language));

        return in_array($language, ['hr', 'en', 'sl'], true) ? $language : 'hr';
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

    private function leadSavedMessage(string $languageCode): string
    {
        return match ($this->normalizeLanguage($languageCode)) {
            'en' => 'Thanks. Your contact is saved and AVC support can continue personally from here.',
            'sl' => 'Hvala. Tvoj kontakt je shranjen in AVC podpora lahko nadaljuje osebno.',
            default => 'Hvala. Tvoj kontakt je spremljen i AVC podrška može nastaviti osobno.',
        };
    }
}
