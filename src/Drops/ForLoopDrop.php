<?php

namespace Keepsuit\Liquid\Drops;

use Keepsuit\Liquid\Drop;

/**
 * @property-read bool $first
 * @property-read bool $last
 * @property-read int  $index
 * @property-read int  $index0
 * @property-read int  $rindex
 * @property-read int  $rindex0
 * @property-read int  $length
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

    public function increment(): void
    {
        $this->index += 1;
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'first' => $this->index === 0,
            'last' => $this->index === $this->length - 1,
            'index' => $this->index + 1,
            'index0' => $this->index,
            'rindex' => $this->length - $this->index,
            'rindex0' => $this->length - $this->index - 1,
            'length' => $this->length,
            default => throw new \InvalidArgumentException('Unknown property: '.$name),
        };
    }
}
