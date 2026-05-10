<?php

declare(strict_types=1);

function avc_load_env_file(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (str_starts_with($line, 'export ')) {
            $line = trim(substr($line, 7));
        }

        $position = strpos($line, '=');
        if ($position === false) {
            continue;
        }

        $key = trim(substr($line, 0, $position));
        $value = trim(substr($line, $position + 1));

        if ($key === '' || preg_match('/^[A-Z0-9_]+$/', $key) !== 1) {
            continue;
        }

        if (getenv($key) !== false || array_key_exists($key, $_ENV) || array_key_exists($key, $_SERVER)) {
            continue;
        }

        $quote = $value[0] ?? '';
        if (($quote === '"' || $quote === "'") && str_ends_with($value, $quote)) {
            $value = substr($value, 1, -1);
            if ($quote === '"') {
                $value = str_replace(['\\n', '\\r', '\\t'], ["\n", "\r", "\t"], $value);
            }
        }

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function avc_load_environment(string $rootPath): void
{
    foreach (['.env.local', '.env.production', '.env'] as $file) {
        avc_load_env_file($rootPath . '/' . $file);
    }
}
