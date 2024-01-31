<?php

namespace Keepsuit\Liquid\Parse;

final class Token
{
    public function __construct(
        public readonly TokenType $type,
        public readonly string $data,
        public readonly int $lineNumber,
    ) {
    }
}
