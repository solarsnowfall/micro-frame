<?php

namespace SSF\MicroFramework\Cache;

use DateInterval;
use DateTimeInterface;
use DateTimeImmutable;
use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    protected string $key;
    protected mixed $value = null;
    protected bool $isHit = false;
    protected float|int|null $expiry = null;

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function expiresAt(?DateTimeInterface $expiration): static
    {
        $this->expiry = null !== $expiration ? (float) $expiration->format('U.u') : null;
        return $this;
    }

    public function expiresAfter(DateInterval|int|null $time): static
    {
        if ($time === null) {
            $this->expiry = null;
        } elseif ($this instanceof DateInterval) {
            $this->expiry = microtime(true) + DateTimeImmutable::createFromFormat('U', 0)->add($time)->format('U.u');
        } elseif (is_int($time)) {
            $this->expiry = microtime(true) + $time;
        } else {
            throw new InvalidArgumentException(sprintf('Expiration date must be an integer, a DateInterval or null, "%s" given.', get_debug_type($time)));
        }

        return $this;
    }
}