<?php

namespace SSF\MicroFramework\Facades;

use SSF\MicroFramework\Application;

class App extends Facade
{

    public static function instanceName(): string
    {
        return Application::class;
    }
}