<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Tag;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class AssignTag extends Tag implements HasParseTreeVisitorChildren
{
    protected const SYNTAX_ERROR = "Syntax Error in 'assign' - Valid syntax: assign [var] = [source]";

    protected string $to;

    protected Variable $from;

    public static function tagName(): string
    {
        return 'assign';
    }

    public function parse(TagParseContext $context): static
    {
        try {
            $to = $context->params->expression();
            $this->to = match (true) {
                $to instanceof VariableLookup, is_string($to) => (string) $to,
                default => throw new SyntaxException(self::SYNTAX_ERROR),
            };

            $context->params->consume(TokenType::Equals);

            $this->from = $context->params->variable();

            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw new SyntaxException(self::SYNTAX_ERROR);
        }

        return $this;
    }

    public function render(RenderContext $context): string
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
