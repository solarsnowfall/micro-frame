<?php

namespace SSF\MicroFramework\Cache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use SSF\MicroFramework\Cache\Exception\InvalidCacheKeyException;

class Storage implements CacheInterface
{
    /**
     * @param CacheInterface $cache
     */
    public function __construct(
        private readonly CacheInterface $cache
    ) {}

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws InvalidCacheKeyException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($key, $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param DateInterval|int|null $ttl
     * @return bool
     * @throws InvalidCacheKeyException
     */
    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        return $this->cache->set($key, $value, $ttl);
    }

    /**
     * @param string $key
     * @return bool
     * @throws InvalidCacheKeyException
     */
    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        return $this->cache->clear();
    }

    /**
     * @param iterable $keys
     * @param mixed|null $default
     * @return iterable
     * @throws InvalidCacheKeyException
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        return $this->cache->getMultiple($keys, $default);
    }

    /**
     * @param iterable $values
     * @param DateInterval|int|null $ttl
     * @return bool
     * @throws InvalidCacheKeyException
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        return $this->cache->setMultiple($values, $ttl);
    }

    /**
     * @param iterable $keys
     * @return bool
     * @throws InvalidCacheKeyException
     */
    public function deleteMultiple(iterable $keys): bool
    {
        return $this->cache->deleteMultiple($keys);
    }

    /**
     * @param string $key
     * @return bool
     * @throws InvalidCacheKeyException
     */
    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }
}