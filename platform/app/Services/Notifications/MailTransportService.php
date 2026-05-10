<?php

declare(strict_types=1);

namespace Avc\Services\Notifications;

final class MailTransportService
{
    public function __construct(private array $config)
    {
    }

    public function send(mixed $to, string $subject, string $textContent, string $htmlContent = '', array $options = []): array
    {
        $recipients = $this->normalizeRecipients($to);
        if ($recipients === []) {
            return $this->failed('log', 'Missing recipient email.');
        }

        $mailConfig = (array) ($this->config['mail'] ?? []);
        $transport = strtolower(trim((string) ($mailConfig['transport'] ?? 'log'))) ?: 'log';

        if ($transport === 'brevo_api') {
            return $this->sendViaBrevo($recipients, $subject, $textContent, $htmlContent, $options, $mailConfig);
        }

        if ($transport === 'smtp') {
            return $this->sendViaSmtp($recipients, $subject, $textContent, $htmlContent, $mailConfig);
        }

        if ($transport === 'mail') {
            return $this->sendViaPhpMail($recipients, $subject, $textContent, $mailConfig);
        }

        $this->log('outbound-mail-fallback.log', [
            'transport' => $transport,
            'to' => array_column($recipients, 'email'),
            'subject' => $subject,
            'text_content' => $textContent,
        ]);

        return $this->failed($transport, 'Mail transport is not configured.');
    }

    private function sendViaBrevo(array $recipients, string $subject, string $textContent, string $htmlContent, array $options, array $mailConfig): array
    {
        $apiKey = trim((string) ($mailConfig['brevo_api_key'] ?? ''));
        $fromEmail = trim((string) ($mailConfig['from_email'] ?? ''));
        $fromName = trim((string) ($mailConfig['from_name'] ?? 'Aloe Vera Centar'));

        if ($apiKey === '' || $fromEmail === '') {
            $this->log('mail-transport.log', [
                'transport' => 'brevo_api',
                'error' => 'Brevo API key or sender email is missing.',
                'subject' => $subject,
                'to' => array_column($recipients, 'email'),
            ]);

            return $this->failed('brevo_api', 'Brevo API key or sender email is missing.');
        }

        if (!function_exists('curl_init')) {
            return $this->failed('brevo_api', 'PHP curl extension is not available.');
        }

        $payload = [
            'sender' => [
                'name' => $fromName,
                'email' => $fromEmail,
            ],
            'to' => $recipients,
            'subject' => $subject,
            'textContent' => $textContent,
            'htmlContent' => $htmlContent !== '' ? $htmlContent : $this->htmlFromText($textContent),
        ];

        $replyToEmail = trim((string) ($options['reply_to_email'] ?? $mailConfig['reply_to_email'] ?? ''));
        if ($replyToEmail !== '' && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
            $payload['replyTo'] = [
                'email' => $replyToEmail,
                'name' => trim((string) ($options['reply_to_name'] ?? $mailConfig['reply_to_name'] ?? $fromName)),
            ];
        }

        if (!empty($options['tags']) && is_array($options['tags'])) {
            $payload['tags'] = array_values(array_filter(array_map(static fn ($tag): string => (string) $tag, $options['tags'])));
        }

        if (!empty($options['headers']) && is_array($options['headers'])) {
            $payload['headers'] = $options['headers'];
        }

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($jsonPayload === false) {
            return $this->failed('brevo_api', 'Could not encode Brevo email payload.');
        }

        $baseUrl = rtrim((string) ($mailConfig['brevo_api_base_url'] ?? 'https://api.brevo.com/v3'), '/');
        $curlHandle = curl_init($baseUrl . '/smtp/email');
        curl_setopt_array($curlHandle, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'api-key: ' . $apiKey,
                'content-type: application/json',
            ],
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $responseBody = curl_exec($curlHandle);
        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curlHandle);
        curl_close($curlHandle);

        $responseJson = is_string($responseBody) ? json_decode($responseBody, true) : null;
        $messageId = is_array($responseJson) ? (string) ($responseJson['messageId'] ?? '') : '';
        $sent = $curlError === '' && $statusCode >= 200 && $statusCode < 300;

