<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class SubstituteFilter
{
    public function substitute(string $input, string ...$params): string
    {
        return preg_replace_callback(
            '/%\{(\w+)\}/',
            fn (array $match) => $params[$match[1]] ?? '', $input
        ) ?? '';
    }
}
