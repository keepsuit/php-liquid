<?php

namespace Keepsuit\Liquid\Compiler;

class CodeBuilder
{
    protected int $indentLevel = 0;

    /** @var string[] */
    protected array $lines = [];

    public function indent(): void
    {
        $this->indentLevel++;
    }

    public function dedent(): void
    {
        $this->indentLevel = max(0, $this->indentLevel - 1);
    }

    public function writeLine(string $line = ''): void
    {
        $this->lines[] = str_repeat('    ', $this->indentLevel).$line;
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getSource(): string
    {
        return implode("\n", $this->lines)."\n";
    }
}
