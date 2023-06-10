<?php

namespace SSF\MicroFramework\Support;

use Closure;
use SSF\MicroFramework\Traits\CachesReturns;

class Str
{
    use CachesReturns;

    /**
     * @param string $string
     * @return string
     */
    public static function camel(string $string): string
    {
        return static::checkCache(__FUNCTION__, $string, function() use ($string) {
            return lcfirst(static::studly($string));
        });
    }

    /**
     * @param string $string
     * @return string
     */
    public static function kebab(string $string): string
    {
        return static::checkCache(__FUNCTION__, $string, function() use ($string) {
            return str_replace('_', '-', static::snake($string));
        });
    }

    /**
     * @param string $string
     * @return string
     */
    public static function lower(string $string): string
    {
        return static::checkCache(__FUNCTION__, $string, function() use ($string) {
            return mb_strtolower($string, 'UTF-8');
        });
    }

    /**
     * @param string $string
     * @return string
     */
    public static function screamingKebab(string $string): string
    {
        return static::checkCache(__FUNCTION__, $string, function() use ($string) {
            return static::upper(static::kebab($string));
        });
    }

    /**
     * @param string $string
     * @return string
     */
    public static function screamingSnake(string $string): string
    {
        return static::checkCache(__FUNCTION__, $string, function() use ($string) {
            return static::upper(static::snake($string));
        });
    }

    /**
     * @param string $string
     * @return string
     */
    public static function snake(string $string): string
    {
        return static::checkCache(__FUNCTION__, $string, function() use ($string) {
            $string = preg_replace('/([a-z])([A-Z])/', '$1_$2', $string);
            $string = str_replace([' ', '-'], '_', $string);
            return strtolower($string);
        });
    }

    /**
     * @param string $string
     * @return string
     */
    public static function studly(string $string): string
    {
        return static::checkCache(__FUNCTION__, $string, function() use ($string) {
            return str_replace('_', '', ucwords(static::snake($string), '_'));
        });
    }

    /**
     * @param string $string
     * @param int $start
     * @param int $length
     * @return string
     */
    public static function substr(string $string, int $start, int $length): string
    {
        $key = $string . $string . $length;
        return static::checkCache(__FUNCTION__, $key, function() use ($string, $start, $length) {
            return mb_substr($string, $start, $length, 'UTF-8');
        });
    }

    /**
     * @param string $string
     * @param int $length
     * @param string $end
     * @return string
     */
    public static function truncate(string $string, int $length, string $end = '...'): string
    {
        $key = $string . $length;
        return static::checkCache(__FUNCTION__, $key, function() use ($string, $length, $end) {
            if (strlen($string) <= $length) {
                return $string;
            }

            $words = explode(' ', $string);
            do {
                $string = implode(' ', $words);
                array_pop($words);
            } while (strlen($string) > $length);

            return $string . $end;
        });
    }

    /**
     * @param string $string
     * @return string
     */
    public static function upper(string $string): string
    {
        return static::checkCache(__FUNCTION__, $string, function() use ($string) {
            return mb_strtoupper($string, 'UTF-8');
        });
    }

    /**
     * @param string $string
     * @param int $count
     * @param string $end
     * @return string
     */
    public static function words(string $string, int $count, string $end = '...'): string
    {
        $words = explode(' ', $string);
        return implode(' ', array_slice($words, 0, $count)) . $end;
    }
}