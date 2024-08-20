<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class TestDrop extends Drop
{
    public function __construct(
        public string $value
    ) {}

    public function registers(): string
    {
        return sprintf('{%s=>%s}', $this->value, $this->context->getRegister($this->value));
    }
}
