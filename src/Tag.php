<?php

namespace Keepsuit\Liquid;

abstract class Tag implements CanBeRendered
{
    public readonly ?int $lineNumber;

    final public function __construct(
        protected string $markup,
        public readonly ParseContext $parseContext
    ) {
        $this->lineNumber = $this->parseContext->lineNumber;
    }

    abstract public static function tagName(): string;

    public function name(): string
    {
        return static::class;
    }

    public function blank(): bool
    {
        return false;
    }

    public function raw(): string
    {
        return sprintf('%s %s', static::tagName(), $this->markup);
    }

    public function parse(Tokenizer $tokenizer): static
    {
        return $this;
    }

    public function render(Context $context): string
    {
        return '';
    }

    protected function parseExpression(string $markup): mixed
    {
        return $this->parseContext->parseExpression($markup);
    }
}
