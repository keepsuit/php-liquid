<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Render\RenderContext;

class Raw extends Node implements CanBeCompiled, HasParseTreeVisitorChildren
{
    public function __construct(
        public readonly string $value,
    ) {}

    public function render(RenderContext $context): string
    {
        return $this->value;
    }

    public function compile(CompilerContext $context): void
    {
        $context->writeOutput($context->writeValue($this->value));
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
