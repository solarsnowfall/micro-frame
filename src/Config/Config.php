<?php

namespace SSF\MicroFramework\Config;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Config
{
    private array $config = [];

    public function __construct(
        private string $directory
    ) {
        if (!file_exists($this->directory)) {
            throw new InvalidArgumentException(sprintf('Config directory not found: %s', $this->directory));
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
        return $this->getDeep($keyArray, $this->config) ?? $default;
    }

    /**
     * @param array $keyArray
     * @param array $config
     * @return mixed
     */
    private function getDeep(array $keyArray, array $config): mixed
    {
        $key = array_shift($keyArray);

        if (!isset($config[$key]) || !empty($keyArray) && !is_array($config[$key])) {
            return null;
        }

        if (is_array($config[$key])) {
            return $this->getDeep($keyArray, $config[$key]);
        }

        return $config[$key];
    }

    /**
     * @return void
     */
    private function loadConfigFiles(): void
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->directory));

        /** @var SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo) {

            if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $key = substr($fileInfo->getFilename(), 0, -4);
            $this->config[$key] = include $fileInfo->getPathname();
        }
    }
}