<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Str;

class Text extends Node implements CanBeCompiled, HasParseTreeVisitorChildren
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
        $context->writeText($this->value);
    }

    public function blank(): bool
    {
        return Str::blank($this->value);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->value];
    }
}
