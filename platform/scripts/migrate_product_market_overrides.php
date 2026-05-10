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
    "CREATE TABLE IF NOT EXISTS product_market_overrides (
        product_market_override_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        translation_group_id BIGINT UNSIGNED NOT NULL,
        market_code VARCHAR(10) NOT NULL,
        destination_url VARCHAR(1000) NOT NULL,
        updated_by_admin_user_id BIGINT UNSIGNED NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_product_market_overrides_group_market (translation_group_id, market_code),
        INDEX idx_product_market_overrides_group (translation_group_id),
        INDEX idx_product_market_overrides_admin (updated_by_admin_user_id),
        CONSTRAINT fk_product_market_overrides_admin
            FOREIGN KEY (updated_by_admin_user_id) REFERENCES admin_users(admin_user_id)
            ON DELETE SET NULL
    )"
);

$statusResult = $mysqli->query("SHOW TABLES LIKE 'product_market_overrides'");
$tableExists = $statusResult instanceof mysqli_result && $statusResult->num_rows > 0;
$statusResult?->close();

$countResult = $mysqli->query('SELECT COUNT(*) AS total FROM product_market_overrides');
$countRow = $countResult ? $countResult->fetch_assoc() : null;
$countResult?->close();

echo json_encode([
    'table' => 'product_market_overrides',
    'exists' => $tableExists,
    'rows' => (int) ($countRow['total'] ?? 0),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
