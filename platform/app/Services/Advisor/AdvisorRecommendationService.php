<?php

declare(strict_types=1);

namespace Avc\Services\Advisor;

use Avc\Repositories\ContentRepository;

final class AdvisorRecommendationService
{
    public function __construct(private array $config)
    {
    }

    public function recommend(string $question, string $languageCode, string $sourcePath = '/', array $context = []): array
    {
        $languageCode = strtolower(trim($languageCode)) ?: 'hr';
        $sourcePath = trim($sourcePath) !== '' ? trim($sourcePath) : '/';
        $candidates = (new ContentRepository($this->config))->findAdvisorCandidates($languageCode);

        if ($candidates === [] && $languageCode !== ($this->config['default_language'] ?? 'hr')) {
            $candidates = (new ContentRepository($this->config))->findAdvisorCandidates((string) ($this->config['default_language'] ?? 'hr'));
        }

        $terms = $this->extractTerms($question, $languageCode);
        $priorityProducts = $this->filterStrings((array) ($context['priority_products'] ?? []));
        $preferredPatterns = $this->filterStrings((array) ($context['preferred_patterns'] ?? []));
        $themePatterns = $this->filterStrings((array) ($context['theme_patterns'] ?? []));
        $lockProductScope = (bool) ($context['lock_product_scope'] ?? false);
        $suppressArticles = (bool) ($context['suppress_articles'] ?? false);
        $requireArticleTermOverlap = (bool) ($context['require_article_term_overlap'] ?? false);
        $limitProducts = max(1, min((int) ($context['limit_products'] ?? 3), 3));
        $limitArticles = max(0, min((int) ($context['limit_articles'] ?? 3), 3));
        $productScoreFloor = max(0, (int) ($context['product_score_floor'] ?? 1));
        $articleScoreFloor = max(0, (int) ($context['article_score_floor'] ?? 1));
        $scored = [];

        foreach ($candidates as $candidate) {
            if ((string) ($candidate['route_path'] ?? '') === $sourcePath) {
                continue;
            }

            $contentType = (string) ($candidate['content_type'] ?? '');
            $termOverlap = $this->scoreTermOverlap($candidate, $terms);
            $priorityMatchScore = $this->bestPriorityProductMatch($candidate, $priorityProducts);
            $score = $this->scoreCandidate($candidate, $terms, $priorityProducts, $preferredPatterns, $themePatterns);

            if ($contentType === 'article' && $termOverlap > 0) {
                $score += $termOverlap * 6;
            }

            if ($lockProductScope && $contentType === 'product_guide' && $priorityProducts !== [] && $priorityMatchScore <= 0) {
                continue;
            }

            if ($suppressArticles && $contentType === 'article') {
                continue;
            }

            if ($requireArticleTermOverlap && $contentType === 'article' && $terms !== [] && $termOverlap <= 0) {
                continue;
            }

            if ($score <= 0 && $terms !== []) {
                continue;
            }

            $candidate['score'] = $score;
            $candidate['term_overlap_score'] = $termOverlap;
            $candidate['priority_match_score'] = $priorityMatchScore;
            $candidate['summary'] = $this->buildSummary($candidate);
            $candidate['match_reason'] = $this->buildMatchReason($candidate, $terms, $languageCode, $priorityMatchScore, $termOverlap);

            if ($contentType === 'product_guide') {
                $candidate['shop_url'] = $this->buildAdvisorShopUrl($candidate, $languageCode, $sourcePath);
            }

            $scored[] = $candidate;
        }

        if ($scored === [] && !$lockProductScope && !$suppressArticles) {
            foreach ($candidates as $candidate) {
                if ((string) ($candidate['route_path'] ?? '') === $sourcePath) {
                    continue;
                }

                $candidate['score'] = 1;
                $candidate['term_overlap_score'] = 0;
                $candidate['priority_match_score'] = 0;
                $candidate['summary'] = $this->buildSummary($candidate);
                $candidate['match_reason'] = $this->buildMatchReason($candidate, $terms, $languageCode, 0, 0);

                if ((string) ($candidate['content_type'] ?? '') === 'product_guide') {
                    $candidate['shop_url'] = $this->buildAdvisorShopUrl($candidate, $languageCode, $sourcePath);
                }

                $scored[] = $candidate;
            }
        }

        usort($scored, static function (array $left, array $right): int {
            $scoreComparison = (int) ($right['score'] ?? 0) <=> (int) ($left['score'] ?? 0);
            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            return strcmp((string) ($left['title'] ?? ''), (string) ($right['title'] ?? ''));
        });

        $products = $this->selectProducts($scored, $priorityProducts, $lockProductScope, $limitProducts, $productScoreFloor);
        $articles = $this->selectArticles($scored, $limitArticles, $articleScoreFloor);
        $topRoutePath = $products[0]['route_path'] ?? $articles[0]['route_path'] ?? $sourcePath;

        return [
            'products' => $products,
            'articles' => $articles,
            'top_route_path' => $topRoutePath,
        ];
    }

