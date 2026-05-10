<?php

declare(strict_types=1);

namespace Avc\Repositories;

use Avc\Core\Database;

final class AdminUserRepository
{
    public function __construct(private array $config)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        $statement = $connection->prepare(
            'SELECT admin_user_id, email, password_hash, full_name, role, is_active
             FROM admin_users
             WHERE email = ?
             LIMIT 1'
        );
        $statement->bind_param('s', $email);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ?: null;
    }

    public function findById(int $adminUserId): ?array
    {
        $connection = Database::connection($this->config);
        if ($connection === null) {
            return null;
        }

        $statement = $connection->prepare(
            'SELECT admin_user_id, email, full_name, role, is_active
             FROM admin_users
             WHERE admin_user_id = ?
             LIMIT 1'
        );
        $statement->bind_param('i', $adminUserId);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ?: null;
    }
}
