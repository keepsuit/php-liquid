<?php

namespace Keepsuit\Liquid\Exceptions;

class UndefinedVariableException extends LiquidException
{
    public function __construct(string $method)
    {
        parent::__construct("Variable `$method` not found");
    }
}
