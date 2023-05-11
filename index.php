<?php

include 'bootstrap.php';

use SSF\MicroFramework\Application as App;

$cache = App::getInstance()->get(\SSF\MicroFramework\Cache\Cache::class);

var_dump($cache);