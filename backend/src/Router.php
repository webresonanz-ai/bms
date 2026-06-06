<?php

namespace BMS;

use BMS\Middleware\CorsMiddleware;
use BMS\Middleware\AuthMiddleware;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    public function __construct()
    {
        $this->middleware['cors'] = new CorsMiddleware();
        $this->middleware['auth'] = new AuthMiddleware();
    }

    public function add(string $method, string $path, callable $handler, array $middlewares = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middlewares
        ];
    }

    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $basePath = '/api';
        
        if ($path && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('/\{(any)\}/', '(.+)', $route['path']);
            $pattern = preg_replace('/\{(\w+)\}/', '(\d+)', $pattern);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path ?? '', $matches)) {
                array_shift($matches);
                $handler = $route['handler'];

                foreach (array_reverse($route['middleware']) as $middleware) {
                    $handler = fn() => $this->middleware[$middleware]->handle($handler);
                }

                call_user_func_array($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not found']);
    }
}