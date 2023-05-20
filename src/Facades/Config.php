<?php

namespace SSF\MicroFramework\Facades;

use SSF\MicroFramework\Config\Configuration;

/**
 * @method static mixed get(string $key)
 */
class Config extends Facade
{

    public static function instanceName(): string
    {
        return Configuration::class;
    }
}