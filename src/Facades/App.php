<?php

namespace SSF\MicroFramework\Facades;

use Closure;
use SSF\MicroFramework\Application;

/**
 * @method static void run()
 * @method static void bind(string $class, mixed $definition)
 * @method static void singleton(string $class, mixed $definition)
 * @method static mixed make(string $class, mixed $definition)
 * @method static bool has(string $id)
 */
class App extends Facade
{

    public static function instanceName(): string
    {
        return Application::class;
    }
}