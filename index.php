<?php

include 'bootstrap.php';

use SSF\MicroFramework\Facades\Cache;
use SSF\MicroFramework\Support\Arr;

$cache = new \SSF\MicroFramework\Cache\Adapter\Apcu([]);
$values = ['a' => 1, 'b' => 2, 'c' => 3];
var_dump($cache->setMultiple($values));
var_dump($cache->getMultiple(['a', 'b', 'c', 'd']));