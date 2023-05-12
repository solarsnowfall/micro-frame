<?php

namespace SSF\MicroFramework\Facades;

class Cache extends Facade
{

    public static function instanceName(): string
    {
        return \SSF\MicroFramework\Cache\Cache::class;
    }
}