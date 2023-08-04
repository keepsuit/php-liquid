<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\RendersToLiquid;

class ThingWithParamToLiquid implements RendersToLiquid
{
    public function __construct(
        public int $value = 0
    ) {
    }

    public function toString(): string
    {
        return sprintf('woot: %s', $this->value);
    }

    public function toLiquid(): string
    {
        $this->value += 1;

        return $this->toString();
    }

    public function __get(string $name): string
    {
        return $this->toString();
    }
}
