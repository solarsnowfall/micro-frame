<?php

include 'vendor/autoload.php';

use SSF\MicroFramework\Config\Env;
use SSF\MicroFramework\Dependency\Container;
use SSF\MicroFramework\Application;

$env = new Env(__DIR__ . '/.env');
$env->loadVars();

$container = new Container(include __DIR__ . '/services/definitions.php');

Application::initialize($container);