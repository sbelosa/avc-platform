<?php

declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$dbHost = getenv('AVC_DB_HOST') ?: 'db';
$dbPort = (int) (getenv('AVC_DB_PORT') ?: 3306);
$dbName = getenv('AVC_DB_NAME') ?: 'avc_platform';
$dbUser = getenv('AVC_DB_USER') ?: 'avc_platform';
$dbPassword = getenv('AVC_DB_PASSWORD') ?: 'avc_platform';

$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
$mysqli->set_charset('utf8mb4');

$mysqli->query(
    "CREATE TABLE IF NOT EXISTS discount_leads (
        discount_lead_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        content_translation_id BIGINT UNSIGNED NULL,
        discount_token VARCHAR(80) NOT NULL,
        language_code VARCHAR(10) NOT NULL,
        country_code VARCHAR(10) NULL,
        market_code VARCHAR(10) NULL,
        name VARCHAR(190) NULL,
        email VARCHAR(190) NULL,
        phone VARCHAR(80) NULL,
        consent_contact TINYINT(1) NOT NULL DEFAULT 0,
        product_title VARCHAR(255) NULL,
        source_path VARCHAR(500) NOT NULL,
        destination_url VARCHAR(1200) NOT NULL,
        fallback_url VARCHAR(1200) NULL,
        lead_status VARCHAR(50) NOT NULL DEFAULT 'new',
        admin_notified_at DATETIME NULL,
        customer_notified_at DATETIME NULL,
        visitor_hash VARCHAR(128) NULL,
        browser_language VARCHAR(120) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_discount_leads_token (discount_token),
        INDEX idx_discount_leads_status_created (lead_status, created_at),
        INDEX idx_discount_leads_language_country (language_code, country_code),
        INDEX idx_discount_leads_contact (email, phone),
        INDEX idx_discount_leads_translation (content_translation_id)
    )"
);

$columnResult = $mysqli->query("SHOW COLUMNS FROM discount_leads LIKE 'customer_notified_at'");
$hasCustomerNotifiedAt = $columnResult instanceof mysqli_result && $columnResult->num_rows > 0;
$columnResult?->close();

if (!$hasCustomerNotifiedAt) {
    $mysqli->query('ALTER TABLE discount_leads ADD COLUMN customer_notified_at DATETIME NULL AFTER admin_notified_at');
}

$defaults = [
    'active_forever_id' => getenv('AVC_ACTIVE_FOREVER_ID') ?: '',
    'admin_notification_email' => getenv('AVC_ADMIN_NOTIFICATION_EMAIL') ?: 'admin@example.com',
    'fcc_discount_enabled' => true,
    'fcc_discount_percent' => 15,
    'fcc_short_url' => 'https://thealoeveraco.shop/wf8afIMZ',
    'fcc_shorten_url' => 'thealoeveraco.shop/wf8afIMZ',
    'fcc_referral_uuid' => '7073bc6f-4b23-4219-99cd-a7a109e23835',
    'fcc_unique_ext_ref_id' => '6e568a04-f257-4f77-97bf-a2f4c20c3566',
    'fcc_discount_config_type' => '11',
    'fcc_title' => 'FCC',
];

$settingResult = $mysqli->query("SELECT setting_value_json FROM settings WHERE setting_key = 'referral' LIMIT 1");
$settingRow = $settingResult ? $settingResult->fetch_assoc() : null;
$settingResult?->close();
$current = [];

if ($settingRow && isset($settingRow['setting_value_json'])) {
    $decoded = json_decode((string) $settingRow['setting_value_json'], true);
    $current = is_array($decoded) ? $decoded : [];
}

$merged = array_merge($defaults, $current);
if (trim((string) ($merged['active_forever_id'] ?? '')) === '') {
    $merged['active_forever_id'] = '360000760944';
}

$json = json_encode($merged, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$statement = $mysqli->prepare(
    'INSERT INTO settings (setting_key, setting_value_json)
     VALUES (\'referral\', ?)
     ON DUPLICATE KEY UPDATE setting_value_json = VALUES(setting_value_json)'
);
$statement->bind_param('s', $json);
$statement->execute();
$statement->close();

$countResult = $mysqli->query('SELECT COUNT(*) AS total FROM discount_leads');
$countRow = $countResult ? $countResult->fetch_assoc() : null;
$countResult?->close();

echo json_encode([
    'table' => 'discount_leads',
    'rows' => (int) ($countRow['total'] ?? 0),
    'referral_settings' => [
        'active_forever_id' => (string) ($merged['active_forever_id'] ?? ''),
        'fcc_discount_enabled' => (bool) ($merged['fcc_discount_enabled'] ?? true),
        'fcc_discount_percent' => (int) ($merged['fcc_discount_percent'] ?? 15),
        'fcc_short_url' => (string) ($merged['fcc_short_url'] ?? ''),
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
