<?php

declare(strict_types=1);

namespace Avc\Core;

use mysqli;
use mysqli_sql_exception;

final class Database
{
    private static ?mysqli $connection = null;

    public static function connection(array $config): ?mysqli
    {
        if (self::$connection instanceof mysqli) {
            return self::$connection;
        }

        $db = $config['db'] ?? [];

        try {
            self::$connection = new mysqli(
                (string) ($db['host'] ?? 'db'),
                (string) ($db['user'] ?? 'avc_platform'),
                (string) ($db['password'] ?? 'avc_platform'),
                (string) ($db['name'] ?? 'avc_platform'),
                (int) ($db['port'] ?? 3306)
            );
            self::$connection->set_charset('utf8mb4');
        } catch (mysqli_sql_exception) {
            return null;
        }

        return self::$connection;
    }
}
