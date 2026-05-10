<?php

declare(strict_types=1);

namespace Avc\Services\Seo;

final class SeoAuditService
{
    public function decorateRows(array $rows): array
    {
        $decorated = [];

        foreach ($rows as $row) {
            $decorated[] = array_merge($row, $this->auditRow($row));
        }

        usort($decorated, static function (array $left, array $right): int {
            $issueComparison = ((int) ($right['issue_count'] ?? 0)) <=> ((int) ($left['issue_count'] ?? 0));
            if ($issueComparison !== 0) {
                return $issueComparison;
            }

            $scoreComparison = ((int) ($left['seo_score'] ?? 0)) <=> ((int) ($right['seo_score'] ?? 0));
            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            return strcmp((string) ($left['route_path'] ?? ''), (string) ($right['route_path'] ?? ''));
        });

        return $decorated;
    }

    public function auditRow(array $row): array
    {
        $title = trim((string) ($row['title'] ?? ''));
        $metaTitle = trim((string) ($row['meta_title'] ?? ''));
        $excerpt = trim((string) ($row['excerpt'] ?? ''));
        $metaDescription = trim((string) ($row['meta_description'] ?? ''));
        $featuredImagePath = trim((string) ($row['featured_image_path'] ?? ''));
        $bodyHtml = (string) ($row['body_html'] ?? '');
        $summaryHtml = trim((string) ($row['summary_html'] ?? ''));
        $faqItems = $this->decodeFaqItems((string) ($row['faq_json'] ?? ''));
        $contentType = trim((string) ($row['content_type'] ?? 'page'));
        $robotsIndex = (int) ($row['robots_index'] ?? 1);
        $bodyText = $this->plainText($bodyHtml);
        $bodyTextLength = mb_strlen($bodyText);
        $internalLinkCount = preg_match_all('/<a\s/i', $bodyHtml, $matches) ?: 0;
        $effectiveMetaTitle = $metaTitle !== '' ? $metaTitle : $title;
        $effectiveDescription = $metaDescription !== '' ? $metaDescription : $excerpt;
        $effectiveDescriptionLength = mb_strlen($effectiveDescription);
        $issues = [];
        $score = 100;

        if ($metaTitle === '') {
            $this->addIssue($issues, $score, 'missing_custom_meta_title', 'Nema custom meta title', 'low', 'Stranica koristi naslov kao fallback za SEO title.');
        }

        if ($effectiveDescription === '') {
            $this->addIssue($issues, $score, 'missing_effective_description', 'Nedostaje meta description', 'high', 'Dodaj meta description ili barem kvalitetan excerpt.');
        } elseif ($metaDescription === '' && $excerpt !== '') {
            $this->addIssue($issues, $score, 'excerpt_fallback_description', 'Meta description koristi excerpt', 'low', 'Radi, ali jači rezultat dobiješ s ručno napisanim meta opisom.');
        }

        if ($effectiveDescription !== '' && $effectiveDescriptionLength < 110) {
            $this->addIssue($issues, $score, 'description_too_short', 'Meta description je prekratak', 'medium', 'Pokušaj ciljati 110-160 znakova da snippet bude jači.');
        }

        if ($effectiveDescriptionLength > 170) {
            $this->addIssue($issues, $score, 'description_too_long', 'Meta description je predugačak', 'medium', 'Skrati opis kako ne bi bio rezan u SERP-u.');
        }

        if ($featuredImagePath === '') {
            $this->addIssue($issues, $score, 'missing_featured_image', 'Nedostaje istaknuta slika', 'medium', 'Slika pomaže dijeljenju, prepoznatljivosti i bogatijim prikazima.');
        }

        if ($robotsIndex !== 1) {
            $this->addIssue($issues, $score, 'noindex', 'Stranica je noindex', 'high', 'Objavljen sadržaj koji želiš rangirati mora biti indexiran.');
        }

        if ($bodyTextLength < $this->bodyLengthThreshold($contentType)) {
            $this->addIssue($issues, $score, 'thin_content', 'Sadržaj je pretanak', 'high', 'Dodaj konkretnije odgovore, primjere i sekcije za bolju SEO vrijednost.');
        }

        if ($internalLinkCount < $this->internalLinkThreshold($contentType)) {
            $this->addIssue($issues, $score, 'low_internal_linking', 'Premalo internih linkova', 'medium', 'Poveži članak s vodičima proizvoda i drugim relevantnim člancima.');
        }

        if ($excerpt === '') {
            $this->addIssue($issues, $score, 'missing_excerpt', 'Nedostaje excerpt', 'low', 'Excerpt pomaže adminu, karticama sadržaja i fallback meta opisu.');
        }

        if (in_array($contentType, ['article', 'product_guide'], true) && $summaryHtml === '') {
            $this->addIssue($issues, $score, 'missing_summary_block', 'Nedostaje AI sažetak', 'medium', 'Dodaj kratak strukturirani sažetak koji jasno prenosi glavne koristi i zaključke.');
        }

        if (in_array($contentType, ['article', 'product_guide'], true) && $faqItems === []) {
            $this->addIssue($issues, $score, 'missing_faq_block', 'Nedostaje FAQ blok', 'low', 'Dodaj 2-4 kratka pitanja i odgovora za AI čitljivost i bogatiji schema signal.');
        }

        return [
            'seo_score' => max(0, $score),
            'issue_count' => count($issues),
            'issues' => $issues,
            'body_text_length' => $bodyTextLength,
            'internal_link_count' => $internalLinkCount,
            'effective_meta_title' => $effectiveMetaTitle,
            'effective_description' => $effectiveDescription,
            'effective_description_length' => $effectiveDescriptionLength,
            'faq_count' => count($faqItems),
            'has_summary_html' => $summaryHtml !== '',
            'uses_meta_title_fallback' => $metaTitle === '',
            'uses_description_fallback' => $metaDescription === '' && $excerpt !== '',
        ];
    }

