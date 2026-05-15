<?php

declare(strict_types=1);

$adminNotificationEmail = trim((string) (getenv('AVC_ADMIN_NOTIFICATION_EMAIL') ?: 'admin@example.com'));
if (strcasecmp($adminNotificationEmail, 'belosa.flp@bmail.com') === 0) {
    $adminNotificationEmail = 'belosa.flp@gmail.com';
}

return [
    'app_name' => getenv('AVC_APP_NAME') ?: 'AVC Platform',
    'app_root' => dirname(__DIR__),
    'base_url' => getenv('AVC_BASE_URL') ?: 'https://aloevera-centar.com',
    'default_language' => getenv('AVC_DEFAULT_LANGUAGE') ?: 'hr',
    'supported_languages' => array_values(array_filter(array_map('trim', explode(',', getenv('AVC_SUPPORTED_LANGUAGES') ?: 'hr,en,sl')))),
    'admin_notification_email' => $adminNotificationEmail,
    'active_forever_id' => getenv('AVC_ACTIVE_FOREVER_ID') ?: '',
    'google_tag_id' => getenv('AVC_GOOGLE_TAG_ID') ?: 'G-WPTBTHXN8H',
    'storage_path' => dirname(__DIR__) . '/storage',
    'ops_readonly' => [
        'enabled' => in_array(strtolower((string) getenv('AVC_OPS_READONLY_ENABLED')), ['1', 'true', 'yes', 'on'], true),
        'key' => getenv('AVC_OPS_READONLY_KEY') ?: '',
    ],
    'mail' => [
        'transport' => getenv('AVC_MAIL_TRANSPORT') ?: ((getenv('AVC_BREVO_API_KEY') ?: getenv('BREVO_API_KEY')) ? 'brevo_api' : 'log'),
        'from_email' => getenv('AVC_MAIL_FROM_EMAIL') ?: 'noreply@aloevera-centar.com',
        'from_name' => getenv('AVC_MAIL_FROM_NAME') ?: 'Aloe Vera Centar',
        'reply_to_email' => getenv('AVC_MAIL_REPLY_TO_EMAIL') ?: (getenv('AVC_MAIL_FROM_EMAIL') ?: ''),
        'reply_to_name' => getenv('AVC_MAIL_REPLY_TO_NAME') ?: (getenv('AVC_MAIL_FROM_NAME') ?: 'Aloe Vera Centar'),
        'brevo_api_key' => getenv('AVC_BREVO_API_KEY') ?: (getenv('BREVO_API_KEY') ?: ''),
        'brevo_api_base_url' => rtrim(getenv('AVC_BREVO_API_BASE_URL') ?: (getenv('BREVO_API_BASE_URL') ?: 'https://api.brevo.com/v3'), '/'),
        'smtp_host' => getenv('AVC_SMTP_HOST') ?: '',
        'smtp_port' => (int) (getenv('AVC_SMTP_PORT') ?: 587),
        'smtp_encryption' => getenv('AVC_SMTP_ENCRYPTION') ?: 'tls',
        'smtp_username' => getenv('AVC_SMTP_USERNAME') ?: '',
        'smtp_password' => getenv('AVC_SMTP_PASSWORD') ?: '',
    ],
    'db' => [
        'host' => getenv('AVC_DB_HOST') ?: 'db',
        'port' => (int) (getenv('AVC_DB_PORT') ?: 3306),
        'name' => getenv('AVC_DB_NAME') ?: 'avc_platform',
        'user' => getenv('AVC_DB_USER') ?: 'avc_platform',
        'password' => getenv('AVC_DB_PASSWORD') ?: 'avc_platform',
    ],
];
