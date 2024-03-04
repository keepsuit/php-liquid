<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Drops\Cache;

class CachableDrop extends Drop
{
    protected int $notCachedCounter = 0;

    protected int $cachedCounter = 0;

    public function notCached(): int
    {
        return $this->notCachedCounter++;
    }

    #[Cache]
    public function cached(): int
    {
        return $this->cachedCounter++;
    }
}