    private function selectProducts(
        array $scored,
        array $priorityProducts,
        bool $lockProductScope,
        int $limitProducts,
        int $productScoreFloor
    ): array {
        if ($limitProducts <= 0) {
            return [];
        }

        if ($lockProductScope && $priorityProducts !== []) {
            $selected = [];
            $usedIndexes = [];

            foreach ($priorityProducts as $priorityProduct) {
                $bestIndex = null;
                $bestScore = 0;

                foreach ($scored as $index => $candidate) {
                    if (isset($usedIndexes[$index]) || (string) ($candidate['content_type'] ?? '') !== 'product_guide') {
                        continue;
                    }

                    $candidateScore = (int) ($candidate['score'] ?? 0);
                    if ($candidateScore < $productScoreFloor) {
                        continue;
                    }

                    $matchScore = $this->scoreProductReferenceMatch($candidate, $priorityProduct);
                    if ($matchScore <= 0) {
                        continue;
                    }

                    $rank = ($matchScore * 1000) + $candidateScore;
                    if ($rank > $bestScore) {
                        $bestScore = $rank;
                        $bestIndex = $index;
                    }
                }

                if ($bestIndex === null) {
                    continue;
                }

                $usedIndexes[$bestIndex] = true;
                $selected[] = $this->mapRecommendation($scored[$bestIndex]);

                if (count($selected) >= $limitProducts) {
                    break;
                }
            }

            return $selected;
        }

        $products = [];
        foreach ($scored as $candidate) {
            if ((string) ($candidate['content_type'] ?? '') !== 'product_guide') {
                continue;
            }

            if ((int) ($candidate['score'] ?? 0) < $productScoreFloor) {
                continue;
            }

            $products[] = $this->mapRecommendation($candidate);
            if (count($products) >= $limitProducts) {
                break;
            }
        }

        return $products;
    }

    private function selectArticles(array $scored, int $limitArticles, int $articleScoreFloor): array
    {
        if ($limitArticles <= 0) {
            return [];
        }

        $articles = [];

        foreach ($scored as $candidate) {
            if ((string) ($candidate['content_type'] ?? '') !== 'article') {
                continue;
            }

            if ((int) ($candidate['score'] ?? 0) < $articleScoreFloor) {
                continue;
            }

            $articles[] = $this->mapRecommendation($candidate);
            if (count($articles) >= $limitArticles) {
                break;
            }
        }

        return $articles;
    }

    private function mapRecommendation(array $candidate): array
    {
        return [
            'content_type' => (string) ($candidate['content_type'] ?? ''),
            'title' => (string) ($candidate['title'] ?? ''),
            'route_path' => (string) ($candidate['route_path'] ?? '/'),
            'summary' => (string) ($candidate['summary'] ?? ''),
            'match_reason' => (string) ($candidate['match_reason'] ?? ''),
            'featured_image_path' => (string) ($candidate['featured_image_path'] ?? ''),
            'shop_url' => (string) ($candidate['shop_url'] ?? ''),
        ];
    }

