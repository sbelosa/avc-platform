<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

$config = require dirname(__DIR__) . '/config/app.php';
$casesPath = dirname(__DIR__) . '/data/advisor/advisor_regression_cases.json';
$cases = json_decode((string) file_get_contents($casesPath), true);

if (!is_array($cases)) {
    fwrite(STDERR, "Advisor regression cases could not be loaded.\n");
    exit(2);
}

$brain = new Avc\Services\Advisor\FccStyleAdvisorBrain($config);
$failures = [];
$passes = 0;

foreach ($cases as $case) {
    if (!is_array($case)) {
        continue;
    }

    $caseId = trim((string) ($case['id'] ?? 'unnamed_case'));
    $messages = array_values(array_filter((array) ($case['messages'] ?? []), static fn (mixed $message): bool => is_string($message) && trim($message) !== ''));
    $expect = is_array($case['expect'] ?? null) ? $case['expect'] : [];
    $language = trim((string) ($case['language'] ?? 'hr')) ?: 'hr';
    $sourcePath = trim((string) ($case['source_path'] ?? '/')) ?: '/';

    $history = [];
    $reply = null;

    foreach ($messages as $message) {
        $reply = $brain->buildReply($message, [
            'language' => $language,
            'source_path' => $sourcePath,
            'history' => $history,
        ]);

        $history[] = [
            'role' => 'user',
            'body_text' => $message,
            'payload' => null,
        ];
        $history[] = [
            'role' => 'assistant',
            'body_text' => (string) ($reply['body'] ?? ''),
            'payload' => is_array($reply['payload'] ?? null) ? $reply['payload'] : [],
        ];
    }

    if (!is_array($reply)) {
        $failures[] = $caseId . ': no reply generated';
        continue;
    }

    $payload = is_array($reply['payload'] ?? null) ? $reply['payload'] : [];
    $caseFailures = validateCase((string) ($reply['body'] ?? ''), $payload, $expect);

    if ($caseFailures === []) {
        $passes++;
        echo "[PASS] {$caseId}\n";
        continue;
    }

    echo "[FAIL] {$caseId}\n";
    foreach ($caseFailures as $caseFailure) {
        echo "  - {$caseFailure}\n";
        $failures[] = $caseId . ': ' . $caseFailure;
    }
}

echo "\nSummary: {$passes} passed, " . count($failures) . " failed.\n";

exit($failures === [] ? 0 : 1);

