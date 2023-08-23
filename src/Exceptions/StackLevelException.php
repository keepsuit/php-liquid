<?php

namespace Keepsuit\Liquid\Exceptions;

class StackLevelException extends LiquidException
{
    public function __construct(string $message = 'Nesting too deep', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
