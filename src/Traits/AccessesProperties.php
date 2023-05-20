<?php

namespace SSF\MicroFramework\Traits;

use ArgumentCountError;

trait AccessesProperties
{
    public function __call(string $name, array $arguments): mixed
    {
        $prefix = substr($name, 0, 3);
        $property = lcfirst(substr($name, 3));

        if ($prefix !== 'get' && $prefix !== 'set' || !property_exists($this, $property)) {
            trigger_error(sprintf("Call to undefined method %s::%s", $this::class, $name), E_USER_ERROR);
        }

        if ($prefix === 'get') {
            return $this->{$property};
        }

        if (count($arguments) < 1) {
            throw new ArgumentCountError(
                sprintf(
                    "Too few arguments to function %s::%s, %s passed", $this::class, $name, count($arguments)
                )
            );
        }

        $this->{$property} = $arguments[0];
        return $this;
    }
}