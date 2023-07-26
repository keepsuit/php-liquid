<?php

namespace Keepsuit\Liquid;

class Tokenizer
{
    protected int $offset = 0;
    protected array $tokens;

    public function __construct(
        protected string $source,
        protected bool $lineNumbers = false,
        protected int $lineNumber = 1,
        protected bool $forLiquidTag = false
    ) {
        $this->tokens = $this->tokenize();
    }

    public function shift(): ?string
    {
        $token = $this->tokens[$this->offset] ?? null;

        if ($token === null) {
            return null;
        }

        $this->offset += 1;

        if ($this->lineNumbers) {
            $this->lineNumber += $this->forLiquidTag ? 1 : substr_count($token, PHP_EOL);
        }

        return $token;
    }

    protected function tokenize(): array
    {
        if (strlen($this->source) === 0) {
            return [];
        }

        if ($this->forLiquidTag) {
            return explode(PHP_EOL, $this->source);
        }

        $regex = sprintf('/%s/m', TemplateParserRegex::TemplateParser);

        $tokens = preg_split($regex, $this->source, flags: PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        if ($tokens === false) {
            return [];
        }

        if ($tokens[0] === '') {
            $this->offset += 1;
        }

        return $tokens;
    }

    public function getLineNumber(): ?int
    {
        if (! $this->lineNumbers) {
            return null;
        }

        return $this->lineNumber;
    }
}
