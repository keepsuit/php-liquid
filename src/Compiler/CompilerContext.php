<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\CanBeExported;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Tag;
use Symfony\Component\VarExporter\VarExporter;

final class CompilerContext
{
    /**
     * @var array<string,mixed>
     */
    private array $fallbackValues = [];

    /**
     * Bodies a runtime tag drives itself, compiled to their own method so the
     * tag keeps its loop and scope handling while the body stops being walked.
     *
     * @var array<string,string>
     */
    private array $methods = [];

    public function __construct(private CodeBuilder $builder = new CodeBuilder) {}

    /**
     * Compiles $body into a standalone method and returns its name, so a tag
     * that cannot be compiled itself can still be handed a compiled body.
     */
    public function compileBodyToMethod(Node $body): string
    {
        $name = 'body'.count($this->methods);
        $rawName = 'body'.(count($this->methods) + 1);
        // Reserve both names before compiling: nested bodies must not reuse them.
        $this->methods[$name] = '';
        $this->methods[$rawName] = '';

        $outerBuilder = $this->builder;

        $this->builder = new CodeBuilder;

        try {
            $this->subcompile($body);

            $this->methods[$rawName] = $this->builder->getSource();
        } finally {
            $this->builder = $outerBuilder;
        }

        $renderScore = $body instanceof BodyNode ? count($body->children()) : 1;
        $this->methods[$name] = sprintf(
            'yield from $this->yieldBody($context, %s, $this->%s($context));',
            $this->writeValue($renderScore),
            $rawName,
        );

        return $name;
    }

    /**
     * @return array<string,string>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function compileRootBody(BodyNode $body): void
    {
        $method = $this->compileBodyToMethod($body);
        $this->write('yield from $this->'.$method.'($context);');
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
        $this->write('yield '.$expression.';');

        return $this;
    }

    public function writeText(string $value): static
    {
        if ($value !== '') {
            $this->writeOutput($this->writeLiteral($value));
        }

        return $this;
    }

    public function writeLineComment(?int $lineNumber): static
    {
        if ($lineNumber !== null) {
            $this->write('// line '.$lineNumber);
        }

        return $this;
    }

    public function canInterrupt(Node $node): bool
    {
        return ! ($node instanceof Text || $node instanceof Raw || ($node instanceof Variable && $this->canCompileVariable($node)));
    }

    public function subcompile(Node $node): static
    {
        if ($node instanceof Text || $node instanceof Raw || $node instanceof BodyNode || $node instanceof Document) {
            $node->compile($this);

            return $this;
        }

        $method = $this->compileNodeToMethod($node);

        $this->write(sprintf(
            'yield from $this->yieldNode($context, %s, $this->%s($context));',
            $this->writeValue($node->lineNumber()),
            $method,
        ));

        return $this;
    }

    private function compileNodeToMethod(Node $node): string
    {
        $name = 'node'.count($this->methods);
        $this->methods[$name] = '';

        $outerBuilder = $this->builder;
        $this->builder = new CodeBuilder;

        try {
            $checkpoint = $this->builder->checkpoint();
            $fallbackValueCount = count($this->fallbackValues);
            $methods = $this->methods;

            try {
                if ($node instanceof Variable && $this->canCompileVariable($node)) {
                    $this->compileVariable($node);
                } elseif ($node instanceof CanBeCompiled) {
                    $this->writeLineComment($node->lineNumber());
                    $node->compile($this);
                } else {
                    $this->compileFallback($node);
                }
            } catch (\Throwable) {
                $this->rollbackCompilation($checkpoint, $fallbackValueCount, $methods);
                $this->compileFallback($node);
            }

            $this->methods[$name] = $this->builder->getSource();
        } finally {
            $this->builder = $outerBuilder;
        }

        return $name;
    }

    /**
     * Compile the node into a lazy generator so the base template can own the
     * runtime error boundary while the surrounding body remains resumable.
     */
    public function compileFallback(Node $node): void
    {
        $value = $this->writeRuntimeValue($node);

        $this->writeLineComment($node->lineNumber());

        if ($node instanceof Disableable && $node instanceof Tag) {
            $this->write($value.'->ensureTagIsEnabled($context);');
        }

        $this->writeOutput($value.'->render($context)');
    }

    private function compileVariable(Variable $node): void
    {
        assert($node->name instanceof VariableLookup);

        $this->writeLineComment($node->lineNumber());
        $this->writeOutput(sprintf(
            '$this->renderCompiledVariable($context, %s, %s, %s)',
            $this->writeValue($node->name->name),
            $this->writeValue($node->name->lookups),
            $this->writeValue($node->filters),
        ));
    }

    private function canCompileVariable(Variable $node): bool
    {
        if (! $node->name instanceof VariableLookup) {
            return false;
        }

        foreach ($node->name->lookups as $lookup) {
            if (! is_string($lookup) && ! is_int($lookup)) {
                return false;
            }
        }

        foreach ($node->filters as $filter) {
            if (! is_array($filter) || count($filter) !== 3 || ! is_string($filter[0])) {
                return false;
            }

            foreach ([$filter[1], $filter[2]] as $arguments) {
                if (! is_array($arguments)) {
                    return false;
                }

                foreach ($arguments as $argument) {
                    if ($argument !== null && ! is_scalar($argument)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function writeValue(mixed $value): string
    {
        if ($value instanceof CanBeExported && ($exported = $value->export($this)) !== null) {
            return $exported;
        }

        // Arrays are only taken apart when they actually hold an exportable
        // value; otherwise VarExporter's output is both smaller and faster.
        if (is_array($value) && $this->containsExportable($value)) {
            $entries = [];

            foreach ($value as $key => $item) {
                $entries[] = $this->writeValue($key).' => '.$this->writeValue($item);
            }

            return '['.implode(', ', $entries).']';
        }

        try {
            return VarExporter::export($value);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Unable to safely encode a compiler value.', previous: $exception);
        }
    }

    /**
     * @param  array<mixed>  $value
     */
    private function containsExportable(array $value): bool
    {
        foreach ($value as $item) {
            if ($item instanceof CanBeExported) {
                return true;
            }

            if (is_array($item) && $this->containsExportable($item)) {
                return true;
            }
        }

        return false;
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

    /**
     * Restore compiler state after a node's direct or native compiler path fails.
     *
     * @param  array{sourceLength:int,indentLevel:int}  $checkpoint
     * @param  array<string,string>  $methods
     */
    private function rollbackCompilation(array $checkpoint, int $fallbackValueCount, array $methods): void
    {
        $this->builder->rollback($checkpoint);
        $this->rollbackFallbackValues($fallbackValueCount);
        $this->methods = $methods;
    }

    public function getSource(): string
    {
        return $this->builder->getSource();
    }

    private function writeLiteral(string $value): string
    {
        if (preg_match('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/', $value) === 1) {
            return $this->writeValue($value);
        }

        return '"'.str_replace(
            ['\\', '"', '$', "\n"],
            ['\\\\', '\\"', '\\$', '\\n'],
            $value,
        ).'"';
    }
}
