<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class TestTagBlockTag extends \Keepsuit\Liquid\TagBlock
{
    public static function tagName(): string
    {
        return 'testblock';
    }
}
