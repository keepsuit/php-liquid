<?php

namespace Keepsuit\Liquid;

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

        return range($start, $end);
    }

    protected function toInteger(mixed $value): int
    {
        return match (true) {
            is_int($value) => $value,
            is_numeric($value) => (int) $value,
            is_string($value) => intval($value),
            $value === null => 0,
            default => throw new \InvalidArgumentException('Invalid integer'),
        };
    }
}
