<?php

namespace SSF\MicroFramework\Cache\Adapter;

use DateInterval;
use Memcached as MemcachedResource;
use Psr\SimpleCache\CacheInterface;

class Memcached extends AbstractAdapter implements CacheInterface
{
    protected MemcachedResource $cache;

    public function __construct(
        protected array $config
    ) {
        $this->cache = new MemcachedResource();
        $this->connect();
    }

    public function connect(): bool
    {
        $servers = $this->config['servers'] ?? [];
        $options = $this->config['options'] ?? [];

        if (! empty($servers)) {
            $this->cache->addServers($servers);
        }

        if (! empty($options)) {
            $this->cache->setOptions($options);
        }

        return true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        static::validateKey($key);

        if (
            false === $value = $this->cache->get($key)
            && $this->cache->getResultCode() === MemcachedResource::RES_NOTFOUND
        ) {
            return $default;
        }

        return $value;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        static::validateKey($key);
        $expire = static::convertTtlToSeconds($ttl);
        $result = $this->cache->set($key, $value, $expire);

        if (
            $value === false
            && $result === false
            && $this->cache->getResultCode() === MemcachedResource::RES_SUCCESS
        ) {
            return true;
        }

        return $result;
    }

    public function delete(string $key): bool
    {
        static::validateKey($key);

        return $this->cache->delete($key);
    }

    public function clear(): bool
    {
        return $this->cache->flush();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keys = static::convertKeysToArray($keys);
        $values = [];

        if (false !== $found = $this->cache->getMulti($keys)) {
            foreach ($keys as $key) {
                $values[$key] = $found[$key] ?? $default;
            }
        }

        return $values;
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $expire = static::convertTtlToSeconds($ttl);
        $values = static::convertValuesToArray($values);

        return $this->cache->setMulti($values, $expire);
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $keys = static::convertKeysToArray($keys);

        return empty($keys) || $this->faultCheck($this->cache->deleteMulti($keys));
    }

    public function has(string $key): bool
    {
        static::validateKey($key);

        return (
            false !== $this->cache->get($key)
            || $this->cache->getResultCode() === MemcachedResource::RES_SUCCESS
        );
    }

    private function faultCheck($result): bool
    {
        return (
            false !== $result
            || $this->cache->getResultCode() === MemcachedResource::RES_NOTFOUND
        );
    }
}