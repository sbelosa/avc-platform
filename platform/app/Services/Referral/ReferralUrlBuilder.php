<?php

declare(strict_types=1);

namespace Avc\Services\Referral;

final class ReferralUrlBuilder
{
    public function __construct(private array $config)
    {
    }

    public function buildProductDestination(array $recommendation, string $marketCode, string $activeForeverId = ''): ?string
    {
        $marketUrls = is_array($recommendation['market_urls'] ?? null) ? $recommendation['market_urls'] : [];
        $baseUrl = trim((string) ($marketUrls[$marketCode] ?? $this->resolveFallbackMarketUrl($marketUrls) ?? $recommendation['external_url'] ?? ''));

        if ($baseUrl === '' || !filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        $strategy = trim((string) ($recommendation['destination_strategy'] ?? 'passthrough'));
        if ($activeForeverId === '' || ($strategy !== 'forever_official' && !$this->isKnownForeverMarketUrl($baseUrl))) {
            return $baseUrl;
        }

        $parsedUrl = parse_url($baseUrl);
        if (!is_array($parsedUrl) || empty($parsedUrl['host'])) {
            return $baseUrl;
        }

        $query = [];
        if (!empty($parsedUrl['query'])) {
            parse_str((string) $parsedUrl['query'], $query);
        }

        $query[$this->getForeverReferralParameter($marketCode, (string) ($parsedUrl['host'] ?? ''))] = $activeForeverId;

        $rebuilt = ($parsedUrl['scheme'] ?? 'https') . '://' . $parsedUrl['host'];
        if (!empty($parsedUrl['port'])) {
            $rebuilt .= ':' . $parsedUrl['port'];
        }

        $rebuilt .= $parsedUrl['path'] ?? '';

        if ($query !== []) {
            $rebuilt .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        if (!empty($parsedUrl['fragment'])) {
            $rebuilt .= '#' . $parsedUrl['fragment'];
        }

        return $rebuilt;
    }

    private function resolveFallbackMarketUrl(array $marketUrls): ?string
    {
        foreach (['hr', 'si', 'gb', 'us'] as $preferredMarketCode) {
            $candidate = trim((string) ($marketUrls[$preferredMarketCode] ?? ''));

            if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_URL)) {
                return $candidate;
            }
        }

        foreach ($marketUrls as $candidate) {
            $candidate = trim((string) $candidate);

            if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_URL)) {
                return $candidate;
            }
        }

        return null;
    }

    private function getForeverReferralParameter(string $marketCode, string $host): string
    {
        $normalizedHost = strtolower(trim($host));
        if (in_array($normalizedHost, ['flpshop.ba', 'www.flpshop.ba', 'foreveralbania.com', 'www.foreveralbania.com'], true)) {
            return 'id';
        }

        $isForeverLivingHost = $normalizedHost === 'foreverliving.com'
            || $normalizedHost === 'www.foreverliving.com'
            || str_ends_with($normalizedHost, '.foreverliving.com');

        return in_array(strtolower($marketCode), ['ba', 'al', 'me', 'xk'], true) && !$isForeverLivingHost
            ? 'id'
            : 'fboId';
    }

    private function isKnownForeverMarketUrl(string $url): bool
    {
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?: ''));

        return $host === 'foreverliving.com'
            || $host === 'www.foreverliving.com'
            || str_ends_with($host, '.foreverliving.com')
            || $host === 'flpshop.ba'
            || $host === 'www.flpshop.ba'
            || $host === 'foreveralbania.com'
            || $host === 'www.foreveralbania.com';
    }
}
