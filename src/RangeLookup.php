<?php

namespace Keepsuit\Liquid;

class RangeLookup implements HasParseTreeVisitorChildren
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
}
