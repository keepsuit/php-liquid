<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Interrupts\ContinueInterrupt;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class ContinueTag extends Tag
{
    public static function tagName(): string
    {
        return 'continue';
    }

    public function parse(TagParseContext $context): static
    {
        $context->params->assertEnd();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $context->pushInterrupt(new ContinueInterrupt());

        return '';
    }
}
