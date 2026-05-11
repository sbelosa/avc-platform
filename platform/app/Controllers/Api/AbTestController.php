<?php

declare(strict_types=1);

namespace Avc\Controllers\Api;

use Avc\Core\Request;
use Avc\Core\Response;
use Avc\Repositories\AbTestRepository;
use Avc\Services\Analytics\TrafficQualityService;

final class AbTestController
{
    public function __construct(
        private array $config,
        private Request $request,
        private Response $response
    ) {
    }

    public function event(): never
    {
        if ((new TrafficQualityService())->isLikelyBot($this->request)) {
            $this->response->json(['status' => 'ok', 'tracked' => false]);
        }

        $eventType = $this->normalizeKey((string) $this->request->input('event_type', ''));
        if (!in_array($eventType, ['impression', 'conversion', 'skip'], true)) {
            $this->response->json(['status' => 'error', 'message' => 'Unknown event type.'], 422);
        }

        $ok = (new AbTestRepository($this->config))->recordEvent([
            'test_key' => $this->request->input('test_key', ''),
            'variant_key' => $this->request->input('variant_key', ''),
            'event_type' => $eventType,
            'visitor_hash' => $this->buildVisitorHash(),
            'source_path' => $this->normalizePath((string) $this->request->input('source_path', '/')),
            'content_translation_id' => (int) $this->request->input('content_translation_id', 0) ?: null,
            'language_code' => strtolower(trim((string) $this->request->input('language_code', ''))),
            'metadata' => [
                'source' => $this->request->input('source', ''),
                'cta_position' => $this->request->input('cta_position', ''),
                'cta_variant' => $this->request->input('cta_variant', ''),
                'event_source' => $this->request->input('event_source', ''),
            ],
        ]);

        $this->response->json(['status' => $ok ? 'ok' : 'ignored', 'tracked' => $ok]);
    }

    private function buildVisitorHash(): string
    {
        $ip = trim((string) ($this->request->server('REMOTE_ADDR', '') ?: ''));
        $userAgent = trim((string) $this->request->header('User-Agent', ''));

        return hash('sha256', $ip . '|' . $userAgent);
    }

    private function normalizeKey(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]+/', '_', $value) ?? '';

        return mb_substr(trim($value, '_'), 0, 80);
    }

    private function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parsedPath = parse_url($path, PHP_URL_PATH);

            return is_string($parsedPath) && $parsedPath !== '' ? $parsedPath : '/';
        }

        return str_starts_with($path, '/') ? $path : '/';
    }
}
