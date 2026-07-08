<?php

declare(strict_types=1);

namespace App\Config;

// classe qui crée une seule connexion à la base de données et la réutilise partout
// dans le projet
// et lit les information de connexion depuis le fichier .env
use App\Support\Env;
use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function connection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host    = Env::get('DB_HOST', '127.0.0.1');
        $port    = Env::get('DB_PORT', '3306');
        $dbname  = Env::get('DB_DATABASE', 'support_tickets');
        $charset = Env::get('DB_CHARSET', 'utf8mb4');
        $user    = Env::get('DB_USERNAME', 'root');
        $pass    = Env::get('DB_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        try {
            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Impossible de se connecter a la base de donnees.',
                previous: $e
            );
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}