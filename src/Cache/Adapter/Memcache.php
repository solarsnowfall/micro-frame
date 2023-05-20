<?php

namespace SSF\MicroFramework\Cache\Adapter;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

use Memcache as MemcacheResource;

class Memcache extends AbstractAdapter implements CacheInterface
{
    protected MemcacheResource $cache;

    public function __construct(
        protected array $config
    ) {
        $this->cache = new MemcacheResource();
        $this->connect();
    }

    public function connect()
    {
        foreach ($this->config['servers'] ?? [] as $server) {
            $this->cache->addServer($server['host'], $server['port']);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        static::validateKey($key);

        if (false !== $value = $this->cache->get($key)) {
            return $value;
        }

        return $default;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        static::validateKey($key);
        $expire = static::convertTtlToSeconds($ttl);

        return $this->cache->set($key, $value, MEMCACHE_COMPRESSED, $expire);
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

        foreach ($keys as $key) {
            $value = $this->cache->get($key);
            $values[$key] = $value !== false ? $value : $default;
        }

        return $values;
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        $values = static::convertValuesToArray($values);
        $expire = static::convertTtlToSeconds($ttl);
        $count = 0;

        foreach (array_keys($values) as $key) {
            $count += (int) $this->cache->set($key, $values[$key], MEMCACHE_COMPRESSED, $expire);
        }

        return $count === count($values);
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $keys = static::convertKeysToArray($keys);
        $count = 0;

        foreach ($keys as $key) {
            $count += (int) $this->cache->delete($key);
        }

        return $count === count($keys);
    }

    public function has(string $key): bool
    {
        return $this->cache->get($key) !== false;
    }
}