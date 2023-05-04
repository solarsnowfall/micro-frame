<?php

namespace SSF\MicroFramework\Cache;

use InvalidArgumentException as InvalidArgException;
use Psr\SimpleCache\InvalidArgumentException;

class InvalidCacheKeyException extends InvalidArgException implements InvalidArgumentException
{

}