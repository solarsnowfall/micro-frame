<?php

namespace SSF\MicroFramework;

use Psr\Container\ContainerInterface;
use SSF\MicroFramework\Facades\Facade;

class Application
{
    private static self $application;

    public function __construct(
        private ContainerInterface $container
    ) {}

    public static function initialize(ContainerInterface $container): void
    {
        static::$application = new Application($container);

        Facade::setContainer(static::$application->container);
    }
}