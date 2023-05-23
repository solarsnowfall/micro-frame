<?php

use SSF\MicroFramework\Config\Environment;
use SSF\MicroFramework\Cache\Adapter\ArrayCache;
use SSF\MicroFramework\Cache\Adapter\FileCache;
use SSF\MicroFramework\Facades\Env;

return [
    'engine' => Env::get('CACHE_ENGINE'),
    'array' => ArrayCache::class,
    'file' => FileCache::class
];