<?php

namespace SSF\MicroFramework\Traits;

use Closure;
use InvalidArgumentException;
use RuntimeException;

trait Singleton
{
    private static ?self $instance = null;

    public static function createNewInstance(...$args): self
    {
        static::$instance = static::newInstance(...$args);
        return static::$instance;
    }

    public static function newInstance(...$args): self
    {
        return new self(...$args);
    }

    public static function setInstance(self|Closure $instance): void
    {
        if (static::$instance !== null) {
            throw new RuntimeException(
                sprintf('Singleton instance has already been set for class: %s', __CLASS__)
            );
        }

        if ($instance instanceof Closure) {
            $instance = $instance();
        }

        if (!$instance instanceof self) {
            throw new InvalidArgumentException(
                sprintf('Instance must be an resolve to an instance of the class: %s', __CLASS__)
            );
        }

        static::$instance = $instance;
    }

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            throw new RuntimeException(
                sprintf('Singleton instance not set for: %s', __CLASS__)
            );
        }

        return static::$instance;
    }
}