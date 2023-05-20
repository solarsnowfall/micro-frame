<?php

namespace SSF\MicroFramework\Services;

use SSF\MicroFramework\Cache\Adapter\FileCache;
use SSF\MicroFramework\Cache\Storage;
use SSF\MicroFramework\Config\Configuration;
use SSF\MicroFramework\Config\Environment;
use SSF\MicroFramework\Facades\App;
use SSF\MicroFramework\Facades\Config;

class Provider
{
    public static function register(): array
    {
        return [];
    }

    public static function registerSingleton(): array
    {
        return [
            Configuration::class => ['directory' => __DIR__ . '/../../config'],
            FileCache::class => ['path' => __DIR__ . '/../../cache'],
            Storage::class => function() {
                return new Storage(
                    App::get(Config::get('cache.' . Environment::get('CACHE_ENGINE')))
                );
            }
        ];
    }
}