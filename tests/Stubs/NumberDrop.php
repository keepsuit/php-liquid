<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\MapsToLiquid;
use Keepsuit\Liquid\Drop;

class NumberDrop extends Drop implements MapsToLiquid
{
    public function __construct(
        protected int|float $value
    ) {
    }

    public function toLiquid(): int|float
    {
        return $this->value;
    }
}
