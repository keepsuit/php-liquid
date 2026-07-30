<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Drop;

final class VariantDrop extends Drop
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $option,
        public readonly int $priceCents,
        public readonly bool $available,
    ) {}
}
