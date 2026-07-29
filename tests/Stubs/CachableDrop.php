<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Attributes\Cache;
use Keepsuit\Liquid\Drop;

class CachableDrop extends Drop
{
    protected int $notCachedCounter = 0;

    protected int $cachedCounter = 0;

    protected int $cachedNullCounter = 0;

    public function notCached(): int
    {
        return $this->notCachedCounter++;
    }

    #[Cache]
    public function cached(): int
    {
        return $this->cachedCounter++;
    }

    #[Cache]
    public function cachedNull(): mixed
    {
        $this->cachedNullCounter++;

        return null;
    }

    public function cachedNullCalls(): int
    {
        return $this->cachedNullCounter;
    }
}
