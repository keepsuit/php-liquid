<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\MapsToLiquid;

class ThingWithParamToLiquid implements MapsToLiquid
{
    public function __construct(
        public int $value = 0
    ) {
    }

    public function toString(): string
    {
        return sprintf('woot: %s', $this->value);
    }

    public function toLiquid(): mixed
    {
        $this->value += 1;

        return $this;
    }

    public function __get(string $name): string
    {
        return $this->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
