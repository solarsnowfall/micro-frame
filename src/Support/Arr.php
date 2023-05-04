<?php

namespace SSF\MicroFramework\Support;

class Arr
{
    public static function isAssoc(array $array): bool
    {
        return !static::isIndexed($array);
    }

    public static function isIndexed(array $array): bool
    {
        return $array === [] || $array === range(0, count($array) -1);
    }
}