    private function buildAdvisorShopUrl(array $candidate, string $languageCode, string $sourcePath): string
    {
        $label = match ($languageCode) {
            'en' => 'Continue to Forever shop',
            'sl' => 'Nadaljuj v Forever shop',
            default => 'Nastavi na Forever shop',
        };
        $params = [
            'id' => (int) ($candidate['content_translation_id'] ?? 0),
            'lang' => strtolower(trim((string) ($candidate['language_code'] ?? $languageCode))) ?: 'hr',
            'source_path' => trim($sourcePath) !== '' ? trim($sourcePath) : '/',
            'source' => 'advisor_shop',
            'cta_position' => 'advisor_shop',
            'cta_variant' => $this->normalizeTrackingKey($label, 'advisor_shop', 80),
            'cta_label' => $label,
        ];

        return '/go/product?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
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

    private function buildMatchReason(array $candidate, array $terms, string $languageCode, int $priorityMatchScore, int $termOverlap): string
    {
        $contentType = (string) ($candidate['content_type'] ?? '');
        $haystack = $this->normalizeText(implode(' ', array_filter([
            (string) ($candidate['title'] ?? ''),
            (string) ($candidate['slug'] ?? ''),
            (string) ($candidate['route_path'] ?? ''),
            $this->buildSummary($candidate),
        ])));

        if ($priorityMatchScore > 0) {
            return match ($languageCode) {
                'en' => 'Placed first because you mentioned it or it clearly fits the goal you described.',
                'sl' => 'Postavljeno višje, ker si ga omenil ali se jasno ujema s ciljem, ki si ga opisal.',
                default => 'Stavljeno visoko jer si ga spomenuo ili se jasno veže uz cilj koji si opisao.',
            };
        }

        if ($contentType === 'product_guide') {
            $knownReason = $this->knownProductReason($haystack, $languageCode);
            if ($knownReason !== '') {
                return $knownReason;
            }
        }

        if ($termOverlap > 0) {
            return match ($languageCode) {
                'en' => 'Added because it fits the topic you described.',
                'sl' => 'Dodano, ker se dobro ujema s temo, ki si jo opisal.',
                default => 'Dodano jer se dobro uklapa u temu koju si opisao.',
            };
        }

        if ($contentType === 'product_guide') {
            if (str_contains($haystack, 'garcinia')) {
                return match ($languageCode) {
                    'en' => 'Closest to appetite, portions and weight-management support.',
                    'sl' => 'Najbližje temi apetita, porcij in uravnavanja telesne teže.',
                    default => 'Najbliže temi apetita, porcija i regulacije tjelesne težine.',
                };
            }

            if (str_contains($haystack, 'aloe vera gel')) {
                return match ($languageCode) {
                    'en' => 'A common starting point for a daily aloe routine and digestion support.',
                    'sl' => 'Pogost začetek za vsakodnevno aloe rutino in podporo prebavi.',
                    default => 'Čest početak za svakodnevnu aloe rutinu i podršku probavi.',
                };
            }

            if (str_contains($haystack, 'collagen') || str_contains($haystack, 'kolagen')) {
                return match ($languageCode) {
                    'en' => 'Useful when the goal is skin, hair, nails or joint-support habits.',
                    'sl' => 'Koristno, kadar je cilj koža, lasje, nohti ali podpora sklepom.',
                    default => 'Ima smisla kada je cilj koža, kosa, nokti ili podrška zglobovima.',
                };
            }
        }

        return match ($languageCode) {
            'en' => $terms !== [] ? 'Selected as a useful next comparison.' : 'A useful starting point if you are still exploring.',
            'sl' => $terms !== [] ? 'Izbrano kot koristna naslednja primerjava.' : 'Koristna začetna točka, če še raziskuješ.',
            default => $terms !== [] ? 'Odabrano kao korisna sljedeća usporedba.' : 'Dobar početak ako još istražuješ opcije.',
        };
    }

    private function scoreCandidate(array $candidate, array $terms, array $priorityProducts = [], array $preferredPatterns = [], array $themePatterns = []): int
    {
        if ($terms === []) {
            $baseScore = (string) ($candidate['content_type'] ?? '') === 'product_guide' ? 3 : 2;
            $baseScore += $this->scorePreferredPatterns($candidate, $preferredPatterns, 18);
            $baseScore += $this->scorePreferredPatterns($candidate, $themePatterns, 12);
            $baseScore += $this->scorePriorityProducts($candidate, $priorityProducts);

            return $baseScore;
        }

        $title = $this->normalizeText((string) ($candidate['title'] ?? ''));
        $summary = $this->normalizeText($this->buildSummary($candidate));
        $routePath = $this->normalizeText((string) ($candidate['route_path'] ?? ''));
        $slug = $this->normalizeText((string) ($candidate['slug'] ?? ''));
        $sku = $this->normalizeText((string) ($candidate['sku'] ?? ''));
        $buttonLabel = $this->normalizeText((string) ($candidate['button_label'] ?? ''));
        $score = 0;

        foreach ($terms as $term) {
            foreach ($this->termVariants($term) as $variant) {
                if (str_contains($title, $variant)) {
                    $score += 8;
                }

                if (str_contains($summary, $variant)) {
                    $score += 4;
                }

                if (str_contains($routePath, $variant)) {
                    $score += 3;
                }

                if ($slug !== '' && str_contains($slug, $variant)) {
                    $score += 4;
                }

                if ($sku !== '' && str_contains($sku, $variant)) {
                    $score += 12;
                }

                if ($buttonLabel !== '' && str_contains($buttonLabel, $variant)) {
                    $score += 2;
                }
            }
        }

        if ($score > 0 && (string) ($candidate['content_type'] ?? '') === 'product_guide') {
            $score += 2;
        }

        $score += $this->scorePriorityProducts($candidate, $priorityProducts);
        $score += $this->scorePreferredPatterns($candidate, $preferredPatterns, 20);
        $score += $this->scorePreferredPatterns($candidate, $themePatterns, 14);

        return $score;
    }

    private function scorePriorityProducts(array $candidate, array $priorityProducts): int
    {
        if ($priorityProducts === []) {
            return 0;
        }

        $bestScore = 0;

        foreach ($priorityProducts as $productTitle) {
            $bestScore = max($bestScore, $this->scoreProductReferenceMatch($candidate, $productTitle));
        }

        return $bestScore;
    }

    private function bestPriorityProductMatch(array $candidate, array $priorityProducts): int
    {
        return $this->scorePriorityProducts($candidate, $priorityProducts);
    }

    private function scoreProductReferenceMatch(array $candidate, string $reference): int
    {
        $referenceTokens = $this->productReferenceTokens($reference);
        if ($referenceTokens === []) {
            return 0;
        }

        $titleTokens = $this->productReferenceTokens((string) ($candidate['title'] ?? ''));
        $slugTokens = $this->productReferenceTokens((string) ($candidate['slug'] ?? ''));
        $buttonTokens = $this->productReferenceTokens((string) ($candidate['button_label'] ?? ''));
        $summaryTokens = $this->productReferenceTokens($this->buildSummary($candidate));
        $referencePhrase = implode(' ', $referenceTokens);
        $titlePhrase = implode(' ', $titleTokens);
        $slugPhrase = implode(' ', $slugTokens);
        $buttonPhrase = implode(' ', $buttonTokens);
        $summaryPhrase = implode(' ', $summaryTokens);

        if ($this->candidateContainsAllReferenceTokens($titleTokens, $referenceTokens)) {
            if ($titlePhrase === $referencePhrase) {
                return 120;
            }

            return 100;
        }

        if ($this->candidateContainsAllReferenceTokens($slugTokens, $referenceTokens)) {
            if ($slugPhrase === $referencePhrase) {
                return 96;
            }

            return 90;
        }

        if ($this->candidateContainsAllReferenceTokens($buttonTokens, $referenceTokens)) {
            return 72;
        }

        if ($this->candidateContainsAllReferenceTokens($summaryTokens, $referenceTokens)) {
            return 42;
        }

        return 0;
    }

    private function candidateContainsAllReferenceTokens(array $candidateTokens, array $referenceTokens): bool
    {
        if ($candidateTokens === [] || $referenceTokens === []) {
            return false;
        }

        $candidateLookup = array_fill_keys($candidateTokens, true);
        foreach ($referenceTokens as $referenceToken) {
            if (!isset($candidateLookup[$referenceToken])) {
                return false;
            }
        }

        return true;
    }

    private function productReferenceTokens(string $value): array
    {
        $value = $this->normalizeText($value);
        $value = (string) preg_replace('/[^a-z0-9]+/u', ' ', $value);
        $parts = preg_split('/\s+/u', trim($value)) ?: [];
        $stopTokens = [
            'forever', 'living', 'products', 'product', 'tm', 'ml', 'l', 'lt', 'dcl', 'kom', 'pack', 'mini',
            'plus', 'the', 'and', 'for', 'with', 'our', 'your', 'formula', 'natural', 'prirodna', 'formula',
            'tableta', 'tablete', 'caps', 'capsule', 'kapsule', 'kutija', 'box',
        ];
        $tokens = [];

        foreach ($parts as $part) {
            if ($part === '' || strlen($part) <= 1 || is_numeric($part) || in_array($part, $stopTokens, true)) {
                continue;
            }

            $tokens[$part] = true;
        }

        return array_keys($tokens);
    }

    private function scoreTermOverlap(array $candidate, array $terms): int
    {
        if ($terms === []) {
            return 0;
        }

        $haystack = $this->normalizeText(implode(' ', array_filter([
            (string) ($candidate['title'] ?? ''),
            (string) ($candidate['slug'] ?? ''),
            (string) ($candidate['route_path'] ?? ''),
            $this->buildSummary($candidate),
        ])));
        $score = 0;

        foreach ($terms as $term) {
            foreach ($this->termVariants($term) as $variant) {
                if ($variant !== '' && str_contains($haystack, $variant)) {
                    $score++;
                    break;
                }
            }
        }

        return $score;
    }

    private function scorePreferredPatterns(array $candidate, array $patterns, int $baseWeight): int
    {
        if ($patterns === []) {
            return 0;
        }

        $haystack = $this->normalizeText(implode(' ', array_filter([
            (string) ($candidate['title'] ?? ''),
            (string) ($candidate['slug'] ?? ''),
            (string) ($candidate['sku'] ?? ''),
            (string) ($candidate['route_path'] ?? ''),
            $this->buildSummary($candidate),
        ])));
        $score = 0;

        foreach ($patterns as $pattern) {
            $normalizedPattern = $this->normalizeText($pattern);
            if ($normalizedPattern === '' || !str_contains($haystack, $normalizedPattern)) {
                continue;
            }

            $score += $baseWeight;
        }

        return $score;
    }

    private function buildSummary(array $candidate): string
    {
        $summary = trim((string) ($candidate['meta_description'] ?? ''));

        if ($summary === '') {
            $summary = trim(strip_tags((string) ($candidate['excerpt'] ?? '')));
        }

        if ($summary === '') {
            $summary = trim((string) ($candidate['title'] ?? ''));
        }

        $summary = $this->stripPromotionalSummaryFragments($summary);
        $humanSummary = $this->humanProductSummary($candidate, $summary);
        if ($humanSummary !== '') {
            $summary = $humanSummary;
        }

        if ($summary === '') {
            $summary = trim((string) ($candidate['title'] ?? ''));
        }

        return mb_strimwidth($summary, 0, 180, '…');
    }

    private function humanProductSummary(array $candidate, string $fallback): string
    {
        if ((string) ($candidate['content_type'] ?? '') !== 'product_guide') {
            return $fallback;
        }

        $languageCode = strtolower(trim((string) ($candidate['language_code'] ?? 'hr'))) ?: 'hr';
        $title = trim((string) ($candidate['title'] ?? ''));
        $haystack = $this->normalizeText(implode(' ', array_filter([
            $title,
            (string) ($candidate['slug'] ?? ''),
            (string) ($candidate['route_path'] ?? ''),
            $fallback,
        ])));
        $knownSummary = $this->knownProductSummary($haystack, $languageCode, $title);
        if ($knownSummary !== '') {
            return $knownSummary;
        }

        if (str_contains($haystack, 'c9') || str_contains($haystack, 'clean 9')) {
            return match ($languageCode) {
                'en' => 'A structured starter program when you want a clearer plan for the first days of your routine.',
                'sl' => 'Strukturiran začetni program, ko želiš jasnejši načrt za prve dni rutine.',
                default => 'Strukturirani program za početak kada želiš jasniji plan prvih dana rutine.',
            };
        }

        if (str_contains($haystack, 'garcinia')) {
            return match ($languageCode) {
                'en' => 'Support for a weight-management routine when appetite, portions and consistency are the main topics.',
                'sl' => 'Podpora rutini uravnavanja teže, ko so glavna tema apetit, porcije in doslednost.',
                default => 'Podrška uz rutinu regulacije težine kada su tema apetit, porcije i dosljednost.',
            };
        }

        if (str_contains($haystack, 'therm')) {
            return match ($languageCode) {
                'en' => 'Worth considering when you want energy support inside a weight-management routine.',
                'sl' => 'Smiselno za razmislek, ko želiš podporo energiji znotraj rutine uravnavanja teže.',
                default => 'Može se razmotriti kada želiš podršku za energiju unutar rutine kontrole težine.',
            };
        }

        if (str_contains($haystack, 'lite ultra') || str_contains($haystack, 'protein') || str_contains($haystack, 'aminotein')) {
            return match ($languageCode) {
                'en' => 'A practical protein option when you want a simpler meal or snack in your routine.',
                'sl' => 'Praktična beljakovinska možnost, ko želiš preprostejši obrok ali prigrizek v rutini.',
                default => 'Praktična proteinska podrška kada želiš jednostavniji obrok ili međuobrok.',
            };
        }

        if (str_contains($haystack, 'gelly') || str_contains($haystack, 'aloe first') || str_contains($haystack, 'msm')) {
            return match ($languageCode) {
                'en' => 'A practical outer-care product when the goal is skin comfort and a simpler care routine.',
                'sl' => 'Praktičen izdelek za zunanjo nego, ko je cilj udobje kože in preprostejša rutina nege.',
                default => 'Praktičan proizvod za vanjsku njegu kada je cilj ugodnija koža i jednostavnija rutina.',
            };
        }

        if (str_contains($haystack, 'aloe vera gel')) {
            return match ($languageCode) {
                'en' => 'A daily aloe drink for people who want simple support for digestion and routine.',
                'sl' => 'Vsakodnevni aloe napitek za tiste, ki želijo preprosto podporo prebavi in rutini.',
                default => 'Svakodnevni aloe napitak za korisnike koji žele jednostavnu podršku probavi i rutini.',
            };
        }

        if (str_contains($haystack, 'collagen') || str_contains($haystack, 'kolagen')) {
            return match ($languageCode) {
                'en' => 'A good fit when the goal is a routine for skin, hair, nails or joint-support habits.',
                'sl' => 'Dobra izbira, ko je cilj rutina za kožo, lase, nohte ali podporo sklepom.',
                default => 'Dobar izbor kada je cilj rutina za kožu, kosu, nokte ili podršku zglobovima.',
            };
        }

        if (str_contains($haystack, 'toothgel') || str_contains($haystack, 'zub')) {
            return match ($languageCode) {
                'en' => 'Everyday oral care with aloe and propolis, without complicating the routine.',
                'sl' => 'Vsakodnevna ustna nega z aloe vero in propolisom, brez zapletanja rutine.',
                default => 'Svakodnevna oralna njega s aloe verom i propolisom, bez nepotrebnog kompliciranja.',
            };
        }

        if ($this->isOverheatedMarketingText($fallback) && $title !== '') {
            return match ($languageCode) {
                'en' => 'A product guide for understanding when ' . $title . ' makes sense in a daily routine.',
                'sl' => 'Vodič za razumevanje, kdaj ima ' . $title . ' smisel v dnevni rutini.',
                default => 'Vodič za razumjeti kada ' . $title . ' ima smisla u svakodnevnoj rutini.',
            };
        }

        return $fallback;
    }

    private function knownProductSummary(string $haystack, string $languageCode, string $title = ''): string
    {
        $languageCode = strtolower(trim($languageCode)) ?: 'hr';
        $definitions = [
            [['c9', 'clean 9'], [
                'hr' => 'Strukturirani program za početak kada želiš jasniji plan prvih dana rutine.',
                'en' => 'A structured starter program when you want a clearer plan for the first days of your routine.',
                'sl' => 'Strukturiran začetni program, ko želiš jasnejši načrt za prve dni rutine.',
            ]],
            [['garcinia'], [
                'hr' => 'Podrška uz rutinu regulacije težine kada su tema apetit, porcije i dosljednost.',
                'en' => 'Support for a weight-management routine when appetite, portions and consistency are the main topics.',
                'sl' => 'Podpora rutini uravnavanja teže, ko so glavna tema apetit, porcije in doslednost.',
            ]],
            [['therm'], [
                'hr' => 'Može se razmotriti kada želiš podršku za energiju unutar rutine kontrole težine.',
                'en' => 'Worth considering when you want energy support inside a weight-management routine.',
                'sl' => 'Smiselno za razmislek, ko želiš podporo energiji znotraj rutine uravnavanja teže.',
            ]],
            [['forever lean'], [
                'hr' => 'Dodatak za rutinu kontrole težine kada želiš paziti na obroke, porcije i dosljednost.',
                'en' => 'A supplement for a weight-management routine focused on meals, portions and consistency.',
                'sl' => 'Dodatek za rutino uravnavanja teže, ko želiš paziti na obroke, porcije in doslednost.',
            ]],
            [['lite ultra', 'protein', 'aminotein'], [
                'hr' => 'Praktična proteinska podrška kada želiš jednostavniji obrok ili međuobrok.',
                'en' => 'A practical protein option when you want a simpler meal or snack in your routine.',
                'sl' => 'Praktična beljakovinska možnost, ko želiš preprostejši obrok ali prigrizek v rutini.',
            ]],
            [['fiber', 'vlakna'], [
                'hr' => 'Jednostavan dodatak vlakana kada želiš podržati probavu i osjećaj sitosti kroz dan.',
                'en' => 'A simple fiber add-on when you want digestion and satiety support during the day.',
                'sl' => 'Preprost dodatek vlaknin, ko želiš podpreti prebavo in občutek sitosti čez dan.',
            ]],
            [['aloe berry', 'berry nectar'], [
                'hr' => 'Aloe napitak s brusnicom i jabukom za one kojima više odgovara voćniji okus.',
                'en' => 'An aloe drink with cranberry and apple for people who prefer a fruitier taste.',
                'sl' => 'Aloe napitek z brusnico in jabolkom za tiste, ki jim bolj ustreza sadnejši okus.',
            ]],
            [['aloe peaches', 'peaches 330'], [
                'hr' => 'Aloe napitak s okusom breskve kada želiš blaži, voćniji ulaz u aloe rutinu.',
                'en' => 'An aloe drink with peach flavor when you want a milder, fruitier aloe routine.',
                'sl' => 'Aloe napitek z okusom breskve, ko želiš blažjo, sadnejšo aloe rutino.',
            ]],
            [['aloe activator'], [
                'hr' => 'Višenamjenska tekućina s aloe verom za čišćenje, osvježenje ili pripremu kože za njegu.',
                'en' => 'A multipurpose aloe liquid for cleansing, refreshing or preparing skin for care.',
                'sl' => 'Večnamenska tekočina z aloe vero za čiščenje, osvežitev ali pripravo kože na nego.',
            ]],
            [['aloe vera gelly', 'gelly', 'aloe first', 'aloe msm gel'], [
                'hr' => 'Praktičan proizvod za vanjsku njegu kada je cilj ugodnija koža i jednostavnija rutina.',
                'en' => 'A practical outer-care product when the goal is skin comfort and a simpler care routine.',
                'sl' => 'Praktičen izdelek za zunanjo nego, ko je cilj udobje kože in preprostejša rutina nege.',
            ]],
            [['aloe vera gel'], [
                'hr' => 'Svakodnevni aloe napitak za korisnike koji žele jednostavnu podršku probavi i rutini.',
                'en' => 'A daily aloe drink for people who want simple support for digestion and routine.',
                'sl' => 'Vsakodnevni aloe napitek za tiste, ki želijo preprosto podporo prebavi in rutini.',
            ]],
            [['active pro b', 'pro b', 'probiotic', 'probiotik'], [
                'hr' => 'Probiotička podrška kada želiš jednostavnije brinuti o probavi iz dana u dan.',
                'en' => 'Probiotic support when you want an easier daily way to care for digestion.',
                'sl' => 'Probiotična podpora, ko želiš preprosteje skrbeti za prebavo iz dneva v dan.',
            ]],
            [['fields of greens'], [
                'hr' => 'Zelena dnevna podrška kada želiš u rutinu dodati ječam, pšenicu i jednostavan biljni dodatak.',
                'en' => 'Daily greens support when you want barley, wheatgrass and a simple plant add-on in your routine.',
                'sl' => 'Zelena dnevna podpora, ko želiš v rutino dodati ječmen, pšenico in preprost rastlinski dodatek.',
            ]],
            [['aloeturm', 'aloe turm'], [
                'hr' => 'Aloe i kurkuma u praktičnom dodatku kada želiš podršku probavi, zglobovima ili dnevnoj ravnoteži.',
                'en' => 'Aloe and turmeric in a practical add-on for digestion, joints or daily balance routines.',
                'sl' => 'Aloe vera in kurkuma v praktičnem dodatku za prebavo, sklepe ali dnevno ravnovesje.',
            ]],
            [['freedom', 'active ha', 'esm complex'], [
                'hr' => 'Podrška rutini pokretljivosti kada su tema zglobovi, kretanje i svakodnevna fleksibilnost.',
                'en' => 'Support for a mobility routine when joints, movement and everyday flexibility are the topic.',
                'sl' => 'Podpora rutini gibljivosti, ko so tema sklepi, gibanje in vsakodnevna prožnost.',
            ]],
            [['calcium', 'nature min'], [
                'hr' => 'Mineralna podrška kada želiš jednostavnije brinuti o kostima i svakodnevnoj prehrani.',
                'en' => 'Mineral support when you want an easier way to care for bones and daily nutrition.',
                'sl' => 'Mineralna podpora, ko želiš preprosteje skrbeti za kosti in dnevno prehrano.',
            ]],
            [['absorbent c', 'absorbent d', 'b12', 'daily', 'immublend', 'immune gummy', 'kids multivitamini'], [
                'hr' => 'Dnevna vitaminska podrška kada želiš uredniju rutinu imuniteta, energije ili prehrane.',
                'en' => 'Daily vitamin support when you want a steadier immunity, energy or nutrition routine.',
                'sl' => 'Dnevna vitaminska podpora, ko želiš urejenejšo rutino odpornosti, energije ali prehrane.',
            ]],
            [['arctic sea', 'omega', 'nutra q10', 'argi', 'arginin'], [
                'hr' => 'Dodatak za rutinu energije, srca i cirkulacije kada želiš dugoročniju dnevnu podršku.',
                'en' => 'A supplement for energy, heart and circulation routines when you want longer-term daily support.',
                'sl' => 'Dodatek za rutino energije, srca in cirkulacije, ko želiš dolgoročnejšo dnevno podporo.',
            ]],
            [['focus'], [
                'hr' => 'Podrška za dane kada želiš više mentalne jasnoće, fokusa i stabilniji radni ritam.',
                'en' => 'Support for days when you want more mental clarity, focus and a steadier work rhythm.',
                'sl' => 'Podpora za dneve, ko želiš več miselne jasnosti, fokusa in stabilnejši delovni ritem.',
            ]],
            [['maca', 'vitolize men', 'vitolize women'], [
                'hr' => 'Ciljana podrška vitalnosti kada želiš proizvod povezan s energijom i muškom ili ženskom rutinom.',
                'en' => 'Targeted vitality support when you want a product connected with energy and men’s or women’s routines.',
                'sl' => 'Ciljana podpora vitalnosti, ko želiš izdelek, povezan z energijo ter moško ali žensko rutino.',
            ]],
            [['ivision'], [
                'hr' => 'Dodatak za rutinu vida kada su tema oči, ekrani i svakodnevno opterećenje vida.',
                'en' => 'A supplement for an eye-care routine when screens and daily visual strain are the topic.',
                'sl' => 'Dodatek za rutino vida, ko so tema oči, zasloni in vsakodnevna obremenitev vida.',
            ]],
            [['bee honey', 'bee pollen', 'bee propolis', 'royal jelly', 'lycium', 'garlic thyme', 'garlic-thyme'], [
                'hr' => 'Prirodna dnevna podrška kada želiš proizvod iz pčelinje ili biljne linije za energiju i otpornost.',
                'en' => 'Natural daily support when you want a bee-derived or botanical product for energy and resilience.',
                'sl' => 'Naravna dnevna podpora, ko želiš čebelji ali rastlinski izdelek za energijo in odpornost.',
            ]],
            [['toothgel', 'zub'], [
                'hr' => 'Svakodnevna oralna njega s aloe verom i propolisom, bez nepotrebnog kompliciranja.',
                'en' => 'Everyday oral care with aloe and propolis, without complicating the routine.',
                'sl' => 'Vsakodnevna ustna nega z aloe vero in propolisom, brez zapletanja rutine.',
            ]],
            [['collagen', 'kolagen'], [
                'hr' => 'Dobar izbor kada je cilj rutina za kožu, kosu, nokte ili podršku zglobovima.',
                'en' => 'A good fit when the goal is a routine for skin, hair, nails or joint-support habits.',
                'sl' => 'Dobra izbira, ko je cilj rutina za kožo, lase, nohte ali podporo sklepom.',
            ]],
            [['jojoba shampoo', 'jojoba conditioner', 'ever shield', 'gentlemen', 'sunscreen', 'aloe activator', 'propolis creme', 'propolis cream', 'bakuchiol'], [
                'hr' => 'Proizvod za njegu kada želiš jednostavniju rutinu za kožu, kosu ili svakodnevnu svježinu.',
                'en' => 'A care product when you want a simpler routine for skin, hair or everyday freshness.',
                'sl' => 'Izdelek za nego, ko želiš preprostejšo rutino za kožo, lase ali vsakodnevno svežino.',
            ]],
            [['aloe liquid soap'], [
                'hr' => 'Blagi tekući sapun za svakodnevno pranje ruku i tijela bez osjećaja pretjeranog isušivanja.',
                'en' => 'A gentle liquid soap for daily hand and body washing without an overly dry feel.',
                'sl' => 'Blago tekoče milo za vsakodnevno umivanje rok in telesa brez pretiranega občutka izsušenosti.',
            ]],
            [['blossom herbal tea'], [
                'hr' => 'Biljni čaj bez kofeina za mirniji dnevni ritual, topao ili hladan.',
                'en' => 'A caffeine-free herbal tea for a calmer daily ritual, hot or cold.',
                'sl' => 'Zeliščni čaj brez kofeina za mirnejši dnevni ritual, topel ali hladen.',
            ]],
        ];

        foreach ($definitions as [$patterns, $copy]) {
            foreach ($patterns as $pattern) {
                $pattern = $this->normalizeText((string) $pattern);
                if ($pattern !== '' && str_contains($haystack, $pattern)) {
                    return (string) ($copy[$languageCode] ?? $copy['hr'] ?? '');
                }
            }
        }

        $title = trim($title);
        if ($this->isOverheatedMarketingText($haystack) && $title !== '') {
            return match ($languageCode) {
                'en' => 'A product guide for understanding when ' . $title . ' makes sense in a daily routine.',
                'sl' => 'Vodič za razumevanje, kdaj ima ' . $title . ' smisel v dnevni rutini.',
                default => 'Vodič za razumjeti kada ' . $title . ' ima smisla u svakodnevnoj rutini.',
            };
        }

        return '';
    }

    private function knownProductReason(string $haystack, string $languageCode): string
    {
        $languageCode = strtolower(trim($languageCode)) ?: 'hr';
        $definitions = [
            [['c9', 'clean 9'], [
                'hr' => 'Vrijedi ga usporediti ako želiš strukturirani početak i jasan plan prvih dana.',
                'en' => 'Worth comparing if you want a structured start and a clear first-days plan.',
                'sl' => 'Vredno primerjave, če želiš strukturiran začetek in jasen načrt prvih dni.',
            ]],
            [['garcinia', 'therm', 'forever lean', 'lite ultra', 'fiber', 'vlakna'], [
                'hr' => 'Dobro se veže uz temu apetita, porcija, energije ili rutine kontrole težine.',
                'en' => 'It fits topics such as appetite, portions, energy or a weight-management routine.',
                'sl' => 'Dobro se navezuje na apetit, porcije, energijo ali rutino uravnavanja teže.',
            ]],
            [['aloe activator', 'gelly', 'aloe first', 'aloe msm', 'toothgel', 'collagen', 'kolagen', 'jojoba', 'liquid soap', 'ever shield', 'gentlemen', 'sunscreen', 'propolis creme', 'bakuchiol'], [
                'hr' => 'Najbliži je rutini njege, kože, kose ili svakodnevne osobne njege.',
                'en' => 'Closest to a care routine for skin, hair or everyday personal care.',
                'sl' => 'Najbližje je rutini nege kože, las ali vsakodnevne osebne nege.',
            ]],
            [['aloe berry', 'berry nectar', 'aloe peaches', 'aloe vera gel', 'active pro b', 'fields of greens'], [
                'hr' => 'Ima smisla ako temu gledaš kroz probavu i jednostavniju svakodnevnu rutinu.',
                'en' => 'It makes sense if you are looking at the topic through digestion and a simpler daily routine.',
                'sl' => 'Smiselno je, če temo gledaš skozi prebavo in preprostejšo dnevno rutino.',
            ]],
            [['freedom', 'active ha', 'esm complex', 'calcium', 'nature min'], [
                'hr' => 'Koristan je za usporedbu kada su tema zglobovi, kosti ili pokretljivost.',
                'en' => 'Useful to compare when joints, bones or mobility are the topic.',
                'sl' => 'Koristen za primerjavo, kadar so tema sklepi, kosti ali gibljivost.',
            ]],
            [['absorbent c', 'absorbent d', 'b12', 'daily', 'immublend', 'immune gummy', 'kids multivitamini'], [
                'hr' => 'Povezuje se s dnevnom podrškom imunitetu, energiji ili osnovnoj prehrani.',
                'en' => 'Connected with daily support for immunity, energy or baseline nutrition.',
                'sl' => 'Povezuje se z dnevno podporo odpornosti, energiji ali osnovni prehrani.',
            ]],
            [['arctic sea', 'omega', 'nutra q10', 'argi', 'arginin', 'focus', 'maca', 'vitolize'], [
                'hr' => 'Dobar je za usporedbu kada tražiš podršku za energiju, fokus ili vitalnost.',
                'en' => 'Useful to compare when you are looking for energy, focus or vitality support.',
                'sl' => 'Dober za primerjavo, ko iščeš podporo za energijo, fokus ali vitalnost.',
            ]],
            [['gelly', 'aloe first', 'aloe msm', 'toothgel', 'collagen', 'kolagen', 'jojoba', 'liquid soap', 'ever shield', 'gentlemen', 'sunscreen', 'aloe activator', 'propolis creme', 'bakuchiol'], [
                'hr' => 'Najbliži je rutini njege, kože, kose ili svakodnevne osobne njege.',
                'en' => 'Closest to a care routine for skin, hair or everyday personal care.',
                'sl' => 'Najbližje je rutini nege kože, las ali vsakodnevne osebne nege.',
            ]],
            [['bee honey', 'bee pollen', 'bee propolis', 'royal jelly', 'lycium', 'blossom herbal tea', 'garlic thyme', 'garlic-thyme', 'aloeturm', 'aloe turm'], [
                'hr' => 'Dobra je usporedba ako želiš prirodniju dnevnu podršku i mirniji ritual.',
                'en' => 'A good comparison if you want natural daily support and a calmer ritual.',
                'sl' => 'Dobra primerjava, če želiš naravnejšo dnevno podporo in mirnejši ritual.',
            ]],
        ];

        foreach ($definitions as [$patterns, $copy]) {
            foreach ($patterns as $pattern) {
                $pattern = $this->normalizeText((string) $pattern);
                if ($pattern !== '' && str_contains($haystack, $pattern)) {
                    return (string) ($copy[$languageCode] ?? $copy['hr'] ?? '');
                }
            }
        }

        return '';
    }

    private function isOverheatedMarketingText(string $text): bool
    {
        return (bool) preg_match('/(?:otkrijte|postignite|savrš|savrs|\bmoć|\bmoc|moćn|mocn|snažn|snazn|vrhunsk|najčiš|najcis|nevjerojat|najnovije\s+tehnologije|ubrzava|ubrzaj|vrača|vraca|detoks|detoksikacij|čudes|cudes|idealn|revolucionar|tajna\s+vitke|vitke\s+linije|sagorite|savladajte|milijun)/iu', $text);
    }

    private function stripPromotionalSummaryFragments(string $text): string
    {
        $text = trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($text === '') {
            return '';
        }

        $sentences = preg_split('/(?<=[.!?])\s+/u', $text) ?: [$text];
        $kept = [];

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if ($sentence === '' || $this->isPromotionalSentence($sentence)) {
                continue;
            }

            $kept[] = $sentence;
        }

        return trim((string) preg_replace('/\s+/u', ' ', implode(' ', $kept)));
    }

