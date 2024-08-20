<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Variable;

class VariableParser
{
    public function __construct(
        protected TokenStream $tokenStream
    ) {}

    public function parseVariable(): Variable
    {
        $currentToken = $this->tokenStream->current();

        if ($currentToken === null) {
            throw SyntaxException::unexpectedEndOfTemplate();
        }

        $expression = $this->tokenStream->expression();

        $filters = [];
        while ($this->tokenStream->consumeOrFalse(TokenType::Pipe)) {
            $filterName = $this->tokenStream->consume(TokenType::Identifier)->data;
            $filterArgs = $this->tokenStream->consumeOrFalse(TokenType::Colon) ? $this->parseFilterArgs() : [];
            $filters[] = $this->parseFilterExpressions($filterName, $filterArgs);
        }

        return (new Variable(
            name: $expression,
            filters: $filters,
        ))->setLineNumber($currentToken->lineNumber);
    }

    protected function parseFilterArgs(): array
    {
        $filterArgs = [$this->tokenStream->argument()];

        while ($this->tokenStream->consumeOrFalse(TokenType::Comma)) {
            $filterArgs[] = $this->tokenStream->argument();
        }

        return $filterArgs;
    }

    /**
     * @param  array<string|array<string,string>>  $filterArgs
     * @return array{0:string, 1:array, 2:array<string,mixed>}
     */
    protected function parseFilterExpressions(string $filterName, array $filterArgs): array
    {
        $parsedArgs = [];
        $parsedNamedArgs = [];

        foreach ($filterArgs as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $key => $value) {
                    $parsedNamedArgs[$key] = $value;
                }
            } else {
                $parsedArgs[] = $arg;
            }
        }

        return [$filterName, $parsedArgs, $parsedNamedArgs];
    }
}
