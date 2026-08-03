<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\CanBeExported;
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Tag;

final class CompilerContext
{
    /**
     * @var array<string,mixed>
     */
    private array $fallbackValues = [];

    public function __construct(private CodeBuilder $builder = new CodeBuilder) {}

    /**
     * Compile a body inline while keeping render-score accounting in the base
     * compiled template.
     */
    public function compileBody(Node $body): static
    {
        $renderScore = $body instanceof BodyNode ? count($body->children()) : 1;
        $source = $this->compileBodySource($body);

        $this->write(sprintf(
            '$this->incrementCompiledRenderScore($context, %s);',
            $this->writeValue($renderScore),
        ));
        $this->writeSource($source['source']);

        return $this;
    }

    public function writeBodyCallback(Node $body, string $suffix = ''): static
    {
        $renderScore = $body instanceof BodyNode ? count($body->children()) : 1;
        $source = $this->compileBodySource($body);

        $this->write('function (RenderContext $context): iterable {');
        $this->indent();
        $this->write(sprintf(
            '$this->incrementCompiledRenderScore($context, %s);',
            $this->writeValue($renderScore),
        ));
        $this->writeSource($source['source']);
        if (! $source['hasYield']) {
            $this->write('return [];');
        }
        $this->outdent()->write('}'.$suffix);

        return $this;
    }

    /**
     * @return array{source:string,hasYield:bool}
     */
    private function compileBodySource(Node $body): array
    {
        $outerBuilder = $this->builder;
        $this->builder = new CodeBuilder;

        try {
            $this->subcompile($body);

            return [
                'source' => $this->builder->getSource(),
                'hasYield' => $this->builder->yieldCount() > 0,
            ];
        } finally {
            $this->builder = $outerBuilder;
        }
    }

    private function writeSource(string $source): void
    {
        foreach (explode("\n", rtrim($source, "\n")) as $line) {
            if ($line !== '') {
                $this->write($line);
            }
        }
    }

    public function compileRootBody(BodyNode $body): void
    {
        $renderScore = count($body->children());
        $source = $this->compileBodySource($body);

        $this->write(sprintf(
            '$this->incrementCompiledRenderScore($context, %s);',
            $this->writeValue($renderScore),
        ));
        $this->writeSource($source['source']);

        if (! $source['hasYield']) {
            $this->write('return [];');
        }
    }

    public function write(string $line = ''): static
    {
        $this->builder->writeLine($line);

        if (str_starts_with(ltrim($line), 'yield ')) {
            $this->builder->markYield();
        }

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
        return ! ($node instanceof Text || $node instanceof Raw || $node instanceof Variable);
    }

    public function subcompile(Node $node): static
    {
        if ($node instanceof Text || $node instanceof Raw || $node instanceof BodyNode || $node instanceof Document) {
            $node->compile($this);

            return $this;
        }

        $this->compileNode($node);

        return $this;
    }

    private function compileNode(Node $node): void
    {
        $checkpoint = $this->builder->checkpoint();
        $fallbackValueCount = count($this->fallbackValues);

        try {
            $this->write(sprintf(
                'yield from $this->yieldNode($context, %s, function () use ($context): iterable {',
                $this->writeValue($node->lineNumber()),
            ));
            $this->indent();
            $nodeBodyCheckpoint = $this->builder->checkpoint();
            $this->writeLineComment($node->lineNumber());

            if ($node instanceof CanBeCompiled) {
                $node->compile($this);
            } else {
                $this->compileFallback($node);
            }

            if ($this->builder->yieldCount() === $nodeBodyCheckpoint['yieldCount']) {
                $this->write('return [];');
            }
            $this->outdent()->write('});');
        } catch (\Throwable) {
            $this->rollbackCompilation($checkpoint, $fallbackValueCount);

            $this->write(sprintf(
                'yield from $this->yieldNode($context, %s, function () use ($context): iterable {',
                $this->writeValue($node->lineNumber()),
            ));
            $this->indent();
            $this->writeLineComment($node->lineNumber());
            $this->compileFallback($node);
            $this->outdent()->write('});');
        }
    }

