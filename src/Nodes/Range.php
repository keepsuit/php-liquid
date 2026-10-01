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
     * Saturates at PHP_INT_MAX so huge ranges can be measured without allocating them.
     */
    public function length(): int
    {
        if ($this->start > $this->end) {
            return 0;
        }

        if ($this->start <= 0 && $this->end >= PHP_INT_MAX + $this->start) {
            return PHP_INT_MAX;
        }

        return $this->end - $this->start + 1;
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
