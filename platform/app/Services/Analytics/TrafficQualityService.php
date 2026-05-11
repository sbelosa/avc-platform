<?php

declare(strict_types=1);

namespace Avc\Services\Analytics;

use Avc\Core\Request;

final class TrafficQualityService
{
    private const BOT_USER_AGENT_PATTERN = '/(?:bot|crawl|crawler|spider|slurp|fetch|preview|index|scrape|monitor|uptime|validator|headless|lighthouse|pagespeed|googlebot|google-inspectiontool|adsbot-google|mediapartners-google|bingbot|bingpreview|duckduckbot|yandex|baiduspider|petalbot|bytespider|facebookexternalhit|facebot|twitterbot|linkedinbot|pinterest|whatsapp|telegrambot|discordbot|slackbot|ahrefs|semrush|mj12bot|dotbot|blexbot|screaming frog|sitebulb|gptbot|oai-searchbot|chatgpt-user|claudebot|claude-searchbot|perplexitybot|perplexity-user|ccbot|applebot|ia_archiver|archive\.org|curl|wget|python-requests|go-http-client|okhttp|java\/|node-fetch|axios)/i';

    public function shouldTrackOutboundClick(Request $request, bool $requireBrowserIntent = false): bool
    {
        if ($request->method() !== 'GET') {
            return false;
        }

        if ($this->isPrefetchRequest($request)) {
            return false;
        }

        if ($this->isLikelyBot($request)) {
            return false;
        }

        return !$requireBrowserIntent || $this->hasRecentBrowserIntent($request);
    }

    public function isLikelyBot(Request $request): bool
    {
        $userAgent = trim((string) $request->header('User-Agent', ''));
        if ($userAgent === '') {
            return true;
        }

        return preg_match(self::BOT_USER_AGENT_PATTERN, $userAgent) === 1;
    }

    private function isPrefetchRequest(Request $request): bool
    {
        $prefetchHeaders = [
            (string) $request->header('Purpose', ''),
            (string) $request->header('Sec-Purpose', ''),
            (string) $request->header('X-Moz', ''),
        ];

        foreach ($prefetchHeaders as $value) {
            if (str_contains(strtolower($value), 'prefetch')) {
                return true;
            }
        }

        return false;
    }

    private function hasRecentBrowserIntent(Request $request): bool
    {
        $timestamp = trim((string) $request->cookie('avc_outbound_intent', ''));
        if ($timestamp === '' || !ctype_digit($timestamp)) {
            return false;
        }

        $age = time() - (int) $timestamp;

        return $age >= -5 && $age <= 120;
    }
}
