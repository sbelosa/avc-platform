<?php

declare(strict_types=1);

namespace Avc\Services\Auth;

use Avc\Core\Request;
use Avc\Repositories\AdminUserRepository;

final class AdminAuthService
{
    private const SESSION_KEY = 'admin_user_id';

    public function __construct(
        private array $config,
        private Request $request
    ) {
    }

    public function check(): bool
    {
        $adminUserId = (int) $this->request->session(self::SESSION_KEY, 0);

        if ($adminUserId <= 0) {
            return false;
        }

        $user = (new AdminUserRepository($this->config))->findById($adminUserId);

        return $user !== null && (int) ($user['is_active'] ?? 0) === 1;
    }

    public function user(): ?array
    {
        $adminUserId = (int) $this->request->session(self::SESSION_KEY, 0);

        if ($adminUserId <= 0) {
            return null;
        }

        $user = (new AdminUserRepository($this->config))->findById($adminUserId);

        return $user !== null && (int) ($user['is_active'] ?? 0) === 1 ? $user : null;
    }

    public function attempt(string $email, string $password): bool
    {
        $email = trim(mb_strtolower($email));
        $user = (new AdminUserRepository($this->config))->findByEmail($email);

        if ($user === null || (int) ($user['is_active'] ?? 0) !== 1) {
            return false;
        }

        if (!password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            return false;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $this->request->setSession(self::SESSION_KEY, (int) $user['admin_user_id']);

        return true;
    }

    public function logout(): void
    {
        $this->request->forgetSession(self::SESSION_KEY);

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
