<?php

namespace Keepsuit\Liquid;

abstract class Tag
{
    public function __construct(
        protected string $tagName,
        protected string $markup,
        public readonly ParseContext $parseContext
    ) {
    }

    abstract public static function name(): string;

    public static function parse(string $tagName, string $markup, Tokenizer $tokenizer, ParseContext $parseContext): static
    {
        return new static($tagName, $markup, $parseContext);
    }

    public function blank(): bool
    {
        return false;
    }
}
