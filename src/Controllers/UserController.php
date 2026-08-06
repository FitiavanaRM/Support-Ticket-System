<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\UserRepository;
use App\Support\Session;
use App\Support\View;

/**
 * Contrôleur administratif minimal pour exposer les ressources utilisateur
 * dont les rôles de supervision ou d'administration ont besoin.
 */
final class UserController
{
    public function agents(Request $request): Response
    {
        $userRepository = new UserRepository();

        return Response::json([
            'status' => 'success',
            'data' => [
                'agents' => $userRepository->findAgentIds(),
            ],
        ]);
    }

    public function createForm(Request $request): Response
    {
        return Response::html(View::render(__DIR__ . '/../Views/users/new.php'));
    }

    public function store(Request $request): Response
    {
        try {
            $fullName = trim((string) ($request->input('full_name') ?? ''));
            $email = trim((string) ($request->input('email') ?? ''));
            $password = (string) ($request->input('password') ?? '');
            $role = strtoupper(trim((string) ($request->input('role') ?? 'AGENT')));

            $errors = [];
            if ($fullName === '') {
                $errors[] = 'Le nom complet est requis.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse email invalide.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            }

            $userRepo = new UserRepository();
            if ($userRepo->emailExists($email)) {
                $errors[] = 'Cette adresse email est déjà utilisée.';
            }

            if (!in_array($role, ['CLIENT', 'AGENT', 'SUPERVISOR', 'ADMIN'], true)) {
                $errors[] = 'Rôle invalide.';
            }

            if (!empty($errors)) {
                if ($request->acceptsHtml()) {
                    return Response::html(View::render(__DIR__ . '/../Views/users/new.php', [
                        'errors' => $errors,
                        'old' => ['full_name' => $fullName, 'email' => $email, 'role' => $role],
                    ]));
                }

                return Response::json(['status' => 'error', 'message' => 'Données invalides.', 'errors' => $errors], 422);
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userRepo->create($fullName, $email, $hash, $role);

            if ($request->acceptsHtml()) {
                return Response::redirect('/users');
            }

            return Response::json(['status' => 'success', 'message' => 'Utilisateur créé.'], 201);
        } catch (\Throwable $e) {
            if ($request->acceptsHtml()) {
                return Response::html(View::render(__DIR__ . '/../Views/errors/error.php', [
                    'message' => $e->getMessage(), 'status' => 500,
                ]), 500);
            }

            return Response::json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, string $id): Response
    {
        $userRepo = new UserRepository();
        $user = $userRepo->findById((int) $id);

        if ($user === null) {
            if ($request->acceptsHtml()) {
                return Response::html(View::render(__DIR__ . '/../Views/errors/forbidden.php', [
                    'message' => 'Utilisateur introuvable.',
                ]), 404);
            }

            return Response::json(['status' => 'error', 'message' => 'Utilisateur introuvable.'], 404);
        }

        return Response::html(View::render(__DIR__ . '/../Views/users/show.php', [
            'user' => $user,
        ]));
    }
}
