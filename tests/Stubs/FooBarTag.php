<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Tag;

class FooBarTag extends Tag
{
    public static function tagName(): string
    {
        return 'foobar';
    }

    public function render(Context $context): string
    {
        return ' ';
    }
}
