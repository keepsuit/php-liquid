<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\CanBeRendered;

class Range implements CanBeRendered
{
    public function __construct(
        public readonly int $start,
        public readonly int $end,
    ) {
    }

    public function render(Context $context): string
    {
        return sprintf('%d..%d', $this->start, $this->end);
    }

    public function toArray(): array
    {
        return range($this->start, $this->end);
    }
}
