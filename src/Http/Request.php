<?php

declare(strict_types=1);

namespace App\Http;

// La classe Request représente la requête envoyée par le navigateur
final class Request
{
    /** @param array<string, mixed> $query donnees $_GET
     *  @param array<string, mixed> $body donnees $_POST / JSON decode
     *  @param array<string, mixed> $server donnees $_SERVER
     *  @param array<string, string> $routeParams parametres extraits de l'URL par le Router
     */
    private function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly array $query,
        private readonly array $body,
        private readonly array $server,
        private array $routeParams = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $body = $_POST;

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw ?: '', true);

            if (is_array($decoded)) {
                $body = $decoded;
            }
        }

        return new self($method, $uri, $_GET, $body, $_SERVER);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    // retourne l'info
    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    // Verification
    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }

    // Enregistre les paramètres récupérés dans l'URL
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

     // Retourne un paramètre de la route
    public function routeParam(string $key, mixed $default = null): mixed
    {
        return $this->routeParams[$key] ?? $default;
    }
}