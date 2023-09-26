<?php

namespace Keepsuit\Liquid\Support;

class OutputsBag
{
    protected array $bags = [];

    public function set(string $key, mixed $value): mixed
    {
        $this->bags[$key] = $value;

        return $value;
    }

    public function get(string $key): mixed
    {
        return $this->bags[$key] ?? null;
    }

    /**
     * @throws \Exception
     */
    public function push(string $key, mixed ...$values): mixed
    {
        $bag = $this->bags[$key] ?? [];

        if (! is_array($bag)) {
            throw new \Exception('Cannot push output to non-array bag');
        }

        foreach ($values as $value) {
            $bag[] = $value;
        }

        $this->bags[$key] = $bag;

        return $bag;
    }

    public function all(): array
    {
        return $this->bags;
    }

    public function merge(array $outputs): void
    {
        foreach ($outputs as $key => $value) {
            $this->bags[$key] = $value;
        }
    }
}
