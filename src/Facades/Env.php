<?php

namespace SSF\MicroFramework\Facades;

use SSF\MicroFramework\Config\Environment;

/**
 * @method static mixed get(string $name, mixed $default = null)
 */
class Env extends Facade
{

    /**
     * @inheritDoc
     */
    public static function instanceName(): string
    {
        return Environment::class;
    }
}