        if (!$sent) {
            $this->log('mail-transport.log', [
                'transport' => 'brevo_api',
                'status_code' => $statusCode,
                'curl_error' => $curlError,
                'response_body' => $responseBody,
                'subject' => $subject,
                'to' => array_column($recipients, 'email'),
            ]);

            if ($this->hasSmtpConfig($mailConfig)) {
                $smtpResult = $this->sendViaSmtp($recipients, $subject, $textContent, $htmlContent, $mailConfig);
                if (!empty($smtpResult['sent'])) {
                    $smtpResult['transport'] = 'smtp_fallback';

                    return $smtpResult;
                }
            }

            return $this->failed('brevo_api', trim('Brevo API request failed. HTTP ' . $statusCode . ' ' . $curlError));
        }

        return [
            'sent' => true,
            'transport' => 'brevo_api',
            'message_id' => $messageId,
            'error' => '',
        ];
    }

    private function sendViaPhpMail(array $recipients, string $subject, string $textContent, array $mailConfig): array
    {
        $fromEmail = trim((string) ($mailConfig['from_email'] ?? 'noreply@aloevera-centar.com'));
        $fromName = trim((string) ($mailConfig['from_name'] ?? 'Aloe Vera Centar'));
        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
        ];
        $sent = true;

        foreach ($recipients as $recipient) {
            $sent = @mail($recipient['email'], $subject, $textContent, implode("\r\n", $headers)) && $sent;
        }

        return $sent ? [
            'sent' => true,
            'transport' => 'mail',
            'message_id' => '',
            'error' => '',
        ] : $this->failed('mail', 'PHP mail() returned false.');
    }

    private function sendViaSmtp(array $recipients, string $subject, string $textContent, string $htmlContent, array $mailConfig): array
    {
        $host = trim((string) ($mailConfig['smtp_host'] ?? ''));
        $port = (int) ($mailConfig['smtp_port'] ?? 587);
        $encryption = strtolower(trim((string) ($mailConfig['smtp_encryption'] ?? 'tls')));
        $username = trim((string) ($mailConfig['smtp_username'] ?? ''));
        $password = (string) ($mailConfig['smtp_password'] ?? '');
        $fromEmail = trim((string) ($mailConfig['from_email'] ?? ''));
        $fromName = trim((string) ($mailConfig['from_name'] ?? 'Aloe Vera Centar'));

        if (!$this->hasSmtpConfig($mailConfig) || $fromEmail === '') {
            return $this->failed('smtp', 'SMTP configuration is incomplete.');
        }

        $remote = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
        if (!is_resource($socket)) {
            $this->log('mail-transport.log', [
                'transport' => 'smtp',
                'error' => $errstr,
                'errno' => $errno,
                'host' => $host,
                'port' => $port,
            ]);

            return $this->failed('smtp', 'SMTP connection failed: ' . $errstr);
        }

        stream_set_timeout($socket, 20);

        try {
            $this->smtpExpect($socket, [220]);
            $this->smtpCommand($socket, 'EHLO aloevera-centar.local', [250]);

            if ($encryption === 'tls') {
                $this->smtpCommand($socket, 'STARTTLS', [220]);
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new \RuntimeException('SMTP STARTTLS negotiation failed.');
                }
                $this->smtpCommand($socket, 'EHLO aloevera-centar.local', [250]);
            }

            if ($username !== '' || $password !== '') {
                $this->smtpCommand($socket, 'AUTH LOGIN', [334]);
                $this->smtpCommand($socket, base64_encode($username), [334]);
                $this->smtpCommand($socket, base64_encode($password), [235]);
            }

            $this->smtpCommand($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
            foreach ($recipients as $recipient) {
                $this->smtpCommand($socket, 'RCPT TO:<' . $recipient['email'] . '>', [250, 251]);
            }
            $this->smtpCommand($socket, 'DATA', [354]);
            fwrite($socket, $this->buildSmtpMessage($recipients, $fromEmail, $fromName, $subject, $textContent, $htmlContent) . "\r\n.\r\n");
            $this->smtpExpect($socket, [250]);
            $this->smtpCommand($socket, 'QUIT', [221]);
            fclose($socket);

            return [
                'sent' => true,
                'transport' => 'smtp',
                'message_id' => '',
                'error' => '',
            ];
        } catch (\Throwable $exception) {
            if (is_resource($socket)) {
                fwrite($socket, "QUIT\r\n");
                fclose($socket);
            }

            $this->log('mail-transport.log', [
                'transport' => 'smtp',
                'error' => $exception->getMessage(),
                'host' => $host,
                'port' => $port,
                'subject' => $subject,
                'to' => array_column($recipients, 'email'),
            ]);

            return $this->failed('smtp', $exception->getMessage());
        }
    }

    private function hasSmtpConfig(array $mailConfig): bool
    {
        return trim((string) ($mailConfig['smtp_host'] ?? '')) !== ''
            && trim((string) ($mailConfig['smtp_username'] ?? '')) !== ''
            && (string) ($mailConfig['smtp_password'] ?? '') !== '';
    }

    private function buildSmtpMessage(array $recipients, string $fromEmail, string $fromName, string $subject, string $textContent, string $htmlContent): string
    {
        $boundary = 'avc_' . bin2hex(random_bytes(12));
        $toHeader = implode(', ', array_map(static fn (array $recipient): string => '<' . $recipient['email'] . '>', $recipients));
        $headers = [
            'From: ' . $this->formatMailbox($fromEmail, $fromName),
            'To: ' . $toHeader,
            'Subject: ' . $this->encodeHeader($subject),
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        return implode("\r\n", $headers)
            . "\r\n\r\n--" . $boundary
            . "\r\nContent-Type: text/plain; charset=UTF-8"
            . "\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
            . $this->dotStuff($textContent)
            . "\r\n--" . $boundary
            . "\r\nContent-Type: text/html; charset=UTF-8"
            . "\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
            . $this->dotStuff($htmlContent !== '' ? $htmlContent : $this->htmlFromText($textContent))
            . "\r\n--" . $boundary . "--";
    }

    private function smtpCommand(mixed $socket, string $command, array $expectedCodes): string
    {
        fwrite($socket, $command . "\r\n");

        return $this->smtpExpect($socket, $expectedCodes);
    }

    private function smtpExpect(mixed $socket, array $expectedCodes): string
    {
        $response = '';
        do {
            $line = fgets($socket, 8192);
            if ($line === false) {
                throw new \RuntimeException('SMTP server closed the connection.');
            }
            $response .= $line;
        } while (isset($line[3]) && $line[3] === '-');

        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            throw new \RuntimeException('Unexpected SMTP response: ' . trim($response));
        }

        return $response;
    }

    private function formatMailbox(string $email, string $name): string
    {
        return ($name !== '' ? $this->encodeHeader($name) . ' ' : '') . '<' . $email . '>';
    }

    private function encodeHeader(string $value): string
    {
        $value = str_replace(["\r", "\n"], '', $value);
        if (preg_match('/^[\\x20-\\x7E]*$/', $value) === 1) {
            return $value;
        }

        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function dotStuff(string $content): string
    {
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", $content);
        $lines = array_map(static fn (string $line): string => str_starts_with($line, '.') ? '.' . $line : $line, $lines);

        return implode("\r\n", $lines);
    }

    private function normalizeRecipients(mixed $to): array
    {
        $addresses = is_array($to) ? $to : [$to];
        $recipients = [];

        foreach ($addresses as $address) {
            $email = '';
            $name = '';

            if (is_array($address)) {
                $email = strtolower(trim((string) ($address['email'] ?? '')));
                $name = trim((string) ($address['name'] ?? ''));
            } else {
                $email = strtolower(trim((string) $address));
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $recipient = ['email' => $email];
            if ($name !== '') {
                $recipient['name'] = $name;
            }
            $recipients[] = $recipient;
        }

        return $recipients;
    }

    private function htmlFromText(string $textContent): string
    {
        return '<div style="font-family:Arial,sans-serif;line-height:1.6;color:#17382a;">'
            . nl2br(htmlspecialchars($textContent, ENT_QUOTES, 'UTF-8'))
            . '</div>';
    }

    private function failed(string $transport, string $error): array
    {
        return [
            'sent' => false,
            'transport' => $transport,
            'message_id' => '',
            'error' => $error,
        ];
    }

    private function log(string $fileName, array $payload): void
    {
        $logDirectory = ($this->config['storage_path'] ?? dirname(__DIR__, 3) . '/storage') . '/logs';
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0775, true);
        }

        file_put_contents(
            $logDirectory . '/' . $fileName,
            '[' . gmdate(DATE_ATOM) . '] ' . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND
        );
    }
}
