<?php

namespace SSF\MicroFramework\Services;

use SSF\MicroFramework\Config\Config;

class Provider
{
    public static function register(): array
    {
        return [];
    }

    public static function registerSingleton(): array
    {
        return [
            Config::class => ['directory' => __DIR__ . '/../../config']
        ];
    }
}