<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class NodeTreeItem
{
    public function __construct(
        public string $type,
        public ?string $value = null,
        public array $children = [],
    ) {}

    public function serialize(): array
    {
        if ($this->children === []) {
            return [$this->type, $this->value];
        }

        $children = array_map(fn (NodeTreeItem $item) => $item->serialize(), $this->children);

        return [$this->type, $this->value, $children];
    }
}
