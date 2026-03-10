<?php

declare(strict_types=1);

namespace Core;

class Router
{
    /**
     * @var array<string, array<string, callable>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /**
     * @var callable
     */
    private $notFoundHandler;

    public function __construct()
    {
        $this->notFoundHandler = static function (): void {
            http_response_code(404);
            echo '404 - Page not found';
        };
    }

    public function get(string $page, callable $handler): void
    {
        $this->routes['GET'][$page] = $handler;
    }

    public function post(string $page, callable $handler): void
    {
        $this->routes['POST'][$page] = $handler;
    }

    public function setNotFound(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch(string $method, string $page): void
    {
        $requestMethod = strtoupper(trim($method));
        $routePage = trim($page);

        $handler = $this->routes[$requestMethod][$routePage] ?? null;

        if ($handler === null) {
            call_user_func($this->notFoundHandler);
            return;
        }

        call_user_func($handler);
    }
}
