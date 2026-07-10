<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
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
        try {
            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), 'break', $e);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $context->pushInterrupt(new BreakInterrupt);

        return '';
    }
}
