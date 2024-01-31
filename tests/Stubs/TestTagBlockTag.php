<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class TestTagBlockTag extends TagBlock
{
    public static function tagName(): string
    {
        return 'testblock';
    }

    public function parse(TagParseContext $context): static
    {
        return $this;
    }

    public function render(RenderContext $context): string
    {
        return '';
    }
}
