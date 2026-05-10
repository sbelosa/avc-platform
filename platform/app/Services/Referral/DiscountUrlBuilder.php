<?php

declare(strict_types=1);

namespace Avc\Services\Referral;

use Avc\Repositories\SettingsRepository;

final class DiscountUrlBuilder
{
    public function __construct(private array $config)
    {
    }

    public function buildProductDiscountDestination(array $recommendation, string $marketCode, string $activeForeverId = ''): ?string
    {
        $settings = (new SettingsRepository($this->config))->getReferralSettings();
        if (!(bool) ($settings['fcc_discount_enabled'] ?? true)) {
            return null;
        }

        $baseUrl = (new ReferralUrlBuilder($this->config))->buildProductDestination($recommendation, $marketCode, $activeForeverId);
        if ($baseUrl === null || !$this->isForeverLivingShopUrl($baseUrl)) {
            return null;
        }

        $discountConfigType = trim((string) ($settings['fcc_discount_config_type'] ?? '11'));
        $uniqueExtRefId = trim((string) ($settings['fcc_unique_ext_ref_id'] ?? ''));
        $referralUuid = trim((string) ($settings['fcc_referral_uuid'] ?? ''));
        $shortenUrl = trim((string) ($settings['fcc_shorten_url'] ?? ''));

        if ($shortenUrl === '') {
            $shortUrl = trim((string) ($settings['fcc_short_url'] ?? ''));
            $host = parse_url($shortUrl, PHP_URL_HOST);
            $path = parse_url($shortUrl, PHP_URL_PATH);
            if (is_string($host) && $host !== '') {
                $shortenUrl = $host . (is_string($path) ? $path : '');
            }
        }

        if ($discountConfigType === '' || $uniqueExtRefId === '' || $referralUuid === '' || $shortenUrl === '') {
            return null;
        }

        return $this->appendQueryParameters($baseUrl, [
            'discountConfigType' => $discountConfigType,
            'uniqueExtRefID' => $uniqueExtRefId,
            'shortenUrl' => $shortenUrl,
            'title' => trim((string) ($settings['fcc_title'] ?? 'FCC')) ?: 'FCC',
            'referralUuid' => $referralUuid,
        ]);
    }

    private function appendQueryParameters(string $url, array $parameters): string
    {
        $parsedUrl = parse_url($url);
        if (!is_array($parsedUrl) || empty($parsedUrl['host'])) {
            return $url;
        }

        $query = [];
        if (!empty($parsedUrl['query'])) {
            parse_str((string) $parsedUrl['query'], $query);
        }

        foreach ($parameters as $key => $value) {
            $query[$key] = $value;
        }

        $rebuilt = ($parsedUrl['scheme'] ?? 'https') . '://' . $parsedUrl['host'];
        if (!empty($parsedUrl['port'])) {
            $rebuilt .= ':' . $parsedUrl['port'];
        }

        $rebuilt .= $parsedUrl['path'] ?? '';

        if ($query !== []) {
            $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
            if (isset($query['shortenUrl'])) {
                $encodedShortenUrl = rawurlencode((string) $query['shortenUrl']);
                $queryString = str_replace('shortenUrl=' . $encodedShortenUrl, 'shortenUrl=' . str_replace('%2F', '/', $encodedShortenUrl), $queryString);
            }

            $rebuilt .= '?' . $queryString;
        }

        if (!empty($parsedUrl['fragment'])) {
            $rebuilt .= '#' . $parsedUrl['fragment'];
        }

        return $rebuilt;
    }

    private function isForeverLivingShopUrl(string $url): bool
    {
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?: ''));
        $path = strtolower((string) (parse_url($url, PHP_URL_PATH) ?: ''));

        return ($host === 'foreverliving.com' || $host === 'www.foreverliving.com' || str_ends_with($host, '.foreverliving.com'))
            && str_starts_with($path, '/shop/');
    }
}
