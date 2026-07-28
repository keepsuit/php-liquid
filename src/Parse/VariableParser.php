<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Variable;

class VariableParser
{
    public function __construct(
        protected TokenStream $tokenStream
    ) {}

    /**
     * @throws SyntaxException
     */
    public function parseVariable(): Variable
    {
        $lineNumber = $this->tokenStream->currentLineNumber();

        if ($lineNumber === null) {
            throw SyntaxException::unexpectedEndOfTemplate();
        }

        $expression = $this->tokenStream->expression();

        $filters = [];
        while ($this->tokenStream->consumeIf(TokenType::Pipe)) {
            $filterName = $this->tokenStream->consumeData(TokenType::Identifier);
            $filterArgs = $this->tokenStream->consumeIf(TokenType::Colon) ? $this->parseFilterArgs() : [];
            $filters[] = $this->parseFilterExpressions($filterName, $filterArgs);
        }

        return (new Variable(
            name: $expression,
            filters: $filters,
        ))->setLineNumber($lineNumber);
    }

    /**
     * @throws SyntaxException
     */
    protected function parseFilterArgs(): array
    {
        $filterArgs = [$this->tokenStream->argument()];

        while ($this->tokenStream->consumeIf(TokenType::Comma)) {
            if ($this->tokenStream->isEnd() || $this->tokenStream->look(TokenType::VariableEnd)) {
                throw SyntaxException::unexpectedEndOfTemplate();
            }

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
