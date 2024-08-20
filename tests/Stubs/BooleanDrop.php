<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\AsLiquidValue;
use Keepsuit\Liquid\Drop;

class BooleanDrop extends Drop implements AsLiquidValue
{
    public function __construct(
        protected bool $value
    ) {}

    public function toLiquidValue(): string|int|float|bool|null
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ? 'Yay' : 'Nay';
    }
}
