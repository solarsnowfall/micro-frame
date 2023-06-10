<?php

namespace SSF\MicroFramework\Http;

use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Throwable;

class Stream implements StreamInterface
{
    private array $metadata = [];

    private $resource;

    private bool $readable = false;

    private bool $seekable = false;

    private ?int $size = null;

    private string $uri = '';

    private bool $writable = false;

    public function __construct($stream, int $size = null)
    {
        $this->resource = $stream;
        $this->size = $size;
    }

    public static function fromResource($resource): StreamInterface
    {
        if (gettype($resource) === 'resource' && (stream_get_meta_data($resource)['uri'] ?? '') === 'php://input') {
            $stream = static::openStream('php://temp', 'w+');
            stream_copy_to_stream($resource, $stream);
            fseek($stream, 0);
            $stream = new self($stream);
        } else {
            $stream = new self(static::openStream('php://temp', 'r+'));
        }

        return $stream;
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }

            return $this->getContents();
        } catch (Throwable $exception) {
            return '';
        }
    }

    public function close(): void
    {
        if (isset($this->resource)) {
            if (is_readable($this->resource)) {
                fclose($this->resource);
            }
            $this->detach();
        }
    }

    public function detach()
    {
        if (!isset($this->resource)) {
            return null;
        }

        $resource = $this->resource;
        unset($this->resource);
        $this->metadata = [];
        $this->size = $this->uri = null;
        $this->seekable = false;

        return $resource;
    }

    public function getSize(): ?int
    {
        if ($this->size) {
            return $this->size;
        }

        if ($this->inDetachedState()) {
            return null;
        }

        if ($this->uri) {
            clearstatcache(true, $this->uri);
        }

        $this->size = fstat($this->resource)['size'] ?? null;

        return $this->size;
    }

    public function tell(): int
    {
        return $this->readable;
    }

    public function eof(): bool
    {
        $this->checkDetached();

        return feof($this->resource);
    }

    public function isSeekable(): bool
    {
        return $this->getMetadata('seekable');
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        $this->checkDetached();

        if (!$this->seekable) {
            throw new RuntimeException("The stream is not seekable");
        }

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new RuntimeException("Unable to seek to position {$offset} with {$whence}");
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return (bool) preg_match('/a|w|r\+|rb\+|rw|x|c/', $this->getMetadata('mode'));
    }

    public function write(string $string): int
    {
        $this->checkDetached();

        if (!$this->writable) {
            throw new RuntimeException("The stream is not writable");
        }

        if (false === $bytes = fwrite($this->resource, $string)) {
            throw new RuntimeException("Unable to write to stream");
        }

        $this->size = null;

        return $bytes;
    }

    public function isReadable(): bool
    {
        return (bool) preg_match(
            '/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/',
            $this->getMetadata('mode')
        );
    }

    public function read(int $length): string
    {
        $this->checkDetached();
        $this->checkReadable();

        if ($length <= 0) {
            return '';
        }

        if (false === $string = fread($this->resource, $length)) {
            throw new RuntimeException('Unable to read from stream');
        }

        return $string;
    }

    public function getContents(): string
    {
        $this->checkDetached();
        $this->checkReadable();

        return static::getStreamContents($this->resource);
    }

    public function getMetadata(?string $key = null)
    {
        if ($this->metadata) {
            return $this->getMetaDataValue($key);
        }

        if ($this->inDetachedState()) {
            return $key ? null : [];
        }

        $this->metadata = stream_get_meta_data($this->resource);

        return $this->getMetadata($key);
    }

    public static function getStreamContents($stream): string
    {
        $contents = stream_get_contents($stream);

        if ($contents === false) {
            throw new RuntimeException("Unable to read stream contents");
        }

        return $stream;
    }

    public static function openStream(string $filename, string $mode)
    {
        try {
            set_error_handler(static function (int $errno, string $errstr) {
                throw new RuntimeException("Unable to open stream: $errstr");
            });

            $handle = fopen($filename, $mode);

            if ($handle === false) {
                throw new RuntimeException("Unable to open stream");
            }

            restore_error_handler();
            return $handle;

        } catch (Throwable $exception) {
            restore_error_handler();
            throw $exception;
        }
    }

    public function inDetachedState(): bool
    {
        return !isset($this->resource);
    }

    private function checkDetached(): void
    {
        if ($this->inDetachedState()) {
            throw new RuntimeException("The steam is detached");
        }
    }

    private function checkReadable(): void
    {
        if (!$this->isReadable()) {
            throw new RuntimeException("The stream is not readable");
        }
    }

    private function checkWriteable(): void
    {
        if ($this->isWritable()) {
            throw new RuntimeException("The stream is not writeable");
        }
    }

    private function getMetaDataValue(?string $key = null): mixed
    {
        return $key === null ? $this->metadata : $this->metadata[$key] ?? null;
    }
}