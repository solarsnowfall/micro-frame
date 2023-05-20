<?php

use Psr\Container\ContainerInterface;
use SSF\MicroFramework\Config\Configuration;
use SSF\MicroFramework\Config\Env;
use SSF\MicroFramework\Log\Handler\FileHandler;
use SSF\MicroFramework\Log\Logger;

return [
    Env::class => [
        'filename' => __DIR__ . '/../.env',
    ],
    Configuration::class => [
        'directory' => __DIR__ . '/../config',
    ],
    FileHandler::class => [
        'filename' => __DIR__ . '/../log/ssf.log',
    ],
    Logger::class => function(ContainerInterface $container) {
        return new Logger($container->get(FileHandler::class));
    }
];