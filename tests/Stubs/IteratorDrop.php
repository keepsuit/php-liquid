<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class IteratorDrop implements \Iterator
{
    protected int $index = 0;

    protected array $data;

    public function __construct(array $data = null)
    {
        $this->data = $data ?? [
            ['foo' => 1, 'bar' => 2],
            ['foo' => 2, 'bar' => 1],
            ['foo' => 3, 'bar' => 3],
        ];
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
