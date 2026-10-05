<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\FilterCoercion;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class Variable extends Node implements CanBeCompiled, CanBeEvaluated, CanBeStreamed, HasParseTreeVisitorChildren
{
    public function __construct(
        /** @var Expression $name */
        public readonly mixed $name,
        /** @var array<array{0:string,1:array,2:array<string,mixed>}> */
        public readonly array $filters = [],
    ) {}

    public function render(RenderContext $context): string
    {
        $output = $this->evaluate($context);

        if (is_string($output)) {
            return $output;
        }

        if ($output instanceof CanBeRendered) {
            return $output->render($context);
        }

        return self::renderOutputValue($output);
    }

    public function compile(CompilerContext $context): void
    {
        if (static::class !== self::class) {
            $context->compileFallback($this);

            return;
        }

        if ($this->filters !== []) {
            $context->writeOutput(self::compileRenderEvaluated($context, $this->compileValue($context)));

            return;
        }

        // These literals have a context-independent output representation.
        // Floats retain runtime coercion, which can depend on PHP settings.
        if (($literal = $this->constantOutput()) !== null) {
            $context->writeText($literal);

            return;
        }

        if ($context->isRendering()) {
            $value = $context->temporaryVariable();
            $context->writeOutput('(is_string('.$value.' = '.$context->writeVariableExpression($this->name).') ? '.$value
                .' : '.$context->writeClassName(self::class).'::renderValue($context, '.$value.'))');
        } else {
            $context->write('if (is_string($value = '.$context->writeVariableExpression($this->name).')'
                .' || is_string($value = '.$context->writeClassName(self::class).'::streamValue($context, $value))) {')
                ->indent();
            $context->writeOutput('$value');
            $context->outdent()->write('} else {')->indent();
            $context->flushStreamBuffer();
            $context->writeYield('from $value')->outdent()->write('}');
        }
    }

    /**
     * Strings are the common output and need no runtime call.
     *
     * @internal
     */
    public static function compileRenderEvaluated(CompilerContext $context, string $value): string
    {
        return '(is_string('.$value.') ? '.$value.' : '.$context->writeClassName(self::class).'::renderEvaluated($context, '.$value.'))';
    }

    /** @internal */
    public function constantOutput(): ?string
    {
        return static::class === self::class && $this->filters === []
            && (is_string($this->name) || is_int($this->name) || is_bool($this->name) || $this->name === null)
            ? self::renderOutputValue($this->name)
            : null;
    }

    public function compileValue(CompilerContext $context): string
    {
        $value = $context->temporaryVariable();
        if (static::class !== self::class) {
            $context->write($value.' = '.$context->writeRuntimeValue($this).'->evaluate($context);');

            return $value;
        }
        if ($this->filters === []) {
            $context->write($value.' = '.$context->writeEvaluatedExpression($this->name).';');

            return $value;
        }

        $context->write($value.' = '.$context->writeClassName(self::class).'::filterInput($context, '
            .$context->writeVariableExpression($this->name).');');

        foreach ($this->filters as [$filterName, $filterArgs, $filterNamedArgs]) {
            $args = '';
            if ($filterArgs !== [] || $filterNamedArgs !== []) {
                $args = ', '.$this->compileFilterArguments($context, $filterArgs, $filterNamedArgs);
            }

            $context->write($value.' = $context->applyFilter('.$context->writeValue($filterName).', '.$value.$args.');');
        }

        return $value;
    }

    private function compileFilterArguments(CompilerContext $context, array $arguments, array $namedArguments): string
    {
        $values = [];
        $position = 0;
        foreach ([$arguments, $namedArguments] as $group) {
            foreach ($group as $key => $argument) {
                // Unpacking both groups reindexes numeric keys without skipping
                // evaluation of expressions whose string keys are overwritten.
                $key = $namedArguments !== [] && is_int($key) ? $position++ : $key;
                $key = $context->writeValue($key);
                $values[] = $key.' => '.$context->writeEvaluatedExpression($argument);
            }
        }

        return '['.implode(', ', $values).']';
    }

    public function stream(RenderContext $context): \Generator
    {
        if ($this->filters !== []) {
            yield $this->render($context);

            return;
        }

        $output = $this->evaluate($context);

        if ($output instanceof CanBeStreamed) {
            yield from $output->stream($context);

            return;
        }

        if ($output instanceof CanBeRendered) {
            yield $output->render($context);

            return;
        }

        if ($output instanceof \Generator) {
            foreach ($output as $chunk) {
                yield self::renderOutputValue($chunk);
            }

            return;
        }

        yield self::renderOutputValue($output);
    }

    /**
     * Resolve a value without allocating a generator for scalar output.
     *
     * @return string|\Generator<string>
     */
    public static function streamValue(RenderContext $context, mixed $output): string|\Generator
    {
        if (is_string($output)) {
            return $output;
        }

        if ($output instanceof CanBeEvaluated) {
            $output = $context->evaluate($output);
        }

        if ($output instanceof CanBeStreamed) {
            return $output->stream($context);
        }

        if ($output instanceof CanBeRendered) {
            return $output->render($context);
        }

        if ($output instanceof \Generator) {
            return self::streamOutput($output);
        }

        return self::renderOutputValue($output);
    }

    /**
     * @return \Generator<string>
     */
    private static function streamOutput(\Generator $output): \Generator
    {
        foreach ($output as $chunk) {
            yield self::renderOutputValue($chunk);
        }
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->name, ...Arr::flatten($this->filters)];
    }

    public function evaluate(RenderContext $context): mixed
    {
        $value = $context->evaluate($this->name);

        return $this->filters === [] ? $value : self::applyFilters($context, $value, $this->filters);
    }

    /**
     * Render a resolved expression, including any remaining evaluators and filters.
     *
     * @param  array<array{0:string,1:array,2:array<string,mixed>}>  $filters
     */
    public static function renderValue(RenderContext $context, mixed $value, array $filters = []): string
    {
        if ($value instanceof CanBeEvaluated) {
            $value = $context->evaluate($value);
        }

        return self::renderEvaluated($context, $filters === [] ? $value : self::applyFilters($context, $value, $filters));
    }

    /**
     * Materialize the input once, before running the compiled filter chain.
     */
    public static function filterInput(RenderContext $context, mixed $value): mixed
    {
        if ($value instanceof CanBeEvaluated) {
            $value = $context->evaluate($value);
        }

        return $value instanceof \Generator ? iterator_to_array($value, preserve_keys: false) : $value;
    }

    /**
     * @param  array<array{0:string,1:array,2:array<string,mixed>}>  $filters
     */
    private static function applyFilters(RenderContext $context, mixed $output, array $filters): mixed
    {
        if ($filters === []) {
            return $output;
        }

        if ($output instanceof \Generator) {
            $output = iterator_to_array($output, preserve_keys: false);
        }

        foreach ($filters as [$filterName, $filterArgs, $filterNamedArgs]) {
            if ($filterArgs === [] && $filterNamedArgs === []) {
                $output = $context->applyFilter($filterName, $output);

                continue;
            }

            $filterArgs = self::evaluateFilterExpressions($context, $filterArgs);

            if ($filterNamedArgs !== []) {
                $filterArgs = [...$filterArgs, ...self::evaluateFilterExpressions($context, $filterNamedArgs)];
            }

            $output = $context->applyFilter($filterName, $output, $filterArgs);
        }

        return $output;
    }

    public static function renderEvaluated(RenderContext $context, mixed $output): string
    {
        if ($output instanceof CanBeRendered) {
            return $output->render($context);
        }

        return self::renderOutputValue($output);
    }

    private static function renderOutputValue(mixed $output): string
    {
        if (is_string($output)) {
            return $output;
        }

        if ($output === null) {
            return '';
        }

        if (is_bool($output)) {
            return $output ? 'true' : 'false';
        }

        if (is_float($output)) {
            return FilterCoercion::toString($output);
        }

        if (is_int($output)) {
            return (string) $output;
        }

        if ($output instanceof \Generator) {
            $output = iterator_to_array($output, preserve_keys: false);
        }

        if (is_array($output)) {
            return implode('', array_map(self::renderOutputValue(...), $output));
        }

        if (is_object($output) && method_exists($output, '__toString')) {
            return (string) $output;
        }

        return '';
    }

    protected static function evaluateFilterExpressions(RenderContext $context, array $filterArgs): array
    {
        $evaluated = [];
        foreach ($filterArgs as $key => $value) {
            $evaluated[$key] = $context->evaluate($value);
        }

        return $evaluated;
    }

    public function debugLabel(): ?string
    {
        return match (true) {
            $this->name instanceof VariableLookup => $this->name->name,
            $this->name instanceof RangeLookup => $this->name->toString(),
            $this->name instanceof Literal => $this->name->value,
            is_string($this->name) => $this->name,
            is_bool($this->name) => $this->name ? 'true' : 'false',
            is_numeric($this->name) => (string) $this->name,
            default => null,
        };
    }
}
