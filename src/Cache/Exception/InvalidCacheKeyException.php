<?php

namespace SSF\MicroFramework\Cache\Exception;

use InvalidArgumentException as InvalidArgException;
use Psr\SimpleCache\InvalidArgumentException;

class InvalidCacheKeyException extends InvalidArgException implements InvalidArgumentException
{

}