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

        if ($output instanceof CanBeRendered) {
            return $output->render($context);
        }

        return $this->renderOutput($output);
    }

    /**
     * Hoist the node into a constructor-built property instead of re-exporting
     * the lookup inline: an inline export rebuilds the whole node graph on every
     * render, which is strictly more work than a parsed template does.
     */
    public function compile(CompilerContext $context): void
    {
        $context->compileFallback($this);
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
                yield $this->renderOutput($chunk);
            }

            return;
        }

        yield $this->renderOutput($output);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->name, ...Arr::flatten($this->filters)];
    }

    public function evaluate(RenderContext $context): mixed
    {
        $output = $context->evaluate($this->name);

        if ($this->filters === []) {
            return $output;
        }

        if ($output instanceof \Generator) {
            $output = iterator_to_array($output, preserve_keys: false);
        }

        foreach ($this->filters as [$filterName, $filterArgs, $filterNamedArgs]) {
            if ($filterArgs === [] && $filterNamedArgs === []) {
                $output = $context->applyFilter($filterName, $output);

                continue;
            }

            $filterArgs = $this->evaluateFilterExpressions($context, $filterArgs);

            if ($filterNamedArgs !== []) {
                $filterArgs = [...$filterArgs, ...$this->evaluateFilterExpressions($context, $filterNamedArgs)];
            }

            $output = $context->applyFilter($filterName, $output, $filterArgs);
        }

        return $output;
    }

    protected function renderOutput(mixed $output): string
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
            return implode('', array_map($this->renderOutput(...), $output));
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
