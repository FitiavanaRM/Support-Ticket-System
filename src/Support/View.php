<?php

declare(strict_types=1);

namespace App\Support;

final class View
{
    /**
     * @param string $path
     * @param array<string, mixed> $params
     */
    public static function render(string $path, array $params = []): string
    {
        if (!is_file($path)) {
            throw new \RuntimeException("Vue introuvable : {$path}");
        }

        extract($params, EXTR_SKIP);

        ob_start();
        require $path;
        return (string) ob_get_clean();
    }
}
