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
        if (!isset(static::$application)) {
            static::$application = Application::getInstance();
        }

        $name = static::instanceName();

        if (!isset(static::$instance[$name])) {
            static::$instance[$name] = static::$application->get($name);
        }

        return static::$application->get($name);
    }
}