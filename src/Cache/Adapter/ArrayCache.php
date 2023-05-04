<?php

namespace SSF\MicroFramework\Cache\Adapter;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

class ArrayCache extends AbstractAdapter implements CacheInterface
{
    private array $expiration = [];
    private array $store = [];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getValue($key, $default);
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        static::validateKey($key);
        $this->setValue($key, $value, $ttl);
        return true;
    }

    public function delete(string $key): bool
    {
        return $this->deleteMultiple([$key]);
    }

    public function clear(): bool
    {
        $this->store = $this->expiration = [];
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $this->purgeExpired();
        $values = [];

        foreach (static::convertKeysToArray($keys) as $key) {
            $values[$key] = isset($this->store[$key])
                ? unserialize($this->store[$key])
                : $default;
        }

        return $values;
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $values = static::convertValuesToArray($values);
        $ttl = static::convertTtlToSeconds($ttl);

        foreach ($values as $key => $value) {
            $this->setValue($key, $values, $ttl);
        }

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach (static::convertKeysToArray($keys) as $key) {
            unset($this->store[$key], $this->expiration[$key]);
        }

        return true;
    }

    public function has(string $key): bool
    {
        return $this->getValue($key) !== null;
    }

    private function getValue(string $key, mixed $default = null)
    {
        static::validateKey($key);
        $this->purgeExpired();

        if (!isset($this->store[$key])) {
            return $default;
        }

        return unserialize($this->store[$key]);
    }

    private function setValue(string $key, mixed $value, null|int|DateInterval $ttl): void
    {
        $this->store[$key] = serialize($value);
        $this->expiration[$key] = static::convertTtlToSeconds($ttl);
    }

    private function purgeExpired(): void
    {
        foreach (array_keys($this->store) as $key) {
            if (time() > $this->expiration[$key]) {
                unset($this->store[$key], $this->expiration[$key]);
            }
        }
    }
}