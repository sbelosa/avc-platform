<?php

declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$email = trim((string) ($argv[1] ?? 'admin@aloevera-centar.local'));
$password = (string) ($argv[2] ?? 'AVC-local-test-2026!');
$fullName = trim((string) ($argv[3] ?? 'AVC Local Admin'));

$dbHost = getenv('AVC_DB_HOST') ?: 'db';
$dbPort = (int) (getenv('AVC_DB_PORT') ?: 3306);
$dbName = getenv('AVC_DB_NAME') ?: 'avc_platform';
$dbUser = getenv('AVC_DB_USER') ?: 'avc_platform';
$dbPassword = getenv('AVC_DB_PASSWORD') ?: 'avc_platform';

$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
$mysqli->set_charset('utf8mb4');

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$statement = $mysqli->prepare(
    'INSERT INTO admin_users (email, password_hash, full_name, role, is_active)
     VALUES (?, ?, ?, ?, 1)
     ON DUPLICATE KEY UPDATE
        password_hash = VALUES(password_hash),
        full_name = VALUES(full_name),
        role = VALUES(role),
        is_active = 1'
);

$role = 'owner';
$statement->bind_param('ssss', $email, $passwordHash, $fullName, $role);
$statement->execute();
$statement->close();

$reportDirectory = '/var/www/html/storage/reports';
if (!is_dir($reportDirectory)) {
    mkdir($reportDirectory, 0775, true);
}

$report = [
    'generated_at' => gmdate(DATE_ATOM),
    'email' => $email,
    'full_name' => $fullName,
    'role' => $role,
    'password_written_to_file' => false,
];

file_put_contents(
    $reportDirectory . '/local_admin_credentials.json',
    json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL
);

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
