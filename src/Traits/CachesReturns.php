<?php

namespace SSF\MicroFramework\Traits;

use Closure;

trait CachesReturns
{
    private static array $cache = [];

    private static function checkCache(string $method, string $string, Closure $callback)
    {
        if (isset(static::$cache[$method][$string])) {
            return static::$cache[$method][$string];
        }

        return static::$cache[$method][$method] = $callback();
    }
}