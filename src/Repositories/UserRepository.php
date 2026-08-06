<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use PDO;
use RuntimeException;

// Implémentation PDO de UserRepositoryInterface

final class UserRepository implements UserRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare($this->baseQuery() . ' WHERE u.id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row !== false ? User::fromDatabaseRow($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare($this->baseQuery() . ' WHERE u.email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row !== false ? User::fromDatabaseRow($row) : null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);

        return $stmt->fetchColumn() !== false;
    }

    /** @return list<int> */
    public function findAgentIds(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id FROM users u INNER JOIN roles r ON r.id = u.role_id WHERE r.code = :role_code AND u.is_active = 1 ORDER BY u.id ASC'
        );

        $stmt->execute(['role_code' => 'AGENT']);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }

    /** @return list<User> */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare($this->baseQuery() . ' ORDER BY u.id ASC');
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map(static fn (array $row): User => User::fromDatabaseRow($row), $rows);
    }

    public function create(string $fullName, string $email, string $passwordHash, string $roleCode): User
    {
        $roleStmt = $this->pdo->prepare('SELECT id FROM roles WHERE code = :code');
        $roleStmt->execute(['code' => $roleCode]);
        $roleId = $roleStmt->fetchColumn();

        if ($roleId === false) {
            throw new RuntimeException("Role inconnu en base : {$roleCode}");
        }

        $insert = $this->pdo->prepare(
            'INSERT INTO users (role_id, full_name, email, password_hash) VALUES (:role_id, :full_name, :email, :password_hash)'
        );
        $insert->execute([
            'role_id' => $roleId,
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        $newId = (int) $this->pdo->lastInsertId();
        $created = $this->findById($newId);

        if ($created === null) {
            throw new RuntimeException('Utilisateur cree introuvable');
        }

        return $created;
    }

    private function baseQuery(): string
    {
        return 'SELECT u.id, u.full_name, u.email, u.password_hash, u.is_active, r.code AS role_code FROM users u INNER JOIN roles r ON r.id = u.role_id';
    }
}