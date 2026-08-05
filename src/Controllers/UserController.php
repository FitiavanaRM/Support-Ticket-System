<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\UserRepository;
use App\Support\Session;

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
}
