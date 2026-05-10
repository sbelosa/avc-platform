<?php

declare(strict_types=1);

namespace Avc\Services\Geo;

use Avc\Core\Request;

final class CountryDetector
{
    private const TRUSTED_HEADERS = [
        'CF-IPCountry',
        'X-Country-Code',
        'GEOIP_COUNTRY_CODE',
        'GEOIP-COUNTRY-CODE',
    ];

    public function detect(Request $request): ?string
    {
        foreach (self::TRUSTED_HEADERS as $header) {
            $value = strtoupper(substr(trim((string) $request->header($header, '')), 0, 2));

            if ($value !== '' && $value !== 'XX' && strlen($value) === 2) {
                return $value;
            }
        }

        $queryOverride = strtoupper(substr(trim((string) $request->input('country', '')), 0, 2));

        if ($queryOverride !== '' && strlen($queryOverride) === 2) {
            return $queryOverride;
        }

        return null;
    }
}
