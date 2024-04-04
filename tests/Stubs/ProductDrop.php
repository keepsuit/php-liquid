<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class ProductDrop extends Drop
{
    public function __construct(
        public string $productName = 'Product'
    ) {
    }

    public function text(): TextDrop
    {
        return new TextDrop();
    }

    public function catchAll(): CatchAllDrop
    {
        return new CatchAllDrop();
    }

    public function context(): ContextDrop
    {
        return new ContextDrop();
    }
}
