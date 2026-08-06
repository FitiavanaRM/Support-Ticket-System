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
use App\Exceptions\AuthenticationException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\BusinessException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\MessageRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use App\Support\Env;
use App\Support\Session;
use App\Support\View;

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

$router->get('/', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);

    $session = new Session();
    $currentUser = (new UserRepository())->findById($session->userId() ?? 0);
    $ticketRepository = new TicketRepository();
    $recentTickets = $ticketRepository->findRecentForUser($session->userId() ?? 0, 5);

    $viewRecentTickets = array_map(
        static fn ($ticket) => [
            'ticket' => $ticket,
            'agentName' => $ticket->agentId() !== null ? ((new UserRepository())->findById($ticket->agentId())?->name() ?? 'Non assigné') : 'Non assigné',
        ],
        $recentTickets
    );

    return Response::html(View::render(__DIR__ . '/../src/Views/dashboard/index.php', [
        'currentUser' => $currentUser,
        'stats' => $ticketRepository->aggregateStatusCountsForUser($session->userId() ?? 0),
        'recentTickets' => $viewRecentTickets,
    ]));
});
$router->get('/dashboard', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);

    $session = new Session();
    $currentUser = (new UserRepository())->findById($session->userId() ?? 0);
    $ticketRepository = new TicketRepository();
    $recentTickets = $ticketRepository->findRecentForUser($session->userId() ?? 0, 5);

    $viewRecentTickets = array_map(
        static fn ($ticket) => [
            'ticket' => $ticket,
            'agentName' => $ticket->agentId() !== null ? ((new UserRepository())->findById($ticket->agentId())?->name() ?? 'Non assigné') : 'Non assigné',
        ],
        $recentTickets
    );

    return Response::html(View::render(__DIR__ . '/../src/Views/dashboard/index.php', [
        'currentUser' => $currentUser,
        'stats' => $ticketRepository->aggregateStatusCountsForUser($session->userId() ?? 0),
        'recentTickets' => $viewRecentTickets,
    ]));
});
$router->get('/login', function (Request $request): Response {
    return Response::html(View::render(__DIR__ . '/../src/Views/auth/login.php'));
});
$router->get('/register', function (Request $request): Response {
    return Response::html(View::render(__DIR__ . '/../src/Views/auth/register.php'));
});
$router->get('/health', [HealthController::class, 'index']);
$router->get('/health/database', [HealthController::class, 'database']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/tickets/new', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);

    $session = new Session();
    $userRepository = new UserRepository();
    $currentUser = $userRepository->findById($session->userId() ?? 0);

    $pdo = new PDO('mysql:host=' . Env::get('DB_HOST', '127.0.0.1') . ';port=' . Env::get('DB_PORT', '3306') . ';dbname=' . Env::get('DB_DATABASE', 'support_tickets') . ';charset=' . Env::get('DB_CHARSET', 'utf8mb4'), Env::get('DB_USERNAME', 'root'), Env::get('DB_PASSWORD', ''));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $categories = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
    $priorities = $pdo->query('SELECT id, name FROM priorities ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

    return Response::html(View::render(__DIR__ . '/../src/Views/tickets/new.php', [
        'currentUser' => $currentUser,
        'categories' => $categories,
        'priorities' => $priorities,
        'errors' => [],
        'old' => [],
    ]));
});

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

    if ($request->acceptsHtml()) {
        $session = new Session();
        $ticketRepository = new TicketRepository();
        $userRepository = new UserRepository();
        $currentUser = $userRepository->findById($session->userId() ?? 0);
        $tickets = $ticketRepository->findRecentForUser($session->userId() ?? 0, 25);
        $flashMessage = $_SESSION['flash_message'] ?? null;
        unset($_SESSION['flash_message']);

        $agentIds = array_unique(array_filter(array_map(
            static fn ($ticket) => $ticket->agentId(),
            $tickets,
        ),
            static fn (?int $id): bool => $id !== null,
        ));

        $ticketAgentNames = [];
        foreach ($agentIds as $agentId) {
            $ticketAgentNames[$agentId] = $userRepository->findById($agentId)?->name() ?? 'Non assigné';
        }

        return Response::html(View::render(__DIR__ . '/../src/Views/tickets/index.php', [
            'currentUser' => $currentUser,
            'tickets' => $tickets,
            'ticketAgentNames' => $ticketAgentNames,
            'flashMessage' => $flashMessage,
        ]));
    }

    return (new TicketController())->index($request);
});

