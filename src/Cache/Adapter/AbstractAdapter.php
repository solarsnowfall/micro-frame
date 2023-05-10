<?php

namespace SSF\MicroFramework\Cache\Adapter;

use DateInterval;
use DateTime;
use SSF\MicroFramework\Cache\InvalidCacheKeyException;

class AbstractAdapter
{
    protected const DEFAULT_TTL = null;

    /**
     * @param string $key
     * @return void
     */
    public static function validateKey(string $key): void
    {
        if (preg_match('/^[0-9a-z-_]+$/i', $key) === false) {
            throw new InvalidCacheKeyException("Invalid key: $key");
        }
    }

    /**
     * @param int|DateInterval|null $ttl
     * @return int
     */
    protected static function convertTtlToSeconds(null|int|DateInterval $ttl): int
    {
        $ttl = $ttl ?? static::DEFAULT_TTL ?? 0;

        if (is_int($ttl)) {
            return $ttl;
        }

        $dtNow = new DateTime();
        $dtExpire = clone $dtNow;
        $dtExpire->add($ttl);

        return $dtExpire->getTimestamp() - $dtNow->getTimestamp();
    }

    /**
     * @param iterable $keys
     * @return array
     */
    protected static function convertKeysToArray(iterable $keys): array
    {
        $keyList = [];

        foreach ($keys as $key) {
            static::validateKey($key);
            $keyList[] = (string) $key;
        }

        return $keyList;
    }

    /**
     * @param iterable $values
     * @return array
     */
    protected static function convertValuesToArray(iterable $values): array
    {
        $valueList = [];

        foreach ($values as $key => $value) {
            static::validateKey($key);
            $valueList[$key] = $value;
        }

        return $valueList;
    }
}