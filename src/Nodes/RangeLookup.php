<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\Expression;
use Keepsuit\Liquid\Render\Context;

class RangeLookup implements HasParseTreeVisitorChildren, CanBeEvaluated
{
    final public function __construct(
        public readonly mixed $start,
        public readonly mixed $end,
    ) {
    }

    public static function parse(string $startMarkup, string $endMarkup): static
    {
        $startObject = Expression::parse($startMarkup);
        $endObject = Expression::parse($endMarkup);

        return new static($startObject, $endObject);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->start, $this->end];
    }

    public function evaluate(Context $context): mixed
    {
        $start = $this->toInteger($context->evaluate($this->start));
        $end = $this->toInteger($context->evaluate($this->end));

        return new Range($start, $end);
    }

    protected function toInteger(mixed $value): int
    {
        return match (true) {
            is_int($value) => $value,
            is_numeric($value) => (int) $value,
            is_string($value) => intval($value),
            $value === null => 0,
            default => throw new SyntaxException(sprintf("Invalid expression type '%s' in range expression", match (true) {
                $value instanceof Range => sprintf('(%s..%s)', $value->start, $value->end),
                is_bool($value) => $value ? 'true' : 'false',
                is_array($value) => 'array',
                default => gettype($value),
            })),
        };
    }
}
