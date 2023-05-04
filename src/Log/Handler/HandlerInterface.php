<?php

namespace SSF\MicroFramework\Log\Handler;

interface HandlerInterface
{
    public const TEMPLATE = '%timestamp% [%level%]: %message%';

    public function handle(array $parameters): void;
}