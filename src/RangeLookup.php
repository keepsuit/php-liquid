<?php

namespace Keepsuit\Liquid;

class RangeLookup implements HasParseTreeVisitorChildren
{
    public function __construct(
        protected mixed $start,
        protected mixed $end,
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
