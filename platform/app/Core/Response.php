<?php

declare(strict_types=1);

namespace Avc\Core;

final class Response
{
    public function raw(string $body, string $contentType = 'text/plain; charset=utf-8', int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: ' . $contentType);
        if ($this->shouldSendBody()) {
            echo $body;
        }
        exit;
    }

    public function html(string $body, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        if ($this->shouldSendBody()) {
            echo $body;
        }
        exit;
    }

    public function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        if ($this->shouldSendBody()) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    public function redirect(string $location, int $status = 302): never
    {
        http_response_code($status);
        header('Location: ' . $location);
        exit;
    }

    private function shouldSendBody(): bool
    {
        return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'HEAD';
    }
}
