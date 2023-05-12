<?php

namespace SSF\MicroFramework\Cache\Adapter;


use Psr\SimpleCache\CacheInterface;

use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileCache extends AbstractAdapter implements CacheInterface
{
    const DEFAULT_TTL = 30*24*60*60;

    public function __construct(
        protected string $path
    ) {
        $realPath = realpath($path);

        if ($realPath === false) {
            throw new InvalidArgumentException(sprintf('Path not found: %s', $path));
        }

        if (! is_writable($realPath)) {
            throw new InvalidArgumentException(sprintf('Path cannot be written to: %s', $path));
        }
    }
    public function get(string $key, mixed $default = null): mixed
    {
        // TODO: Implement get() method.
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        // TODO: Implement set() method.
    }

    public function delete(string $key): bool
    {
        // TODO: Implement delete() method.
    }

    public function clear(): bool
    {
        // TODO: Implement clear() method.
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        // TODO: Implement getMultiple() method.
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        // TODO: Implement setMultiple() method.
    }

    public function deleteMultiple(iterable $keys): bool
    {
        // TODO: Implement deleteMultiple() method.
    }

    public function has(string $key): bool
    {
        // TODO: Implement has() method.
    }

    public function listFiles(): \Generator
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->path,
                FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $path) {
            yield $path;
        }
    }

    protected function getFilename(string $key, bool $validateKey = true): string
    {
        if ($validateKey) {
            static::validateKey($key);
        }

        $hash = hash('sha256', $key);
        $path = str_split(substr($hash, 0, 2));
        $path[] = substr($hash, 2);

        return $this->path . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @param string $filename
     * @return int|false
     */
    protected function getFileModificationTime(string $filename): int|false
    {
        return @filemtime($filename);
    }

    protected function saveFile(string $filename, string $contents, int $mtime): bool
    {
        if (! file_exists($filename)) {

            $parts = explode(DIRECTORY_SEPARATOR, $filename);
            $path = '';

            while (count($parts) > 1) {
                $path .= array_shift($parts);
                if (! file_exists($path)) {
                    mkdir($path, '0775');
                }
                $path .= DIRECTORY_SEPARATOR;
            }
        }

        if (@file_put_contents($filename, $contents) === false) {
            return false;
        }

        if ($mtime && !@touch($filename, $mtime)) {
            unlink($filename);
            return false;
        }

        return true;
    }
}