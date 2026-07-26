<?php

declare(strict_types=1);

// excecute les fichiers .sql par ordre alphabetique
use App\Support\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');

$host = Env::get('DB_HOST', '127.0.0.1');
$port = Env::get('DB_PORT', '3306');
$dbname = Env::get('DB_DATABASE', 'support_tickets');
$charset = Env::get('DB_CHARSET', 'utf8mb4');
$user = Env::get('DB_USERNAME', 'root');
$pass = Env::get('DB_PASSWORD', '');

$dsn = "mysql:host={$host};port={$port};charset={$charset}";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "Connexion MySQL impossible : {$e->getMessage()}\n");
    exit(1);
}

// La base doit exister avant de pouvoir y créer la table de suivi des migrations.
$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS `{$dbname}`.`migrations` (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL UNIQUE,
        executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB
");

$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.sql');
// par ordre
sort($files);

$already = [];
try {
    $stmt = $pdo->query("SELECT filename FROM `{$dbname}`.`migrations`");
    $already = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException) {
    $already = [];
}

$executedCount = 0;

foreach ($files as $file) {
    $filename = basename($file);

    if (in_array($filename, $already, true)) {
        echo "SKIP  {$filename} (deja executee)\n";
        continue;
    }

    $sql = file_get_contents($file);

    try {
        $pdo->exec($sql);

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `{$dbname}`.`migrations` (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL UNIQUE,
                executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
        ");

        $insert = $pdo->prepare("INSERT INTO `{$dbname}`.`migrations` (filename) VALUES (:filename)");
        $insert->execute(['filename' => $filename]);

        echo "OK : {$filename}\n";
        $executedCount++;
    } catch (PDOException $e) {
        fwrite(STDERR, "ECHEC {$filename} : {$e->getMessage()}\n");
        exit(1);
    }
}

echo "\n{$executedCount} migration executee sur [" . count($files) . "] fichier(s) trouve(s).\n";
