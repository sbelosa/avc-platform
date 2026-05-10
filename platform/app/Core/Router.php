<?php

declare(strict_types=1);

namespace Avc\Core;

use Avc\Controllers\Admin\AuthController;
use Avc\Controllers\Site\ContentController;
use Avc\Repositories\ContentRepository;
use Avc\Services\Auth\AdminAuthService;

final class Router
{
    private array $routes = [];

    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $this->request->method();
        $path = $this->request->path();
        $handler = $this->routes[$method][$path] ?? null;

        if ($this->isProtectedAdminPath($path)) {
            $auth = new AdminAuthService($this->config, $this->request);

            if (!$auth->check()) {
                $this->response->redirect('/admin/login?redirect=' . rawurlencode($path));
            }
        }

        if ($path === '/admin/login') {
            $auth = new AdminAuthService($this->config, $this->request);

            if ($auth->check()) {
                $this->response->redirect('/admin');
            }
        }

        if ($handler === null) {
            $dynamicRoute = $this->resolveDynamicRoute($path);

            if ($dynamicRoute !== null) {
                if ((string) ($dynamicRoute['route_type'] ?? 'content') === 'content' && (int) ($dynamicRoute['http_status_code'] ?? 200) === 200) {
                    $controller = new ContentController($this->config, $this->request, $this->response);
                    $controller->show($dynamicRoute);
                }

                if (!empty($dynamicRoute['redirect_target_path'])) {
                    http_response_code((int) ($dynamicRoute['http_status_code'] ?? 301));
                    header('Location: ' . $dynamicRoute['redirect_target_path']);
                    exit;
                }
            }

            $this->response->html('<h1>404</h1><p>Route not found.</p>', 404);
        }

        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass($this->config, $this->request, $this->response);
        $controller->{$action}();
    }

    private function isProtectedAdminPath(string $path): bool
    {
        if (!str_starts_with($path, '/admin')) {
            return false;
        }

        return !in_array($path, ['/admin/login'], true);
    }

    private function resolveDynamicRoute(string $path): ?array
    {
        $normalizedPath = $path === '/' ? '/' : '/' . trim($path, '/') . '/';

        return (new ContentRepository($this->config))->findByRoutePath($normalizedPath);
    }
}
