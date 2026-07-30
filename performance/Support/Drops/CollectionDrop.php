<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Drop;

final class CollectionDrop extends Drop
{
    /**
     * @param  list<string>  $tags
     * @param  list<ProductDrop>  $products
     */
    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        public readonly string $label,
        public readonly string $description,
        public readonly array $tags,
        public readonly array $products,
        public readonly ImageDrop $image,
    ) {}

    public function url(): string
    {
        return '/collections/'.$this->handle;
    }

    public function productsCount(): int
    {
        return count($this->products);
    }
}
