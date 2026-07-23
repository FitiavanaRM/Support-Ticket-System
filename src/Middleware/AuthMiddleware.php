<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\AuthenticationException;
use App\Http\Request;
use App\Support\Session;

// coupe la requete avant controleur
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
