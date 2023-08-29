<?php

namespace Keepsuit\Liquid\Exceptions;

class UndefinedDropMethodException extends LiquidException
{
    public function __construct(string $method)
    {
        parent::__construct("Undefined Drop method `$method`");
    }
}
