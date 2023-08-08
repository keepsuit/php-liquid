<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\MapsToLiquid;

class ThingWithToLiquid implements MapsToLiquid
{
    public function toLiquid(): string
    {
        return 'foobar';
    }
}
