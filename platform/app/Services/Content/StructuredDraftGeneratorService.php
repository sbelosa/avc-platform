<?php

declare(strict_types=1);

namespace Avc\Services\Content;

final class StructuredDraftGeneratorService
{
    public function generate(array $record): array
    {
        $languageCode = strtolower(trim((string) ($record['language_code'] ?? 'hr'))) ?: 'hr';
        $contentType = trim((string) ($record['content_type'] ?? 'article'));
        $title = trim((string) ($record['title'] ?? ''));
        $metaDescription = trim((string) ($record['meta_description'] ?? ''));
        $excerpt = trim((string) ($record['excerpt'] ?? ''));
        $bodyText = $this->plainText((string) ($record['body_html'] ?? ''));
        $sentences = $this->extractSentences($bodyText, 8);

        $summaryPoints = $this->buildSummaryPoints($metaDescription, $excerpt, $sentences, $languageCode, $contentType);
        $faqItems = $this->buildFaqItems($title, $metaDescription, $excerpt, $sentences, $languageCode, $contentType);

        return [
            'summary_html' => $this->renderSummaryHtml($summaryPoints),
            'faq_items' => $faqItems,
        ];
    }

    private function buildSummaryPoints(string $metaDescription, string $excerpt, array $sentences, string $languageCode, string $contentType): array
    {
        $points = [];

        foreach ([$metaDescription, $excerpt] as $candidate) {
            $candidate = $this->cleanSentence($candidate);
            if ($candidate !== '') {
                $points[] = $candidate;
            }
        }

        foreach ($sentences as $sentence) {
            $sentence = $this->cleanSentence($sentence);
            if ($sentence === '') {
                continue;
            }

            if ($this->isDuplicatePoint($sentence, $points)) {
                continue;
            }

            $points[] = $sentence;

            if (count($points) >= 3) {
                break;
            }
        }

        if ($points === []) {
            $points[] = $this->fallbackSummary($languageCode, $contentType);
        }

        return array_slice($points, 0, 3);
    }

    private function buildFaqItems(string $title, string $metaDescription, string $excerpt, array $sentences, string $languageCode, string $contentType): array
    {
        $primaryAnswer = $this->cleanSentence($metaDescription !== '' ? $metaDescription : ($excerpt !== '' ? $excerpt : ($sentences[0] ?? '')));
        $secondaryAnswer = $this->cleanSentence($sentences[1] ?? $sentences[0] ?? '');

        $questions = $this->questionTemplates($languageCode, $contentType, $title);
        $items = [];

        if ($primaryAnswer !== '') {
            $items[] = [
                'question' => $questions[0],
                'answer' => $primaryAnswer,
            ];
        }

        if ($secondaryAnswer !== '') {
            $items[] = [
                'question' => $questions[1],
                'answer' => $secondaryAnswer,
            ];
        }

        if ($items === []) {
            $items[] = [
                'question' => $questions[0],
                'answer' => $this->fallbackAnswer($languageCode, $contentType),
            ];
        }

        return array_slice($items, 0, 3);
    }

    private function questionTemplates(string $languageCode, string $contentType, string $title): array
    {
        $shortTitle = trim(preg_replace('/\s+/', ' ', $title) ?? $title);

        $templates = [
            'hr' => $contentType === 'product_guide'
                ? [
                    'Kome bi mogao odgovarati ovaj proizvod?',
                    'Na što paziti prije kupnje ili korištenja?',
                ]
                : [
                    'Što je najvažnije znati o ovoj temi?',
                    'Na što posebno treba obratiti pažnju?',
                ],
            'en' => $contentType === 'product_guide'
                ? [
                    'Who might find this product helpful?',
                    'What should you pay attention to before buying or using it?',
                ]
                : [
                    'What is the main takeaway from this topic?',
                    'What should readers pay special attention to?',
                ],
            'sl' => $contentType === 'product_guide'
                ? [
                    'Komu bi lahko ta izdelek ustrezal?',
                    'Na kaj paziti pred nakupom ali uporabo?',
                ]
                : [
                    'Kaj je najpomembneje vedeti o tej temi?',
                    'Na kaj je treba posebej paziti?',
                ],
        ];

        $selected = $templates[$languageCode] ?? $templates['hr'];

        if ($shortTitle !== '' && mb_strlen($shortTitle) <= 90) {
            $selected[0] = match ($languageCode) {
                'en' => 'What is most important to know about "' . $shortTitle . '"?',
                'sl' => 'Kaj je najpomembneje vedeti o temi "' . $shortTitle . '"?',
                default => 'Što je najvažnije znati o temi "' . $shortTitle . '"?',
            };
        }

        return $selected;
    }

