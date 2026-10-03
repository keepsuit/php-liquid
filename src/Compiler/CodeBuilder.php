<?php

namespace Keepsuit\Liquid\Compiler;

class CodeBuilder
{
    protected int $indentLevel = 0;

    protected string $source = '';

    protected int $yieldCount = 0;

    /**
     * Facts about the emitted stream: checked means the buffer is below the
     * flush threshold; empty also permits skipping an unconditional flush.
     *
     * @var array{checked: bool, empty: bool}
     */
    protected array $streamBufferState = ['checked' => false, 'empty' => false];

    /** @var list<array{checked: bool, empty: bool}> */
    protected array $streamBufferScopes = [];

    public function indent(): static
    {
        $this->streamBufferScopes[] = $this->streamBufferState;
        $this->indentLevel++;

        return $this;
    }

    public function dedent(): static
    {
        if (($entry = array_pop($this->streamBufferScopes)) !== null) {
            // A scope may be skipped. Keep only facts true on both paths.
            $this->streamBufferState = [
                'checked' => $entry['checked'] && $this->streamBufferState['checked'],
                'empty' => $entry['empty'] && $this->streamBufferState['empty'],
            ];
        }
        $this->indentLevel = max(0, $this->indentLevel - 1);

        return $this;
    }

    public function writeLine(string $line = ''): static
    {
        if (trim($line) === '$buffer = "";') {
            $this->streamBufferState = ['checked' => true, 'empty' => true];
        } elseif (str_contains($line, '$buffer')) {
            $this->streamBufferState = ['checked' => false, 'empty' => false];
        }
        if ($this->source !== '' && ! str_ends_with($this->source, "\n")) {
            $this->source .= "\n";
        }

        $this->source .= str_repeat('    ', $this->indentLevel).$line."\n";

        return $this;
    }

    public function writeRaw(string $fragment): static
    {
        $this->streamBufferState = ['checked' => false, 'empty' => false];
        $this->source .= $fragment;

        return $this;
    }

    /**
     * @return array{sourceLength:int,indentLevel:int,yieldCount:int,bufferState:array{checked:bool,empty:bool},bufferScopes:list<array{checked:bool,empty:bool}>}
     */
    public function checkpoint(): array
    {
        return [
            'sourceLength' => strlen($this->source),
            'indentLevel' => $this->indentLevel,
            'yieldCount' => $this->yieldCount,
            'bufferState' => $this->streamBufferState,
            'bufferScopes' => $this->streamBufferScopes,
        ];
    }

    /**
     * @param  array{sourceLength:int,indentLevel:int,yieldCount:int,bufferState:array{checked:bool,empty:bool},bufferScopes:list<array{checked:bool,empty:bool}>}  $checkpoint
     */
    public function rollback(array $checkpoint): static
    {
        $this->source = substr($this->source, 0, $checkpoint['sourceLength']);
        $this->indentLevel = $checkpoint['indentLevel'];
        $this->yieldCount = $checkpoint['yieldCount'];
        $this->streamBufferState = $checkpoint['bufferState'];
        $this->streamBufferScopes = $checkpoint['bufferScopes'];

        return $this;
    }

    public function markYield(): static
    {
        $this->yieldCount++;

        return $this;
    }

    public function yieldCount(): int
    {
        return $this->yieldCount;
    }

    /** @return array{checked: bool, empty: bool} */
    public function streamBufferState(): array
    {
        return $this->streamBufferState;
    }

    /** @param array{checked: bool, empty: bool} $state */
    public function setStreamBufferState(array $state): void
    {
        $this->streamBufferState = $state;
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
