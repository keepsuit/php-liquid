<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class BooleanDrop extends Drop
{
    public function __construct(
        protected bool $value
    ) {
    }

    public function toLiquidValue(): bool
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ? 'Yay' : 'Nay';
    }
}
