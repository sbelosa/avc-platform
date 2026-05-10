<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);

require_once $rootPath . '/bootstrap/env.php';
avc_load_environment($rootPath);

spl_autoload_register(static function (string $class) use ($rootPath): void {
    $prefix = 'Avc\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $path = $rootPath . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($path)) {
        require_once $path;
    }
});

$config = require $rootPath . '/config/app.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('avc_local');
    session_start();
}

$request = new Avc\Core\Request($_SERVER, $_GET, $_POST, (string) file_get_contents('php://input'));
$response = new Avc\Core\Response();
$router = new Avc\Core\Router($config, $request, $response);

require $rootPath . '/routes/web.php';

return [$router, $request, $response];
