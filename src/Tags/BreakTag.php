<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Interrupts\BreakInterrupt;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class BreakTag extends Tag
{
    public static function tagName(): string
    {
        return 'break';
    }

    public function parse(TagParseContext $context): static
    {
        $context->params->assertEnd();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $context->pushInterrupt(new BreakInterrupt);

        return '';
    }
}
