<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AuthenticationException;
use App\Exceptions\ValidationException;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Support\Session;
use App\Validation\LoginValidator;
use App\Validation\RegisterValidator;

// authentification et gestion de session utilisateur
final class AuthService
{
    private const DEFAULT_ROLE = 'CLIENT';
    private const AUTHENTICATION_FAILED_MESSAGE = 'Email ou mot de passe incorrect';

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly Session $session,
        private readonly RegisterValidator $registerValidator,
        private readonly LoginValidator $loginValidator,
    ) {
    }

    public function register(array $data): User
    {
        $this->registerValidator->validate($data);

        $email = trim((string) ($data['email'] ?? ''));
        if ($this->userRepository->emailExists($email)) {
            throw new ValidationException('Données d inscription invalides.', [
                'email' => 'Cette adresse e-mail est déjà utilisée.',
            ]);
        }

        $passwordHash = password_hash((string) $data['password'], PASSWORD_DEFAULT);
        return $this->userRepository->create(
            trim((string) ($data['full_name'] ?? '')),
            $email,
            $passwordHash,
            self::DEFAULT_ROLE,
        );
    }

    public function login(array $data): User
    {
        $this->loginValidator->validate($data);

        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $user = $this->userRepository->findByEmail($email);

        if ($user === null || !$user->verifyPassword($password) || !$user->isActive()) {
            throw new AuthenticationException(self::AUTHENTICATION_FAILED_MESSAGE);
        }

        $this->session->login($user->id());
        return $user;
    }

    public function logout(): void
    {
        $this->session->logout();
    }

    public function currentUser(): ?User
    {
        $userId = $this->session->userId();
        if ($userId === null) {
            return null;
        }

        return $this->userRepository->findById($userId);
    }
}