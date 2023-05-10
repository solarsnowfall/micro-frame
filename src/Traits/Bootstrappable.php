<?php

namespace SSF\MicroFramework\Traits;

trait Bootstrappable
{
    public static function setup(...$args): self
    {
        return new self(...$args);
    }
}