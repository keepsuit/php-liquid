<?php

namespace Keepsuit\Liquid\Compiler;

class CodeBuilder
{
    protected int $indentLevel = 0;

    protected string $source = '';

    protected int $yieldCount = 0;

    /** @var array<int, string> */
    protected array $indentation = [0 => ''];

    /**
     * Facts about the emitted stream: checked means the buffer is below the
     * flush threshold; empty also permits skipping an unconditional flush.
     *
     * @var array{checked: bool, empty: bool, maxLength: ?int}
     */
    protected array $streamBufferState = ['checked' => false, 'empty' => false, 'maxLength' => null];

    /** @var list<array{checked: bool, empty: bool, maxLength: ?int}> */
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
                'maxLength' => $entry['maxLength'] !== null && $this->streamBufferState['maxLength'] !== null
                    ? max($entry['maxLength'], $this->streamBufferState['maxLength'])
                    : null,
            ];
        }
        $this->indentLevel = max(0, $this->indentLevel - 1);

        return $this;
    }

    public function writeLine(string $line = ''): static
    {
        if ($this->source !== '' && ! str_ends_with($this->source, "\n")) {
            $this->source .= "\n";
        }

        $this->source .= ($line === '' ? '' : ($this->indentation[$this->indentLevel] ??= str_repeat('    ', $this->indentLevel))).$line."\n";

        return $this;
    }

    public function writeLines(string $source, bool $skipEmptyLines = false): static
    {
        $source = rtrim($source, "\n")."\n";
        if ($skipEmptyLines) {
            $source = preg_replace('/^\n/m', '', $source);
            assert($source !== null);
        }

        if ($source === '') {
            return $this;
        }

        if ($this->source !== '' && ! str_ends_with($this->source, "\n")) {
            $this->source .= "\n";
        }

        if ($this->indentLevel > 0) {
            $indentation = $this->indentation[$this->indentLevel] ??= str_repeat('    ', $this->indentLevel);
            $source = preg_replace('/^(?=.)/m', $indentation, $source);
            assert($source !== null);
        }

        $this->source .= $source;

        return $this;
    }

    public function writeRaw(string $fragment): static
    {
        $this->streamBufferState = ['checked' => false, 'empty' => false, 'maxLength' => null];
        $this->source .= $fragment;

        return $this;
    }

    /**
     * @return array{sourceLength:int,indentLevel:int,yieldCount:int,bufferState:array{checked:bool,empty:bool,maxLength:?int},bufferScopes:list<array{checked:bool,empty:bool,maxLength:?int}>}
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
     * @param  array{sourceLength:int,indentLevel:int,yieldCount:int,bufferState:array{checked:bool,empty:bool,maxLength:?int},bufferScopes:list<array{checked:bool,empty:bool,maxLength:?int}>}  $checkpoint
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

    /** @return array{checked: bool, empty: bool, maxLength: ?int} */
    public function streamBufferState(): array
    {
        return $this->streamBufferState;
    }

    /** @param array{checked: bool, empty: bool, maxLength: ?int} $state */
    public function setStreamBufferState(array $state): void
    {
        $this->streamBufferState = $state;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
