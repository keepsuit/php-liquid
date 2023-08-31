<?php

namespace Keepsuit\Liquid\Parse;

use Closure;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Support\I18n;
use Keepsuit\Liquid\Support\TagRegistry;
use Keepsuit\Liquid\Template;

class ParseContext
{
    public ?int $lineNumber = null;

    public bool $trimWhitespace = false;

    public int $depth = 0;

    protected bool $partial = false;

    /**
     * @var array<SyntaxException>
     */
    protected array $warnings = [];

    public function __construct(
        bool|int $startLineNumber = null,
        public readonly I18n $locale = new I18n(),
        public readonly TagRegistry $tagRegistry = new TagRegistry(),
    ) {
        $this->lineNumber = match (true) {
            is_int($startLineNumber) => $startLineNumber,
            $startLineNumber === true => 1,
            default => null,
        };
    }

    public function newTokenizer(string $markup, bool $forLiquidTag = false): Tokenizer
    {
        return new Tokenizer($markup, startLineNumber: $this->lineNumber, forLiquidTag: $forLiquidTag);
    }

    public function parseExpression(string $markup): mixed
    {
        return Expression::parse($markup);
    }

    public function logWarning(SyntaxException $e): void
    {
        $this->warnings[] = $e;
    }

    /**
     * @template TResult
     *
     * @param  Closure(ParseContext $parseContext): TResult  $closure
     * @return TResult
     */
    public function partial(Closure $closure)
    {
        $oldLineNumber = $this->lineNumber;

        $this->partial = true;
        $this->lineNumber = $this->lineNumber !== null ? 1 : null;

        try {
            return $closure($this);
        } finally {
            $this->partial = false;
            $this->lineNumber = $oldLineNumber;
        }
    }
}
