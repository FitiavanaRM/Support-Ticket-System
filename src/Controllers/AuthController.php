<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Support\Session;
use App\Validation\LoginValidator;
use App\Validation\RegisterValidator;

// controle d'authentification
final class AuthController
{
    public function register(Request $request): Response
    {
        $user = $this->authService()->register($request->all());
        return Response::json([
            'status' => 'success',
            'data' => $user->toArray(),
        ], 201);
    }

    public function login(Request $request): Response
    {
        $user = $this->authService()->login($request->all());
        return Response::json([
            'status' => 'success',
            'data' => $user->toArray(),
        ]);
    }

    public function logout(Request $request): Response
    {
        $this->authService()->logout();
        return Response::json([
            'status' => 'success',
            'message' => 'Déconnexion réussie.',
        ]);
    }

    public function me(Request $request): Response
    {
        $user = $this->authService()->currentUser();
        if ($user === null) {
            return Response::json([
                'status' => 'error',
                'message' => 'Utilisateur non authentifié.',
            ], 401);
        }

        return Response::json([
            'status' => 'success',
            'data' => $user->toArray(),
        ]);
    }

    private function authService(): AuthService
    {
        return new AuthService(
            new UserRepository(),
            new Session(),
            new RegisterValidator(),
            new LoginValidator(),
        );
    }
}