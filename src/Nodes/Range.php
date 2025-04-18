<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Render\RenderContext;

class Range extends Node
{
    public function __construct(
        public readonly int $start,
        public readonly int $end,
    ) {}

    public function render(RenderContext $context): string
    {
        return sprintf('%d..%d', $this->start, $this->end);
    }

    public function toArray(): array
    {
        return range($this->start, $this->end);
    }

    public function debugLabel(): string
    {
        return sprintf('(%s..%s)', $this->start, $this->end);
    }
}
