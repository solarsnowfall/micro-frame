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
    protected array $bindings = [];

    protected array $instances = [];

    protected array $singletons = [];

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    public function get(string $id)
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

    private function resolve(string $id, array|Closure $bindings)
    {
        if ($bindings instanceof Closure) {
            return $bindings($this);
        } elseif (class_exists($id)) {
            return $this->make($id, $bindings);
        }

        return $bindings;
    }

    public function set(string $id, Closure|array|null $bindings, bool $singleton = false): void
    {
        $this->bindings[$id] = $bindings;

        if ($singleton) {
            $this->singletons[$id] = true;
        }
    }

    public function drop(string $id): void
    {
        unset(
            $this->bindings[$id],
            $this->instances[$id],
            $this->singletons[$id]
        );
    }

    private function make(string $class, array $bindings = []): mixed
    {
        $reflector = new ReflectionClass($class);

        if (! $reflector->hasMethod('__construct')) {
            return $reflector->newInstance();
        }

        return $reflector->newInstanceArgs(
            $this->resolveInstanceArguments($reflector, $bindings)
        );
    }

    private function makeSingleton(string $class, array $bindings): mixed
    {
        if (! isset($this->instances[$class])) {
            $this->instances[$class] = $this->make($class, $bindings);
        }

        return $this->instances[$class];
    }

    private function resolveInstanceArguments(ReflectionClass $reflector, array $bindings): array
    {
        $arguments = [];
        $isAssoc = Arr::isAssoc($bindings);

        foreach ($reflector->getConstructor()->getParameters() as $parameter) {
            $key = $isAssoc ? $parameter->getName() : count($arguments);
            if (isset($bindings[$key])) {
                $arguments[] = $bindings[$key];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                $arguments[] = null;
            }
        }

        return $arguments;
    }
}