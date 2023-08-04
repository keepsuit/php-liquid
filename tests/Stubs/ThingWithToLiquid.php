<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\RendersToLiquid;

class ThingWithToLiquid implements RendersToLiquid
{
    public function toLiquid(): string
    {
        return 'foobar';
    }
}
