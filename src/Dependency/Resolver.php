<?php

namespace SSF\MicroFramework\Dependency;

use ReflectionClass;
use RuntimeException;
use SSF\MicroFramework\Support\Arr;

class Resolver
{
    public function __construct(
        private string $class,
        private array $parameters = []
    ) {}

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function __invoke(): mixed
    {
        $reflection = new ReflectionClass($this->class);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Cannot instantiate class: $this->class");
        }

        if (!$reflection->hasMethod('__construct') || empty($this->parameters)) {
            return $reflection->newInstance();
        }

        return $reflection->newInstanceArgs($this->resolveInstanceArgs($reflection));
    }

    /**
     * @param ReflectionClass $reflection
     * @return array
     */
    private function resolveInstanceArgs(ReflectionClass $reflection): array
    {
        $arguments = [];
        $isAssoc = Arr::isAssoc($this->parameters);

        foreach ($reflection->getConstructor()->getParameters() as $parameter) {
            $key = $isAssoc ? $parameter->getName() : count($arguments);
            if (isset($this->parameters[$key])) {
                $arguments[] = $this->parameters[$key];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                $arguments[] = null;
            }
        }

        return $arguments;
    }
}