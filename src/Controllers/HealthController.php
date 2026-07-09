<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Http\Request;
use App\Http\Response;
use PDOException;
use RuntimeException;

// controller pour tester

final class HealthController
{
    public function index(Request $request): Response
    {
        return Response::json([
            'status' => 'ok',
            'app' => 'support-tickets',
            'php_version' => PHP_VERSION,
        ]);
    }

    public function database(Request $request): Response
    {
        try {
            $pdo = Database::connection();
            $pdo->query('SELECT 1');
        } catch (RuntimeException|PDOException $e) {
            return Response::json([
                'status' => 'error',
                'message' => 'Connexion a la base de donnees impossible.',
            ], 500);
        }

        return Response::json([
            'status' => 'ok',
            'message' => 'Connexion a la base de donnees etablie.',
        ]);
    }
}