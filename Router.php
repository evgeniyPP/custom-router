<?php

class Router
{
    const GET = 'GET';
    const POST = 'POST';

    private $routeCollection = [];

    public function get(string $uri, Closure $closure)
    {
        $parsedUri = $this->parseUri($uri);
        $this->addRoute(self::GET, $parsedUri['path'], $parsedUri['paramsNum'], $closure);
    }

    public function post(string $uri, Closure $closure)
    {
        $parsedUri = $this->parseUri($uri);
        $this->addRoute(self::POST, $parsedUri['path'], $parsedUri['paramsNum'], $closure);
    }

    public function run()
    {
        $route = $this->findRoute(
            $this->routeCollection[$_SERVER['REQUEST_METHOD']],
            explode('/', $_SERVER['REQUEST_URI'])
        );
        call_user_func_array($route['callback'], $route['params']);
    }

    private function addRoute(string $method, string $uri, int $paramsNum, Closure $closure)
    {
        $this->routeCollection[$method][$uri] = [
            'paramsNum' => $paramsNum,
            'callback' => $closure,
        ];
    }

    private function parseUri(string $uri)
    {
        $parsedUri = explode('/:', $uri);

        return [
            'path' => $parsedUri[0],
            'paramsNum' => count($parsedUri) - 1,
        ];
    }

    private function findRoute(array $routes, array $uri_array)
    {
        $currentRoute = '/';
        $isRouteFound = false;

        while (!empty($uri_array)) {
            $uriPiece = array_shift($uri_array);
            $currentRoute = $currentRoute === '/' ? "${currentRoute}{$uriPiece}" : "{$currentRoute}/{$uriPiece}";
            $route = $routes[$currentRoute] ?? null;

            if (isset($route) && $route['paramsNum'] === count($uri_array)) {
                $isRouteFound = true;
                $params = $uri_array;
                break;
            }
        }

        if (!$isRouteFound) {
            throw new Exception('Ошибка 404 – Страница не найдена', 404);
        }

        return [
            'callback' => $routes[$currentRoute]['callback'],
            'params' => $params,
        ];
    }
}