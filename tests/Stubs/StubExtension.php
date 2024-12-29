<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Extensions\Extension;

class StubExtension extends Extension
{
    public function getNodeVisitors(): array
    {
        return [
            new StubNodeVisitor,
        ];
    }

    public function getRegisters(): array
    {
        return [
            'test' => 'stub',
        ];
    }
}
