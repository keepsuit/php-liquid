<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Attributes\Cache;
use Keepsuit\Liquid\Drop;

final class ProductDrop extends Drop
{
    /**
     * @param  list<string>  $badges
     * @param  non-empty-list<ImageDrop>  $images
     * @param  non-empty-list<VariantDrop>  $variants
     */
    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        public readonly string $vendor,
        public readonly string $type,
        public readonly string $description,
        public readonly int $priceCents,
        public readonly int $compareAtPriceCents,
        public readonly array $badges,
        public readonly array $images,
        public readonly array $variants,
        public readonly MetafieldsDrop $metafields,
    ) {}

    public function url(): string
    {
        return '/products/'.$this->handle;
    }

    public function featuredImage(): ImageDrop
    {
        return $this->images[0];
    }

    public function onSale(): bool
    {
        return $this->compareAtPriceCents > $this->priceCents;
    }

    public function savingCents(): int
    {
        return $this->compareAtPriceCents - $this->priceCents;
    }

    /**
     * Walks every variant instead of returning a stored field, which is why it is
     * a method rather than a property.
     *
     * The cache earns its keep on the product page, where variant_picker and specs
     * both read it for the same instance. On a card it is read once, so the cache
     * is populated and never hit — that asymmetry is realistic and deliberate.
     */
    #[Cache]
    public function inStockVariantCount(): int
    {
        $count = 0;

        foreach ($this->variants as $variant) {
            if ($variant->available) {
                $count++;
            }
        }

        return $count;
    }
}
