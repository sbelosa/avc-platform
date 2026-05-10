<?php

declare(strict_types=1);

namespace Avc\Services\Content;

final class StructuredContentService
{
    public function decodeFaqJson(?string $faqJson): array
    {
        $faqJson = trim((string) $faqJson);
        if ($faqJson === '') {
            return [];
        }

        $decoded = json_decode($faqJson, true);
        if (is_array($decoded) && array_key_exists('items', $decoded) && is_array($decoded['items'])) {
            $decoded = $decoded['items'];
        }

        return $this->normalizeFaqItems(is_array($decoded) ? $decoded : []);
    }

    public function parseFaqInput(string $input): array
    {
        $input = trim($input);
        if ($input === '') {
            return ['items' => [], 'error' => null];
        }

        if (preg_match('/^\s*[\[{]/', $input) === 1) {
            $decoded = json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                return ['items' => [], 'error' => 'FAQ mora biti valjan JSON niz ili blokovi u formatu pitanje | odgovor.'];
            }

            if (array_key_exists('items', $decoded) && is_array($decoded['items'])) {
                $decoded = $decoded['items'];
            }

            return ['items' => $this->normalizeFaqItems($decoded), 'error' => null];
        }

        $blocks = preg_split('/\R{2,}/u', $input) ?: [];
        $items = [];

        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            if (str_contains($block, '|')) {
                [$question, $answer] = array_pad(explode('|', $block, 2), 2, '');
                $items[] = [
                    'question' => trim($question),
                    'answer' => trim($answer),
                ];
                continue;
            }

            $question = '';
            $answerLines = [];
            $lines = preg_split('/\R/u', $block) ?: [];

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                if ($question === '' && preg_match('/^(?:q|question|p|pitanje)\s*:\s*(.+)$/iu', $line, $matches) === 1) {
                    $question = trim((string) $matches[1]);
                    continue;
                }

                if (preg_match('/^(?:a|answer|o|odgovor)\s*:\s*(.+)$/iu', $line, $matches) === 1) {
                    $answerLines[] = trim((string) $matches[1]);
                    continue;
                }

                if ($question === '') {
                    $question = $line;
                    continue;
                }

                $answerLines[] = $line;
            }

            $items[] = [
                'question' => $question,
                'answer' => trim(implode("\n", $answerLines)),
            ];
        }

        $items = $this->normalizeFaqItems($items);

        if ($items === []) {
            return ['items' => [], 'error' => 'FAQ unos je prazan ili nije prepoznatljiv. Koristi JSON ili format pitanje | odgovor.'];
        }

        return ['items' => $items, 'error' => null];
    }

    public function encodeFaqItems(array $items): string
    {
        return $items === []
            ? ''
            : (string) json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function formatFaqItemsForEditor(array $items): string
    {
        if ($items === []) {
            return '';
        }

        $blocks = [];

        foreach ($items as $item) {
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            $blocks[] = 'Q: ' . $question . "\n" . 'A: ' . $answer;
        }

        return implode("\n\n", $blocks);
    }

    public function normalizeFaqItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $question = trim((string) ($item['question'] ?? $item['q'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? $item['a'] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            $normalized[] = [
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $normalized;
    }
}
