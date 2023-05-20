<?php

namespace SSF\MicroFramework\Routing;

use Closure;
use SSF\MicroFramework\Controller;

abstract class Router
{
    /**
     * @var Controller[]|Closure[]
     */
    protected array $handlers = [];

    /**
     * @param string $route
     * @param Controller|Closure $handler
     * @return void
     */
    public function bind(string $route, Controller|Closure $handler): void
    {
        $route = static::formatRoute($route);
        $this->handlers[$route] = $handler;
    }

    /**
     * @param string $route
     * @return bool
     */
    public function has(string $route): bool
    {
        return $this->find($route) !== null;
    }

    /**
     * @param string $route
     * @return Closure|Controller|null
     */
    public function find(string $route): Closure|Controller|null
    {
        $route = static::formatRoute($route);
        return $this->handlers[$route] ?? null;
    }

    protected static function formatRoute(string $route): string
    {
        return trim($route, DIRECTORY_SEPARATOR);
    }
}