<?php

namespace SSF\MicroFramework\Container;

use Closure;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use SSF\MicroFramework\Support\Arr;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    protected array $bindings = [];

    /**
     * @var array
     */
    protected array $instances = [];

    /**
     * @var array
     */
    protected array $singletons = [];

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        if (! $this->has($id)) {
            throw new NotFoundException("Service not found: $id");
        }

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        $instance = $this->resolve($id, $this->bindings[$id]);

        if (isset($this->singletons[$id])) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    /**
     * @param string $id
     * @param mixed $bindings
     * @param bool $singleton
     * @return void
     */
    public function register(string $id, mixed $bindings, bool $singleton = false): void
    {
        if (!is_object($bindings) && !is_array($bindings)) {
            throw new InvalidArgumentException(
                'A binding must be a Closure, a class instance or an array'
            );
        }

        $this->bindings[$id] = $bindings;

        if ($singleton) {
            $this->singletons[$id] = true;
        }
    }

    /**
     * @param string $id
     * @return void
     */
    public function drop(string $id): void
    {
        unset(
            $this->bindings[$id],
            $this->instances[$id],
            $this->singletons[$id]
        );
    }


    /**
     * @param string $class
     * @param array $parameters
     * @return mixed
     */
    public function make(string $class, array $parameters = []): mixed
    {
        return new $class(...$parameters);
    }

    /**
     * @param string $id
     * @param mixed $bindings
     * @return mixed
     */
    private function resolve(string $id, mixed $bindings): mixed
    {
        if (is_object($bindings)) {
            return $bindings instanceof Closure ? $bindings($this) : $bindings;
        }

        if (class_exists($id)) {
            return $this->make($id, $bindings);
        }

        return $bindings;
    }
}