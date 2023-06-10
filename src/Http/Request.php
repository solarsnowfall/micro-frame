<?php

namespace SSF\MicroFramework\Http;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
   use MessageTrait;

   private string $method;

   private ?string $requestTarget;

   private UriInterface $uri;

   public function __construct(
       string $method,
       string|UriInterface $uri,
       array $headers = [],
       StreamInterface|null $body = null,
       string $version = '1.1'
   ) {
       if (is_string($uri)) {
           $uri = new Uri($uri);
       }

       $this->method = $method;
       $this->uri = $uri;
       $this->headers = $headers;
       $this->stream = Stream::fromResource($body);
       $this->protocol = $version;
   }

    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath() ?? '/';

        if ($this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException('Request target cannot contain whitespace');
        }

        $clone = clone $this;
        $clone->requestTarget = $requestTarget;
        return $clone;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        if (empty($method)) {
            throw new InvalidArgumentException("Request method cannot be empty");
        }

        $clone = clone $this;
        $this->method = strtoupper($method);
        return $clone;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        if ($this->uri === $uri) {
            return $this;
        }

        $clone = clone $this;
        $clone->uri = $uri;

        if (!$preserveHost && !isset($this->headerNames['host'])) {
            if ($host = $this->uri->getHost()) {
                if ($port = $this->uri->getPort()) {
                    $host .= ":$port";
                }
                $this->headerNames['host'] = 'Host';
                $this->headers['host'] = [$host];
            }
        }

        return $clone;
    }
}