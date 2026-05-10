<?php

declare(strict_types=1);

namespace Avc\Services\Content;

final class SeoDraftGeneratorService
{
    public function generate(array $record): array
    {
        $languageCode = strtolower(trim((string) ($record['language_code'] ?? 'hr'))) ?: 'hr';
        $contentType = trim((string) ($record['content_type'] ?? 'article'));
        $title = $this->cleanText((string) ($record['title'] ?? ''));
        $metaDescription = $this->cleanText((string) ($record['meta_description'] ?? ''));
        $excerpt = $this->cleanText((string) ($record['excerpt'] ?? ''));
        $bodyText = $this->plainText((string) ($record['body_html'] ?? ''));
        $sentences = $this->extractSentences($bodyText, 10);

        $excerptDraft = $excerpt !== ''
            ? $excerpt
            : $this->buildExcerpt($metaDescription, $sentences, $languageCode, $contentType);

        return [
            'excerpt' => $excerptDraft,
            'meta_title' => $this->buildMetaTitle($title, $languageCode, $contentType),
            'meta_description' => $metaDescription !== ''
                ? $metaDescription
                : $this->buildMetaDescription($excerptDraft, $sentences, $languageCode, $contentType),
        ];
    }

    private function buildExcerpt(string $metaDescription, array $sentences, string $languageCode, string $contentType): string
    {
        $candidates = [];

        if ($metaDescription !== '') {
            $candidates[] = $metaDescription;
        }

        if ($sentences !== []) {
            $candidates[] = $this->truncateByWords(implode(' ', array_slice($sentences, 0, 2)), 220);
            $candidates[] = $this->truncateByWords((string) ($sentences[0] ?? ''), 220);
        }

        foreach ($candidates as $candidate) {
            $candidate = $this->cleanText($candidate);
            if ($candidate !== '') {
                return $candidate;
            }
        }

        return $this->fallbackExcerpt($languageCode, $contentType);
    }

    private function buildMetaTitle(string $title, string $languageCode, string $contentType): string
    {
        if ($title === '') {
            return $this->fallbackMetaTitle($languageCode, $contentType);
        }

        $suffix = match ($languageCode) {
            'en' => $contentType === 'product_guide' ? ' | Forever guide' : ' | Aloevera centar',
            'sl' => $contentType === 'product_guide' ? ' | Forever vodnik' : ' | Aloevera center',
            default => $contentType === 'product_guide' ? ' | Forever vodič' : ' | Aloevera centar',
        };

        if (mb_strlen($title . $suffix) <= 60) {
            return $title . $suffix;
        }

        return $this->truncateByWords($title, 60);
    }

    private function buildMetaDescription(string $excerptDraft, array $sentences, string $languageCode, string $contentType): string
    {
        $candidates = [];

        if ($excerptDraft !== '') {
            $candidates[] = $excerptDraft;
        }

        if ($sentences !== []) {
            $candidates[] = $this->truncateByWords(implode(' ', array_slice($sentences, 0, 2)), 155);
            $candidates[] = $this->truncateByWords((string) ($sentences[0] ?? ''), 155);
        }

        foreach ($candidates as $candidate) {
            $candidate = $this->cleanText($candidate);
            if ($candidate !== '') {
                $candidate = $this->truncateByWords($candidate, 155);

                if (mb_strlen($candidate) < 110) {
                    $candidate = $this->extendMetaDescription($candidate, $languageCode, $contentType);
                }

                return $candidate;
            }
        }

        return $this->fallbackMetaDescription($languageCode, $contentType);
    }

    private function extractSentences(string $text, int $limit = 8): array
    {
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/(?<=[.!?])\s+/u', $text) ?: [];
        $sentences = [];

        foreach ($parts as $part) {
            $part = $this->cleanText($part);
            if ($part === '' || mb_strlen($part) < 45) {
                continue;
            }

            $sentences[] = $part;

            if (count($sentences) >= $limit) {
                break;
            }
        }

        return $sentences;
    }

