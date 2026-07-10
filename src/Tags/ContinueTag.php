<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Interrupts\ContinueInterrupt;
use Keepsuit\Liquid\Parse\TagParseContext;
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
        try {
            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), 'continue', $e);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $context->pushInterrupt(new ContinueInterrupt);

        return '';
    }
}
