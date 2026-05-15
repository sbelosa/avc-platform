<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class SettingsRepository
{
    private const LEGACY_ADMIN_NOTIFICATION_EMAIL = 'belosa.flp@bmail.com';
    private const CURRENT_ADMIN_NOTIFICATION_EMAIL = 'belosa.flp@gmail.com';

    public function __construct(private array $config)
    {
    }

    public function getJsonSetting(string $key): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        $statement = $connection->prepare('SELECT setting_value_json FROM settings WHERE setting_key = ? LIMIT 1');
        $statement->bind_param('s', $key);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        if (!$result || !isset($result['setting_value_json'])) {
            return null;
        }

        $decoded = json_decode((string) $result['setting_value_json'], true);

        return is_array($decoded) ? $decoded : null;
    }

    public function putJsonSetting(string $key, array $payload): void
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return;
        }

        if ($key === 'referral' && array_key_exists('admin_notification_email', $payload)) {
            $payload['admin_notification_email'] = $this->normalizeAdminNotificationEmail((string) $payload['admin_notification_email']);
        }

        $statement = $connection->prepare(
            'INSERT INTO settings (setting_key, setting_value_json)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value_json = VALUES(setting_value_json)'
        );
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $statement->bind_param('ss', $key, $json);
        $statement->execute();
        $statement->close();
    }

    public function getReferralSettings(): array
    {
        $settings = $this->getJsonSetting('referral') ?? [];
        $rawAdminNotificationEmail = (string) ($settings['admin_notification_email'] ?? ($this->config['admin_notification_email'] ?? 'admin@example.com'));
        $adminNotificationEmail = $this->normalizeAdminNotificationEmail(
            $rawAdminNotificationEmail
        );

        if (isset($settings['admin_notification_email']) && $adminNotificationEmail !== trim($rawAdminNotificationEmail)) {
            $settings['admin_notification_email'] = $adminNotificationEmail;
            $this->putJsonSetting('referral', $settings);
        }

        return array_merge($this->defaultReferralSettings(), $settings, [
            'active_forever_id' => trim((string) ($settings['active_forever_id'] ?? ($this->config['active_forever_id'] ?? ''))),
            'admin_notification_email' => $adminNotificationEmail,
            'fcc_discount_enabled' => (bool) ($settings['fcc_discount_enabled'] ?? true),
            'fcc_discount_percent' => (int) ($settings['fcc_discount_percent'] ?? 15),
            'fcc_short_url' => trim((string) ($settings['fcc_short_url'] ?? 'https://thealoeveraco.shop/wf8afIMZ')),
            'fcc_shorten_url' => trim((string) ($settings['fcc_shorten_url'] ?? 'thealoeveraco.shop/wf8afIMZ')),
            'fcc_referral_uuid' => trim((string) ($settings['fcc_referral_uuid'] ?? '7073bc6f-4b23-4219-99cd-a7a109e23835')),
            'fcc_unique_ext_ref_id' => trim((string) ($settings['fcc_unique_ext_ref_id'] ?? '6e568a04-f257-4f77-97bf-a2f4c20c3566')),
            'fcc_discount_config_type' => trim((string) ($settings['fcc_discount_config_type'] ?? '11')),
            'fcc_title' => trim((string) ($settings['fcc_title'] ?? 'FCC')),
        ]);
    }

    public function defaultReferralSettings(): array
    {
        return [
            'active_forever_id' => trim((string) ($this->config['active_forever_id'] ?? '')),
            'admin_notification_email' => $this->normalizeAdminNotificationEmail((string) ($this->config['admin_notification_email'] ?? 'admin@example.com')),
            'fcc_discount_enabled' => true,
            'fcc_discount_percent' => 15,
            'fcc_short_url' => 'https://thealoeveraco.shop/wf8afIMZ',
            'fcc_shorten_url' => 'thealoeveraco.shop/wf8afIMZ',
            'fcc_referral_uuid' => '7073bc6f-4b23-4219-99cd-a7a109e23835',
            'fcc_unique_ext_ref_id' => '6e568a04-f257-4f77-97bf-a2f4c20c3566',
            'fcc_discount_config_type' => '11',
            'fcc_title' => 'FCC',
        ];
    }

    private function normalizeAdminNotificationEmail(string $email): string
    {
        $email = trim($email);
        if (strcasecmp($email, self::LEGACY_ADMIN_NOTIFICATION_EMAIL) === 0) {
            return self::CURRENT_ADMIN_NOTIFICATION_EMAIL;
        }

        return $email;
    }
}
