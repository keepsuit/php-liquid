<?php

namespace Keepsuit\Liquid;

class ParseContext
{
    protected array $templateOptions;

    protected int $depth = 0;

    protected bool $partial = false;

    public function __construct(array $options = [])
    {
        $this->templateOptions = $options['dup'] ?? [];
    }

    public function newTokenizer(string $markup, int $startLineNumber = null, bool $forLiquidTag = false): Tokenizer
    {
        return new Tokenizer($markup, lineNumber: $startLineNumber, forLiquidTag: $forLiquidTag);
    }
}
