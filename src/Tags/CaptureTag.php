<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class CaptureTag extends TagBlock
{
    protected string $to;

    protected BodyNode $body;

    public function parse(TagParseContext $context): static
    {
        assert($context->body !== null);

        $this->body = $context->body;

        try {
            $this->to = $context->params->simpleVariableName();

            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException('capture', 'capture <var>', $e);
        }

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
        $captureValue = $context->resourceLimits->withCapture(fn () => $this->body->render($context));

        $context->setToActiveScope($this->to, $captureValue);

        return '';
    }

    public function parseTreeVisitorChildren(): array
    {
        return [
            $this->body,
        ];
    }
}
