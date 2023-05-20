<?php

namespace SSF\MicroFramework;

use Closure;
use SSF\MicroFramework\Container\Container;
use SSF\MicroFramework\Services\Provider;
use SSF\MicroFramework\Traits\Singleton;

class Application extends Container
{
    use Singleton;

    /**
     * @param string $path
     */
    public function __construct(
        protected readonly string $path
    ) {
        static::setInstance($this);
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->registerServices();
    }

    /**
     * @param string $class
     * @param mixed $definition
     * @return void
     */
    public function bind(string $class, mixed $definition = []): void
    {
        $this->register($class, $definition);
    }

    /**
     * @param string $class
     * @param mixed $definition
     * @return void
     */
    public function singleton(string $class, mixed $definition = []): void
    {
        $this->register($class, $definition, true);
    }

    /**
     * @return Application
     */
    public static function getInstance(): Application
    {
        return static::$instance;
    }

    /**
     * @return void
     */
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