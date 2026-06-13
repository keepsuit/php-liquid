<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\SyntaxException;

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
     *
     * @throws SyntaxException
     */
    public function parseArgument(): mixed
    {
        if ($this->tokenStream->isEnd()) {
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
