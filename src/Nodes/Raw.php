<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Render\RenderContext;

class Raw extends Node implements HasParseTreeVisitorChildren
{
    public function __construct(
        public readonly string $value,
    ) {
    }

    public function render(RenderContext $context): string
    {
        return $this->value;
    }

    public function blank(): bool
    {
        return false;
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->value];
    }
}
