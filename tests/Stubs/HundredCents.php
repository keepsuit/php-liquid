<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\MapsToLiquid;

class HundredCents implements MapsToLiquid
{
    public function toLiquid(): mixed
    {
        return 100;
    }
}
