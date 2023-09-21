<?php

namespace Keepsuit\Liquid\Nodes;

use Generator;
use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\Expression;
use Keepsuit\Liquid\Parse\Parser;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\GeneratorToString;

class Variable implements CanBeEvaluated, CanBeRendered, HasParseTreeVisitorChildren
{
    use GeneratorToString;

    /**
     * @throws SyntaxException
     */
    public function __construct(
        protected mixed $name,
        /** @var array<array{0:string,1:array}> */
        protected array $filters = [],
        protected string $markup = '',
        public readonly ?int $lineNumber = null
    ) {
    }

    public static function fromMarkup(string $markup, int $lineNumber = null): Variable
    {
        try {
            $variable = static::fromParser(new Parser($markup), $lineNumber);
        } catch (SyntaxException $exception) {
            $exception->markupContext = sprintf('{{%s}}', $markup);
            throw $exception;
        }

        $variable->markup = $markup;

        return $variable;
    }

    public static function fromParser(Parser $parser, int $lineNumber = null): Variable
    {
        if ($parser->look(TokenType::EndOfString)) {
            return new Variable(
                name: null,
                filters: [],
                lineNumber: $lineNumber,
            );
        }

        $markup = $parser->toString();

        $name = static::parseExpression($parser->expression());

        $filters = [];
        while ($parser->consumeOrFalse(TokenType::Pipe)) {
            $filterName = $parser->consume(TokenType::Identifier);
            $filterArgs = $parser->consumeOrFalse(TokenType::Colon) ? static::parseFilterArgs($parser) : [];
            $filters[] = static::parseFilterExpressions($filterName, $filterArgs);
        }

        $parser->consume(TokenType::EndOfString);

        return new Variable(
            name: $name,
            filters: $filters,
            markup: $markup,
            lineNumber: $lineNumber,
        );
    }

    public function name(): mixed
    {
        return $this->name;
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function raw(): string
    {
        return $this->markup;
    }

    public function render(Context $context): string
    {
        return $this->generatorToString($this->renderAsync($context));
    }

    /**
     * @return Generator<string>
     */
    public function renderAsync(Context $context): Generator
    {
        $output = $this->evaluate($context);

        if ($output instanceof Generator) {
            yield from $output;

            return;
        }

        if ($output instanceof CanBeRendered) {
            yield from $output->renderAsync($context);

            return;
        }

        yield match (true) {
            is_array($output) => implode('', $output),
            is_bool($output) => $output ? 'true' : 'false',
            is_string($output) || is_numeric($output) => (string) $output,
            is_object($output) && method_exists($output, '__toString') => (string) $output,
            default => '',
        };
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->name, ...Arr::flatten($this->filters)];
    }

    public function evaluate(Context $context): mixed
    {
        $output = $context->evaluate($this->name);

        if ($this->filters === []) {
            return $output;
        }

        if ($output instanceof Generator) {
            $output = iterator_to_array($output);
        }

        foreach ($this->filters as [$filterName, $filterArgs, $filterNamedArgs]) {
            $filterArgs = $this->evaluateFilterExpressions($context, $filterArgs ?? []);
            $filterNamedArgs = $this->evaluateFilterExpressions($context, $filterNamedArgs ?? []);
            $output = $context->applyFilter($filterName, $output, ...$filterArgs, ...$filterNamedArgs);
        }

        return $output;
    }

    protected static function parseFilterArgs(Parser $parser): array
    {
        $filterArgs = [$parser->argument()];
        while ($parser->consumeOrFalse(TokenType::Comma)) {
            $filterArgs[] = $parser->argument();
        }

        return $filterArgs;
    }

    /**
     * @param  array<string|array<string,string>>  $filterArgs
     * @return array{0:string, 1:array, 2:array<string,mixed>}
     */
    protected static function parseFilterExpressions(string $filterName, array $filterArgs): array
    {
        $parsedArgs = [];
        $parsedNamedArgs = [];

        foreach ($filterArgs as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $key => $value) {
                    $parsedNamedArgs[$key] = static::parseExpression($value);
                }
            } else {
                $parsedArgs[] = static::parseExpression($arg);
            }
        }

        return [$filterName, $parsedArgs, $parsedNamedArgs];
    }

    protected static function evaluateFilterExpressions(Context $context, array $filterArgs): array
    {
        return array_map(
            fn (mixed $value) => $context->evaluate($value),
            $filterArgs
        );
    }

    protected static function parseExpression(string $markup): mixed
    {
        return Expression::parse($markup);
    }
}
