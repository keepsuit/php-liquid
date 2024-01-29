<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Exceptions\TagDisabledException;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;

abstract class Tag extends Node
{
    abstract public static function tagName(): string;

    public function name(): string
    {
        return static::class;
    }

    public function blank(): bool
    {
        return false;
    }

    //    public function disabledTags(): array
    //    {
    //        return [];
    //    }

    //    public function raw(): string
    //    {
    //        return sprintf('%s %s', static::tagName(), $this->markup);
    //    }

    /**
     * @throws SyntaxException
     */
    abstract public function parse(TagParseContext $context): static;

    abstract public function render(RenderContext $context): string;

    public function ensureTagIsEnabled(RenderContext $context): void
    {
        if (! $this instanceof Disableable) {
            return;
        }

        if (! $context->tagDisabled(static::tagName())) {
            return;
        }

        throw new TagDisabledException(static::tagName(), $context->locale);
    }
}
