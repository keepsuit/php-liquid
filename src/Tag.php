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

    public function parse(Tokenizer $tokenizer): static
    {
        return $this;
    }

    public function blank(): bool
    {
        return false;
    }

    protected function parseExpression(string $markup): mixed
    {
        return $this->parseContext->parseExpression($markup);
    }
}
