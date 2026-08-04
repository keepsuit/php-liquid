<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class AssignTag extends Tag implements HasParseTreeVisitorChildren
{
    protected string $to;

    protected Variable $from;

    public static function tagName(): string
    {
        return 'assign';
    }

    public function parse(TagParseContext $context): static
    {
        try {
            $this->to = $context->params->simpleVariableName();

            $context->params->consume(TokenType::Equals);

            $this->from = $context->params->variable();

            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException('assign', 'assign <var> = <source>', $e);
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
        if (is_string($value)) {
            return strlen($value);
        }

        if (! is_array($value)) {
            return 1;
        }

        $score = 1;
        $isList = array_is_list($value);

        foreach ($value as $key => $item) {
            if (! $isList) {
                $score += static::computeAssignScore($key);
            }

            $score += static::computeAssignScore($item);
        }

        return $score;
    }
}
