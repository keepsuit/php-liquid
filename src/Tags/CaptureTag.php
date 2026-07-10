<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class CaptureTag extends TagBlock
{
    protected const SYNTAX_ERROR = "Syntax Error in 'capture' - Valid syntax: capture [var]";

    protected string $to;

    protected BodyNode $body;

    public function parse(TagParseContext $context): static
    {
        assert($context->body !== null);

        $this->body = $context->body;

        $this->to = $context->params->simpleVariableName(self::SYNTAX_ERROR);

        $context->params->assertEnd();

        return $this;
    }

    public static function tagName(): string
    {
        return 'capture';
    }

    public function blank(): bool
    {
        return true;
    }

    public function render(RenderContext $context): string
    {
        $context->resourceLimits->withCapture(function () use ($context) {
            $captureValue = $this->body->render($context);

            $context->setToActiveScope($this->to, $captureValue);
        });

        return '';
    }

    public function parseTreeVisitorChildren(): array
    {
        return [
            $this->body,
        ];
    }
}
