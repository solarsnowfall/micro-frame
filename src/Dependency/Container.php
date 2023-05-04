<?php

namespace SSF\MicroFramework\Dependency;

use Closure;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $definitions = [];

    private array $instances = [];

    public function __construct(array $definitions)
    {
        $this->definitions = array_merge(
            $definitions,
            [ContainerInterface::class => $this]
        );
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf('Class not found: %s', $id));
        }

        if (!isset($this->instances[$id])) {
            return $this->instances[$id] = $this->make($id, $this->definitions[$id]);
        }

        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]) || isset($this->instances[$id]);
    }

    private function make(string $id, array|Closure $definition)
    {
        if ($definition instanceof Closure) {
            return $definition($this);
        }

        if (class_exists($id)) {
            $resolver = new Resolver($id, $this->definitions[$id] ?? []);
            return $resolver();
        }

        return $definition;
    }
}