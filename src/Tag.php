<?php

namespace Keepsuit\Liquid;

abstract class Tag
{
    final public function __construct(
        protected string $tagName,
        protected string $markup,
        public readonly ParseContext $parseContext
    ) {
    }

    abstract public static function name(): string;

    abstract public static function parse(string $tagName, string $markup, Tokenizer $tokenizer, ParseContext $parseContext): static;

    public function blank(): bool
    {
        return false;
    }
}
