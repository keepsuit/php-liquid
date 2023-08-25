<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Parser\Parser;
use Keepsuit\Liquid\Parser\ParserSwitching;
use Keepsuit\Liquid\Parser\Regex;
use Keepsuit\Liquid\Parser\TokenType;

class Variable implements HasParseTreeVisitorChildren, CanBeRendered, CanBeEvaluated
{
    use ParserSwitching;

    const FilterMarkupRegex = '/'.Regex::FilterSeparator.'\s*(.*)/';

    const FilterParser = '/(?:\s+|'.Regex::QuotedFragment.'|'.Regex::ArgumentSeparator.')+/';

    const FilterArgsRegex = '/(?:'.Regex::FilterArgumentSeparator.'|'.Regex::ArgumentSeparator.')\s*((?:\w+\s*\:\s*)?'.Regex::QuotedFragment.')/';

    const JustTagAttributes = '/\A'.Regex::TagAttributes.'\z/';

    const MarkupWithQuotedFragment = '/('.Regex::QuotedFragment.')(.*)/s';

    protected mixed $name = null;

    public readonly ?int $lineNumber;

    /**
     * @var array<array{0:string,1:array}>
     */
    protected array $filters = [];

    public function __construct(
        public readonly string $markup,
        public readonly ParseContext $parseContext
    ) {
        $this->lineNumber = $this->parseContext->lineNumber;

        $this->strictParseWithErrorModeFallback($markup, $this->parseContext);
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

    protected function strictParse(string $markup): mixed
    {
        $this->filters = [];

        $parser = new Parser($markup);

        if ($parser->look(TokenType::EndOfString)) {
            return null;
        }

        $this->name = $this->parseContext->parseExpression($parser->expression());

        while ($parser->consumeOrFalse(TokenType::Pipe)) {
            $filterName = $parser->consume(TokenType::Identifier);
            $filterArgs = $parser->consumeOrFalse(TokenType::Colon) ? $this->parseFilterArgs($parser) : [];
            $this->filters[] = $this->parseFilterExpressions($filterName, $filterArgs);
        }

        $parser->consume(TokenType::EndOfString);

        return null;
    }

    protected function laxParse(string $markup): mixed
    {
        $this->filters = [];

        if (preg_match(self::MarkupWithQuotedFragment, $markup, $matches) !== 1) {
            return null;
        }

        $nameMarkup = $matches[1];
        $filterMarkup = $matches[2];

        $this->name = $this->parseContext->parseExpression($nameMarkup);

        if (preg_match(self::FilterMarkupRegex, $filterMarkup, $matches) === 1) {
            preg_match(self::FilterParser, $matches[1], $filters);
            foreach ($filters as $f) {
                if (preg_match('/\w+/', $f, $filterMatches) !== 1) {
                    continue;
                }
                $filterName = $filterMatches[0];
                preg_match_all(self::FilterArgsRegex, $f, $filterArgsMatches);
                $this->filters[] = $this->parseFilterExpressions($filterName, $filterArgsMatches[1]);
            }
        }

        return null;
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

    protected function evaluateFilterExpressions(Context $context, array $filterArgs): array
    {
        return array_map(
            fn (mixed $value) => $context->evaluate($value),
            $filterArgs
        );
    }

    protected function markupContext(string $markup): string
    {
        return sprintf('in "{{%s}}"', $markup);
    }
}
