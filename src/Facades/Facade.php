<?php

namespace SSF\MicroFramework\Facades;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SSF\MicroFramework\Application;

abstract class Facade
{
    /**
     * @var Application
     */
    private static Application $application;

    /**
     * @var array
     */
    private static array $instance = [];

    /**
     * @return string
     */
    abstract public static function instanceName(): string;

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return static::resolveInstance()->$name(...$arguments);
    }

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function resolveInstance(): mixed
    {
        $name = static::instanceName();

        if (! isset(static::$application)) {
            static::$application = Application::getInstance();
        }

        if (! isset(static::$instance[$name])) {
            static::$instance[$name] = $name !== Application::class
                ? static::$application->get($name)
                : static::$application;
        }

        return static::$instance[$name];
    }
}