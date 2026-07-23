<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\AuthenticationException;
use App\Http\Request;
use App\Support\Session;

/**
 * Coupe la requête avant le contrôleur afin que les actions protégées ne
 * puissent jamais dépendre d'un contrôle d'authentification oublié.
 */
final class AuthMiddleware
{
    public function __construct(private readonly Session $session)
    {
    }

    public function handle(Request $request): void
    {
        if (!$this->session->isAuthenticated()) {
            throw new AuthenticationException();
        }
    }
}