    private function cleanText(string $value): string
    {
        $value = trim(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    private function truncateByWords(string $value, int $limit): string
    {
        $value = $this->cleanText($value);
        if ($value === '' || mb_strlen($value) <= $limit) {
            return $value;
        }

        $words = preg_split('/\s+/u', $value) ?: [];
        $buffer = '';

        foreach ($words as $word) {
            $candidate = $buffer === '' ? $word : $buffer . ' ' . $word;
            if (mb_strlen($candidate) > $limit) {
                break;
            }

            $buffer = $candidate;
        }

        if ($buffer === '') {
            return mb_substr($value, 0, max(0, $limit - 1)) . '…';
        }

        return rtrim($buffer, " ,.;:") . '…';
    }

    private function plainText(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function fallbackExcerpt(string $languageCode, string $contentType): string
    {
        return match ($languageCode) {
            'en' => $contentType === 'product_guide'
                ? 'Practical guide to benefits, usage context and the key details to review before choosing this Forever product.'
                : 'Helpful overview with practical takeaways, context and next steps related to this Forever topic.',
            'sl' => $contentType === 'product_guide'
                ? 'Praktičen vodnik skozi koristi, kontekst uporabe in ključne podrobnosti pred izbiro tega Forever izdelka.'
                : 'Uporaben pregled teme s praktičnimi poudarki, kontekstom in naslednjimi koraki.',
            default => $contentType === 'product_guide'
                ? 'Praktičan vodič kroz koristi, kontekst korištenja i ključne detalje prije odabira ovog Forever proizvoda.'
                : 'Koristan pregled teme s praktičnim zaključcima, kontekstom i sljedećim koracima.',
        };
    }

    private function fallbackMetaTitle(string $languageCode, string $contentType): string
    {
        return match ($languageCode) {
            'en' => $contentType === 'product_guide' ? 'Forever product guide' : 'Forever support article',
            'sl' => $contentType === 'product_guide' ? 'Forever vodnik izdelka' : 'Forever podporni članek',
            default => $contentType === 'product_guide' ? 'Forever vodič proizvoda' : 'Forever članak podrške',
        };
    }

    private function fallbackMetaDescription(string $languageCode, string $contentType): string
    {
        return match ($languageCode) {
            'en' => $contentType === 'product_guide'
                ? 'Explore the key benefits, practical context and buying guidance for this Forever product recommendation.'
                : 'Read a practical overview with helpful takeaways, guidance and links to related Forever recommendations.',
            'sl' => $contentType === 'product_guide'
                ? 'Razišči glavne koristi, praktični kontekst in smernice za nakup tega priporočila Forever izdelka.'
                : 'Preberi praktičen pregled s koristnimi poudarki, usmeritvami in povezavami na sorodna Forever priporočila.',
            default => $contentType === 'product_guide'
                ? 'Istraži glavne koristi, praktični kontekst i smjernice za kupnju ove preporuke Forever proizvoda.'
                : 'Pročitaj praktičan pregled s korisnim zaključcima, smjernicama i linkovima na povezana Forever preporuke.',
        };
    }

    private function extendMetaDescription(string $candidate, string $languageCode, string $contentType): string
    {
        $candidate = rtrim($candidate, " \t\n\r\0\x0B,;:");
        $suffix = match ($languageCode) {
            'en' => $contentType === 'product_guide'
                ? ' Discover the key benefits, usage tips and guidance before choosing the product.'
                : ' Discover practical benefits, useful context and the next best steps for readers.',
            'sl' => $contentType === 'product_guide'
                ? ' Odkrij glavne koristi, praktične nasvete za uporabo in usmeritve pred izbiro izdelka.'
                : ' Odkrij praktične koristi, uporaben kontekst in naslednje korake za bralca.',
            default => $contentType === 'product_guide'
                ? ' Otkrij glavne koristi, savjete za korištenje i važne smjernice prije odabira proizvoda.'
                : ' Otkrij praktične koristi, koristan kontekst i sljedeće korake za čitatelja.',
        };

        return $this->truncateByWords($candidate . $suffix, 155);
    }
}
