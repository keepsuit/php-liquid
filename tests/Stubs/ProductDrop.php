<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class ProductDrop extends Drop
{
    public function texts(): TextDrop
    {
        return new TextDrop();
    }

    public function catchall(): CatchAllDrop
    {
        return new CatchAllDrop();
    }

    public function context(): ContextDrop
    {
        return new ContextDrop();
    }
}
