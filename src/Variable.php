<?php

namespace Keepsuit\Liquid;

class Variable implements HasParseTreeVisitorChildren
{
    use ParserSwitching;

    const FilterMarkupRegex = '/'.Regex::FilterSeparator.'\s*(.*)/';

    const FilterParser = '/(?:\s+|'.Regex::QuotedFragment.'|'.Regex::ArgumentSeparator.')+/';

    const FilterArgsRegex = '/(?:'.Regex::FilterArgumentSeparator.'|'.Regex::ArgumentSeparator.')\s*((?:\w+\s*\:\s*)?'.Regex::QuotedFragment.')/';

    const JustTagAttributes = '/\A'.Regex::TagAttributes.'\z/';

    const MarkupWithQuotedFragment = '/('.Regex::QuotedFragment.')(.*)/s';

    protected mixed $name = null;

    protected ?int $lineNumber = null;

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
            return;
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
            return;
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

    protected function parseFilterExpressions(string $filterName, array $filterArgs): array
    {
        $parsedFilterArgs = [];
        $keywordArgs = null;

        foreach ($filterArgs as $arg) {
            if (preg_match(self::JustTagAttributes, $arg, $matches) === 1) {
                $keywordArgs = $keywordArgs ?? [];
                $keywordArgs[$matches[1]] = $this->parseContext->parseExpression($matches[2]);
            } else {
                $parsedFilterArgs[] = $this->parseContext->parseExpression($arg);
            }
        }

        $result = [$filterName, $parsedFilterArgs];
        if ($keywordArgs !== null) {
            $result[] = $keywordArgs;
        }

        return $result;
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->name, ...Arr::flatten($this->filters)];
    }
}
