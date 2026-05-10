<?php

declare(strict_types=1);

namespace Avc\Services\Notifications;

final class AdminLeadNotificationService
{
    public function __construct(
        private array $config,
        private string $recipientEmail
    ) {
    }

    public function notify(array $lead): bool
    {
        if ($this->recipientEmail === '') {
            return false;
        }

        $subjectPrefix = trim((string) ($lead['subject_prefix'] ?? 'AVC AI lead'));
        $leadType = trim((string) ($lead['lead_type'] ?? 'AI'));
        $subject = $subjectPrefix . ': ' . trim((string) ($lead['name'] ?? $lead['email'] ?? 'new contact'));
        $bodyLines = [
            'Novi ' . $leadType . ' lead je stigao u AVC admin.',
            '',
            'Ime: ' . trim((string) ($lead['name'] ?? '')),
            'Email: ' . trim((string) ($lead['email'] ?? '')),
            'Telefon: ' . trim((string) ($lead['phone'] ?? '')),
            'Jezik: ' . trim((string) ($lead['language_code'] ?? '')),
            'Država: ' . trim((string) ($lead['country_code'] ?? '')),
            'Izvorna stranica: ' . trim((string) ($lead['source_path'] ?? '')),
            'Preporučeni sadržaj: ' . trim((string) ($lead['recommended_content_path'] ?? '')),
            'Preporučeni market: ' . trim((string) ($lead['recommended_market_code'] ?? '')),
            '',
            'Pitanje:',
            trim((string) ($lead['lead_question'] ?? '')),
        ];

        $body = implode(PHP_EOL, $bodyLines);
        $result = (new MailTransportService($this->config))->send(
            $this->recipientEmail,
            $subject,
            $body,
            '<pre style="font-family:Arial,sans-serif;line-height:1.55;color:#17382a;white-space:pre-wrap;">' . htmlspecialchars($body, ENT_QUOTES, 'UTF-8') . '</pre>',
            ['tags' => ['avc_admin_lead', 'avc_' . strtolower($leadType)]]
        );

        return (bool) ($result['sent'] ?? false);
    }
}
