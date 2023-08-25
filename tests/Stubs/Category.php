<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\MapsToLiquid;

class Category implements MapsToLiquid
{
    public function __construct(
        public readonly string $name
    ) {
    }

    public function toLiquid(): mixed
    {
        return new CategoryDrop($this);
    }
}
