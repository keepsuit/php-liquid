<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class SimpleClass
{
    public string $simpleProperty = 'foo';
    public ?string $nullProperty = null;
    protected string $protectedProperty = 'foo';

    public function simpleMethod(): string
    {
        return 'foo';
    }
}
