<?php

declare(strict_types=1);

namespace Avc\Controllers\Site;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\ContentRepository;
use Avc\Repositories\SeoRepository;

final class SeoController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function robots(): never
    {
        $baseUrl = rtrim((string) ($this->config['base_url'] ?? ''), '/');
        $body = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /admin/',
            '',
            'User-agent: GPTBot',
            'Allow: /',
            '',
            'User-agent: OAI-SearchBot',
            'Allow: /',
            '',
            'User-agent: ChatGPT-User',
            'Allow: /',
            '',
            'Sitemap: ' . $baseUrl . '/sitemap.xml',
            '',
        ]);

        $this->response->raw($body, 'text/plain; charset=utf-8');
    }

    public function sitemapIndex(): never
    {
        $repository = new SeoRepository($this->config);
        $supportedLanguages = (array) ($this->config['supported_languages'] ?? ['hr']);
        $sitemaps = [];

        foreach ($supportedLanguages as $languageCode) {
            $entries = $repository->listSitemapEntries((string) $languageCode, 1);
            if ($entries === []) {
                continue;
            }

            $sitemaps[] = [
                'loc' => $this->absoluteUrl('/sitemaps/' . rawurlencode((string) $languageCode) . '.xml'),
                'lastmod' => $this->resolveLastmod($entries[0]),
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($sitemaps as $sitemap) {
            $xml .= '<sitemap>'
                . '<loc>' . $this->xml((string) ($sitemap['loc'] ?? '')) . '</loc>'
                . '<lastmod>' . $this->xml((string) ($sitemap['lastmod'] ?? '')) . '</lastmod>'
                . '</sitemap>';
        }

        $xml .= '</sitemapindex>';

        $this->response->raw($xml, 'application/xml; charset=utf-8');
    }

    public function sitemapLanguage(): never
    {
        $path = (string) $this->request->path();
        $languageCode = strtolower((string) preg_replace('/^\/sitemaps\/([a-z-]+)\.xml$/i', '$1', $path));
        $supportedLanguages = array_map('strtolower', (array) ($this->config['supported_languages'] ?? ['hr']));

        if ($languageCode === '' || !in_array($languageCode, $supportedLanguages, true)) {
            $this->response->raw('Sitemap not found.', 'text/plain; charset=utf-8', 404);
        }

        $entries = (new SeoRepository($this->config))->listSitemapEntries($languageCode, 50000);
        $contentRepository = new ContentRepository($this->config);
        $defaultLanguage = strtolower((string) ($this->config['default_language'] ?? 'hr'));
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        foreach ($entries as $entry) {
            $alternateLinks = $this->sitemapAlternateLinks($contentRepository, (int) ($entry['content_item_id'] ?? 0), $defaultLanguage, (string) ($entry['route_path'] ?? ''));
            $xml .= '<url>'
                . '<loc>' . $this->xml($this->absoluteUrl((string) ($entry['route_path'] ?? '/'))) . '</loc>'
                . '<lastmod>' . $this->xml($this->resolveLastmod($entry)) . '</lastmod>';

            foreach ($alternateLinks as $alternateLink) {
                $xml .= '<xhtml:link rel="alternate" hreflang="' . $this->xml((string) ($alternateLink['hreflang'] ?? '')) . '" href="' . $this->xml((string) ($alternateLink['href'] ?? '')) . '"/>';
            }

            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        $this->response->raw($xml, 'application/xml; charset=utf-8');
    }

    private function resolveLastmod(array $entry): string
    {
        $candidates = array_filter([
            trim((string) ($entry['seo_updated_at'] ?? '')),
            trim((string) ($entry['translation_updated_at'] ?? '')),
            trim((string) ($entry['route_updated_at'] ?? '')),
            trim((string) ($entry['published_at'] ?? '')),
        ]);

        foreach ($candidates as $candidate) {
            $timestamp = strtotime((string) $candidate);
            if ($timestamp !== false) {
                return date(DATE_ATOM, $timestamp);
            }
        }

        return date(DATE_ATOM);
    }

    private function absoluteUrl(string $path): string
    {
        return rtrim((string) ($this->config['base_url'] ?? ''), '/') . ($path !== '' ? $path : '/');
    }

    private function sitemapAlternateLinks(ContentRepository $contentRepository, int $contentItemId, string $defaultLanguage, string $routePath = ''): array
    {
        if ($this->isCatalogPath($routePath)) {
            return $this->catalogAlternateLinks($defaultLanguage, $routePath);
        }

        if ($contentItemId <= 0) {
            return [];
        }

        $links = [];
        $defaultHref = '';

        foreach ($contentRepository->findAlternatesForContentItem($contentItemId) as $alternate) {
            $languageCode = strtolower((string) ($alternate['language_code'] ?? ''));
            $routePath = (string) ($alternate['route_path'] ?? '');

            if ($languageCode === '' || $routePath === '') {
                continue;
            }

            $href = $this->absoluteUrl($routePath);
            $links[] = [
                'hreflang' => $languageCode,
                'href' => $href,
            ];

            if ($languageCode === $defaultLanguage) {
                $defaultHref = $href;
            }
        }

        if ($defaultHref !== '') {
            $links[] = [
                'hreflang' => 'x-default',
                'href' => $defaultHref,
            ];
        }

        return $links;
    }

    private function isCatalogPath(string $routePath): bool
    {
        return $this->catalogPathGroup($routePath) !== [];
    }

    private function catalogAlternateLinks(string $defaultLanguage, string $routePath): array
    {
        $paths = $this->catalogPathGroup($routePath);
        $links = [];

        foreach ($paths as $languageCode => $path) {
            $links[] = [
                'hreflang' => $languageCode,
                'href' => $this->absoluteUrl($path),
            ];
        }

        $links[] = [
            'hreflang' => 'x-default',
            'href' => $this->absoluteUrl($paths[$defaultLanguage] ?? $paths['hr']),
        ];

        return $links;
    }

    private function catalogPathGroup(string $routePath): array
    {
        $groups = [
            [
                'hr' => '/forever-proizvodi/',
                'en' => '/en/forever-products/',
                'sl' => '/sl/forever-izdelki/',
            ],
            [
                'hr' => '/clanci/',
                'en' => '/en/articles/',
                'sl' => '/sl/clanki/',
            ],
        ];

        foreach ($groups as $paths) {
            if (in_array($routePath, $paths, true)) {
                return $paths;
            }
        }

        return [];
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
