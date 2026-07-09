<?php

declare(strict_types=1);

namespace App\Http;

// classe contraire an Request satria il prepare ce que le server renvoie au navigateur
final class Response
{
    private function __construct(
        private readonly int $status,
        private readonly string $body,
        /** @var array<string, string> */
        private readonly array $headers = [],
    ) {
    }

    public static function html(string $content, int $status = 200): self
    {
        return new self($status, $content, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /** @param mixed $data structure serialisable en JSON */
    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            $status,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ['Content-Type' => 'application/json; charset=utf-8']
        );
    }

    public static function redirect(string $location, int $status = 302): self
    {
        return new self($status, '', ['Location' => $location]);
    }

    public static function text(string $content, int $status = 200): self
    {
        return new self($status, $content, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    // envoie ny reponse an amin client
    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->body;
    }
}