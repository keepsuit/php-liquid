<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class CatchAllDrop extends Drop
{
    protected function liquidMethodMissing(string $name): mixed
    {
        return sprintf('catchall_method: %s', $name);
    }
}
