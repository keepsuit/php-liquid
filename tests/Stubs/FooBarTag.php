<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class FooBarTag extends Tag
{
    public static function tagName(): string
    {
        return 'foobar';
    }

    public function parse(TagParseContext $context): static
    {
        return $this;
    }

    public function render(RenderContext $context): string
    {
        return ' ';
    }
}
