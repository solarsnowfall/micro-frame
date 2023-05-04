<?php

namespace SSF\MicroFramework\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachePool implements CacheItemPoolInterface
{

    public function getItem(string $key): CacheItemInterface
    {

    }

    public function getItems(array $keys = []): iterable
    {
        // TODO: Implement getItems() method.
    }

    public function hasItem(string $key): bool
    {
        // TODO: Implement hasItem() method.
    }

    public function clear(): bool
    {
        // TODO: Implement clear() method.
    }

    public function deleteItem(string $key): bool
    {
        // TODO: Implement deleteItem() method.
    }

    public function deleteItems(array $keys): bool
    {
        // TODO: Implement deleteItems() method.
    }

    public function save(CacheItemInterface $item): bool
    {
        // TODO: Implement save() method.
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        // TODO: Implement saveDeferred() method.
    }

    public function commit(): bool
    {
        // TODO: Implement commit() method.
    }
}