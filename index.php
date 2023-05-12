<?php

include 'bootstrap.php';

use SSF\MicroFramework\Facades\Cache;

Cache::set('test', 'toast');
echo Cache::get('test');