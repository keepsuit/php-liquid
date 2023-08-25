<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class CentsDrop extends Drop
{
    public function amount(): HundredCents
    {
        return new HundredCents();
    }

    public function nonZero(): bool
    {
        return true;
    }
}
