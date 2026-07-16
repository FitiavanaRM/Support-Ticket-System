<?php

declare(strict_types=1);

namespace App\Models;

// Représente un utilisateur dans le système
final class User
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $email,
        private readonly string $passwordHash,
        private readonly string $role,
        private readonly bool $isActive,
    ) {
    }

    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            name: (string)$row['name'],
            email: (string)$row['email'],
            passwordHash: (string)$row['password_hash'],
            role: (string)$row['role'],
            isActive: (bool)$row['is_active'],
        );
    }
    
    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->isActive,
        ];
    }
}