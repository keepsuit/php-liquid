<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\SyntaxException;

class ErrorDrop extends Drop
{
    public function __get(string $name)
    {
        return match ($name) {
            'standard_error' => throw new \Exception('Standard error'),
            'argument_error' => throw new \InvalidArgumentException('Argument error'),
            'syntax_error' => throw new SyntaxException('Syntax error'),
            'runtime_error' => throw new \RuntimeException('Runtime error'),
            default => throw new \Exception('Unknown error'),
        };
    }
}
