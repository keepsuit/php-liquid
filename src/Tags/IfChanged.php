<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class IfChanged extends TagBlock
{
    protected BodyNode $body;

    public static function tagName(): string
    {
        return 'ifchanged';
    }

    public function parse(TagParseContext $context): static
    {
        assert($context->body !== null);

        $this->body = $context->body;

        $context->params->assertEnd();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $output = $this->body->render($context);

        if ($context->getRegister('ifchanged') === $output) {
            return '';
        }

        $context->setRegister('ifchanged', $output);

        return $output;
    }
}
