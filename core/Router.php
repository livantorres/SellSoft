<?php
declare(strict_types=1);

namespace SellSoft\Core;

class Router
{
    private $routes = [];
    private $groupPrefix = '';
    private $groupMiddleware = [];

    public function get(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function any(string $path, $handler, array $middleware = []): self
    {
        $this->addRoute('GET', $path, $handler, $middleware);
        $this->addRoute('POST', $path, $handler, $middleware);
        return $this;
    }

    public function group(array $options, callable $callback): void
    {
        $prevPrefix     = $this->groupPrefix;
        $prevMiddleware = $this->groupMiddleware;
        $this->groupPrefix     = $prevPrefix . ($options['prefix'] ?? '');
        $this->groupMiddleware = array_merge($prevMiddleware, $options['middleware'] ?? []);
        $callback($this);
        $this->groupPrefix     = $prevPrefix;
        $this->groupMiddleware = $prevMiddleware;
    }

    public function dispatch(): void
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $uri    = $this->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            $pattern = $this->toPattern($route['path']);
            if (!preg_match($pattern, $uri, $matches)) continue;

            $params = array_filter($matches, function ($key) { return !is_int($key); }, ARRAY_FILTER_USE_KEY);

            foreach ($route['middleware'] as $mw) {
                $instance = new $mw();
                $instance->handle();
            }
            $this->executeHandler($route['handler'], $params);
            return;
        }
        $this->notFound();
    }

    private function addRoute(string $method, string $path, $handler, array $middleware): self
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $this->groupPrefix . $path,
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
        return $this;
    }

    private function toPattern(string $path): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . str_replace('/', '\/', $pattern) . '$#';
    }

    private function getUri(): string
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        if (strpos($uri, '?') !== false) {
            $uri = strstr($uri, '?', true);
        }
        $uri = '/' . trim($uri, '/');
        return $uri === '' ? '/' : $uri;
    }

    private function executeHandler($handler, array $params): void
    {
        if (is_array($handler)) {
            list($class, $method) = $handler;
            $instance = new $class();
            call_user_func_array([$instance, $method], array_values($params));
        } elseif (is_callable($handler)) {
            call_user_func_array($handler, array_values($params));
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        $view = VIEWS_PATH . '/errors/404.php';
        if (file_exists($view)) {
            include $view;
        } else {
            echo '<h1>404 - Page Not Found</h1>';
        }
    }
}
