<?php

namespace Keepsuit\Liquid;

class Variable
{
    protected ?string $name = null;
    protected ?int $lineNumber = null;

    public function __construct(
        public readonly string $markup,
        public readonly ParseContext $parseContext
    ) {
        $this->lineNumber = $this->parseContext->lineNumber;
    }
}
