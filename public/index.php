<?php

declare(strict_types=1);

// charge la configuration, demarre la session
// mamorona router, mandray anle requete
// traite la requete et gère les erreurs et envoie la réponse au navigateur
use App\Controllers\HealthController;
use App\Exceptions\BusinessException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Support\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');

// affiche ny erreur rehetra
if (Env::get('APP_DEBUG', false) === true) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

session_name(Env::get('SESSION_NAME', 'support_tickets_session'));
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

$router = new Router();

$router->get('/', [HealthController::class, 'index']);
$router->get('/health', [HealthController::class, 'index']);
$router->get('/health/database', [HealthController::class, 'database']);

$router->notFound(function (Request $request): Response {
    return Response::json([
        'status' => 'error',
        'message' => "Route introuvable : {$request->method()} {$request->uri()}",
    ], 404);
});

try {
    $response = $router->dispatch(Request::fromGlobals());
} catch (\Throwable $e) {
    $debug = Env::get('APP_DEBUG', false) === true;

    $response = Response::json([
        'status' => 'error',
        'message' => $debug ? $e->getMessage() : 'Une erreur interne est survenue.',
    ], 500);
}

try {
    $response = $router->dispatch(Request::fromGlobals());
} catch (BusinessException $e) {
    $response = Response::json([
        'status' => 'error',
        'message' => $e->getMessage(),...$e->context(),
    ], $e->httpStatusCode());
} catch (\Throwable $e) {
    $debug = Env::get('APP_DEBUG', false) === true;
    $response = Response::json([
        'status' => 'error',
        'message' => $debug ? $e->getMessage() : 'Une erreur interne est survenue.',
    ], 500);
}


$response->send();