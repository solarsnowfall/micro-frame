<?php

namespace SSF\MicroFramework\Facades;

use SSF\MicroFramework\Config\Config as ConfigClass;

/**
 * @method static mixed get(string $key)
 */
class Config extends Facade
{

    public static function instanceName(): string
    {
        return ConfigClass::class;
    }
}