<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\CanBeExported;
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
class Variable extends Node implements CanBeCompiled, CanBeEvaluated, CanBeExported, CanBeStreamed, HasParseTreeVisitorChildren
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
            $context->write('yield from '.$context->writeRuntimeValue($this).'->stream($context);');

            return;
        }

        if ($this->filters !== []) {
            $value = $this->compileValue($context);
            $context->writeOutput('\\'.self::class.'::renderEvaluated($context, '.$value.')');

            return;
        }

        if ($context->isRendering()) {
            $context->writeOutput('\\'.self::class.'::renderValue($context, '
                .$context->writeVariableExpression($this->name).')');
        } else {
            $context->write('if (is_string($value = \\'.self::class.'::streamValue($context, '
                .$context->writeVariableExpression($this->name).'))) {')
                ->indent();
            $context->writeOutput('$value');
            $context->outdent()->write('} else {')->indent();
            $context->flushStreamBuffer();
            $context->write('yield from $value;')->outdent()->write('}');
        }
    }

    public function compileValue(CompilerContext $context): string
    {
        $value = $context->temporaryVariable();
        if (static::class !== self::class) {
            $context->write($value.' = '.$context->writeRuntimeValue($this).'->evaluate($context);');

            return $value;
        }
        if ($this->filters === []) {
            $context->write($value.' = $context->evaluate('.$context->writeVariableExpression($this->name).');');

            return $value;
        }

        $context->write($value.' = \\'.self::class.'::filterInput($context, '
            .$context->writeVariableExpression($this->name).');');

        foreach ($this->filters as [$filterName, $filterArgs, $filterNamedArgs]) {
            $args = '';
            if ($filterArgs !== [] || $filterNamedArgs !== []) {
                $args = $this->compileFilterArguments($context, $filterArgs);
                if ($filterNamedArgs !== []) {
                    $namedArgs = $this->compileFilterArguments($context, $filterNamedArgs);
                    $args = '[...'.$args.', ...'.$namedArgs.']';
                }
                $args = ', '.$args;
            }

            $context->write($value.' = $context->applyFilter('.$context->writeValue($filterName).', '.$value.$args.');');
        }

        return $value;
    }

    private function compileFilterArguments(CompilerContext $context, array $arguments): string
    {
        $values = [];
        foreach ($arguments as $key => $argument) {
            $key = $context->writeValue($key);
            $source = $context->writeCachedValue($argument);
            $values[] = $key.' => '.(is_scalar($argument) || $argument === null
                ? $source
                : '$context->evaluate('.$source.')');
        }

        return '['.implode(', ', $values).']';
    }

    public function export(CompilerContext $context): ?string
    {
        if (static::class !== self::class) {
            return null;
        }

        $expression = 'new \\'.self::class.'('
            .$context->writeValue($this->name).', '
            .$context->writeValue($this->filters).')';

        return $this->lineNumber === null
            ? $expression
            : '('.$expression.')->setLineNumber('.$this->lineNumber.')';
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

        if (is_numeric($output)) {
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
