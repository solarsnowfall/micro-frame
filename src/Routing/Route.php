<?php

namespace SSF\MicroFramework\Routing;

use InvalidArgumentException;

class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array<string>
     */
    private $parameters = [];

    /**
     * @var array<string>
     */
    private $methods = [];

    /**
     * @var array<string>
     */
    private $vars = [];

    /**
     * Route constructor.
     * @param string $name
     * @param string $path
     * @param array $parameters
     *    $parameters = [
     *      0 => (string) Controller name : HomeController::class.
     *      1 => (string|null) Method name or null if invoke method
     *    ]
     * @param array $methods
     */
    public function __construct(string $name, string $path, array $parameters, array $methods = ['GET'])
    {
        if (empty($methods)) {
            throw new InvalidArgumentException(
                'HTTP methods argument was empty; must contain at least one method'
            );
        }

        $this->name = $name;
        $this->path = $path;
        $this->parameters = $parameters;
        $this->methods = $methods;
    }

    public function match(string $path, string $method): bool
    {
        $path = self::trimPath($path);
        $pattern = $this->getPath();

        foreach ($this->getVarNames() as $variable) {
            $varName = trim($variable, '{\}');
            $pattern = str_replace($variable, '(?P<' . $varName . '>[^/]++)', $pattern);
        }

        if ([] !== $matches = $this->patternMatchesForMethod($method, $pattern, $path)) {

            $values = array_filter($matches, fn($key) => is_string($key), ARRAY_FILTER_USE_KEY);

            foreach ($values as $key => $value) {
                $this->vars[$key] = $value;
            }

            return true;
        }

        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getVarNames(): array
    {
        preg_match_all('/{[^}]*}/', $this->path, $matches);
        return reset($matches) ?? [];
    }

    public function hasVars(): bool
    {
        return $this->getVarNames() !== [];
    }

    public function getVars(): array
    {
        return $this->vars;
    }

    public static function trimPath(string $path): string
    {
        return '/' . rtrim(ltrim(trim($path), '/'), '/');
    }

    private function patternMatchesForMethod(string $method, string $pattern, string $path): array
    {
        if (! in_array($method, $this->methods)) {
            return [];
        }

        preg_match('#^' . $pattern . '$#sD', static::trimPath($pattern), $matches);
        return $matches;
    }
}