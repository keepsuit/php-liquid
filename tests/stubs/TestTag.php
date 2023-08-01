<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Tag;

class TestTag extends Tag
{
    public static function tagName(): string
    {
        return 'test';
    }
}