    /**
     * Compile the node into a lazy generator so the base template can own the
     * runtime error boundary while the surrounding body remains resumable.
     */
    public function compileFallback(Node $node): void
    {
        $value = $this->writeRuntimeValue($node);

        if ($node instanceof Disableable && $node instanceof Tag) {
            $this->write($value.'->ensureTagIsEnabled($context);');
        }

        if ($node instanceof CanBeStreamed) {
            $this->write('yield from '.$value.'->stream($context);');
        } else {
            $this->writeOutput($value.'->render($context)');
        }
    }

    public function writeValue(mixed $value): string
    {
        if ($value instanceof CanBeExported && ($exported = $value->export($this)) !== null) {
            return $exported;
        }

        if (is_string($value)) {
            return $this->writeExpressionString($value);
        }

        if (is_array($value)) {
            $entries = [];
            $isList = array_is_list($value);

            foreach ($value as $key => $item) {
                $entries[] = ($isList ? '' : $this->writeValue($key).' => ').$this->writeValue($item);
            }

            return '['.implode(', ', $entries).']';
        }

        if (is_object($value)) {
            return $this->writeSerializedObject($value);
        }

        if (is_resource($value)) {
            throw new \RuntimeException('Unable to safely encode a compiler value containing a resource.');
        }

        return var_export($value, true);
    }

    private function writeSerializedObject(object $value): string
    {
        // Keep generated artifacts independent from Symfony's object exporter.
        $this->assertNoResources($value);

        try {
            $serialized = serialize($value);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Unable to safely encode a compiler value.', previous: $exception);
        }

        return '\\unserialize('.$this->writeValue($serialized).')';
    }

    /**
     * @param  array<int,true>  $seenObjects
     */
    private function assertNoResources(mixed $value, array &$seenObjects = []): void
    {
        if (is_resource($value)) {
            throw new \RuntimeException('Unable to safely encode a compiler value containing a resource.');
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                $this->assertNoResources($item, $seenObjects);
            }

            return;
        }

        if (! is_object($value)) {
            return;
        }

        $objectId = spl_object_id($value);
        if (isset($seenObjects[$objectId])) {
            return;
        }

        $seenObjects[$objectId] = true;
        $reflection = new \ReflectionObject($value);

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic() || ! $property->isInitialized($value)) {
                continue;
            }

            $this->assertNoResources($property->getValue($value), $seenObjects);
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
     * @param  array{sourceLength:int,indentLevel:int,yieldCount:int}  $checkpoint
     */
    private function rollbackCompilation(array $checkpoint, int $fallbackValueCount): void
    {
        $this->builder->rollback($checkpoint);
        $this->rollbackFallbackValues($fallbackValueCount);
    }

    public function getSource(): string
    {
        return $this->builder->getSource();
    }

    private function writeLiteral(string $value): string
    {
        $value = strtr($value, [
            '\\' => '\\\\',
            '"' => '\\"',
            '$' => '\\$',
            "\n" => '\\n',
            "\r" => '\\r',
            "\t" => '\\t',
            "\v" => '\\v',
            "\e" => '\\e',
            "\f" => '\\f',
        ]);

        $value = preg_replace_callback(
            '/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/',
            static fn (array $match): string => sprintf('\\x%02X', ord($match[0])),
            $value,
        );

        assert($value !== null);

        return '"'.$value.'"';
    }

    private function writeExpressionString(string $value): string
    {
        if (preg_match('/[\\x00-\\x1F\\x7F]/', $value) === 1) {
            return $this->writeLiteral($value);
        }

        return "'".str_replace(
            ['\\', "'"],
            ['\\\\', "\\'"],
            $value,
        )."'";
    }
}
