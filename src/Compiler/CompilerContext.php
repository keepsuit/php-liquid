<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Nodes\Node;

final class CompilerContext
{
    public function __construct(private readonly CodeBuilder $builder = new CodeBuilder) {}

    public function write(string $line = ''): static
    {
        $this->builder->writeLine($line);

        return $this;
    }

    /**
     * Write a trusted compiler or plugin fragment without data encoding.
     */
    public function raw(string $fragment): static
    {
        $this->builder->writeRaw($fragment);

        return $this;
    }

    public function indent(): static
    {
        $this->builder->indent();

        return $this;
    }

    public function outdent(): static
    {
        $this->builder->dedent();

        return $this;
    }

    public function writeOutput(string $expression): static
    {
        $this->write('$output .= '.$expression.';');

        return $this;
    }

    public function subcompile(Node $node): static
    {
        $checkpoint = $this->builder->checkpoint();

        if ($node instanceof CanBeCompiled) {
            try {
                $node->compile($this);

                return $this;
            } catch (\Throwable) {
                $this->builder->rollback($checkpoint);
            }
        }

        try {
            $this->compileFallback($node);

            return $this;
        } catch (\Throwable $exception) {
            $this->builder->rollback($checkpoint);

            throw $exception;
        }
    }

    public function compileFallback(Node $node): void
    {
        $this->writeOutput(
            '\\Keepsuit\\Liquid\\Compiler\\CompiledTemplate::renderNode('
            .'$context, '.$this->writeValue($node).', '.$this->writeValue($node->lineNumber()).')'
        );
    }

    public function writeValue(mixed $value): string
    {
        $exported = $this->exportValue($value);

        if ($exported === null) {
            throw new \RuntimeException('Unable to safely encode a compiler value.');
        }

        return $exported;
    }

    /**
     * Export a value as PHP data. Scalars and scalar arrays stay readable in
     * the artifact; other values use a serialized data payload.
     */
    public function exportValue(mixed $value): ?string
    {
        if (is_null($value) || is_bool($value) || is_int($value) || is_string($value)) {
            return var_export($value, true);
        }

        if (is_float($value) && is_finite($value)) {
            return var_export($value, true);
        }

        if (is_array($value)) {
            $parts = [];

            foreach ($value as $key => $item) {
                $keyCode = $this->exportValue($key);
                $itemCode = $this->exportValue($item);

                if ($keyCode === null || $itemCode === null) {
                    break;
                }

                $parts[] = $keyCode.' => '.$itemCode;
            }

            if (count($parts) === count($value)) {
                return '['.implode(', ', $parts).']';
            }
        }

        return $this->exportSerializedValue($value);
    }

    public function exportSerializedValue(mixed $value): ?string
    {
        try {
            $serialized = serialize($value);
        } catch (\Throwable) {
            return null;
        }

        return '\\Keepsuit\\Liquid\\Compiler\\CompiledTemplate::decodeValue('
            .var_export(base64_encode($serialized), true).')';
    }

    public function getSource(): string
    {
        return $this->builder->getSource();
    }
}
