<?php

declare(strict_types=1);

namespace App\Http;

use RuntimeException;

// recevoir une requête, trouver la bonne route et appeler le bon contrôleur.
final class Router
{
    /**
     * @var array<string, array<int, array{pattern: string, regex: string, paramNames: array<int, string>, handler: callable|array}>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    /** @var callable|null appele quand aucune route ne correspond */
    private $notFoundHandler = null;

    public function get(string $pattern, callable|array $handler): void
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable|array $handler): void
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable|array $handler): void
    {
        $this->addRoute('PUT', $pattern, $handler);
    }

    public function patch(string $pattern, callable|array $handler): void
    {
        $this->addRoute('PATCH', $pattern, $handler);
    }

    public function delete(string $pattern, callable|array $handler): void
    {
        $this->addRoute('DELETE', $pattern, $handler);
    }

    public function notFound(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    private function addRoute(string $method, string $pattern, callable|array $handler): void
    {
        $paramNames = [];

        // Transforme une route comme /tickets/{id} en expression régulière
        $regex = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            function (array $matches) use (&$paramNames): string {
                $paramNames[] = $matches[1];
                return '(?<' . $matches[1] . '>[^/]+)';
            },
            $pattern
        );

        $regex = '#^' . rtrim($regex, '/') . '/?$#';

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'regex' => $regex,
            'paramNames' => $paramNames,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = rtrim($request->uri(), '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $uri, $matches) === 1) {
                $params = [];
                foreach ($route['paramNames'] as $name) {
                    $params[$name] = $matches[$name];
                }

                $request->setRouteParams($params);

                return $this->callHandler($route['handler'], $request, $params);
            }
        }

        if ($this->notFoundHandler !== null) {
            return ($this->notFoundHandler)($request);
        }

        return Response::text('404 Not Found', 404);
    }

    /** @param array<string, string> $params */
    private function callHandler(callable|array $handler, Request $request, array $params): Response
    {
        if (is_array($handler)) {
            [$controllerClass, $methodName] = $handler;
            $controller = new $controllerClass();

            if (!method_exists($controller, $methodName)) {
                throw new RuntimeException(
                    "Methode {$methodName} introuvable sur " . $controllerClass
                );
            }

            return $controller->{$methodName}($request, ...array_values($params));
        }

        return $handler($request, ...array_values($params));
    }
}