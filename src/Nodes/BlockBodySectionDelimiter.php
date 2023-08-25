<?php

namespace Keepsuit\Liquid\Nodes;

class BlockBodySectionDelimiter
{
    public function __construct(
        public readonly string $tag,
        public readonly string $markup = ''
    ) {
    }
}
