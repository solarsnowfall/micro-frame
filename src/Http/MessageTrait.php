<?php

namespace SSF\MicroFramework\Http;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    private array $headers = [];

    private array $headerNames = [];

    private string $protocol = '1.1';

    private StreamInterface $stream;

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $clone = clone $this;
        $clone->protocol = $version;

        return $clone;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeader(string $header, $value): void
    {
        $this->checkHeaderName($header);
        $this->checkHeaderValue($value);
        $name = $this->headerName($header);

        if (isset($this->headerNames[$name])) {
            $this->headers[$this->headerNames[$name]] = array_merge($this->headers[$this->headerNames[$name]], $value);
        } else {
            $this->headerNames[$name] = $header;
            $this->headers[$header] = $value;
        }
    }

    public function setHeaders(array $headers): void
    {
        $this->headerNames = $this->headers = [];

        foreach ($headers as $header => $value) {
            $this->setHeader($header, $value);
        }
    }

    public function hasHeader(string $header): bool
    {
        return isset($this->headerNames[$this->headerName($header)]);
    }

    public function getHeader(string $header): array
    {
        return $this->headerValue($header);
    }

    public function getHeaderLine(string $header): string
    {
        return implode(', ', $this->getHeader($header));
    }

    public function withHeader(string $header, $value): MessageInterface
    {
        $clone = clone $this;
        $clone->headerNames[$this->headerName($header)] = $header;
        $clone->headers[$header] = $value;

        return $clone;
    }

    public function withAddedHeader(string $header, $value): MessageInterface
    {
        $this->checkHeaderName($header);
        $this->checkHeaderValue($value);
        $name = $this->headerName($header);
        $clone = clone $this;

        if (isset($clone->headerNames[$name])) {
            $clone->headers[$this->headerNames[$name]] = array_merge($this->headerValue($header), $value);
        } else {
            $clone->headerNames[$name] = $header;
            $clone->headers[$header] = $value;
        }

        return $clone;
    }

    public function withoutHeader(string $header): MessageInterface
    {
        $name = $this->headerName($header);

        if (!isset($this->headerNames[$name])) {
            return $this;
        }

        $clone = clone $this;
        unset($clone->headerNames[$name], $clone->headers[$header]);

        return $clone;
    }

    public function getBody(): StreamInterface
    {
        return $this->stream;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        if ($body === $this->stream) {
            return $this;
        }

        $clone = clone $this;
        $clone->stream = $body;

        return $clone;
    }



    private function headerName(string $header): string
    {
        return strtolower($header);
    }

    private function headerValue(string $header): array
    {
        return $this->headers[$this->headerNames[$this->headerName($header)]] ?? [];
    }

    private function checkHeaderName(string $header)
    {
        if (! preg_match('/^[a-z0-9\'`#$%&*+.^_|~!-]+$/i', $header)) {
            throw new InvalidArgumentException("Invalid header name provided: $header");
        }
    }

    private function checkHeaderValue($value)
    {

        if (!preg_match('/^[ -~\t]*$/', $value)) {
            throw new InvalidArgumentException("Invalid header value, must contain only ASCII characters");
        }

        if (!preg_match('/^[\x20-\x7E\x09]*$/', $value)) {
            throw new InvalidArgumentException("Invalid header provided: $value");
        }
    }
}