    private function isPromotionalSentence(string $sentence): bool
    {
        return (bool) preg_match('/(?:15\s*%|popust|discount|iskoristi|ostvari|claim|save|ušted|ustede|uštede|sagorite\s+masne|putem naše preporuke|putem nase preporuke|putem naših linkova|putem nasih linkova|uz našu preporuku|s našom preporukom|with our recommendation|z našim priporočilom|bez potrebe za registracijom|bez registracije|direktno na službenom|directly on the official|neposredno v uradni|thealoeveraco\.shop|forevercard\.club)/iu', $sentence);
    }

    private function extractTerms(string $question, string $languageCode): array
    {
        $question = $this->normalizeText($question);
        if ($question === '') {
            return [];
        }

        $parts = preg_split('/[^a-z0-9]+/u', $question) ?: [];
        $stopWords = $this->stopWords($languageCode);
        $terms = [];

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part === '' || mb_strlen($part) < 3 || isset($stopWords[$part])) {
                continue;
            }

            $terms[$part] = true;
        }

        return array_keys($terms);
    }

    private function stopWords(string $languageCode): array
    {
        $common = [
            'ali', 'ako', 'and', 'are', 'bio', 'biti', 'budem', 'da', 'do', 'for', 'from', 'how', 'ili', 'ima',
            'imam', 'je', 'jer', 'kaj', 'kao', 'koji', 'koja', 'koje', 'kako', 'li', 'na', 'nakon', 'nego',
            'nije', 'nisam', 'od', 'po', 'pri', 'sa', 'sam', 'se', 'su', 'the', 'to', 'u', 'uz', 'već', 'za',
            'zbog', 'što', 'this', 'that', 'with', 'your', 'you', 'want', 'trebam', 'treba', 'zelim', 'želim',
            'mene', 'meni', 'sve', 'više', 'less', 'more', 'preporucujem', 'preporucujete',
            'preporuka', 'preporuku', 'proizvod', 'proizvodi', 'zanimaju', 'savjet', 'savjetnik',
        ];

        $languageSpecific = [
            'hr' => ['boli', 'zanima', 'pomoc', 'pomoć', 'trebao', 'trebala', 'trebamo'],
            'en' => ['please', 'help', 'need', 'looking', 'best'],
            'sl' => ['pomoc', 'pomoč', 'rad', 'rada', 'želim', 'zelim'],
        ];

        $words = array_merge($common, $languageSpecific[$languageCode] ?? []);

        return array_fill_keys(array_map([$this, 'normalizeText'], $words), true);
    }

    private function normalizeText(string $value): string
    {
        $value = mb_strtolower($value);
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if (is_string($transliterated) && $transliterated !== '') {
            $value = $transliterated;
        }

        return trim($value);
    }

    private function termVariants(string $term): array
    {
        $variants = [$term];
        $length = strlen($term);

        if ($length >= 6) {
            $variants[] = substr($term, 0, 5);
        }

        if ($length >= 8) {
            $variants[] = substr($term, 0, 6);
        }

        return array_values(array_unique(array_filter($variants, static fn (string $variant): bool => $variant !== '')));
    }

    private function filterStrings(array $values): array
    {
        return array_values(array_filter(array_map(
            static fn (mixed $value): string => trim((string) $value),
            $values
        )));
    }
}
