<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Parser;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\Arr;

class Variable implements CanBeEvaluated, CanBeRendered, HasParseTreeVisitorChildren
{
    const JustTagAttributes = '/\A'.Regex::TagAttributes.'\z/';

    protected mixed $name = null;

    public readonly ?int $lineNumber;

    /**
     * @var array<array{0:string,1:array}>
     */
    protected array $filters = [];

    /**
     * @throws SyntaxException
     */
    public function __construct(
        public readonly string $markup,
        public readonly ParseContext $parseContext
    ) {
        $this->lineNumber = $this->parseContext->lineNumber;

        try {
            $this->parseVariable($markup);
        } catch (SyntaxException $exception) {
            $exception->markupContext = sprintf('{{%s}}', $markup);
            throw $exception;
        }
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

    public function evaluate(Context $context): mixed
    {
        $output = $context->evaluate($this->name);

        foreach ($this->filters as [$filterName, $filterArgs, $filterNamedArgs]) {
            $filterArgs = $this->evaluateFilterExpressions($context, $filterArgs ?? []);
            $filterNamedArgs = $this->evaluateFilterExpressions($context, $filterNamedArgs ?? []);
            $output = $context->applyFilter($filterName, $output, ...$filterArgs, ...$filterNamedArgs);
        }

        return $output;
    }

    /**
     * @throws SyntaxException
     */
    protected function parseVariable(string $markup): void
    {
        $this->filters = [];

        $parser = new Parser($markup);

        if ($parser->look(TokenType::EndOfString)) {
            return;
        }

        $this->name = $this->parseContext->parseExpression($parser->expression());

        while ($parser->consumeOrFalse(TokenType::Pipe)) {
            $filterName = $parser->consume(TokenType::Identifier);
            $filterArgs = $parser->consumeOrFalse(TokenType::Colon) ? $this->parseFilterArgs($parser) : [];
            $this->filters[] = $this->parseFilterExpressions($filterName, $filterArgs);
        }

        $parser->consume(TokenType::EndOfString);
    }

    protected function parseFilterArgs(Parser $parser): array
    {
        $filterArgs = [$parser->argument()];
        while ($parser->consumeOrFalse(TokenType::Comma)) {
            $filterArgs[] = $parser->argument();
        }

        return $filterArgs;
    }

    /**
     * @return array{0:string, 1:array, 2:array<string,mixed>}
     */
    protected function parseFilterExpressions(string $filterName, array $filterArgs): array
    {
        $parsedArgs = [];
        $parsedNamedArgs = [];

        foreach ($filterArgs as $arg) {
            if (preg_match(self::JustTagAttributes, $arg, $matches) === 1) {
                $parsedNamedArgs[$matches[1]] = $this->parseContext->parseExpression($matches[2]);
            } else {
                $parsedArgs[] = $this->parseContext->parseExpression($arg);
            }
        }

        return [$filterName, $parsedArgs, $parsedNamedArgs];
    }

    protected function evaluateFilterExpressions(Context $context, array $filterArgs): array
    {
        return array_map(
            fn (mixed $value) => $context->evaluate($value),
            $filterArgs
        );
    }
}
