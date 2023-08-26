<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Drop;

class EnumerableDrop extends Drop implements \Iterator
{
    protected int $index = 0;

    protected array $data = [1, 2, 3];

    protected function liquidMethodMissing(string $name): mixed
    {
        return $name;
    }

    public function size(): int
    {
        return $this->count();
    }

    public function first(): int
    {
        return $this->data[0];
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function min(): int
    {
        return min($this->data);
    }

    public function max(): int
    {
        return max($this->data);
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
