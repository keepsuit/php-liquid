<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class TestObject extends Drop
{
    public function __construct(
        public readonly ?string $a
    ) {}
}
