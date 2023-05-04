<?php

namespace SSF\MicroFramework\Facades;

use Psr\Container\ContainerInterface;

abstract class Facade
{
    private static ContainerInterface $container;

    private static array $instance = [];

    abstract public static function instanceName(): string;

    public static function __callStatic(string $name, array $arguments)
    {
        return static::resolveInstance()->$name(...$arguments);
    }

    public static function setContainer(ContainerInterface $container): void
    {
        static::$container = $container;
    }

    private static function resolveInstance()
    {
        $name = static::instanceName();

        if (!isset(static::$instance[$name])) {
            static::$instance[$name] = static::$container->get($name);
        }

        return static::$container->get($name);
    }
}