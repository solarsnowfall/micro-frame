<?php

use SSF\MicroFramework\Facades\Env;

return [
    'engine' => 'mysql',
    'mysql' => [
        'default' => [
            'host' => Env::get('MYSQL_HOSTNAME'),
            'username' => Env::get('MYSQL_USERNAME'),
            'password' => Env::get('MYSQL_PASSWORD'),
            'database' => Env::get('MYSQL_DATABASE'),
            'port' => Env::get('MYSQL_PORT')
        ]
    ]
];