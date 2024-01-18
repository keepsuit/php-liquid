<?php

namespace Keepsuit\Liquid\Parse;

class Tokenizer
{
    protected ?int $startLineNumber = null;

    protected ?int $endLineNumber = null;

    protected int $offset = 0;

    /**
     * @var array<string>
     */
    protected array $tokens;

    public function __construct(
        protected string $source,
        int|bool|null $startLineNumber = null,
        public readonly bool $forLiquidTag = false
    ) {
        $this->tokens = $this->tokenize();

        $this->startLineNumber = match (true) {
            is_int($startLineNumber) => $startLineNumber,
            $startLineNumber === true => 1,
            default => null,
        };
    }

    /**
     * @return \Generator<string>
     */
    public function shift(): \Generator
    {
        while ($this->offset < count($this->tokens)) {
            $token = $this->tokens[$this->offset];

            $this->offset += 1;

            if ($this->startLineNumber !== null) {
                $this->startLineNumber = $this->endLineNumber ?? $this->startLineNumber;
                $this->endLineNumber = $this->startLineNumber + ($this->forLiquidTag ? 1 : substr_count($token, PHP_EOL));
            }

            yield $token;
        }
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

    public function getStartLineNumber(): ?int
    {
        return $this->startLineNumber;
    }

    public function getEndLineNumber(): ?int
    {
        return $this->endLineNumber;
    }
}
