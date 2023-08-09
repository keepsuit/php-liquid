<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\Str;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Tokenizer;
use Keepsuit\Liquid\Variable;

class AssignTag extends Tag implements HasParseTreeVisitorChildren
{
    const Syntax = '/('.Regex::VariableSignature.')\s*=\s*(.*)\s*/m';

    protected string $to;

    protected Variable $from;

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        if (preg_match(static::Syntax, $this->markup, $matches)) {
            $this->to = $matches[1];
            $this->from = new Variable($matches[2], $this->parseContext);
        } else {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.assign'));
        }

        return $this;
    }

    public static function tagName(): string
    {
        return 'assign';
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->from];
    }

    public function render(Context $context): string
    {
        $value = $this->from->render($context);

        $context->setToActiveScope($this->to, $value);
        $context->resourceLimits->incrementAssignScore($this->computeAssignScore($value));

        return '';
    }

    protected function computeAssignScore(string $value): int
    {
        return Str::length($value);
    }
}
