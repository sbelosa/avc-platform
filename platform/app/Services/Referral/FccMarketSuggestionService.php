<?php

declare(strict_types=1);

namespace Avc\Services\Referral;

final class FccMarketSuggestionService
{
    private const EXPORT_PATH = '/exports/fcc/product_market_links.tsv';

    public function __construct(private array $config)
    {
    }

    public function suggestForGroup(array $group, int $limit = 5): array
    {
        $records = $this->loadRecords();
        if ($records === []) {
            return [];
        }

        $translations = (array) ($group['translations'] ?? []);
        $suggestions = [];

        foreach ($records as $record) {
            [$score, $reasons] = $this->scoreRecord($record, $translations);
            if ($score < 18) {
                continue;
            }

            $suggestions[] = [
                'record_key' => (string) ($record['record_key'] ?? ''),
                'slug' => (string) ($record['slug'] ?? ''),
                'language_code' => (string) ($record['language_code'] ?? ''),
                'title' => (string) ($record['title'] ?? ''),
                'sku' => (string) ($record['sku'] ?? ''),
                'market_urls' => (array) ($record['market_urls'] ?? []),
                'market_count' => count((array) ($record['market_urls'] ?? [])),
                'score' => $score,
                'reasons' => array_values(array_unique($reasons)),
            ];
        }

        usort($suggestions, static function (array $left, array $right): int {
            $scoreComparison = (int) ($right['score'] ?? 0) <=> (int) ($left['score'] ?? 0);
            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            return strcmp((string) ($left['title'] ?? ''), (string) ($right['title'] ?? ''));
        });

        $topScore = (int) ($suggestions[0]['score'] ?? 0);
        $threshold = max(18, $topScore - 20);
        $suggestions = array_values(array_filter($suggestions, static fn (array $suggestion): bool => (int) ($suggestion['score'] ?? 0) >= $threshold));

        return array_slice($suggestions, 0, max(1, $limit));
    }

    public function findRecordByKey(string $recordKey): ?array
    {
        $recordKey = trim($recordKey);
        if ($recordKey === '') {
            return null;
        }

        foreach ($this->loadRecords() as $record) {
            if ((string) ($record['record_key'] ?? '') === $recordKey) {
                return $record;
            }
        }

        return null;
    }

    private function loadRecords(): array
    {
        static $cache = null;

        if (is_array($cache)) {
            return $cache;
        }

        $path = rtrim((string) ($this->config['app_root'] ?? '/var/www/html'), '/') . self::EXPORT_PATH;
        if (!is_file($path)) {
            $cache = [];

            return $cache;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            $cache = [];

            return $cache;
        }

        $records = [];

        while (($line = fgets($handle)) !== false) {
            $line = rtrim($line, "\r\n");
            if ($line === '') {
                continue;
            }

            $parts = explode("\t", $line, 5);
            if (count($parts) < 5) {
                continue;
            }

            [$slug, $languageLabel, $title, $sku, $webshopLinksBase64] = $parts;
            $decodedJson = base64_decode($webshopLinksBase64, true);
            if ($decodedJson === false) {
                continue;
            }

            $marketUrls = json_decode($decodedJson, true);
            if (!is_array($marketUrls) || $marketUrls === []) {
                continue;
            }

            $normalizedMarketUrls = [];

            foreach ($marketUrls as $marketCode => $marketUrl) {
                $marketCode = strtolower(trim((string) $marketCode));
                $marketUrl = trim((string) $marketUrl);

                if ($marketCode === '' || $marketUrl === '' || !filter_var($marketUrl, FILTER_VALIDATE_URL)) {
                    continue;
                }

                $normalizedMarketUrls[$marketCode] = $marketUrl;
            }

            if ($normalizedMarketUrls === []) {
                continue;
            }

            $languageCode = $this->mapLanguage((string) $languageLabel);
            $records[] = [
                'record_key' => trim((string) $slug) . '|' . $languageCode,
                'slug' => trim((string) $slug),
                'slug_tokens' => $this->tokenizeSlug((string) $slug),
                'title' => trim((string) $title),
                'title_tokens' => $this->tokenizeText((string) $title),
                'language_code' => $languageCode,
                'sku' => trim((string) $sku),
                'market_urls' => $normalizedMarketUrls,
            ];
        }

        fclose($handle);
        $cache = $records;

        return $cache;
    }

