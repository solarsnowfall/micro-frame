<?php

namespace SSF\MicroFramework\Services;

use SSF\MicroFramework\Cache\Cache;
use SSF\MicroFramework\Config\Config;
use SSF\MicroFramework\Config\Environment;

class Provider
{
    public static function register(): array
    {
        return [];
    }

    public static function registerSingleton(): array
    {
        return [
            Config::class => ['directory' => __DIR__ . '/../../config'],
            Cache::class => function() {
                $interface = \SSF\MicroFramework\Facades\Config::get('cache.' . Environment::get('CACHE_ENGINE'));
                return new Cache(new $interface);
            }
        ];
    }
}