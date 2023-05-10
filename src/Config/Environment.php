<?php

namespace SSF\MicroFramework\Config;

use InvalidArgumentException;
use SplFileObject;
use SSF\MicroFramework\Traits\Singleton;

class Environment
{
    use Singleton;

    public function __construct(
        private string $filename
    ) {
        if (!file_exists($this->filename)) {
            throw new InvalidArgumentException(sprintf('Unable to access env file: %s', $this->filename));
        }

        static::setInstance($this);
    }

    public static function setup(string $filename): void
    {
        $env = new Environment($filename);
        $env->loadVars();
    }

    /**
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public static function get(string $name, mixed $default = null): mixed
    {
        $value = getenv($name);
        return $value !== false ? $value : $default;
    }

    /**
     * @return void
     */
    public function loadVars(): void
    {
        $lines = file($this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {

            $line = trim($line);

            if (str_starts_with($line, '#')) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);

            if (!isset($_SERVER[$name], $_ENV[$name])) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $_SERVER[$name] = $value;
            }
        }
    }
}