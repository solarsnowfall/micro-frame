<?php

include 'bootstrap.php';

use SSF\MicroFramework\Facades\Config;

var_dump(getenv('MYSQL_USERNAM') ?? 'duh');