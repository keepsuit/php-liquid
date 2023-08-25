<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\MapsToLiquid;

class ThingWithToLiquid implements MapsToLiquid
{
    public function toLiquid(): string
    {
        return 'foobar';
    }
}