    private function renderSummaryHtml(array $points): string
    {
        if ($points === []) {
            return '';
        }

        $html = '<ul>';
        foreach ($points as $point) {
            $html .= '<li>' . htmlspecialchars($point, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    private function extractSentences(string $text, int $limit = 6): array
    {
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/(?<=[.!?])\s+/u', $text) ?: [];
        $sentences = [];

        foreach ($parts as $part) {
            $part = $this->cleanSentence($part);
            if ($part === '' || mb_strlen($part) < 50) {
                continue;
            }

            $sentences[] = $part;

            if (count($sentences) >= $limit) {
                break;
            }
        }

        return $sentences;
    }

    private function cleanSentence(string $value): string
    {
        $value = trim(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    private function isDuplicatePoint(string $candidate, array $points): bool
    {
        $normalizedCandidate = mb_strtolower($candidate);

        foreach ($points as $point) {
            $normalizedPoint = mb_strtolower((string) $point);
            if ($normalizedPoint === $normalizedCandidate) {
                return true;
            }

            if (str_contains($normalizedPoint, $normalizedCandidate) || str_contains($normalizedCandidate, $normalizedPoint)) {
                return true;
            }
        }

        return false;
    }

    private function plainText(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function fallbackSummary(string $languageCode, string $contentType): string
    {
        return match ($languageCode) {
            'en' => $contentType === 'product_guide'
                ? 'This guide explains the key benefits, usage context and the most important buying considerations.'
                : 'This article highlights the main practical takeaways, context and the points readers should remember.',
            'sl' => $contentType === 'product_guide'
                ? 'Ta vodič povzame glavne koristi, kontekst uporabe in najpomembnejše točke pred nakupom.'
                : 'Ta članek povzame glavna praktična sporočila, kontekst in ključne poudarke za bralca.',
            default => $contentType === 'product_guide'
                ? 'Ovaj vodič sažima glavne koristi, kontekst korištenja i najvažnije točke prije kupnje.'
                : 'Ovaj članak sažima glavne praktične zaključke, kontekst i najvažnije naglaske za čitatelja.',
        };
    }

    private function fallbackAnswer(string $languageCode, string $contentType): string
    {
        return match ($languageCode) {
            'en' => $contentType === 'product_guide'
                ? 'Use the guide to understand who the product may suit, what to check before purchase and how it fits into a broader routine.'
                : 'Use the article as a practical overview, then connect the topic with related guides and product recommendations.',
            'sl' => $contentType === 'product_guide'
                ? 'Ta vodič pomaga razumeti, komu bi izdelek lahko ustrezal, kaj preveriti pred nakupom in kako se vključi v širšo rutino.'
                : 'Članek služi kot praktičen pregled teme in kot izhodišče za povezovanje z drugimi vodiči ter priporočili izdelkov.',
            default => $contentType === 'product_guide'
                ? 'Ovaj vodič pomaže razumjeti kome proizvod može odgovarati, što provjeriti prije kupnje i kako ga uklopiti u širu rutinu.'
                : 'Članak služi kao praktičan pregled teme i polazište za povezivanje s drugim vodičima i preporukama proizvoda.',
        };
    }
}
