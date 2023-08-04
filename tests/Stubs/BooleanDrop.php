<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class BooleanDrop extends Drop
{
    public function __construct(
        protected bool $value
    ) {
    }

    public function toLiquid(): mixed
    {
        return $this->value ? 'Yay' : 'Nay';
    }
}
