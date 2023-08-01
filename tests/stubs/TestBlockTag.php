<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class TestBlockTag extends \Keepsuit\Liquid\Block
{
    public static function tagName(): string
    {
        return 'test_block';
    }
}