    public function summarize(array $decoratedRows): array
    {
        $summary = [
            'total' => count($decoratedRows),
            'needs_attention_total' => 0,
            'average_score' => 0,
            'missing_custom_meta_title_total' => 0,
            'missing_effective_description_total' => 0,
            'missing_excerpt_total' => 0,
            'thin_content_total' => 0,
            'low_internal_linking_total' => 0,
            'missing_featured_image_total' => 0,
            'noindex_total' => 0,
            'missing_summary_block_total' => 0,
            'missing_faq_block_total' => 0,
        ];

        if ($decoratedRows === []) {
            return $summary;
        }

        $scoreSum = 0;

        foreach ($decoratedRows as $row) {
            $scoreSum += (int) ($row['seo_score'] ?? 0);

            if ((int) ($row['issue_count'] ?? 0) > 0) {
                $summary['needs_attention_total']++;
            }

            foreach ((array) ($row['issues'] ?? []) as $issue) {
                $code = (string) ($issue['code'] ?? '');

                if ($code === 'missing_custom_meta_title') {
                    $summary['missing_custom_meta_title_total']++;
                }

                if ($code === 'missing_effective_description') {
                    $summary['missing_effective_description_total']++;
                }

                if ($code === 'missing_excerpt') {
                    $summary['missing_excerpt_total']++;
                }

                if ($code === 'thin_content') {
                    $summary['thin_content_total']++;
                }

                if ($code === 'low_internal_linking') {
                    $summary['low_internal_linking_total']++;
                }

                if ($code === 'missing_featured_image') {
                    $summary['missing_featured_image_total']++;
                }

                if ($code === 'noindex') {
                    $summary['noindex_total']++;
                }

                if ($code === 'missing_summary_block') {
                    $summary['missing_summary_block_total']++;
                }

                if ($code === 'missing_faq_block') {
                    $summary['missing_faq_block_total']++;
                }
            }
        }

        $summary['average_score'] = (int) round($scoreSum / count($decoratedRows));

        return $summary;
    }

    private function addIssue(array &$issues, int &$score, string $code, string $label, string $severity, string $help): void
    {
        $issues[] = [
            'code' => $code,
            'label' => $label,
            'severity' => $severity,
            'help' => $help,
        ];

        $score -= match ($severity) {
            'high' => 18,
            'medium' => 10,
            default => 5,
        };
    }

    private function plainText(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function bodyLengthThreshold(string $contentType): int
    {
        return match ($contentType) {
            'article' => 1200,
            'product_guide' => 700,
            default => 250,
        };
    }

    private function internalLinkThreshold(string $contentType): int
    {
        return match ($contentType) {
            'article' => 2,
            'product_guide' => 1,
            default => 0,
        };
    }

    private function decodeFaqItems(string $faqJson): array
    {
        $faqJson = trim($faqJson);
        if ($faqJson === '') {
            return [];
        }

        $decoded = json_decode($faqJson, true);
        if (!is_array($decoded)) {
            return [];
        }

        if (array_key_exists('items', $decoded) && is_array($decoded['items'])) {
            $decoded = $decoded['items'];
        }

        $items = [];

        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }

            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));
            if ($question === '' || $answer === '') {
                continue;
            }

            $items[] = [
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $items;
    }
}
