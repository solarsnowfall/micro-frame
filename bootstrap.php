<?php

include 'vendor/autoload.php';

use SSF\MicroFramework\Config\Environment;
use SSF\MicroFramework\Application;

Environment::setup(__DIR__ . '/.env');

$application = new Application(__DIR__);
$application->run();

return $application;