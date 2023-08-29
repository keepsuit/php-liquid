<?php

namespace Keepsuit\Liquid\Exceptions;

class UndefinedFilterException extends LiquidException
{
    public function __construct(string $filter)
    {
        parent::__construct("Undefined filter `$filter`");
    }
}
