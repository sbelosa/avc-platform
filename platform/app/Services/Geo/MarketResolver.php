<?php

declare(strict_types=1);

namespace Avc\Services\Geo;

final class MarketResolver
{
    private const COUNTRY_MARKET_MAP = [
        'HR' => 'hr',
        'SI' => 'si',
        'RS' => 'rs',
        'BA' => 'ba',
        'AL' => 'al',
        'ME' => 'me',
        'XK' => 'al',
        'GB' => 'gb',
        'US' => 'us',
        'DE' => 'de',
        'AT' => 'at',
        'CH' => 'ch',
    ];

    private const ACCEPT_LANGUAGE_MARKET_MAP = [
        'hr' => 'hr',
        'sl' => 'si',
        'sr' => 'rs',
        'bs' => 'ba',
        'sq' => 'al',
        'de' => 'de',
        'en' => 'gb',
    ];

    public function resolveFromCountryCode(?string $countryCode): string
    {
        $normalizedCountryCode = strtoupper(trim((string) $countryCode));

        if ($normalizedCountryCode === '') {
            return 'hr';
        }

        return self::COUNTRY_MARKET_MAP[$normalizedCountryCode] ?? 'gb';
    }

    public function resolvePreferredMarket(?string $countryCode, ?string $acceptLanguageHeader = null, array $availableMarketCodes = []): string
    {
        $available = array_values(array_unique(array_filter(array_map(static fn(string $value): string => strtolower(trim($value)), $availableMarketCodes))));

        $countryMarket = $this->resolveFromCountryCode($countryCode);
        if ($countryMarket !== '' && ($available === [] || in_array($countryMarket, $available, true))) {
            return $countryMarket;
        }

        foreach ($this->extractAcceptLanguageCandidates($acceptLanguageHeader) as $candidate) {
            if ($available === [] || in_array($candidate, $available, true)) {
                return $candidate;
            }
        }

        if ($available !== []) {
            foreach (['hr', 'si', 'gb', 'us'] as $fallbackMarket) {
                if (in_array($fallbackMarket, $available, true)) {
                    return $fallbackMarket;
                }
            }

            return $available[0];
        }

        return $countryMarket !== '' ? $countryMarket : 'gb';
    }

    public function extractAcceptLanguageCandidates(?string $acceptLanguageHeader): array
    {
        if ($acceptLanguageHeader === null || trim($acceptLanguageHeader) === '') {
            return [];
        }

        preg_match_all('/([a-z]{2,3})(?:-([a-z]{2}))?/i', $acceptLanguageHeader, $matches, PREG_SET_ORDER);

        $candidates = [];

        foreach ($matches as $match) {
            $languageCode = strtolower((string) ($match[1] ?? ''));
            $regionCode = strtoupper((string) ($match[2] ?? ''));

            if ($regionCode !== '' && isset(self::COUNTRY_MARKET_MAP[$regionCode])) {
                $candidates[] = self::COUNTRY_MARKET_MAP[$regionCode];
            }

            if (isset(self::ACCEPT_LANGUAGE_MARKET_MAP[$languageCode])) {
                $candidates[] = self::ACCEPT_LANGUAGE_MARKET_MAP[$languageCode];
            }
        }

        return array_values(array_unique($candidates));
    }
}
