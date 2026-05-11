<?php

declare(strict_types=1);

namespace Avc\Core;

final class Request
{
    private array $jsonPayload;

    public function __construct(
        private array $server,
        private array $query,
        private array $post,
        private string $rawBody = ''
    ) {
        $this->jsonPayload = $this->decodeJsonPayload();
    }

    public function method(): string
    {
        return strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));
    }

    public function path(): string
    {
        $uri = (string) ($this->server['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        return rtrim($path, '/') === '' ? '/' : rtrim($path, '/');
    }

    public function input(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }

        if (array_key_exists($key, $this->jsonPayload)) {
            return $this->jsonPayload[$key];
        }

        if (array_key_exists($key, $this->query)) {
            return $this->query[$key];
        }

        return $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->jsonPayload, $this->post);
    }

    public function header(string $key, mixed $default = null): mixed
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $key));

        if (array_key_exists($normalized, $this->server)) {
            return $this->server[$normalized];
        }

        if ($key === 'Content-Type' && array_key_exists('CONTENT_TYPE', $this->server)) {
            return $this->server['CONTENT_TYPE'];
        }

        return $default;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $_COOKIE[$key] ?? $default;
    }

    public function rawBody(): string
    {
        return $this->rawBody;
    }

    public function session(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function setSession(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forgetSession(string $key): void
    {
        unset($_SESSION[$key]);
    }

    private function decodeJsonPayload(): array
    {
        $contentType = strtolower((string) $this->header('Content-Type', ''));
        if ($this->rawBody === '' || !str_contains($contentType, 'application/json')) {
            return [];
        }

        $decoded = json_decode($this->rawBody, true);

        return is_array($decoded) ? $decoded : [];
    }
}
