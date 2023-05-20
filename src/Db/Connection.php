<?php

namespace SSF\MicroFramework\Db;

use SSF\MicroFramework\Traits\Singleton;

class Connection
{
    protected static array $connection = [];

    public static function get(string $connection = 'default')
    {
        if (isset(static::$connection[$connection])) {
            return static::$connection[$connection];
        }


    }
}