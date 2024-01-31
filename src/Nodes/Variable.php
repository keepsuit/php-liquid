<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class Variable extends Node implements CanBeEvaluated, HasParseTreeVisitorChildren
{
    public function __construct(
        /** @var Expression $name */
        public readonly mixed $name,
        /** @var array<array{0:string,1:array,2:array<string,mixed>}> */
        public readonly array $filters = [],
    ) {
    }

    public function render(RenderContext $context): string
    {
        $output = $this->evaluate($context);

        if ($output instanceof CanBeRendered) {
            return $output->render($context);
        }

        if (is_array($output)) {
            return implode('', $output);
        }

        if ($output === null) {
            return '';
        }

        if (is_bool($output)) {
            return $output ? 'true' : 'false';
        }

        if (is_string($output) || is_numeric($output)) {
            return (string) $output;
        }

        if (is_object($output) && method_exists($output, '__toString')) {
            return (string) $output;
        }

        return '';
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->name, ...Arr::flatten($this->filters)];
    }

    public function evaluate(RenderContext $context): mixed
    {
        $output = $context->evaluate($this->name);

        if ($output instanceof \Generator) {
            $output = iterator_to_array($output);
        }

        foreach ($this->filters as [$filterName, $filterArgs, $filterNamedArgs]) {
            $filterArgs = $this->evaluateFilterExpressions($context, $filterArgs ?? []);
            $filterNamedArgs = $this->evaluateFilterExpressions($context, $filterNamedArgs ?? []);
            $output = $context->applyFilter($filterName, $output, ...$filterArgs, ...$filterNamedArgs);
        }

        return $output;
    }

    protected static function evaluateFilterExpressions(RenderContext $context, array $filterArgs): array
    {
        return array_map(
            fn (mixed $value) => $context->evaluate($value),
            $filterArgs
        );
    }
}
