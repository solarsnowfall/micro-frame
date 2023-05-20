<?php

namespace SSF\MicroFramework\Config;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Configuration
{
    private array $definitions = [];

    public function __construct(
        private readonly string $directory
    ) {
        if (!file_exists($this->directory)) {
            throw new InvalidArgumentException(
                sprintf('Configuration directory not found: %s', $this->directory)
            );
        }

        $this->loadConfigFiles();
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keyArray = explode('.', $key);
        return $this->getDeep($keyArray, $this->definitions) ?? $default;
    }

    /**
     * @param array $keyArray
     * @param array $definition
     * @return array|null
     */
    private function getDeep(array $keyArray, array $definition): mixed
    {
        $key = array_shift($keyArray);

        if (!isset($definition[$key]) || !empty($keyArray) && !is_array($definition[$key])) {
            return null;
        }

        if (is_array($definition[$key])) {
            return $this->getDeep($keyArray, $definition[$key]);
        }

        return $definition[$key];
    }

    /**
     * @return void
     */
    private function loadConfigFiles(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->directory)
        );

        /** @var SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo) {

            if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $key = substr($fileInfo->getFilename(), 0, -4);
            $this->definitions[$key] = include $fileInfo->getPathname();
        }
    }
}