<?php

namespace SSF\MicroFramework\Cache\Adapter;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

class ArrayCache extends AbstractAdapter implements CacheInterface
{
    /**
     * @var array
     */
    private array $expiration = [];

    /**
     * @var array
     */
    private array $store = [];

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getValue($key, $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param DateInterval|int|null $ttl
     * @return bool
     */
    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        static::validateKey($key);
        $this->setValue($key, $value, $ttl);
        return true;
    }

    /**
     * @param string $key
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete(string $key): bool
    {
        return $this->deleteMultiple([$key]);
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $this->store = $this->expiration = [];
        return true;
    }

    /**
     * @param iterable $keys
     * @param mixed|null $default
     * @return iterable
     */
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

    /**
     * @param iterable $values
     * @param DateInterval|int|null $ttl
     * @return bool
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $values = static::convertValuesToArray($values);
        $ttl = static::convertTtlToSeconds($ttl);

        foreach ($values as $key => $value) {
            $this->setValue($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach (static::convertKeysToArray($keys) as $key) {
            unset($this->store[$key], $this->expiration[$key]);
        }

        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->getValue($key) !== null;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    private function getValue(string $key, mixed $default = null)
    {
        static::validateKey($key);
        $this->purgeExpired();

        if (!isset($this->store[$key])) {
            return $default;
        }

        return unserialize($this->store[$key]);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int|DateInterval|null $ttl
     * @return void
     */
    private function setValue(string $key, mixed $value, null|int|DateInterval $ttl): void
    {
        $this->store[$key] = serialize($value);
        $this->expiration[$key] = static::convertTtlToSeconds($ttl);
    }

    /**
     * @return void
     */
    private function purgeExpired(): void
    {
        foreach (array_keys($this->store) as $key) {
            if ($this->expiration[$key] && time() > $this->expiration[$key]) {
                unset($this->store[$key], $this->expiration[$key]);
            }
        }
    }
}