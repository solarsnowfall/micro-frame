<?php

namespace SSF\MicroFramework\Log\Handler;

class FileHandler implements HandlerInterface
{
    public function __construct(
        private string $filename
    ) {
        $directory = dirname($this->filename);
        if (!file_exists($directory) && !mkdir($directory, 0777, true)) {
            throw new \UnexpectedValueException(sprintf('Directory not found: %s', $directory));
        }
    }

    public function handle(array $parameters): void
    {
        $search = array_map(fn($key) => "%{$key}%", array_keys($parameters));
        $output = str_replace($search, $parameters, static::TEMPLATE) . PHP_EOL;
        file_put_contents($this->filename, $output, FILE_APPEND);
    }
}