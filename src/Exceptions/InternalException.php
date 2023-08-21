<?php

namespace Keepsuit\Liquid\Exceptions;

class InternalException extends LiquidException
{
    public function __construct(\Throwable $exception)
    {
        parent::__construct('Internal exception', previous: $exception);
    }
}
