<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class EchoTag extends Tag implements HasParseTreeVisitorChildren
{
    protected Variable $variable;

    public function parse(TagParseContext $context): static
    {
        $this->variable = $context->params->variable();

        $context->params->assertEnd();

        return $this;
    }

    public static function tagName(): string
    {
        return 'echo';
    }

    public function blank(): bool
    {
        return true;
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->variable];
    }

    public function render(RenderContext $context): string
    {
        return $this->variable->render($context);
    }
}
