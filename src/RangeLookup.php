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
        $start = $context->evaluate($this->start);
        assert(is_numeric($start));
        $end = $context->evaluate($this->end);
        assert(is_numeric($end));

        return range($start, $end);
    }
}
