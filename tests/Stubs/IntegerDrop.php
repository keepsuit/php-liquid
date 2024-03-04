<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\MapsToLiquid;
use Keepsuit\Liquid\Drop;

class IntegerDrop extends Drop implements MapsToLiquid
{
    protected int $value;

    public function __construct(
        string $value
    ) {
        $this->value = (int) $value;
    }

    public function toLiquid(): int
    {
        return $this->value;
    }
}
