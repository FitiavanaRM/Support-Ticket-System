<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function emailExists(string $email): bool;
    /** @return list<int> */
    public function findAgentIds(): array;
    public function create(
        string $fullName,
        string $email,
        string $passwordHash,
        string $roleCode,
    ): User;
}
