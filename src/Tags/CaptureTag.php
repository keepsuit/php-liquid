<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Block;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;

class CaptureTag extends Block
{
    protected const Syntax = '/('.Regex::VariableSignature.'+)/';

    protected string $to;

    public function __construct(string $markup, ParseContext $parseContext)
    {
        parent::__construct($markup, $parseContext);

        if (preg_match(static::Syntax, $markup, $matches)) {
            $this->to = $matches[1];
        } else {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.capture'));
        }
    }

    public static function tagName(): string
    {
        return 'capture';
    }

    public function blank(): bool
    {
        return true;
    }
}