function validateCase(string $body, array $payload, array $expect): array
{
    $failures = [];
    $bodyNormalized = normalizeForMatch($body);
    $primaryProduct = (string) ($payload['primary_product'] ?? '');
    $supportProducts = array_values(array_filter(array_map(
        static fn (mixed $value): string => trim((string) $value),
        (array) ($payload['support_products'] ?? [])
    )));
    $productTitles = recommendationTitles((array) (($payload['recommendations']['products'] ?? [])));
    $articleTitles = recommendationTitles((array) (($payload['recommendations']['articles'] ?? [])));

    if (array_key_exists('needs_clarification', $expect) && (bool) ($payload['needs_clarification'] ?? false) !== (bool) $expect['needs_clarification']) {
        $failures[] = 'needs_clarification expected ' . var_export((bool) $expect['needs_clarification'], true);
    }

    if (array_key_exists('context_reused', $expect) && (bool) ($payload['context_reused'] ?? false) !== (bool) $expect['context_reused']) {
        $failures[] = 'context_reused expected ' . var_export((bool) $expect['context_reused'], true);
    }

    if (!empty($expect['follow_up_mode']) && (string) ($payload['follow_up_mode'] ?? '') !== (string) $expect['follow_up_mode']) {
        $failures[] = 'follow_up_mode expected ' . $expect['follow_up_mode'];
    }

    if (!empty($expect['primary_product_contains']) && !containsText($primaryProduct, (string) $expect['primary_product_contains'])) {
        $failures[] = 'primary_product missing "' . $expect['primary_product_contains'] . '"';
    }

    if (!empty($expect['monthly_quantity_note_contains']) && !containsText((string) ($payload['monthly_quantity_note'] ?? ''), (string) $expect['monthly_quantity_note_contains'])) {
        $failures[] = 'monthly_quantity_note missing "' . $expect['monthly_quantity_note_contains'] . '"';
    }

    foreach ((array) ($expect['support_products_contain'] ?? []) as $expectedSupport) {
        if (!arrayContainsSubstring($supportProducts, (string) $expectedSupport)) {
            $failures[] = 'support_products missing "' . $expectedSupport . '"';
        }
    }

    foreach ((array) ($expect['body_contains'] ?? []) as $expectedBody) {
        if (!str_contains($bodyNormalized, normalizeForMatch((string) $expectedBody))) {
            $failures[] = 'body missing "' . $expectedBody . '"';
        }
    }

    foreach ((array) ($expect['body_not_contains'] ?? []) as $unexpectedBody) {
        if (str_contains($bodyNormalized, normalizeForMatch((string) $unexpectedBody))) {
            $failures[] = 'body unexpectedly contains "' . $unexpectedBody . '"';
        }
    }

    foreach ((array) ($expect['recommendation_product_titles_contain'] ?? []) as $expectedTitle) {
        if (!arrayContainsSubstring($productTitles, (string) $expectedTitle)) {
            $failures[] = 'recommended products missing "' . $expectedTitle . '"';
        }
    }

    foreach ((array) ($expect['recommendation_product_titles_not_contain'] ?? []) as $unexpectedTitle) {
        if (arrayContainsSubstring($productTitles, (string) $unexpectedTitle)) {
            $failures[] = 'recommended products unexpectedly contain "' . $unexpectedTitle . '"';
        }
    }

    foreach ((array) ($expect['recommendation_article_titles_contain'] ?? []) as $expectedArticleTitle) {
        if (!arrayContainsSubstring($articleTitles, (string) $expectedArticleTitle)) {
            $failures[] = 'recommended articles missing "' . $expectedArticleTitle . '"';
        }
    }

    foreach ((array) ($expect['recommendation_article_titles_not_contain'] ?? []) as $unexpectedArticleTitle) {
        if (arrayContainsSubstring($articleTitles, (string) $unexpectedArticleTitle)) {
            $failures[] = 'recommended articles unexpectedly contain "' . $unexpectedArticleTitle . '"';
        }
    }

    if (array_key_exists('recommendation_product_count_max', $expect) && count($productTitles) > (int) $expect['recommendation_product_count_max']) {
        $failures[] = 'recommended products count exceeds ' . (int) $expect['recommendation_product_count_max'];
    }

    if (array_key_exists('recommendation_article_count_max', $expect) && count($articleTitles) > (int) $expect['recommendation_article_count_max']) {
        $failures[] = 'recommended articles count exceeds ' . (int) $expect['recommendation_article_count_max'];
    }

    return $failures;
}

function recommendationTitles(array $rows): array
{
    $titles = [];

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $title = trim((string) ($row['title'] ?? ''));
        if ($title !== '') {
            $titles[] = $title;
        }
    }

    return $titles;
}

function arrayContainsSubstring(array $values, string $needle): bool
{
    $needle = normalizeForMatch($needle);
    if ($needle === '') {
        return false;
    }

    foreach ($values as $value) {
        if (str_contains(normalizeForMatch((string) $value), $needle)) {
            return true;
        }
    }

    return false;
}

function containsText(string $haystack, string $needle): bool
{
    $needle = normalizeForMatch($needle);

    return $needle !== '' && str_contains(normalizeForMatch($haystack), $needle);
}

function normalizeForMatch(string $value): string
{
    $value = mb_strtolower($value);
    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

    if (is_string($transliterated) && $transliterated !== '') {
        $value = $transliterated;
    }

    return trim((string) preg_replace('/\s+/u', ' ', $value));
}
