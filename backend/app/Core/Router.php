<?php
namespace App\Core;

/**
 * Mediflow Router
 * Simple routing engine to handle API requests.
 */
class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_.\-]+)', $path);
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => "#^" . $path . "$#",
            'handler' => $handler
        ];
    }

    public function dispatch($uri, $method) {
        $uri = parse_url($uri, PHP_URL_PATH);
        // Universal Normalization: Find where /api/ starts and take everything after it
        $apiPos = strpos($uri, '/api');
        if ($apiPos !== false) {
            $uri = substr($uri, $apiPos + 4);
        }
        // If it's a direct backend/public hit
        $uri = str_replace('/backend/public', '', $uri);
        if ($uri === '') $uri = '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && preg_match($route['path'], $uri, $matches)) {
                $handler = explode('@', $route['handler']);
                $controllerName = "App\\Controllers\\" . $handler[0];
                $action = $handler[1];

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    return call_user_func_array([$controller, $action], array_values($params));
                } else {
                    $this->error(500, "Controller $controllerName not found");
                    return;
                }
            }
        }

        $this->error(404, "Route not found");
    }

    private function error($code, $message) {
        http_response_code($code);
        echo json_encode(['error' => $message, 'status' => $code]);
    }
}
