<?php

namespace SSF\MicroFramework;

use Closure;
use SSF\MicroFramework\Container\Container;
use SSF\MicroFramework\Services\Provider;
use SSF\MicroFramework\Traits\Singleton;

class Application extends Container
{
    use Singleton;

    public function __construct(
        protected string $path
    ) {
        static::setInstance($this);
    }

    public function run(): void
    {
        $this->registerServices();
    }

    public function bind(string $class, Closure|array $definition = []): void
    {
        $this->set($class, $definition);
    }

    public function singleton(string $class, Closure|array $definition = []): void
    {
        $this->set($class, $definition, true);
    }

    /**
     * @return Application
     */
    public static function getInstance(): Application
    {
        return static::$instance;
    }

    protected function registerServices(): void
    {
        foreach (Provider::register() as $id => $definition) {
            $this->bind($id, $definition);
        }

        foreach (Provider::registerSingleton() as $id => $definition) {
            $this->singleton($id, $definition);
        }
    }
}