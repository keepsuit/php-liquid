<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class MagicClass
{
    public array $data = [
        'simpleProperty' => 'foo',
        'nullProperty' => null,
    ];

    public function simpleMethod(): string
    {
        return 'foo';
    }

    public function __get(string $property): mixed
    {
        if (! array_key_exists($property, $this->data)) {
            throw new \Error('Property does not exist: '.$property);
        }

        return $this->data[$property];
    }

    public function __set(string $property, mixed $value): void
    {
        $this->data[$property] = $value;
    }

    public function __isset(string $property): bool
    {
        return array_key_exists($property, $this->data);
    }

    public function __unset(string $property): void
    {
        unset($this->data[$property]);
    }
}
