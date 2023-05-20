<?php

use SSF\MicroFramework\Config\Environment;
use SSF\MicroFramework\Cache\Adapter\ArrayCache;
use SSF\MicroFramework\Cache\Adapter\FileCache;

return [
    'engine' => Environment::get('CACHE_ENGINE'),
    'array' => ArrayCache::class,
    'file' => FileCache::class
];