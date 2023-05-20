<?php

namespace SSF\MicroFramework\Support;

class Arr
{
    public static function anyEmpty(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            if (empty($array[$key])) {
                return true;
            }
        }

        return false;
    }

    public static function isAssoc(array $array): bool
    {
        return ! static::isSequential($array);
    }

    public static function isSequential(array $array): bool
    {
        return $array === [] || array_keys($array) === range(0, count($array) -1);
    }
}