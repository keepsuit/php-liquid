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
     * Apply array_slice semantics before allocating the selected values.
     *
     * @return list<int>
     */
    public function slice(int $offset, ?int $length = null): array
    {
        if ($this->start > $this->end || $length === 0) {
            return [];
        }

        if ($offset >= 0) {
            if ($this->start > PHP_INT_MAX - $offset) {
                return [];
            }
            $first = $this->start + $offset;
        } else {
            // -(PHP_INT_MIN + 1) is representable, unlike -PHP_INT_MIN.
            $distance = -($offset + 1);
            $first = $this->end < PHP_INT_MIN + $distance
                ? $this->start
                : max($this->start, $this->end - $distance);
        }

        $last = $this->end;
        if ($length !== null && $length > 0) {
            $distance = $length - 1;
            if ($first <= PHP_INT_MAX - $distance) {
                $last = min($last, $first + $distance);
            }
        } elseif ($length !== null) {
            $distance = -($length + 1);
            if ($last <= PHP_INT_MIN + $distance) {
                return [];
            }
            $last = $last - $distance - 1;
        }

        return $first > $last ? [] : range($first, $last);
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
