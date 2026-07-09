<?php

declare(strict_types=1);

namespace Database;

use PDO;
use PDOException;
use RuntimeException;
use Throwable;
use App\Support\Env;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// seeding base de données
class DatabaseSeeder
{
    private PDO $pdo;
    private string $demoPasswordHash;
    private array $demoUsers = [
        ['role' => 'CLIENT', 'name' => 'Rotsy Client', 'email' => 'client@demo.test'],
        ['role' => 'AGENT','name' => 'Nomena Agent', 'email' => 'agent@demo.test'],
        ['role' => 'SUPERVISOR', 'name' => 'Hery Superviseur',  'email' => 'superviseur@demo.test'],
        ['role' => 'ADMIN','name' => 'Fitiavana Admin', 'email' => 'admin@demo.test'],
    ];

    public function __construct()
    {
        $this->bootstrapEnvironment();
        $this->pdo = $this->initializeConnection();
        $this->demoPasswordHash = password_hash('Password123!', PASSWORD_DEFAULT);
    }

    // charge l'environnement et bloque l'exécution en production
    private function bootstrapEnvironment(): void
    {
        Env::load(dirname(__DIR__) . '/.env');

        if (Env::get('APP_ENV', 'local') === 'production') {
            fwrite(STDERR, "Erreur critique : impossible d'exécuter un seeder en production.\n");
            exit(1);
        }
    }

    // initialise la connexion
    private function initializeConnection(): PDO
    {
        $host = Env::get('DB_HOST', '127.0.0.1');
        $port = Env::get('DB_PORT', '3306');
        $dbname = Env::get('DB_DATABASE', 'support_tickets');
        $charset = Env::get('DB_CHARSET', 'utf8mb4');
        $user = Env::get('DB_USERNAME', 'root');
        $pass = Env::get('DB_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            fwrite(STDERR, "Erreur de connexion : {$e->getMessage()}\n");
            fwrite(STDERR, "Exécutez d'abord les migrations via 'php database/migrate.php'\n");
            exit(1);
        }
    }

    // point d'entree
    public function run(): void
    {
        $this->pdo->beginTransaction();

        try {
            $this->seedRoles();
            $this->seedCategories();
            $this->seedPriorities();

            $userIds = $this->seedUsers();
            $this->seedSampleTicket($userIds);

            $this->pdo->commit();
            $this->displaySuccessMessage();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            fwrite(STDERR, "Échec du seeding (transaction annulée) : {$e->getMessage()}\n");
            exit(1);
        }
    }

    // creation role
    private function seedRoles(): void
    {
        foreach (['CLIENT', 'AGENT', 'SUPERVISOR', 'ADMIN'] as $roleCode) {
            $this->ensureLookupValue('roles', 'code', $roleCode, 'name', $roleCode);
        }
    }

    // creation categorie
    private function seedCategories(): void
    {
        $this->ensureLookupValue('categories', 'name', 'Logiciel');
    }

    // creation priorites
    private function seedPriorities(): void
    {
        $this->ensureLookupValue('priorities', 'name', 'Moyenne');
    }

    // insertion de valeur dans une table si elle n'existe pas
    private function ensureLookupValue(
        string $table,
        string $keyColumn,
        string $keyValue,
        ?string $labelColumn = null,
        ?string $labelValue = null
    ): int {
        $stmt = $this->pdo->prepare("SELECT id FROM {$table} WHERE {$keyColumn} = :value");
        $stmt->execute(['value' => $keyValue]);

        $existingId = $stmt->fetchColumn();
        if ($existingId !== false) {
            return (int) $existingId;
        }

        $columns = $this->getTableColumns($table);

        $sql = "INSERT INTO {$table} ";
        $params = ['value' => $keyValue];

        if ($labelColumn !== null && $labelValue !== null && in_array($labelColumn, $columns, true)) {
            $sql .= "({$keyColumn}, {$labelColumn}) VALUES (:value, :label)";
            $params['label'] = $labelValue;
        } else {
            $sql .= "({$keyColumn}) VALUES (:value)";
        }

        $insert = $this->pdo->prepare($sql);
        $insert->execute($params);

        return (int) $this->pdo->lastInsertId();
    }

    // récupère les colonnes d'une table
    private function getTableColumns(string $table): array
    {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM {$table}");
        $columns = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $row['Field'];
        }

        return $columns;
    }

    // insère les utilisateurs de démonstration et retourne leurs ID
    private function seedUsers(): array
    {
        $roleStmt = $this->pdo->prepare('SELECT id FROM roles WHERE code = :code');
        $userInsert = $this->pdo->prepare(
            'INSERT IGNORE INTO users (role_id, full_name, email, password_hash)
             VALUES (:role_id, :full_name, :email, :password_hash)'
        );
        $idStmt = $this->pdo->prepare('SELECT id FROM users WHERE email = :email');

        $userIds = [];

        foreach ($this->demoUsers as $demoUser) {
            $roleStmt->execute(['code' => $demoUser['role']]);
            $roleId = $roleStmt->fetchColumn();

            if ($roleId === false) {
                throw new RuntimeException("Rôle introuvable : {$demoUser['role']}.");
            }

            $userInsert->execute([
                'role_id' => $roleId,
                'full_name' => $demoUser['name'],
                'email' => $demoUser['email'],
                'password_hash' => $this->demoPasswordHash,
            ]);

            $idStmt->execute(['email' => $demoUser['email']]);
            $userIds[$demoUser['role']] = $idStmt->fetchColumn();
        }

        return $userIds;
    }

    // insère un ticket de démonstration pour le client
    private function seedSampleTicket(array $userIds): void
    {
        $categoryId = $this->pdo->query("SELECT id FROM categories WHERE name = 'Logiciel'")->fetchColumn();
        $priorityId = $this->pdo->query("SELECT id FROM priorities WHERE name = 'Moyenne'")->fetchColumn();

        if (!$categoryId || !$priorityId) {
            throw new RuntimeException("Données de référence manquantes (catégories ou priorités).");
        }

        $ticketInsert = $this->pdo->prepare(
            'INSERT INTO tickets (subject, description, client_id, category_id, priority_id, status)
             VALUES (:subject, :description, :client_id, :category_id, :priority_id, :status)'
        );

        $ticketInsert->execute([
            'subject' => 'Impossible de me connecter à mon compte',
            'description' => "J'ai un message d'erreur dès que j'entre mon mot de passe.",
            'client_id' => $userIds['CLIENT'],
            'category_id' => $categoryId,
            'priority_id' => $priorityId,
            'status' => 'open',
        ]);
    }

    // affiche un message de succès avec les comptes créés
    private function displaySuccessMessage(): void
    {
        echo "Seeder exécuté avec succès.\n";
        echo "Comptes de démo créés (mot de passe : Password123!) :\n";

        foreach ($this->demoUsers as $user) {
            echo " - [{$user['role']}] {$user['email']}\n";
        }
    }
}

// Exécute le seeder
$seeder = new DatabaseSeeder();
$seeder->run();