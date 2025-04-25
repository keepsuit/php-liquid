<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class DocTag extends TagBlock
{
    protected Raw $body;

    public static function tagName(): string
    {
        return 'doc';
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
            default => throw new SyntaxException('doc tag must have a single raw body'),
        };

        $this->ensureNoNestedDocTags();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        return '';
    }

    /**
     * @throws SyntaxException
     */
    protected function ensureNoNestedDocTags(): void
    {
        if (preg_match('/{%-?\s*doc\s*-?%}/', $this->body->value) === 1) {
            throw new SyntaxException('Nested doc tags are not allowed');
        }
    }
}
