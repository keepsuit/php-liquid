<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\TagBlock;

class CaptureTag extends TagBlock
{
    protected string $to;

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        try {
            $parser = $this->newParser();
            $this->to = $parser->consume(TokenType::Identifier);
        } catch (SyntaxException $exception) {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.capture'), previous: $exception);
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

    public function render(Context $context): string
    {
        $context->resourceLimits->withCapture(function () use ($context) {
            $captureValue = parent::render($context);

            $context->setToActiveScope($this->to, $captureValue);
        });

        return '';
    }
}
