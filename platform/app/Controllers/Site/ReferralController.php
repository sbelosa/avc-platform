<?php

declare(strict_types=1);

namespace Avc\Controllers\Site;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\DiscountLeadRepository;
use Avc\Repositories\OutboundClickRepository;
use Avc\Repositories\ProductRecommendationRepository;
use Avc\Services\Analytics\TrafficQualityService;
use Avc\Services\Geo\CountryDetector;
use Avc\Services\Geo\MarketResolver;
use Avc\Services\Referral\ActiveForeverIdService;
use Avc\Services\Referral\ReferralUrlBuilder;

final class ReferralController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function product(): never
    {
        $contentTranslationId = (int) $this->request->input('id', 0);
        $sourcePath = $this->normalizePath((string) $this->request->input('source_path', '/'));

        if ($contentTranslationId <= 0) {
            $this->redirectNoIndex($sourcePath);
        }

        $recommendation = (new ProductRecommendationRepository($this->config))->findByContentTranslationId($contentTranslationId);
        if ($recommendation === null) {
            $this->redirectNoIndex($sourcePath);
        }

        $countryCode = (new CountryDetector())->detect($this->request);
        $marketCode = (new MarketResolver())->resolvePreferredMarket(
            $countryCode,
            (string) $this->request->header('Accept-Language', ''),
            array_keys((array) ($recommendation['market_urls'] ?? []))
        );

        $activeForeverId = (new ActiveForeverIdService($this->config))->getActiveForeverId();
        $destinationUrl = (new ReferralUrlBuilder($this->config))->buildProductDestination($recommendation, $marketCode, $activeForeverId);

        if ($destinationUrl === null) {
            $this->redirectNoIndex($sourcePath);
        }

        $clickSource = $this->normalizeClickSource((string) $this->request->input('source', 'content_cta'));
        $ctaPosition = $this->normalizeTrackingKey((string) $this->request->input('cta_position', $clickSource), $clickSource, 80);
        $ctaLabel = $this->normalizeCtaLabel((string) $this->request->input('cta_label', ''));
        $ctaVariantFallback = $ctaLabel !== '' ? $ctaLabel : $clickSource;
        $ctaVariant = $this->normalizeTrackingKey((string) $this->request->input('cta_variant', $ctaVariantFallback), $clickSource, 80);

        $clickPayload = [
            'content_translation_id' => $contentTranslationId,
            'source_path' => $sourcePath,
            'destination_url' => $destinationUrl,
            'destination_market_code' => $marketCode,
            'forever_id_used' => $activeForeverId !== '' ? $activeForeverId : null,
            'language_code' => trim((string) ($this->request->input('lang', '') ?: $recommendation['language_code'] ?? '')),
            'country_code' => $countryCode,
            'city_name' => null,
            'browser_language' => trim((string) $this->request->header('Accept-Language', '')),
            'click_source' => $clickSource,
            'cta_position' => $ctaPosition,
            'cta_variant' => $ctaVariant,
            'cta_label' => $ctaLabel !== '' ? $ctaLabel : null,
            'utm_source' => 'avc',
            'utm_medium' => 'product_guide',
            'utm_campaign' => 'content_translation:' . $contentTranslationId,
            'visitor_hash' => $this->buildVisitorHash(),
        ];

        $clickRepository = new OutboundClickRepository($this->config);
        if ((new TrafficQualityService())->shouldTrackOutboundClick($this->request, true)
            && !$clickRepository->hasRecentDuplicate($clickPayload)
        ) {
            $clickRepository->create($clickPayload);
        }

        $this->redirectNoIndex($destinationUrl);
    }

    public function discount(): never
    {
        $token = trim((string) $this->request->input('token', ''));
        if ($token === '') {
            $this->redirectNoIndex('/');
        }

        $repository = new DiscountLeadRepository($this->config);
        $lead = $repository->findByToken($token);
        if ($lead === null) {
            $this->redirectNoIndex('/');
        }

        $sourcePath = $this->normalizePath((string) ($lead['source_path'] ?? '/'));
        $destinationUrl = trim((string) ($lead['destination_url'] ?? ''));
        if ($destinationUrl === '' || !filter_var($destinationUrl, FILTER_VALIDATE_URL)) {
            $this->redirectNoIndex($sourcePath);
        }

        $countryCode = trim((string) ($lead['country_code'] ?? ''));
        if ($countryCode === '') {
            $countryCode = (new CountryDetector())->detect($this->request);
        }

        $discountLeadId = (int) ($lead['discount_lead_id'] ?? 0);
        $isTrackableClick = (new TrafficQualityService())->shouldTrackOutboundClick($this->request);
        if ($isTrackableClick && (string) ($lead['lead_status'] ?? 'new') === 'new') {
            $repository->updateStatus($discountLeadId, 'sent');
        }

        $activeForeverId = (new ActiveForeverIdService($this->config))->getActiveForeverId();
        $clickPayload = [
            'content_translation_id' => (int) ($lead['content_translation_id'] ?? 0) ?: null,
            'source_path' => $sourcePath,
            'destination_url' => $destinationUrl,
            'destination_market_code' => trim((string) ($lead['market_code'] ?? '')),
            'forever_id_used' => $activeForeverId !== '' ? $activeForeverId : null,
            'language_code' => trim((string) ($lead['language_code'] ?? '')),
            'country_code' => $countryCode !== '' ? $countryCode : null,
            'city_name' => null,
            'browser_language' => trim((string) $this->request->header('Accept-Language', (string) ($lead['browser_language'] ?? ''))),
            'click_source' => 'discount_lead',
            'cta_position' => 'discount_modal_submit',
            'cta_variant' => 'discount_15_fcc',
            'cta_label' => 'Aktiviraj 15% popusta',
            'utm_source' => 'avc',
            'utm_medium' => 'discount_lead',
            'utm_campaign' => 'discount_lead:' . $discountLeadId,
            'visitor_hash' => trim((string) ($lead['visitor_hash'] ?? '')) !== '' ? (string) $lead['visitor_hash'] : $this->buildVisitorHash(),
        ];

        $clickRepository = new OutboundClickRepository($this->config);
        if ($isTrackableClick && !$clickRepository->hasRecentDuplicate($clickPayload)) {
            $clickRepository->create($clickPayload);
        }

        $this->redirectNoIndex($destinationUrl);
    }

    private function redirectNoIndex(string $location): never
    {
        header('X-Robots-Tag: noindex, nofollow', false);
        $this->response->redirect($location);
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

    private function normalizeClickSource(string $source): string
    {
        $source = strtolower(trim($source));
        $source = preg_replace('/[^a-z0-9_-]+/', '_', $source) ?? '';
        $source = trim($source, '_');

        return $source !== '' ? mb_substr($source, 0, 50) : 'content_cta';
    }

    private function normalizeTrackingKey(string $value, string $fallback, int $maxLength): string
    {
        $value = mb_strtolower(trim(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $value = strtr($value, [
            'č' => 'c',
            'ć' => 'c',
            'đ' => 'd',
            'š' => 's',
            'ž' => 'z',
        ]);
        $value = preg_replace('/[^a-z0-9_-]+/u', '_', $value) ?? '';
        $value = trim($value, '_');

        return $value !== '' ? mb_substr($value, 0, $maxLength) : mb_substr($fallback, 0, $maxLength);
    }

    private function normalizeCtaLabel(string $value): string
    {
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = trim((string) preg_replace('/\s+/u', ' ', $value));

        return $value !== '' ? mb_substr($value, 0, 180) : '';
    }
}
