<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class NumberDrop extends Drop
{
    public function __construct(
        protected int|float $value
    ) {
    }

    public function toLiquid(): mixed
    {
        return $this->value;
    }
}
