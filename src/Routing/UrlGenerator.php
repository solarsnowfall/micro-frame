<?php

namespace SSF\MicroFramework\Routing;

use ArrayAccess;
use InvalidArgumentException;

class UrlGenerator
{
    /**
     * @param Route[] $routes
     */
    public function __construct(
        private readonly array $routes
    ) {}

    public function generate(string $name, array $parameters = []): string
    {
        if (! isset($this->routes[$name])) {
            throw new InvalidArgumentException("Unknown route name: $name");
        }

        $route = $this->routes[$name];

        if ($route->hasVars() && empty($parameters)) {
            throw new InvalidArgumentException(
                "Route '$name' missing expected parameters: " . implode(', ', $route->getVarNames())
            );
        }

        return $this->resolveUri($route, $parameters);
    }

    private function resolveUri(Route $route, array $parameters)
    {
        $uri = $route->getPath();

        foreach ($route->getVarNames() as $variable) {

            $varName = trim($variable, '{\}');

            if (isset($parameters[$varName])) {
                throw new \InvalidArgumentException(
                    sprintf('%s not found in parameters to generate url', $varName)
                );
            }

            $uri = str_replace($variable, $parameters[$varName], $uri);
        }

        return $uri;
    }
}