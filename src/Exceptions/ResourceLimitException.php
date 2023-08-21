<?php

namespace Keepsuit\Liquid\Exceptions;

class ResourceLimitException extends LiquidException
{
    public function __construct()
    {
        parent::__construct('Memory limit exceeded');
    }
}
