<?php

use Psr\Container\ContainerInterface;
use SSF\MicroFramework\Log\Handler\FileHandler;
use SSF\MicroFramework\Log\Logger;

return [
    \SSF\MicroFramework\Config\Config::class => [
        'directory' => __DIR__ . '/../config'
    ],
    FileHandler::class => [
        'filename' => __DIR__ . '/../log/ssf.log'
    ],
    Logger::class => function(ContainerInterface $container) {
        return new Logger($container->get(FileHandler::class));
    }
];