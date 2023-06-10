<?php

namespace SSF\MicroFramework\Routing;

use Closure;
use Psr\Http\Message\RequestInterface;
use SSF\MicroFramework\Controller;

abstract class Router
{
    /**
     * @var Route[][]
     */
    protected array $routes = [];

    public function dispatch(RequestInterface $request)
    {
        if (null !== $route = $this->matchRequest($request)) {
            return $route->invokeHandler($request);
        }


    }

    /**
     * @param string $method
     * @param string $path
     * @return Route|null
     */
    public function match(string $method, string $path): ?Route
    {
        foreach ($this->routes[$method] as $route) {
            if ($route->match($path)) {
                return $route;
            }
        }

        return null;
    }

    public function matchRequest(RequestInterface $request)
    {
        return $this->match($request->getMethod(), $request->getUri());
    }

    public function register(string $method, string $path, string $name, mixed $handler)
    {
        $this->routes[$method][] = new Route($name, $path, $handler);
    }

    public static function getPathKey(string $path): string
    {
        return preg_replace('/\{[^}]+}/', '*', $path);
    }
}