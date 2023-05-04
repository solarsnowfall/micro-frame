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

    public function get(string $key)
    {
        $keyArray = explode('.', $key);
        return $this->getDeep($keyArray, $this->config);
    }

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