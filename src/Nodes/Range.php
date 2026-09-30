<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Render\RenderContext;

/**
 * @implements \IteratorAggregate<int, int>
 */
class Range extends Node implements \IteratorAggregate
{
    public function __construct(
        public readonly int $start,
        public readonly int $end,
    ) {}

    public function render(RenderContext $context): string
    {
        return sprintf('%d..%d', $this->start, $this->end);
    }

    /**
     * @return list<int>
     */
    public function toArray(): array
    {
        return $this->start > $this->end ? [] : range($this->start, $this->end);
    }

    /**
     * @return \ArrayIterator<int, int>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->toArray());
    }

    public function debugLabel(): string
    {
        return sprintf('(%s..%s)', $this->start, $this->end);
    }
}
