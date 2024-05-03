<?php

namespace Keepsuit\Liquid\Exceptions;

class UndefinedVariableException extends LiquidException
{
    public function __construct(string $variable)
    {
        parent::__construct("Variable `$variable` not found");
    }
}
