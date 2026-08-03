<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\CanBeExported;
use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class Variable extends Node implements CanBeEvaluated, CanBeExported, CanBeStreamed, HasParseTreeVisitorChildren
{
    public function __construct(
        /** @var Expression $name */
        public readonly mixed $name,
        /** @var array<array{0:string,1:array,2:array<string,mixed>}> */
        public readonly array $filters = [],
    ) {}

    public function render(RenderContext $context): string
    {
        return self::renderEvaluated($context, $this->evaluate($context));
    }

    /**
     * Render a variable from its parsed parts without rebuilding a Variable node.
     *
     * @param  array<string|int>  $lookups
     * @param  array<array{0:string,1:array,2:array<string,mixed>}>  $filters
     */
    public static function renderParts(RenderContext $context, string $name, array $lookups, array $filters): string
    {
        return self::renderEvaluated(
            $context,
            self::applyFilters($context, VariableLookup::evaluateParts($context, $name, $lookups), $filters),
        );
    }

    public function export(CompilerContext $context): ?string
    {
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

    public function parseTreeVisitorChildren(): array
    {
        return [$this->name, ...Arr::flatten($this->filters)];
    }

    public function evaluate(RenderContext $context): mixed
    {
        return self::applyFilters($context, $context->evaluate($this->name), $this->filters);
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

    private static function renderEvaluated(RenderContext $context, mixed $output): string
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
        return array_map(
            fn (mixed $value) => $context->evaluate($value),
            $filterArgs
        );
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
