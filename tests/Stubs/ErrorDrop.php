<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Exceptions\SyntaxException;

class ErrorDrop extends Drop
{
    protected function liquidMethodMissing(string $name): mixed
    {
        return match ($name) {
            'standard_error' => throw new StandardException('Standard error'),
            'argument_error' => throw new InvalidArgumentException('Argument error'),
            'syntax_error' => throw new SyntaxException('Syntax error'),
            'runtime_error' => throw new \RuntimeException('Runtime error'),
            default => throw new \Exception('Unknown error'),
        };
    }
}
