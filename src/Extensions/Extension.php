<?php

namespace Keepsuit\Liquid\Extensions;

use Keepsuit\Liquid\Contracts\LiquidExtension;

class Extension implements LiquidExtension
{
    public function getNodeVisitors(): array
    {
        return [];
    }

    public function getRegisters(): array
    {
        return [];
    }
}
