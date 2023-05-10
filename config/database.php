<?php

use SSF\MicroFramework\Config\Environment;

return [
    'mysql' => [
        'host' => Environment::get('MYSQL_HOSTNAME'),
        'username' => Environment::get('MYSQL_USERNAME'),
        'password' => Environment::get('MYSQL_PASSWORD'),
        'database' => Environment::get('MYSQL_DATABASE'),
        'port' => Environment::get('MYSQL_PORT')
    ]
];