    private function scoreRecord(array $record, array $translations): array
    {
        $bestScore = 0;
        $bestReasons = [];

        foreach ($translations as $translation) {
            $score = 0;
            $reasons = [];

            $translationSlug = (string) ($translation['slug'] ?? '');
            $translationTitle = (string) ($translation['title'] ?? '');
            $translationLanguage = strtolower(trim((string) ($translation['language_code'] ?? '')));
            $slugTokens = $this->tokenizeSlug($translationSlug);
            $titleTokens = $this->tokenizeText($translationTitle);

            if ($translationLanguage !== '' && $translationLanguage === (string) ($record['language_code'] ?? '')) {
                $score += 4;
                $reasons[] = 'isti jezik';
            }

            $slugOverlap = $this->tokenOverlapScore($slugTokens, (array) ($record['slug_tokens'] ?? []));
            if ($slugOverlap > 0) {
                $score += $slugOverlap;
                $reasons[] = 'slug sličnost';
            }

            $titleOverlap = $this->tokenOverlapScore($titleTokens, (array) ($record['title_tokens'] ?? []));
            if ($titleOverlap > 0) {
                $score += $titleOverlap;
                $reasons[] = 'title sličnost';
            }

            $translatedRecordTitle = $this->tokenizeText((string) ($record['title'] ?? ''));
            if ($this->isSubset($translatedRecordTitle, $titleTokens) || $this->isSubset($translatedRecordTitle, $slugTokens)) {
                $score += 10;
                $reasons[] = 'FCC naziv je podskup AVC naziva';
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestReasons = $reasons;
            }
        }

        return [$bestScore, $bestReasons];
    }

    private function tokenOverlapScore(array $leftTokens, array $rightTokens): int
    {
        if ($leftTokens === [] || $rightTokens === []) {
            return 0;
        }

        $intersection = array_values(array_intersect($leftTokens, $rightTokens));
        if ($intersection === []) {
            return 0;
        }

        $shared = count($intersection);
        $maxCount = max(count($leftTokens), count($rightTokens));
        $minCount = min(count($leftTokens), count($rightTokens));
        $score = ($shared * 10);

        if ($shared === $minCount) {
            $score += 8;
        }

        if ($shared >= 2) {
            $score += 4;
        }

        if ($maxCount <= 3 && $shared >= 1) {
            $score += 2;
        }

        return $score;
    }

    private function isSubset(array $needleTokens, array $haystackTokens): bool
    {
        if ($needleTokens === [] || $haystackTokens === []) {
            return false;
        }

        return count(array_diff($needleTokens, $haystackTokens)) === 0;
    }

    private function tokenizeSlug(string $value): array
    {
        $normalized = $this->normalizeText($value);
        $normalized = str_replace('-', ' ', $normalized);

        return $this->tokenizeNormalized($normalized);
    }

    private function tokenizeText(string $value): array
    {
        return $this->tokenizeNormalized($this->normalizeText($value));
    }

    private function tokenizeNormalized(string $normalized): array
    {
        $parts = preg_split('/[^a-z0-9]+/', $normalized) ?: [];
        $stopTokens = [
            'a', 'and', 'aminotein', 'c', 'chocolate', 'copy', 'dcl', 'deodorant', 'flp', 'gel',
            'hr', 'en', 'sl', 'kopiraj', 'living', 'mini', 'ml', 'omega', 'pack', 'products', 'proizvodi', 'spray',
            'the', 'toothgel', 'ultra', 'vanilla', 'vera', 'vitamin', 'with',
        ];
        $tokens = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '' || strlen($part) <= 1 || in_array($part, $stopTokens, true)) {
                continue;
            }

            if (ctype_digit($part)) {
                continue;
            }

            $tokens[] = $part;
        }

        return array_values(array_unique($tokens));
    }

    private function normalizeText(string $value): string
    {
        $value = html_entity_decode(mb_strtolower(trim($value)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (function_exists('iconv')) {
            $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($transliterated !== false && $transliterated !== '') {
                $value = $transliterated;
            }
        }

        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }

    private function mapLanguage(string $languageLabel): string
    {
        $normalized = strtolower(trim($languageLabel));

        return match ($normalized) {
            'english' => 'en',
            'slovenian', 'slovenski', 'slovenščina' => 'sl',
            default => 'hr',
        };
    }
}
