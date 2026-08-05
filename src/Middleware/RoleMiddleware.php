<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\AuthenticationException;
use App\Exceptions\AuthorizationException;
use App\Http\Request;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Support\Session;

// recharge l'utilisateur 
final class RoleMiddleware
{
    /** @param list<string> $allowedRoles */
    public function __construct(
        private readonly Session $session,
        private readonly UserRepositoryInterface $userRepository,
        private readonly array $allowedRoles,
    ) {
    }

    public function handle(Request $request): void
    {
        $userId = $this->session->userId();
        if ($userId === null) {
            throw new AuthenticationException();
        }

        $user = $this->userRepository->findById($userId);
        if ($user === null || !$user->isActive()) {
            throw new AuthenticationException();
        }

        if (!in_array($user->toArray()['role'], $this->allowedRoles, true)) {
            throw new AuthorizationException();
        }
    }

    /** @return list<string> */
    public function allowedRoles(): array
    {
        return $this->allowedRoles;
    }
}
