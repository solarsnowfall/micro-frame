<?php

namespace SSF\MicroFramework\Cache\Adapter;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

class Apcu extends AbstractAdapter implements CacheInterface
{

    public function get(string $key, mixed $default = null): mixed
    {
        static::validateKey($key);
        $value = apcu_fetch($key, $success);

        return $success ? $value : $default;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        static::validateKey($key);
        $ttl = static::convertTtlToSeconds($ttl);

        return apcu_store($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        static::validateKey($key);

        return apcu_delete($key);
    }

    public function clear(): bool
    {
        return apcu_clear_cache();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keys = static::convertKeysToArray($keys);
        $values = apcu_fetch($keys, $success);

        if (! $success) {
            return array_fill(0, count($keys), $default);
        }

        foreach ($keys as $key) {
            if (! isset($values[$key])) {
                $values[$key] = $default;
            }
        }

        return $values;
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $values = static::convertValuesToArray($values);
        $ttl = static::convertTtlToSeconds($ttl);
        $errors = apcu_store($values, null, $ttl);

        return empty($errors);
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return apcu_delete($keys) !== false;
    }

    public function has(string $key): bool
    {
        return apcu_enabled($key);
    }
}