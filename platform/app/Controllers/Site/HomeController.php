<?php

declare(strict_types=1);

namespace Avc\Controllers\Site;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\ContentRepository;
use Avc\Services\Geo\MarketResolver;
use Avc\Services\Referral\ActiveForeverIdService;
use Avc\Support\PageRenderer;

final class HomeController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function index(): never
    {
        $routePath = $this->normalizeHomePath($this->request->path());
        $contentRecord = (new ContentRepository($this->config))->findByRoutePath($routePath);

        $controller = new ContentController($this->config, $this->request, $this->response);

        if ($contentRecord !== null) {
            $controller->show($contentRecord);
        }

        $controller->show($this->fallbackContentRecord($routePath));
    }

    public function debug(): never
    {
        $foreverIdService = new ActiveForeverIdService($this->config);
        $marketResolver = new MarketResolver();
        $routePath = $this->normalizeHomePath($this->request->path());

        $payload = [
            'app' => $this->config['app_name'],
            'default_language' => $this->config['default_language'],
            'active_forever_id' => $foreverIdService->getActiveForeverId(),
            'supported_languages' => $this->config['supported_languages'],
            'market_preview' => $marketResolver->resolveFromCountryCode('HR'),
            'route_path' => $routePath,
            'message' => 'AVC home route now resolves through the public homepage controller.',
        ];

        $body = '<div class="shell"><section class="hero"><div class="hero-panel">'
            . '<span class="hero-kicker">Debug</span>'
            . '<div class="content-prose"><h1>AVC home route debug</h1></div>'
            . '<pre>' . htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') . '</pre>'
            . '</div></section></div>';

        $this->response->html(PageRenderer::render('AVC Home Debug', $body, [
            'lang' => 'hr',
        ]));
    }

    private function normalizeHomePath(string $path): string
    {
        return match ($path) {
            '/en', '/en/' => '/en/',
            '/sl', '/sl/' => '/sl/',
            default => '/',
        };
    }

    private function fallbackContentRecord(string $routePath): array
    {
        $languageCode = match ($routePath) {
            '/en/' => 'en',
            '/sl/' => 'sl',
            default => 'hr',
        };

        $copy = $this->fallbackCopy($languageCode);

        return [
            'route_path' => $routePath,
            'http_status_code' => 200,
            'redirect_target_path' => null,
            'route_type' => 'content',
            'content_translation_id' => 0,
            'content_item_id' => 0,
            'source_wp_post_id' => null,
            'language_code' => $languageCode,
            'title' => $copy['title'],
            'slug' => trim($routePath, '/'),
            'excerpt' => $copy['excerpt'],
            'body_html' => '',
            'summary_html' => '',
            'faq_json' => '',
            'featured_image_path' => '',
            'published_at' => null,
            'content_type' => 'page',
            'lifecycle_status' => 'published',
            'meta_title' => $copy['meta_title'],
            'meta_description' => $copy['excerpt'],
            'canonical_url' => rtrim((string) ($this->config['base_url'] ?? ''), '/') . $routePath,
            'robots_index' => 1,
            'robots_follow' => 1,
            'breadcrumb_title' => $copy['title'],
            'open_graph_title' => $copy['meta_title'],
            'open_graph_description' => $copy['excerpt'],
            'open_graph_image_path' => '',
        ];
    }

    private function fallbackCopy(string $languageCode): array
    {
        return match ($languageCode) {
            'en' => [
                'title' => 'Aloe Vera Centar',
                'meta_title' => 'Aloe Vera Centar | Products, articles and Forever support',
                'excerpt' => 'Products, articles and support that help people explore Forever products and move toward a clearer next step.',
            ],
            'sl' => [
                'title' => 'Aloe Vera Centar',
                'meta_title' => 'Aloe Vera Centar | Izdelki, članki in Forever podpora',
                'excerpt' => 'Izdelki, članki in podpora, ki pomagajo lažje raziskati Forever izdelke in najti jasnejši naslednji korak.',
            ],
            default => [
                'title' => 'Aloe Vera Centar',
                'meta_title' => 'Aloe Vera Centar | Proizvodi, članci i preporuke za Forever',
                'excerpt' => 'Proizvodi, članci i preporuke koji pomažu lakše pregledati Forever proizvode i odabrati sljedeći korak.',
            ],
        };
    }
}
