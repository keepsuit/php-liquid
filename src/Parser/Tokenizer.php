<?php

namespace Keepsuit\Liquid\Parser;

class Tokenizer
{
    protected ?int $lineNumber = null;

    protected int $offset = 0;

    protected array $tokens;

    public function __construct(
        protected string $source,
        int|bool $lineNumber = null,
        public readonly bool $forLiquidTag = false
    ) {
        $this->tokens = $this->tokenize();

        $this->lineNumber = match (true) {
            is_int($lineNumber) => $lineNumber,
            $lineNumber === true => 1,
            default => null,
        };
    }

    public function shift(): ?string
    {
        $token = $this->tokens[$this->offset] ?? null;

        if ($token === null) {
            return null;
        }

        $this->offset += 1;

        if ($this->lineNumber !== null) {
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

        $regex = sprintf('/%s/m', Regex::TemplateParser);

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
        return $this->lineNumber;
    }
}
