<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class CategoryDrop extends Drop
{
    public function __construct(
        public readonly Category $category
    ) {}
}
