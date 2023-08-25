<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Interrupts\BreakInterrupt;
use Keepsuit\Liquid\Tag;

class BreakTag extends Tag
{
    public static function tagName(): string
    {
        return 'break';
    }

    public function render(Context $context): string
    {
        $context->pushInterrupt(new BreakInterrupt());

        return '';
    }
}
