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

        if (isset($this->singletons[$id])) {
            return $this->singletons[$id];
        }

        if ($this->bindings[$id] instanceof Closure) {
            return $this->bindings[$id]($this);
        }

        if (class_exists($id)) {
            $this->make($id);
        }

        return $this->bindings[$id];
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

    private function make(string $class): mixed
    {
        $reflector = new ReflectionClass($class);

        if (! $reflector->hasMethod('__construct')) {
            return $reflector->newInstance();
        }

        return $reflector->newInstanceArgs(
            $this->resolveInstanceArguments($reflector)
        );
    }

    private function resolveInstanceArguments(ReflectionClass $reflector): array
    {
        $arguments = [];
        $isAssoc = Arr::isAssoc($this->bindings[$reflector->name]);

        foreach ($reflector->getConstructor()->getParameters() as $parameter) {
            $key = $isAssoc ? $parameter->getName() : count($arguments);
            if (isset($this->bindings[$reflector->name][$key])) {
                $arguments[] = $parameter[$key];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                $arguments[] = null;
            }
        }

        return $arguments;
    }
}