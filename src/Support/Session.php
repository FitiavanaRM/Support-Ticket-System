<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

// session d'utilisateur, stockée dans $_SESSION
final class Session
{
    private const USER_ID = 'user_id';

    public function __construct()
    {
        $this->ensureSessionStarted();
    }

    public function login(int $userId): void
    {
        $this->ensureSessionStarted();
        $_SESSION[self::USER_ID] = $userId;
        session_regenerate_id(true);
    }

    public function logout(): void
    {
        $this->ensureSessionStarted();
        unset($_SESSION[self::USER_ID]);
        session_regenerate_id(true);
    }

    public function isAuthenticated(): bool
    {
        $this->ensureSessionStarted();
        return isset($_SESSION[self::USER_ID]) && is_numeric($_SESSION[self::USER_ID]);
    }

    public function userId(): ?int
    {
        $this->ensureSessionStarted();

        if (!$this->isAuthenticated()) {
            return null;
        }

        return (int) $_SESSION[self::USER_ID];
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException('La session doit etre demarree avant d’utiliser Session.');
        }
    }
}