<?php

namespace Keepsuit\Liquid;

class ParseContext
{
    public readonly I18n $locale;

    public ErrorMode $errorMode;

    protected array $templateOptions;

    public ?int $lineNumber = null;

    public bool $trimWhitespace = false;

    public int $depth = 0;

    protected bool $partial = false;

    /**
     * @var array<SyntaxException>
     */
    protected array $warnings = [];

    public function __construct(array $options = [])
    {
        $this->templateOptions = $options['dup'] ?? [];
        $this->locale = (($options['locale'] ?? null) instanceof I18n) ? $options['locale'] : new I18n();
        $this->errorMode = $options['errorMode'] ?? Template::$errorMode;
    }

    public function newTokenizer(string $markup, int $startLineNumber = null, bool $forLiquidTag = false): Tokenizer
    {
        return new Tokenizer($markup, lineNumber: $startLineNumber, forLiquidTag: $forLiquidTag);
    }

    public function parseExpression(string $markup): mixed
    {
        return Expression::parse($markup);
    }

    public function newBlockBody(): BlockBody
    {
        return new BlockBody();
    }

    public function logWarning(SyntaxException $e): void
    {
        $this->warnings[] = $e;
    }
}
