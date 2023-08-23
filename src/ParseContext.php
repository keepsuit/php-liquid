<?php

namespace Keepsuit\Liquid;

use Closure;
use Keepsuit\Liquid\Exceptions\SyntaxException;

class ParseContext
{
    public readonly I18n $locale;

    public ErrorMode $errorMode;

    public ?int $lineNumber = null;

    public bool $trimWhitespace = false;

    public int $depth = 0;

    protected bool $partial = false;

    /**
     * @var array<SyntaxException>
     */
    protected array $warnings = [];

    public function __construct(
        ErrorMode $errorMode = null,
        I18n $locale = null,
    ) {
        $this->locale = $locale ?? new I18n();
        $this->errorMode = $errorMode ?? Template::$errorMode;
    }

    public function newTokenizer(string $markup, int $startLineNumber = null, bool $forLiquidTag = false): Tokenizer
    {
        return new Tokenizer($markup, lineNumber: $startLineNumber, forLiquidTag: $forLiquidTag);
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
        $this->partial = true;

        try {
            return $closure($this);
        } finally {
            $this->partial = false;
        }
    }
}
