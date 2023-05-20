<?php

namespace SSF\MicroFramework\Facades;

use DateInterval;
use SSF\MicroFramework\Cache\Storage;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static bool set(string $key, mixed $value, DateInterval|int|null $ttl = null)
 * @method static bool delete(string $key)
 * @method static bool clear()
 * @method static iterable getMultiple(iterable $keys, mixed $default = null)
 * @method static bool deleteMultiple(iterable $keys)
 * @method static bool has(string $key)
 */
class Cache extends Facade
{

    public static function instanceName(): string
    {
        return Storage::class;
    }
}