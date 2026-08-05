<?php

declare(strict_types=1);

// charge la configuration, demarre la session
// mamorona router, mandray anle requete
// traite la requete et gère les erreurs et envoie la réponse au navigateur
use App\Controllers\AssignmentSettingsController;
use App\Controllers\HealthController;
use App\Controllers\AuthController;
use App\Controllers\MessageController;
use App\Controllers\TicketController;
use App\Controllers\UserController;
use App\Exceptions\BusinessException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\UserRepository;
use App\Support\Env;
use App\Support\Session;

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
$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->post('/tickets', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    return (new TicketController())->create($request);
});

$router->get('/me', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    return (new AuthController())->me($request);
});

$router->get('/tickets', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    return (new TicketController())->index($request);
});

$router->get('/tickets/{id}', function (Request $request, string $id): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    return (new TicketController())->show($request, $id);
});

$router->patch('/tickets/{id}/status', function (Request $request, string $id): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    return (new TicketController())->updateStatus($request, $id);
});

$router->patch('/tickets/{id}/assign', function (Request $request, string $id): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    return (new TicketController())->assign($request, $id);
});

$router->get('/tickets/{id}/messages', function (Request $request, string $id): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    return (new MessageController())->index($request, $id);
});

$router->post('/tickets/{id}/messages', function (Request $request, string $id): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    return (new MessageController())->store($request, $id);
});

$router->get('/users/agents', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);
    return (new UserController())->agents($request);
});

$router->get('/assignment-settings', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);
    return (new AssignmentSettingsController())->index($request);
});

$router->patch('/assignment-settings', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);
    return (new AssignmentSettingsController())->update($request);
});

$router->notFound(function (Request $request): Response {
    return Response::json([
        'status' => 'error',
        'message' => "Route introuvable : {$request->method()} {$request->uri()}",
    ], 404);
});

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
        'message' => $debug ? $e->getMessage() : 'Une erreur interne est survenue',
    ], 500);
}

$response->send();