<?php

use SSF\MicroFramework\Support\Str;

include 'bootstrap.php';

$request = new \SSF\MicroFramework\Http\Request('GET', '/');
echo $request;