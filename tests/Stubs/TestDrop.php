<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class TestDrop extends Drop
{
    public function __construct(
        public mixed $value
    ) {
    }
}
