<?php

namespace SSF\MicroFramework\Facades;

use Psr\Container\ContainerInterface;
use SSF\MicroFramework\Application;

abstract class Facade
{
    private static Application $application;

    private static array $instance = [];

    abstract public static function instanceName(): string;

    public static function __callStatic(string $name, array $arguments)
    {
        return static::resolveInstance()->$name(...$arguments);
    }

    private static function resolveInstance()
    {
        $name = static::instanceName();

        if (!isset(static::$application)) {
            static::$application = Application::getInstance();
        }

        if (!isset(static::$instance[$name])) {
            static::$instance[$name] = $name !== Application::class
                ? static::$application->get($name)
                : static::$application;
        }

        return static::$instance[$name];
    }
}