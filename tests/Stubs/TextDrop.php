<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class TextDrop extends Drop
{
    public function array(): array
    {
        return ['text1', 'text2'];
    }

    public function text(): string
    {
        return 'text1';
    }
}
