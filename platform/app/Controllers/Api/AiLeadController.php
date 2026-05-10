<?php

declare(strict_types=1);

namespace Avc\Controllers\Api;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\AiLeadRepository;
use Avc\Services\Advisor\AdvisorRecommendationService;
use Avc\Services\Geo\CountryDetector;
use Avc\Services\Geo\MarketResolver;
use Avc\Services\Notifications\AdminLeadNotificationService;
use Avc\Services\Referral\ActiveForeverIdService;

final class AiLeadController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function store(): never
    {
        $name = trim((string) $this->request->input('name', ''));
        $email = trim((string) $this->request->input('email', ''));
        $phone = trim((string) $this->request->input('phone', ''));
        $question = trim((string) ($this->request->input('question', $this->request->input('lead_question', ''))));
        $languageCode = strtolower(trim((string) $this->request->input('language_code', 'hr'))) ?: 'hr';
        $sourcePath = $this->normalizePath((string) $this->request->input('source_path', '/'));
        $advisorRecommendations = (new AdvisorRecommendationService($this->config))->recommend($question, $languageCode, $sourcePath);
        $recommendedContentPath = $this->normalizePath((string) ($advisorRecommendations['top_route_path'] ?? $this->request->input('recommended_content_path', $sourcePath)));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $question === '') {
            $this->fail('Email i pitanje su obavezni.', $sourcePath);
        }

        $countryCode = (new CountryDetector())->detect($this->request);
        $recommendedMarketCode = (new MarketResolver())->resolvePreferredMarket(
            $countryCode,
            (string) $this->request->header('Accept-Language', ''),
            []
        );

        $repository = new AiLeadRepository($this->config);
        $aiLeadId = $repository->create([
            'content_translation_id' => (int) $this->request->input('content_translation_id', 0) ?: null,
            'advisor_session_key' => $this->buildSessionKey(),
            'conversation_public_id' => null,
            'language_code' => $languageCode,
            'country_code' => $countryCode,
            'name' => $name !== '' ? $name : null,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'lead_question' => $question,
            'source_path' => $sourcePath,
            'recommended_content_path' => $recommendedContentPath,
            'recommended_market_code' => $recommendedMarketCode,
            'lead_status' => 'new',
        ]);

        if ($aiLeadId <= 0) {
            $this->fail('Lead insert failed.', $sourcePath);
        }

        $activeForeverIdService = new ActiveForeverIdService($this->config);
        $notificationService = new AdminLeadNotificationService($this->config, $activeForeverIdService->getAdminNotificationEmail());
        $notificationSent = $notificationService->notify([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'language_code' => $languageCode,
            'country_code' => $countryCode,
            'source_path' => $sourcePath,
            'recommended_content_path' => $recommendedContentPath,
            'recommended_market_code' => $recommendedMarketCode,
            'lead_question' => $question,
        ]);

        if ($notificationSent) {
            $repository->markAdminNotified($aiLeadId);
        }

        if ($this->wantsJson()) {
            $this->response->json([
                'status' => 'ok',
                'ai_lead_id' => $aiLeadId,
                'admin_notified' => $notificationSent,
                'message' => $this->successMessage($languageCode),
                'recommendations' => [
                    'intro' => $this->recommendationIntro($languageCode),
                    'products_title' => $this->productsTitle($languageCode),
                    'articles_title' => $this->articlesTitle($languageCode),
                    'open_article_label' => $this->openArticleLabel($languageCode),
                    'open_guide_label' => $this->openGuideLabel($languageCode),
                    'shop_label' => $this->shopLabel($languageCode),
                    'products' => $advisorRecommendations['products'] ?? [],
                    'articles' => $advisorRecommendations['articles'] ?? [],
                ],
            ], 201);
        }

        $this->response->redirect($sourcePath . '?ai_lead=success#ai-advisor');
    }

    private function fail(string $message, string $sourcePath): never
    {
        if ($this->wantsJson()) {
            $this->response->json([
                'status' => 'error',
                'message' => $message,
            ], 422);
        }

        $this->response->redirect($sourcePath . '?ai_lead=error#ai-advisor', 302);
    }

    private function wantsJson(): bool
    {
        $accept = strtolower((string) $this->request->header('Accept', ''));
        $contentType = strtolower((string) $this->request->header('Content-Type', ''));

        return str_contains($accept, 'application/json') || str_contains($contentType, 'application/json');
    }

    private function buildSessionKey(): string
    {
        return substr(hash('sha256', uniqid('avc-ai-', true) . '|' . mt_rand()), 0, 40);
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

    private function successMessage(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'Thanks. We saved your request and prepared a few relevant recommendations.',
            'sl' => 'Hvala. Shranili smo tvoje vprašanje in pripravili nekaj uporabnih priporočil.',
            default => 'Hvala. Spremili smo tvoj upit i pripremili nekoliko korisnih preporuka.',
        };
    }

    private function recommendationIntro(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'Based on your question, these guides and articles look like the best next step.',
            'sl' => 'Na temelju tvojega vprašanja so ti vodiči in članki najkorisnejši sljedeći korak.',
            default => 'Na temelju tvog pitanja ovo su trenutno najkorisniji vodiči i članci.',
        };
    }

    private function productsTitle(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'Recommended products',
            'sl' => 'Priporočeni izdelki',
            default => 'Preporučeni proizvodi',
        };
    }

    private function articlesTitle(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'Helpful articles',
            'sl' => 'Korisni članki',
            default => 'Korisni članci',
        };
    }

    private function openArticleLabel(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'Open article',
            'sl' => 'Odpri članek',
            default => 'Otvori članak',
        };
    }

    private function openGuideLabel(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'Open guide',
            'sl' => 'Odpri vodič',
            default => 'Otvori vodič',
        };
    }

    private function shopLabel(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'Go to shop',
            'sl' => 'Pojdi v shop',
            default => 'Idi na kupnju',
        };
    }
}
