<?php

namespace Keepsuit\Liquid\Exceptions;

class StackLevelException extends LiquidException
{
    public static function nestingTooDeep(): StackLevelException
    {
        return new self('Nesting too deep');
    }
}
