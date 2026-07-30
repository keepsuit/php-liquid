<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Drop;

/**
 * Scalars only. The cart earns a drop because the layout header reads it on every
 * page, but nothing renders line items, so there is no LineItemDrop.
 *
 * Totals are stored, not summed: Database assigns, it does not compute.
 */
final class CartDrop extends Drop
{
    public function __construct(
        public readonly int $itemCount,
        public readonly int $totalPrice,
    ) {}

    public function isEmpty(): bool
    {
        return $this->itemCount === 0;
    }
}
