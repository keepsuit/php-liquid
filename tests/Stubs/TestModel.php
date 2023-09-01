<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\MapsToLiquid;

class TestModel implements MapsToLiquid
{
    public function __construct(
        public string|int $value
    ) {
    }

    public function toLiquid(): mixed
    {
        return new TestDrop((string) $this->value);
    }
}
