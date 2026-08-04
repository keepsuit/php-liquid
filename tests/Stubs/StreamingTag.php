<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

/**
 * A tag outside the built-in set that yields its own chunks, to check that
 * resource limits cover chunks the library did not produce itself.
 */
class StreamingTag extends Tag implements CanBeStreamed
{
    public static function tagName(): string
    {
        return 'streaming';
    }

    public function parse(TagParseContext $context): static
    {
        return $this;
    }

    public function render(RenderContext $context): string
    {
        return 'abcdef';
    }

    public function stream(RenderContext $context): \Generator
    {
        yield 'abc';
        yield 'def';
    }
}
