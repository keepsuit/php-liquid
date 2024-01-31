<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class TestTag extends Tag
{
    public function parse(TagParseContext $context): static
    {
        return $this;
    }

    public function render(RenderContext $context): string
    {
        return '';
    }

    public static function tagName(): string
    {
        return 'test';
    }
}
