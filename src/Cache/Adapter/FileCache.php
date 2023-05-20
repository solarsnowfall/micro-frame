<?php

namespace SSF\MicroFramework\Cache\Adapter;


use DateInterval;
use FilesystemIterator;
use Generator;
use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileCache extends AbstractAdapter implements CacheInterface
{
    const DEFAULT_TTL = 30*24*60*60;

    /**
     * @param string $path
     */
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

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getMultiple([$key], $default)[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param DateInterval|int|null $ttl
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        return $this->setMultiple([$key => $value], $ttl);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return @unlink($this->getFilename($key));
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        foreach ($this->listFiles() as $filename) {
            @unlink($filename);
        }

        return true;
    }

    /**
     * @param iterable $keys
     * @param mixed|null $default
     * @return iterable
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keys = static::convertKeysToArray($keys);
        $values = [];

        foreach ($keys as $key) {

            $filename = $this->getFilename($key);

            if (! file_exists($filename)) {
                $values[$key] = $default;
                continue;
            }

            $modified = $this->getFileModificationTime($filename);

            if ($modified === false) {
                $values[$key] = $default;
                continue;
            }

            if ($modified < time()) {
                unlink($filename);
                $values[$key] = $default;
                continue;
            }

            $contents = file_get_contents($filename);
            $value = unserialize($contents);
            $values[$key] = $value ?: $default;
        }

        return $values;
    }

    /**
     * @param iterable $values
     * @param DateInterval|int|null $ttl
     * @return bool
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $values = static::convertValuesToArray($values);
        $seconds = static::convertTtlToSeconds($ttl);
        $mtime = $seconds !== 0 ? time() + $seconds : 0;
        $count = 0;

        foreach ($values as $key => $value) {
            $filename = $this->getFilename($key);
            $count += (int) $this->saveFile($filename, serialize($value), $mtime);
        }

        return count($values) === $count;
    }

    /**
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $keys = static::convertKeysToArray($keys);
        $count = 0;

        foreach ($keys as $key) {
            $count += (int) $this->delete($key);
        }

        return count($keys) === $count;
    }

    /**
     * @param string $key
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function has(string $key): bool
    {
        return $this->get($key, false) != false;
    }

    /**
     * @return Generator
     */
    public function listFiles(): Generator
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

    /**
     * @return int
     */
    public function purgeExpired(): int
    {
        $time = time();
        $count = 0;

        foreach ($this->listFiles() as $filename) {
            if (filemtime($filename) < $time) {
                @unlink($filename);
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param string $key
     * @param bool $validateKey
     * @return string
     */
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

    /**
     * @param string $filename
     * @param string $contents
     * @param int $mtime
     * @return bool
     */
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