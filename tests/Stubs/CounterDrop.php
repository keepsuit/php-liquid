<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class CounterDrop extends Drop
{
    protected int $count = 0;

    public function count(): int
    {
        $this->count += 1;

        return $this->count;
    }
}
