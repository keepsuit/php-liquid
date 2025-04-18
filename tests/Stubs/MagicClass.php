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

    public function __get(string $property)
    {
        return $this->data[$property];
    }

    public function __set(string $property, $value)
    {
        $this->data[$property] = $value;
    }

    public function __isset($property)
    {
        return array_key_exists($property, $this->data);
    }

    public function __unset($property)
    {
        unset($this->data[$property]);
    }
}
