<?php

namespace SSF\MicroFramework\Log;

use DateTimeImmutable;
use Psr\Log\AbstractLogger;
use SSF\MicroFramework\Log\Handler\HandlerInterface;
use Stringable;

class Logger extends AbstractLogger
{
    protected const DEFAULT_DATETIME_FORMAT = 'c';

    public function __construct(
        private HandlerInterface $handler
    ) {}

    /**
     * @inheritDoc
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->handler->handle([
            'timestamp' => (new DateTimeImmutable())->format(static::DEFAULT_DATETIME_FORMAT),
            'level' => strtoupper($level),
            'message' => $this->interpolate((string) $message, $context)
        ]);
    }

    protected function interpolate(string $message, array $context = []): string
    {
        if (empty($context)) {
            return $message;
        }

        $replace = array_map(fn($key, $value) => ["{{$key}}" => $value], $context);

        return strtr($message, $replace);
    }
}