<?php

declare(strict_types=1);

$appRoot = getenv('AVC_APP_ROOT') ?: dirname(__DIR__) . '/avc-platform';
$bootstrap = rtrim($appRoot, '/') . '/bootstrap/app.php';

if (!is_file($bootstrap)) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Aloe Vera Centar deployment is not complete.';
    exit;
}

chdir(dirname($bootstrap));

[$router] = require $bootstrap;
$router->dispatch();
