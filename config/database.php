<?php

use SSF\MicroFramework\Config\Environment;

return [
    'engine' => 'mysql',
    'mysql' => [
        'default' => [
            'host' => Environment::get('MYSQL_HOSTNAME'),
            'username' => Environment::get('MYSQL_USERNAME'),
            'password' => Environment::get('MYSQL_PASSWORD'),
            'database' => Environment::get('MYSQL_DATABASE'),
            'port' => Environment::get('MYSQL_PORT')
        ]
    ]
];