$router->get('/tickets/{id}', function (Request $request, string $id): Response {
    (new AuthMiddleware(new Session()))->handle($request);

    if ($request->acceptsHtml()) {
        $session = new Session();
        $ticketRepository = new TicketRepository();
        $userRepository = new UserRepository();
        $ticket = $ticketRepository->findById((int) $id);
        if ($ticket === null) {
            throw new \App\Exceptions\TicketNotFoundException((int) $id);
        }

        $currentUser = $userRepository->findById($session->userId() ?? 0);
        $isOwner = $ticket->clientId() === ($session->userId() ?? 0);
        $isAssignedAgent = $ticket->agentId() !== null && $ticket->agentId() === ($session->userId() ?? 0);

        if (!$isOwner && !$isAssignedAgent) {
            throw new AuthorizationException();
        }

        $messageRepository = new MessageRepository();
        $messages = $messageRepository->findByTicketId((int) $id);
        $agent = $ticket->agentId() !== null ? $userRepository->findById($ticket->agentId()) : null;
        $client = $userRepository->findById($ticket->clientId());

        $authorIds = array_unique(array_map(
            static fn ($message) => $message->authorId(),
            $messages,
        ));
        $authorNames = [];
        foreach ($authorIds as $authorId) {
            $authorNames[$authorId] = $userRepository->findById($authorId)?->name() ?? 'Utilisateur inconnu';
        }

        return Response::html(View::render(__DIR__ . '/../src/Views/tickets/show.php', [
            'currentUser' => $currentUser,
            'ticket' => $ticket,
            'messages' => $messages,
            'agent' => $agent,
            'client' => $client,
            'authorNames' => $authorNames,
        ]));
    }

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

$router->get('/users', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);

    $session = new Session();
    $userRepository = new UserRepository();
    $currentUser = $userRepository->findById($session->userId() ?? 0);
    $users = $userRepository->findAll();

    return Response::html(View::render(__DIR__ . '/../src/Views/users/index.php', [
        'currentUser' => $currentUser,
        'users' => $users,
    ]));
});

$router->get('/users/new', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);

    return (new App\Controllers\UserController())->createForm($request);
});

$router->post('/users', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);

    return (new App\Controllers\UserController())->store($request);
});

$router->get('/users/agents', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);
    return (new UserController())->agents($request);
});

$router->get('/users/{id}', function (Request $request, string $id): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);

    return (new App\Controllers\UserController())->show($request, $id);
});

$router->get('/assignment-settings', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);

    if ($request->acceptsHtml()) {
        return Response::html(View::render(__DIR__ . '/../src/Views/settings/index.php'));
    }

    return (new AssignmentSettingsController())->index($request);
});

$router->post('/assignment-settings', function (Request $request): Response {
    (new AuthMiddleware(new Session()))->handle($request);
    (new RoleMiddleware(new Session(), new UserRepository(), ['SUPERVISOR', 'ADMIN']))->handle($request);
    return (new AssignmentSettingsController())->update($request);
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
} catch (AuthenticationException $e) {
    $request = Request::fromGlobals();
    if ($request->acceptsHtml()) {
        $response = Response::redirect('/login');
    } else {
        $response = Response::json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], $e->httpStatusCode());
    }
} catch (BusinessException $e) {
    $request = Request::fromGlobals();

    if ($request->acceptsHtml()) {
        if ($e instanceof AuthorizationException) {
            $response = Response::html(View::render(__DIR__ . '/../src/Views/errors/forbidden.php', [
                'message' => $e->getMessage(),
            ]), $e->httpStatusCode());
        } else {
            $response = Response::html(View::render(__DIR__ . '/../src/Views/errors/error.php', [
                'message' => $e->getMessage(),
                'status' => $e->httpStatusCode(),
            ]), $e->httpStatusCode());
        }
    } else {
        $response = Response::json([
            'status' => 'error',
            'message' => $e->getMessage(),...$e->context(),
        ], $e->httpStatusCode());
    }
} catch (\Throwable $e) {
    $debug = Env::get('APP_DEBUG', false) === true;
    $response = Response::json([
        'status' => 'error',
        'message' => $debug ? $e->getMessage() : 'Une erreur interne est survenue',
    ], 500);
}

$response->send();