<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\AuthenticationException;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Support\Session;
use App\Support\View;
use App\Validation\LoginValidator;
use App\Validation\RegisterValidator;

// controle d'authentification
final class AuthController
{
    public function register(Request $request): Response
    {
        try {
            $this->authService()->register($request->all());

            if ($request->acceptsHtml()) {
                return Response::redirect('/login');
            }

            return Response::json([
                'status' => 'success',
                'message' => 'Inscription réussie.',
            ], 201);
        } catch (ValidationException $exception) {
            if ($request->acceptsHtml()) {
                return Response::html(View::render(__DIR__ . '/../Views/auth/register.php', [
                    'errors' => array_values($exception->context()),
                    'old' => $request->all(),
                ]));
            }

            return Response::json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                ...$exception->context(),
            ], $exception->httpStatusCode());
        }
    }

    public function login(Request $request): Response
    {
        try {
            $this->authService()->login($request->all());

            if ($request->acceptsHtml()) {
                return Response::redirect('/dashboard');
            }

            return Response::json([
                'status' => 'success',
                'message' => 'Connexion réussie.',
            ]);
        } catch (AuthenticationException | ValidationException $exception) {
            if ($request->acceptsHtml()) {
                $errors = $exception instanceof ValidationException
                    ? array_values($exception->context())
                    : [$exception->getMessage()];

                return Response::html(View::render(__DIR__ . '/../Views/auth/login.php', [
                    'errors' => $errors,
                    'old' => ['email' => $request->input('email')],
                ]));
            }

            return Response::json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                ...$exception->context(),
            ], $exception->httpStatusCode());
        }
    }

    public function logout(Request $request): Response
    {
        $this->authService()->logout();

        if ($request->acceptsHtml()) {
            return Response::redirect('/login');
        }

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