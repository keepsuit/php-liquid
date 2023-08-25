<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Interrupts\ContinueInterrupt;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tag;

class ContinueTag extends Tag
{
    public static function tagName(): string
    {
        return 'continue';
    }

    public function render(Context $context): string
    {
        $context->pushInterrupt(new ContinueInterrupt());

        return '';
    }
}
