<?php

namespace SSF\MicroFramework\Support;

class Str
{
    protected static array $cache = [];

    public static function kebab(string $string)
    {
        return str_replace('_', '-', static::snake($string));
    }

    public static function lower(string $string): string
    {
        return mb_strtolower($string, 'UTF-8');
    }

    public static function screamingSnake(string $string): string
    {
        return strtoupper(static::snake($string));
    }

    public static function snake(string $string): string
    {
        $string = preg_replace('/([a-z])([A-Z])/', '$1_$2', $string);
        $string = str_replace('-', '_', $string);
        return strtolower($string);
    }

}