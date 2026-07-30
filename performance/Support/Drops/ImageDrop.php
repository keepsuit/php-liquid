<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Drop;

/**
 * Stored fields are public typed properties, which resolve through the cheapest
 * branch of Drop::__get. See performance/README.md for the strategy matrix.
 */
final class ImageDrop extends Drop
{
    public function __construct(
        public readonly string $src,
        public readonly string $alt,
        public readonly int $width,
        public readonly int $height,
    ) {}

    public function aspectRatio(): string
    {
        return $this->width.'/'.$this->height;
    }
}
