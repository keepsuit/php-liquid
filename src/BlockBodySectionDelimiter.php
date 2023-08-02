<?php

namespace Keepsuit\Liquid;

class BlockBodySectionDelimiter
{
    public function __construct(
        public readonly string $tag,
        public readonly string $markup = ''
    ) {
    }
}
