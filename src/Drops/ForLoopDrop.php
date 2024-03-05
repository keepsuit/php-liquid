<?php

namespace Keepsuit\Liquid\Drops;

use Keepsuit\Liquid\Attributes\Hidden;
use Keepsuit\Liquid\Drop;

/**
 * @property-read bool             $first      Returns true if the current iteration is the first. Returns false if not.
 * @property-read bool             $last       Returns true if the current iteration is the last. Returns false if not.
 * @property-read int              $index      The 1-based index of the current iteration.
 * @property-read int              $index0     The 0-based index of the current iteration.
 * @property-read int              $rindex     The 1-based index of the current iteration, in reverse order.
 * @property-read int              $rindex0    The 0-based index of the current iteration, in reverse order.
 * @property-read int              $length     The total number of iterations in the loop.
 * @property-read ForLoopDrop|null $parentloop The parent forloop object. If the current for loop isn’t nested inside another for loop, then null is returned.
 */
class ForLoopDrop extends Drop
{
    protected int $index = 0;

    public function __construct(
        protected string $name,
        protected int $length,
        public readonly ?ForLoopDrop $parentLoop = null,
    ) {
    }

    #[Hidden]
    public function increment(): void
    {
        $this->index += 1;
    }

    protected function liquidMethodMissing(string $name): mixed
    {
        return match ($name) {
            'first' => $this->index === 0,
            'last' => $this->index === $this->length - 1,
            'index' => $this->index + 1,
            'index0' => $this->index,
            'rindex' => $this->length - $this->index,
            'rindex0' => $this->length - $this->index - 1,
            'length' => $this->length,
            'parentloop' => $this->parentLoop,
            default => parent::liquidMethodMissing($name),
        };
    }
}
