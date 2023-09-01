<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

/**
 * @implements \Iterator<int, mixed>
 */
class LoaderDrop extends Drop implements \Iterator
{
    protected int $index = 0;

    public function __construct(
        protected array $data = [],
    ) {
    }

    public function current(): mixed
    {
        return $this->data[$this->index];
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->index]);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}
