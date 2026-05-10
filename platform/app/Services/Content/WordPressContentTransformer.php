<?php

declare(strict_types=1);

namespace Avc\Services\Content;

use Avc\Repositories\ContentRepository;

final class WordPressContentTransformer
{
    public function __construct(private array $config)
    {
    }

    public function transform(string $html, string $languageCode): string
    {
        $transformed = trim($html);

        if ($transformed === '') {
            return '';
        }

        // Imported WordPress bodies often still contain wrapper markup and a top-level H1.
        // The AVC page template already renders the canonical page title, so we demote
        // the legacy body heading and remove the outer article wrapper.
        $transformed = preg_replace('/<article\b[^>]*>/i', '', $transformed) ?? $transformed;
        $transformed = preg_replace('/<\/article>/i', '', $transformed) ?? $transformed;
        $transformed = preg_replace_callback('/<h1(\b[^>]*)>(.*?)<\/h1>/is', static function (array $matches): string {
            $attributes = (string) ($matches[1] ?? '');
            $innerHtml = (string) ($matches[2] ?? '');

            return '<h2' . $attributes . '>' . $innerHtml . '</h2>';
        }, $transformed, 1) ?? $transformed;
        $transformed = preg_replace('/\[(\/?)(fusion|avada|woocommerce|contact-form-7|caption|gallery)[^\]]*\]/i', '', $transformed) ?? $transformed;
        $transformed = preg_replace_callback('/href=(["\'])([^"\']+)\1/i', function (array $matches) use ($languageCode): string {
            $quote = $matches[1];
            $href = (string) $matches[2];
            $rewritten = $this->rewriteHref($href, $languageCode);

            return 'href=' . $quote . htmlspecialchars($rewritten, ENT_QUOTES, 'UTF-8') . $quote;
        }, $transformed) ?? $transformed;
        $transformed = preg_replace('/(?:\R\s*){3,}/u', "\n\n", $transformed) ?? $transformed;

        return $transformed;
    }

    private function rewriteHref(string $href, string $languageCode): string
    {
        $trimmed = trim($href);
        if ($trimmed === '') {
            return $trimmed;
        }

        if (preg_match('/^(?:https?:\/\/(?:www\.)?(?:aloevera-centar\.com|aloavera-centar\.com))?\/?\?p=(\d+)/i', $trimmed, $matches)) {
            $mapped = (new ContentRepository($this->config))->findRouteBySourceWordPressId((int) $matches[1], $languageCode);

            return $mapped['route_path'] ?? $trimmed;
        }

        if (preg_match('/^https?:\/\/(?:www\.)?(?:aloevera-centar\.com|aloavera-centar\.com)(\/[^"\']*)$/i', $trimmed, $matches)) {
            return $matches[1];
        }

        return $trimmed;
    }
}
