<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class ContextSensitiveDrop extends Drop
{
    public function test(): mixed
    {
        return $this->context->get('test');
    }
}
