<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class FakeTag extends \Keepsuit\Liquid\Block {
    public static function name(): string
    {
        return 'fake';
    }
}
