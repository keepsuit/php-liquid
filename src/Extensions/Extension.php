<?php

namespace Keepsuit\Liquid\Extensions;

use Keepsuit\Liquid\Contracts\LiquidExtension;

abstract class Extension implements LiquidExtension
{
    public function getTags(): array
    {
        return [];
    }

    public function getFiltersProviders(): array
    {
        return [];
    }

    public function getRegisters(): array
    {
        return [];
    }

    public function getNodeVisitors(): array
    {
        return [];
    }
}
