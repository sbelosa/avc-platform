<?php

declare(strict_types=1);

namespace Avc\Controllers\Api;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\AbTestRepository;
use Avc\Repositories\ContentRepository;
use Avc\Repositories\DiscountLeadRepository;
use Avc\Repositories\ProductRecommendationRepository;
use Avc\Services\Geo\CountryDetector;
use Avc\Services\Geo\MarketResolver;
use Avc\Services\Notifications\AdminLeadNotificationService;
use Avc\Services\Notifications\CustomerDiscountEmailService;
use Avc\Services\Referral\ActiveForeverIdService;
use Avc\Services\Referral\DiscountUrlBuilder;
use Avc\Services\Referral\ReferralUrlBuilder;

final class DiscountLeadController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function store(): never
    {
        $contentTranslationId = (int) $this->request->input('content_translation_id', (int) $this->request->input('id', 0));
        $sourcePath = $this->normalizePath((string) $this->request->input('source_path', '/'));
        $languageCode = strtolower(trim((string) $this->request->input('language_code', (string) $this->request->input('lang', 'hr')))) ?: 'hr';
        $name = $this->normalizeText((string) $this->request->input('name', ''), 190);
        $email = strtolower($this->normalizeText((string) $this->request->input('email', ''), 190));
        $phone = $this->normalizeText((string) $this->request->input('phone', ''), 80);
        $consent = $this->truthy($this->request->input('consent_contact', false));
        $abTestKey = $this->normalizeTrackingKey((string) $this->request->input('ab_test_key', ''));
        $abVariantKey = $this->normalizeTrackingKey((string) $this->request->input('ab_variant_key', ''));
        if ($abTestKey === AbTestRepository::DISCOUNT_MODAL_TEST_KEY) {
            if ($abVariantKey === 'email_only') {
                $phone = '';
            } elseif ($abVariantKey === 'phone_only') {
                $email = '';
            }
        }

        if ($contentTranslationId <= 0) {
            $this->fail($this->message('missing_product', $languageCode), 422, $sourcePath);
        }

        if (!$consent) {
            $this->fail($this->message('consent_required', $languageCode), 422, $sourcePath);
        }

        if ($email === '' && !$this->hasUsablePhone($phone)) {
            $this->fail($this->message('contact_required', $languageCode), 422, $sourcePath);
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail($this->message('email_invalid', $languageCode), 422, $sourcePath);
        }

        $recommendation = (new ProductRecommendationRepository($this->config))->findByContentTranslationId($contentTranslationId);
        if ($recommendation === null) {
            $this->fail($this->message('missing_product', $languageCode), 404, $sourcePath);
        }

        $contentRecord = (new ContentRepository($this->config))->findForAdminEdit($contentTranslationId) ?? [];
        $productTitle = $this->normalizeText((string) ($contentRecord['title'] ?? ''), 255);
        $languageCode = strtolower(trim((string) ($contentRecord['language_code'] ?? $languageCode))) ?: $languageCode;
        if ($sourcePath === '/' && trim((string) ($contentRecord['route_path'] ?? '')) !== '') {
            $sourcePath = $this->normalizePath((string) $contentRecord['route_path']);
        }

        $countryCode = (new CountryDetector())->detect($this->request);
        $marketCode = (new MarketResolver())->resolvePreferredMarket(
            $countryCode,
            (string) $this->request->header('Accept-Language', ''),
            array_keys((array) ($recommendation['market_urls'] ?? []))
        );

        $activeForeverIdService = new ActiveForeverIdService($this->config);
        $activeForeverId = $activeForeverIdService->getActiveForeverId();
        $fallbackUrl = (new ReferralUrlBuilder($this->config))->buildProductDestination($recommendation, $marketCode, $activeForeverId);
        $discountUrl = (new DiscountUrlBuilder($this->config))->buildProductDiscountDestination($recommendation, $marketCode, $activeForeverId);

        if ($discountUrl === null) {
            $this->fail($this->message('discount_unavailable', $languageCode), 409, $sourcePath, [
                'fallback_url' => $fallbackUrl,
            ]);
        }

        $token = $this->generateToken();
        $repository = new DiscountLeadRepository($this->config);
        $discountLeadId = $repository->create([
            'content_translation_id' => $contentTranslationId,
            'discount_token' => $token,
            'language_code' => $languageCode,
            'country_code' => $countryCode,
            'market_code' => $marketCode,
            'name' => $name !== '' ? $name : null,
            'email' => $email !== '' ? $email : null,
            'phone' => $phone !== '' ? $phone : null,
            'consent_contact' => 1,
            'product_title' => $productTitle !== '' ? $productTitle : null,
            'source_path' => $sourcePath,
            'destination_url' => $discountUrl,
            'fallback_url' => $fallbackUrl,
            'lead_status' => 'new',
            'visitor_hash' => $this->buildVisitorHash(),
            'browser_language' => trim((string) $this->request->header('Accept-Language', '')),
            'ab_test_key' => $abTestKey !== '' ? $abTestKey : null,
            'ab_variant_key' => $abVariantKey !== '' ? $abVariantKey : null,
        ]);

        if ($discountLeadId <= 0) {
            $this->fail($this->message('save_failed', $languageCode), 500, $sourcePath);
        }

        if ($abTestKey !== '' && $abVariantKey !== '') {
            (new AbTestRepository($this->config))->recordEvent([
                'test_key' => $abTestKey,
                'variant_key' => $abVariantKey,
                'event_type' => 'conversion',
                'visitor_hash' => $this->buildVisitorHash(),
                'source_path' => $sourcePath,
                'content_translation_id' => $contentTranslationId,
                'language_code' => $languageCode,
                'metadata' => [
                    'discount_lead_id' => $discountLeadId,
                    'contact_type' => $email !== '' ? 'email' : 'phone',
                    'product_title' => $productTitle,
                ],
            ]);
        }

        $discountRedirectPath = '/go/discount?token=' . rawurlencode($token);
        $discountRedirectUrl = $this->absoluteUrl($discountRedirectPath);

        $notificationService = new AdminLeadNotificationService($this->config, $activeForeverIdService->getAdminNotificationEmail());
        $notificationSent = $notificationService->notify([
            'lead_type' => 'discount',
            'subject_prefix' => 'AVC popust lead',
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'language_code' => $languageCode,
            'country_code' => $countryCode,
            'source_path' => $sourcePath,
            'recommended_content_path' => $sourcePath,
            'recommended_market_code' => $marketCode,
            'lead_question' => 'Zeli aktivirati 15% popusta za proizvod: ' . ($productTitle !== '' ? $productTitle : ('CT #' . $contentTranslationId)) . PHP_EOL . 'Discount link: ' . $discountUrl,
        ]);

        if ($notificationSent) {
            $repository->markAdminNotified($discountLeadId);
        }

        $customerNotificationSent = false;
        if ($email !== '') {
            $customerNotificationSent = (new CustomerDiscountEmailService($this->config))->notify([
                'name' => $name,
                'email' => $email,
                'language_code' => $languageCode,
                'product_title' => $productTitle,
                'discount_lead_id' => $discountLeadId,
            ], $discountRedirectUrl);

            if ($customerNotificationSent) {
                $repository->markCustomerNotified($discountLeadId);
            }
        }

        $this->response->json([
            'status' => 'ok',
            'discount_lead_id' => $discountLeadId,
            'admin_notified' => $notificationSent,
            'customer_notified' => $customerNotificationSent,
            'redirect_url' => $discountRedirectPath,
            'message' => $this->message('success', $languageCode),
        ], 201);
    }

    private function fail(string $message, int $status, string $sourcePath, array $extra = []): never
    {
        $this->response->json(array_merge([
            'status' => 'error',
            'message' => $message,
            'source_path' => $sourcePath,
        ], $extra), $status);
    }

    private function generateToken(): string
    {
        try {
            return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        } catch (\Throwable) {
            return substr(hash('sha256', uniqid('avc-discount-', true) . '|' . mt_rand()), 0, 48);
        }
    }

    private function normalizeText(string $value, int $maxLength): string
    {
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = trim((string) preg_replace('/\s+/u', ' ', $value));

        return $value !== '' ? mb_substr($value, 0, $maxLength) : '';
    }

    private function normalizeTrackingKey(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]+/', '_', $value) ?? '';

        return mb_substr(trim($value, '_'), 0, 80);
    }

    private function hasUsablePhone(string $phone): bool
    {
        return strlen((string) preg_replace('/\D+/', '', $phone)) >= 6;
    }

    private function truthy(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on', 'da'], true);
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

    private function buildVisitorHash(): string
    {
        $ip = trim((string) ($this->request->server('REMOTE_ADDR', '') ?: ''));
        $userAgent = trim((string) $this->request->header('User-Agent', ''));

        return hash('sha256', $ip . '|' . $userAgent);
    }

    private function absoluteUrl(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $host = trim((string) $this->request->header('Host', ''));
        if ($host !== '') {
            $forwardedProto = strtolower(trim((string) $this->request->header('X-Forwarded-Proto', '')));
            $isHttps = strtolower((string) $this->request->server('HTTPS', '')) === 'on';
            $scheme = $forwardedProto !== '' ? $forwardedProto : ($isHttps ? 'https' : 'http');

            return $scheme . '://' . $host . '/' . ltrim($path, '/');
        }

        return rtrim((string) ($this->config['base_url'] ?? 'https://aloavera-centar.com'), '/') . '/' . ltrim($path, '/');
    }

    private function message(string $key, string $languageCode): string
    {
        $messages = [
            'hr' => [
                'missing_product' => 'Ne mogu pronaci proizvod za aktivaciju popusta.',
                'consent_required' => 'Za slanje linka s popustom trebamo tvoju potvrdu.',
                'contact_required' => 'Upisi email ili broj mobitela kako bismo ti spremili link s popustom.',
                'email_invalid' => 'Email adresa nije ispravna.',
                'discount_unavailable' => 'Popust trenutno nije dostupan za ovaj proizvod. Možeš nastaviti na službeni shop.',
                'save_failed' => 'Kontakt trenutno nije spremljen. Pokušaj ponovno za koji trenutak.',
                'success' => 'Super, popust je spreman. Otvaramo službeni Forever shop.',
            ],
            'en' => [
                'missing_product' => 'We could not find the product for this discount.',
                'consent_required' => 'Please confirm so we can save and send your discount link.',
                'contact_required' => 'Enter your email or phone number so we can save your discount link.',
                'email_invalid' => 'The email address is not valid.',
                'discount_unavailable' => 'The discount is currently unavailable for this product. You can still continue to the official shop.',
                'save_failed' => 'The contact was not saved yet. Please try again shortly.',
                'success' => 'Great, your discount is ready. Opening the official Forever shop.',
            ],
            'sl' => [
                'missing_product' => 'Izdelka za aktivacijo popusta ne najdemo.',
                'consent_required' => 'Za shranjevanje povezave s popustom potrebujemo tvojo potrditev.',
                'contact_required' => 'Vpiši email ali telefon, da shranimo povezavo s popustom.',
                'email_invalid' => 'Email naslov ni pravilen.',
                'discount_unavailable' => 'Popust za ta izdelek trenutno ni na voljo. Lahko nadaljuješ v uradni shop.',
                'save_failed' => 'Kontakt trenutno ni shranjen. Poskusi znova čez trenutek.',
                'success' => 'Super, popust je pripravljen. Odpiramo uradni Forever shop.',
            ],
        ];

        $language = strtolower($languageCode);

        return $messages[$language][$key] ?? $messages['hr'][$key] ?? '';
    }
}
