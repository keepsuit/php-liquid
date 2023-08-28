<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class SubstituteFilter
{
    public function substitute(string $input, ...$params): string
    {
        return preg_replace_callback('/%\{(\w+)\}/', fn ($match) => $params[$match[1]] ?? '', $input);
    }
}
