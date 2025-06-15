<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class RawTag extends TagBlock
{
    protected Raw $body;

    public static function tagName(): string
    {
        return 'raw';
    }

    public static function hasRawBody(): bool
    {
        return true;
    }

    public function parse(TagParseContext $context): static
    {
        $context->params->assertEnd();

        assert($context->body instanceof BodyNode);

        $body = $context->body->children()[0] ?? null;
        $this->body = match (true) {
            $body instanceof Raw => $body,
            default => throw new SyntaxException('raw tag must have a single raw body'),
        };

        return $this;
    }

    public function render(RenderContext $context): string
    {
        return $this->body->render($context);
    }

    public function getBody(): Raw
    {
        return $this->body;
    }
}
