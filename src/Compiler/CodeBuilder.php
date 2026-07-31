<?php

namespace Keepsuit\Liquid\Compiler;

class CodeBuilder
{
    protected int $indentLevel = 0;

    protected string $source = '';

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
        if ($this->source !== '' && ! str_ends_with($this->source, "\n")) {
            $this->source .= "\n";
        }

        $this->source .= str_repeat('    ', $this->indentLevel).$line."\n";
    }

    public function writeRaw(string $fragment): void
    {
        $this->source .= $fragment;
    }

    /**
     * @return array{sourceLength:int,indentLevel:int}
     */
    public function checkpoint(): array
    {
        return [
            'sourceLength' => strlen($this->source),
            'indentLevel' => $this->indentLevel,
        ];
    }

    /**
     * @param  array{sourceLength:int,indentLevel:int}  $checkpoint
     */
    public function rollback(array $checkpoint): void
    {
        $this->source = substr($this->source, 0, $checkpoint['sourceLength']);
        $this->indentLevel = $checkpoint['indentLevel'];
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        if ($this->source === '') {
            return [];
        }

        return explode("\n", rtrim($this->source, "\n"));
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
