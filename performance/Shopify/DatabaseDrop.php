<?php

namespace Keepsuit\Liquid\Performance\Shopify;

use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;

class DatabaseDrop extends Drop implements \JsonSerializable
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        private readonly array $data,
    ) {}

    public function liquidMethodMissing(string $name): mixed
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw new UndefinedDropMethodException($name);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
