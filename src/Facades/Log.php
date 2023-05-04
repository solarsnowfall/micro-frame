<?php

namespace SSF\MicroFramework\Facades;

use SSF\MicroFramework\Log\Logger;
use Stringable;

/**
 * @method static alert(string|Stringable $message, array $context = [])
 * @method static critical(string|Stringable $message, array $context = [])
 * @method static debug(string|Stringable $message, array $context = [])
 * @method static emergency(string|Stringable $message, array $context = [])
 * @method static error(string|Stringable $message, array $context = [])
 * @method static info(string|Stringable $message, array $context = [])
 * @method static notice(string|Stringable $message, array $context = [])
 * @method static warning(string|Stringable $message, array $context = [])
 */
class Log extends Facade
{

    public static function instanceName(): string
    {
        return Logger::class;
    }
}