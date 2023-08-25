<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Arr;
use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parser\Tokenizer;
use Keepsuit\Liquid\Parser\Regex;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Variable;

class AssignTag extends Tag implements HasParseTreeVisitorChildren
{
    const Syntax = '/('.Regex::VariableSignature.')\s*=\s*(.*)\s*/m';

    protected string $to;

    protected Variable $from;

    public static function tagName(): string
    {
        return 'assign';
    }

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

    public function render(Context $context): string
    {
        $value = $this->from->evaluate($context);

        $context->setToActiveScope($this->to, $value);
        $context->resourceLimits->incrementAssignScore(static::computeAssignScore($value));

        return '';
    }

    public function blank(): bool
    {
        return true;
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->from];
    }

    protected static function computeAssignScore(mixed $value): int
    {
        return match (true) {
            is_string($value) => strlen($value),
            is_array($value) && array_is_list($value) => 1 + (int) array_sum(Arr::map($value, fn (mixed $item) => static::computeAssignScore($item))),
            is_array($value) => 1 + (int) array_sum(Arr::map($value, fn (mixed $key, mixed $item) => static::computeAssignScore($key) + static::computeAssignScore($item))),
            default => 1,
        };
    }
}
