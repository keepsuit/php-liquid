<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Tag;
use Symfony\Component\VarExporter\VarExporter;

final class CompilerContext
{
    /**
     * @var array<string,mixed>
     */
    private array $fallbackValues = [];

    /**
     * Bodies are inlined rather than wrapped in a closure, so each nesting level
     * needs its own accumulator variable.
     */
    private int $outputDepth = 0;

    public function __construct(private readonly CodeBuilder $builder = new CodeBuilder) {}

    public function outputVariable(): string
    {
        return '$output'.$this->outputDepth;
    }

    public function pushOutputScope(): string
    {
        $this->outputDepth++;

        return $this->outputVariable();
    }

    public function popOutputScope(): void
    {
        $this->outputDepth = max(0, $this->outputDepth - 1);
    }

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
        $this->write($this->outputVariable().' .= '.$expression.';');

        return $this;
    }

    public function writeNodeErrorHandling(?int $lineNumber): static
    {
        $line = $this->writeValue($lineNumber);

        return $this
            ->outdent()
            ->write('} catch (\\Keepsuit\\Liquid\\Exceptions\\UndefinedVariableException|\\Keepsuit\\Liquid\\Exceptions\\UndefinedDropMethodException|\\Keepsuit\\Liquid\\Exceptions\\UndefinedFilterException $exception) {')
            ->indent()
            ->write('$context->handleError($exception, '.$line.');')
            ->outdent()
            ->write('} catch (\\Throwable $exception) {')
            ->indent()
            ->write($this->outputVariable().' .= $context->handleError($exception, '.$line.');')
            ->outdent()
            ->write('}');
    }

    public function subcompile(Node $node): static
    {
        $checkpoint = $this->builder->checkpoint();
        $fallbackValueCount = count($this->fallbackValues);
        $outputDepth = $this->outputDepth;

        if ($node instanceof CanBeCompiled) {
            try {
                $node->compile($this);

                return $this;
            } catch (\Throwable) {
                $this->builder->rollback($checkpoint);
                $this->rollbackFallbackValues($fallbackValueCount);
                $this->outputDepth = $outputDepth;
            }
        }

        try {
            $this->compileFallback($node);

            return $this;
        } catch (\Throwable $exception) {
            $this->builder->rollback($checkpoint);
            $this->rollbackFallbackValues($fallbackValueCount);
            $this->outputDepth = $outputDepth;

            throw $exception;
        }
    }

    /**
     * The dispatch is inlined rather than routed through a runtime helper: the
     * helper costs a call frame per node, and whether the node needs a
     * tag-enabled check is already known here, at compile time.
     */
    public function compileFallback(Node $node): void
    {
        $value = $this->writeRuntimeValue($node);

        $this->write('try {')->indent();

        if ($node instanceof Disableable && $node instanceof Tag) {
            $this->write($value.'->ensureTagIsEnabled($context);');
        }

        $this->writeOutput($value.'->render($context)');
        $this->writeNodeErrorHandling($node->lineNumber());
    }

    public function writeValue(mixed $value): string
    {
        try {
            return VarExporter::export($value);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Unable to safely encode a compiler value.', previous: $exception);
        }
    }

    public function exportValue(mixed $value): ?string
    {
        try {
            return VarExporter::export($value);
        } catch (\Throwable) {
            return null;
        }
    }

    public function writeRuntimeValue(mixed $value): string
    {
        return $this->registerFallbackValue($value);
    }

    /**
     * @return array<string,mixed>
     */
    public function getFallbackValues(): array
    {
        return $this->fallbackValues;
    }

    private function registerFallbackValue(mixed $value): string
    {
        $property = 'value'.count($this->fallbackValues);
        $this->fallbackValues[$property] = $value;

        return '$this->'.$property;
    }

    private function rollbackFallbackValues(int $count): void
    {
        $this->fallbackValues = array_slice($this->fallbackValues, 0, $count, preserve_keys: true);
    }

    public function getSource(): string
    {
        return $this->builder->getSource();
    }
}
