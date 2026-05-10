<?php

declare(strict_types=1);

namespace Avc\Services\Referral;

use Avc\Repositories\SettingsRepository;

final class ActiveForeverIdService
{
    private SettingsRepository $settingsRepository;

    public function __construct(private array $config)
    {
        $this->settingsRepository = new SettingsRepository($this->config);
    }

    public function getActiveForeverId(): string
    {
        $settings = $this->settingsRepository->getReferralSettings();
        $hasDatabaseValue = is_array($settings) && array_key_exists('active_forever_id', $settings);
        $databaseValue = trim((string) ($settings['active_forever_id'] ?? ''));

        if ($hasDatabaseValue) {
            return $databaseValue;
        }

        return trim((string) ($this->config['active_forever_id'] ?? ''));
    }

    public function hasActiveForeverId(): bool
    {
        return $this->getActiveForeverId() !== '';
    }

    public function getAdminNotificationEmail(): string
    {
        $settings = $this->settingsRepository->getReferralSettings();

        return trim((string) ($settings['admin_notification_email'] ?? ($this->config['admin_notification_email'] ?? 'admin@example.com')));
    }

    public function updateReferralSettings(string $activeForeverId, string $adminNotificationEmail, ?int $changedByAdminUserId = null, ?string $changeNote = null): void
    {
        $currentActiveForeverId = $this->getActiveForeverId();
        $currentSettings = $this->settingsRepository->getReferralSettings();

        $this->settingsRepository->putJsonSetting('referral', array_merge($currentSettings, [
            'active_forever_id' => trim($activeForeverId),
            'admin_notification_email' => trim($adminNotificationEmail),
        ]));

        if (trim($activeForeverId) !== '' && trim($activeForeverId) !== $currentActiveForeverId) {
            $this->logHistory(trim($activeForeverId), $changedByAdminUserId, $changeNote);
        }
    }

    public function updateDiscountSettings(array $payload, ?int $changedByAdminUserId = null, ?string $changeNote = null): void
    {
        $currentActiveForeverId = $this->getActiveForeverId();
        $currentSettings = $this->settingsRepository->getReferralSettings();
        $activeForeverId = trim((string) ($payload['active_forever_id'] ?? $currentSettings['active_forever_id'] ?? ''));

        $updatedSettings = array_merge($currentSettings, [
            'active_forever_id' => $activeForeverId,
            'admin_notification_email' => trim((string) ($payload['admin_notification_email'] ?? $currentSettings['admin_notification_email'] ?? '')),
            'fcc_discount_enabled' => (bool) ($payload['fcc_discount_enabled'] ?? false),
            'fcc_discount_percent' => max(1, min(80, (int) ($payload['fcc_discount_percent'] ?? $currentSettings['fcc_discount_percent'] ?? 15))),
            'fcc_short_url' => trim((string) ($payload['fcc_short_url'] ?? $currentSettings['fcc_short_url'] ?? '')),
            'fcc_shorten_url' => trim((string) ($payload['fcc_shorten_url'] ?? $currentSettings['fcc_shorten_url'] ?? '')),
            'fcc_referral_uuid' => trim((string) ($payload['fcc_referral_uuid'] ?? $currentSettings['fcc_referral_uuid'] ?? '')),
            'fcc_unique_ext_ref_id' => trim((string) ($payload['fcc_unique_ext_ref_id'] ?? $currentSettings['fcc_unique_ext_ref_id'] ?? '')),
            'fcc_discount_config_type' => trim((string) ($payload['fcc_discount_config_type'] ?? $currentSettings['fcc_discount_config_type'] ?? '11')),
            'fcc_title' => trim((string) ($payload['fcc_title'] ?? $currentSettings['fcc_title'] ?? 'FCC')),
        ]);

        if ($updatedSettings['fcc_shorten_url'] === '' && $updatedSettings['fcc_short_url'] !== '') {
            $parsedHost = parse_url($updatedSettings['fcc_short_url'], PHP_URL_HOST);
            $parsedPath = parse_url($updatedSettings['fcc_short_url'], PHP_URL_PATH);
            if (is_string($parsedHost) && $parsedHost !== '') {
                $updatedSettings['fcc_shorten_url'] = $parsedHost . (is_string($parsedPath) ? $parsedPath : '');
            }
        }

        $this->settingsRepository->putJsonSetting('referral', $updatedSettings);

        if (trim($activeForeverId) !== '' && trim($activeForeverId) !== $currentActiveForeverId) {
            $this->logHistory(trim($activeForeverId), $changedByAdminUserId, $changeNote);
        }
    }

    private function logHistory(string $activeForeverId, ?int $changedByAdminUserId, ?string $changeNote): void
    {
        $connection = \Avc\Core\Database::connection($this->config);
        if ($connection === null) {
            return;
        }

        $statement = $connection->prepare(
            'INSERT INTO referral_settings_history (active_forever_id, changed_by_admin_user_id, change_note)
             VALUES (?, ?, ?)'
        );
        $statement->bind_param('sis', $activeForeverId, $changedByAdminUserId, $changeNote);
        $statement->execute();
        $statement->close();
    }
}
