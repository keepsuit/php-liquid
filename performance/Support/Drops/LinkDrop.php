<?php

namespace Keepsuit\Liquid\Performance\Support\Drops;

use Keepsuit\Liquid\Drop;

final class LinkDrop extends Drop
{
    public function __construct(
        public readonly string $title,
        public readonly string $url,
    ) {}
}
