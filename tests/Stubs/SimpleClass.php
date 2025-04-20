<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class SimpleClass
{
    public string $simpleProperty = 'foo';

    public ?string $nullProperty = null;

    protected string $protectedProperty = 'foo';

    public static string $staticProperty = 'foo';

    public static ?string $staticNullProperty = null;

    public function simpleMethod(): string
    {
        return 'foo';
    }
}
