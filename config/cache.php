<?php

use SSF\MicroFramework\Config\Environment;
use SSF\MicroFramework\Cache\Adapter\ArrayCache;

return [
    'engine' => Environment::get('CACHE_ENGINE'),
    'array' => ArrayCache::class
];