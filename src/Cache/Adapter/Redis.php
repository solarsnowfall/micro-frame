<?php

namespace SSF\MicroFramework\Cache\Adapter;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use Redis as RedisResource;
use RedisException;

class Redis extends AbstractAdapter implements CacheInterface
{
    protected RedisResource $cache;

    public function __construct(
        protected array $config
    ) {
        $this->cache = new RedisResource();
        $this->connect();
    }

    /**
     * @return bool
     * @throws RedisException
     */
    public function connect(): bool
    {
        $host = $this->config['host'] ?? '127.0.0.1';
        $port = $this->config['port'] ?? 6379;

        return $this->cache->connect($host, $port);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        static::validateKey($key);
        $value = $this->cache->get($key);

        return $value !== null ? unserialize($value) : $default;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        static::validateKey($key);
        $expire = static::convertTtlToSeconds($ttl);

        return $this->setValue($this->cache, $key, $value, $expire);
    }

    public function delete(string $key): bool
    {
        static::validateKey($key);

        return $this->cache->del($key);
    }

    public function clear(): bool
    {
        return $this->cache->flushDB();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keys = static::convertKeysToArray($keys);
        $values = $this->cache->mget($keys);
        $callback = fn($value) => $value !== false ? unserialize($value) : $default;

        return array_combine($keys, array_map($callback, $values));
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $values = static::convertValuesToArray($values);
        $expire = static::convertTtlToSeconds($ttl);
        $pipeline = $this->cache->multi();

        foreach (array_keys($values) as $key) {
            $this->setValue($pipeline, $key, $values[$key], $expire);
        }

        $results = array_unique($pipeline->exec());

        return count($results) === 1 && $results[0];
    }

    public function deleteMultiple(iterable $keys): bool
    {
        static::convertKeysToArray($keys);

        return $this->cache->del($keys);
    }

    public function has(string $key): bool
    {
        static::validateKey($key);

        return $this->cache->exists($key);
    }

    private function setValue(RedisResource $redis, string $key, mixed $value, int $expire)
    {
        $value = serialize($value);

        return $expire
            ? $redis->setex($key, $expire, $value)
            : $redis->set($key, $value);
    }
}
