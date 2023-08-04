<?php

namespace Keepsuit\Liquid;

class Drop implements RendersToLiquid
{
    public function toLiquid(): mixed
    {
        return $this;
    }
}
