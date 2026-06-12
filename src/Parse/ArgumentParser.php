<?php

namespace Keepsuit\Liquid\Parse;

/**
 * @phpstan-import-type Expression from ExpressionParser
 *
 * @phpstan-type Argument array<string, Expression>|Expression
 */
class ArgumentParser
{
    public function __construct(
        protected TokenStream $tokenStream
    ) {}

    /**
     * @return Argument
     */
    public function parseArgument(): mixed
    {
        if ($this->tokenStream->isEnd() || $this->tokenStream->look(TokenType::Comma) || $this->tokenStream->look(TokenType::Pipe)) {
            throw SyntaxException::unexpectedEndOfTemplate();
        }

        if (
            $this->tokenStream->look(TokenType::Identifier)
            && $this->tokenStream->look(TokenType::Colon, 1)
        ) {
            $identifier = $this->tokenStream->consume(TokenType::Identifier);
            $this->tokenStream->consume(TokenType::Colon);

            return [$identifier->data => $this->tokenStream->expression()];
        }

        return $this->tokenStream->expression();
    }
}
