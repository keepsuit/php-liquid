<?php

namespace Keepsuit\Liquid\Exceptions;

class StackLevelException extends LiquidException
{
    public function __construct(string $message = 'Nesting too deep', int $code = 0, int $severity = 1, ?string $filename = __FILE__, ?int $line = __LINE__, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $severity, $filename, $line, $previous);
    }
}
