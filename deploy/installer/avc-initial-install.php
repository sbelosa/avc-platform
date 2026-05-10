<?php

declare(strict_types=1);

set_time_limit(0);
ignore_user_abort(true);

$startedAt = microtime(true);
$appRoot = getenv('AVC_APP_ROOT') ?: dirname(__DIR__) . '/avc-platform';
$appRoot = rtrim((string) $appRoot, '/');
$envLoader = $appRoot . '/bootstrap/env.php';
$configFile = $appRoot . '/config/app.php';
$sqlFile = $appRoot . '/storage/imports/avc_platform_initial.sql.gz';
$lockFile = $appRoot . '/storage/imports/avc_platform_initial.lock.json';

respondIf(!is_file($envLoader) || !is_file($configFile), 503, [
    'ok' => false,
    'error' => 'AVC platform files are not deployed.',
]);

require_once $envLoader;
avc_load_environment($appRoot);

$expectedToken = trim((string) (getenv('AVC_INITIAL_INSTALL_TOKEN') ?: getenv('AVC_OPS_READONLY_KEY')));
$providedToken = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$confirm = trim((string) ($_GET['confirm'] ?? $_POST['confirm'] ?? ''));

respondIf($expectedToken === '' || $providedToken === '' || !hash_equals($expectedToken, $providedToken), 404, [
    'ok' => false,
    'error' => 'Not found.',
]);

respondIf($confirm !== 'IMPORT_AVC_DATABASE', 400, [
    'ok' => false,
    'error' => 'Missing confirmation.',
]);

respondIf(is_file($lockFile) && (string) ($_GET['force'] ?? $_POST['force'] ?? '') !== '1', 409, [
    'ok' => false,
    'error' => 'Initial database import was already completed. Use force=1 only if you intentionally want to re-import.',
]);

respondIf(!is_file($sqlFile) || !is_readable($sqlFile), 503, [
    'ok' => false,
    'error' => 'SQL import file is missing.',
]);

$config = require $configFile;
$db = (array) ($config['db'] ?? []);
$mysqli = mysqli_init();
mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 15);

try {
    $connected = @$mysqli->real_connect(
        (string) ($db['host'] ?? 'localhost'),
        (string) ($db['user'] ?? ''),
        (string) ($db['password'] ?? ''),
        (string) ($db['name'] ?? ''),
        (int) ($db['port'] ?? 3306)
    );

    respondIf(!$connected, 503, [
        'ok' => false,
        'error' => 'Could not connect to the production database.',
        'errno' => $mysqli->connect_errno,
    ]);

    $mysqli->set_charset('utf8mb4');
    $statementCount = importSqlGzip($mysqli, $sqlFile);
    $duration = round(microtime(true) - $startedAt, 2);

    @file_put_contents($lockFile, json_encode([
        'imported_at' => gmdate('c'),
        'statements' => $statementCount,
        'duration_seconds' => $duration,
        'sql_sha256' => hash_file('sha256', $sqlFile),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

    if ((string) ($_GET['cleanup'] ?? $_POST['cleanup'] ?? '1') === '1') {
        @unlink(__FILE__);
    }

    respond(200, [
        'ok' => true,
        'message' => 'AVC initial database import completed.',
        'statements' => $statementCount,
        'duration_seconds' => $duration,
    ]);
} catch (Throwable $exception) {
    respond(500, [
        'ok' => false,
        'error' => 'Database import failed.',
        'message' => $exception->getMessage(),
    ]);
}

function importSqlGzip(mysqli $mysqli, string $path): int
{
    $handle = gzopen($path, 'rb');
    if ($handle === false) {
        throw new RuntimeException('Could not open compressed SQL file.');
    }

    $buffer = '';
    $statementCount = 0;

    while (!gzeof($handle)) {
        $line = gzgets($handle);
        if ($line === false) {
            break;
        }

        $trimmed = trim($line);
        if ($buffer === '' && ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#'))) {
            continue;
        }

        $buffer .= $line;

        if (!statementLooksComplete($buffer)) {
            continue;
        }

        $sql = trim($buffer);
        $buffer = '';

        if ($sql === '') {
            continue;
        }

        if (!$mysqli->query($sql)) {
            throw new RuntimeException('SQL statement #' . ($statementCount + 1) . ' failed: ' . $mysqli->error);
        }

        while ($mysqli->more_results() && $mysqli->next_result()) {
            // Drain multi-result statements if the server returns any.
        }

        $statementCount++;
    }

    gzclose($handle);

    if (trim($buffer) !== '') {
        throw new RuntimeException('SQL import ended with an incomplete statement.');
    }

    return $statementCount;
}

function statementLooksComplete(string $sql): bool
{
    return str_ends_with(rtrim($sql), ';');
}

function respondIf(bool $condition, int $status, array $payload): void
{
    if ($condition) {
        respond($status, $payload);
    }
}

function respond(int $status, array $payload): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
