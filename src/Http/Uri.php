<?php

namespace SSF\MicroFramework\Http;

use InvalidArgumentException;
use JsonSerializable;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface, JsonSerializable
{
    const DEFAULT_HOST = 'localhost';
    const DEFAULT_PORTS = [
        'ftp' => 21,
        'gopher' => 70,
        'imap' => 143,
        'http' => 80,
        'https' => 443,
        'ldap' => 389,
        'news' => 119,
        'nntp' => 119,
        'pop' => 110,
        'telnet' => 23,
        'tn3270' => 23,
    ];

    private string $fragment = '';

    private string $host = '';

    private string $path = '';

    private ?int $port = null;

    private string $scheme = '';

    private string $userInfo = '';

    private string $query = '';

    public function __construct(string $uri = '')
    {
        $parts = parse_url($uri);

        if ($parts === false) {
            throw new InvalidArgumentException("Invalid uri provieded: $uri");
        }

        $this->scheme = $parts['scheme'] ?? '';
        $this->userInfo = $parts['user'] ?? '';
        $this->host = $parts['host'] ?? '';
        $this->path = $parts['path'] ?? '';
        $this->query = $parts['query'] ?? '';
        $this->fragment = $parts['fragment'] ?? '';

        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $parts['pass'];
        }

        if (isset($parts['port']) && static::isDefaultPort($this->scheme, $parts['port'])) {
            $this->port = $parts['port'];
        }
    }

    public function __toString(): string
    {
        return static::createUri(
            $this->scheme,
            $this->getAuthority(),
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        $authority = $this->host;

        if ($this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->port) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme(string $scheme): UriInterface
    {
        if ($this->scheme === $scheme) {
            return $this;
        }

        $clone = clone $this;
        $clone->scheme = $scheme;

        if ($clone->port && $clone->port = static::portForScheme($scheme)) {
            $clone->port = null;
        }

        return $clone;
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $info = $user;

        if ($password) {
            $info .= ':' . $password;
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $clone = clone $this;
        $clone->userInfo = $info;

        return $clone;
    }

    public function withHost(string $host): UriInterface
    {
        if ($this->host === $host) {
            return $this;
        }

        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    public function withPort(?int $port): UriInterface
    {
        if ($this->port === $port) {
            return $this;
        }

        $clone = clone $this;
        $clone->port = static::isDefaultPort($this->scheme, $port) ? null : $port;

        return $clone;
    }

    public function withPath(string $path): UriInterface
    {
        if ($this->path === $path) {
            return $this;
        }

        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    public function withQuery(string $query): UriInterface
    {
        if ($this->query === $query) {
            return $this;
        }

        $clone = clone $this;
        $this->query = $query;

        return $clone;
    }

    public function withFragment(string $fragment): UriInterface
    {
        if ($this->fragment === $fragment) {
            return $this;
        }

        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    public function jsonSerialize(): mixed
    {
        return $this->__toString();
    }

    public static function createUri(
        ?string $scheme,
        ?string $authority,
        string $path,
        ?string $query,
        ?string $fragment
    ) {
        $uri = '';

        if (!empty($scheme)) {
            $uri .= $scheme . ':';
        }

        if (!empty($authority) || $scheme === 'file') {
            $uri .= '//' . $authority;
            if (!empty($path)) {
                $path = '/' . $path;
            }
        }

        $uri .= $path;

        if (!empty($query)) {
            $uri .= '?' . $query;
        }

        if (!empty($fragment)) {
            $uri .= "#" . $fragment;
        }

        return $uri;
    }

    public static function isDefaultPort(string $scheme, ?int $port): bool
    {
        return static::validDefaultPort($port) && $port == static::DEFAULT_PORTS[$scheme];
    }

    public static function portForScheme(string $scheme): ?int
    {
        return static::DEFAULT_PORTS[$scheme] ?? null;
    }

    public static function validDefaultPort(?int $port): bool
    {
        if (null === $port) {
            return false;
        }

        return in_array($port, static::DEFAULT_PORTS);
    }

    private static function formatAuthority(string $user = '', string $pass = ''): string
    {
        if (empty($user)) {
            return '';
        }

        return $pass ? "{$user}:{$pass}" : $user;
    }
}