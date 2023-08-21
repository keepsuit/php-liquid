<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\TagBlock;
use Keepsuit\Liquid\Tokenizer;

class CaptureTag extends TagBlock
{
    protected const Syntax = '/('.Regex::VariableSignature.'+)/';

    protected string $to;

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        if (preg_match(static::Syntax, $this->markup, $matches)) {
            $this->to = $matches[1];
        } else {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.capture'));
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
