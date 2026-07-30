<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Drop;

final class ShopDrop extends Drop
{
    public function __construct(
        public readonly string $name,
        public readonly string $domain,
        public readonly string $currency,
        public readonly string $description,
    ) {}
}
