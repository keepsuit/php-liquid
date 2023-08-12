<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class ThingWithValue extends Drop
{
    public function __construct(
        public readonly int $value = 3
    ) {
    }
}
