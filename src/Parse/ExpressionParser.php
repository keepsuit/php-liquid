<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Literal;
use Keepsuit\Liquid\Nodes\RangeLookup;
use Keepsuit\Liquid\Nodes\VariableLookup;

/**
 * @phpstan-type Expression string|int|float|bool|Literal|VariableLookup|RangeLookup|null
 */
class ExpressionParser
{
    protected const LITERALS = [
        'nil' => null,
        'null' => null,
        '' => null,
        'true' => true,
        'false' => false,
        'blank' => Literal::Blank,
        'empty' => Literal::Empty,
    ];

    public function __construct(
        protected TokenStream $tokenStream
    ) {}

    /**
     * @return Expression
     *
     * @throws SyntaxException
     */
    public function parseExpression(): mixed
    {
        $token = $this->tokenStream->current();

        if ($token === null) {
            return null;
        }

        return match ($token->type) {
            TokenType::OpenRound => $this->parseRange(),
            TokenType::String => $this->parseString(),
            TokenType::Number => $this->parseNumber(),
            TokenType::Identifier => array_key_exists($token->data, self::LITERALS) ? $this->parseLiteral() : $this->parseVariable(),
            TokenType::VariableEnd => null,
            default => throw SyntaxException::invalidExpression($token->data),
        };
    }

    protected function parseVariable(): VariableLookup
    {
        $name = $this->tokenStream->consume(TokenType::Identifier)->data;
        $lookups = $this->parseVariableLookups();

        return new VariableLookup(
            name: $name,
            lookups: $lookups,
        );
    }

    /**
     * @throws SyntaxException
     */
    protected function parseVariableLookups(): array
    {
        $lookups = [];

        while (true) {
            if ($this->tokenStream->consumeOrFalse(TokenType::Dot)) {
                $lookups[] = $this->tokenStream->consume(TokenType::Identifier)->data;

                continue;
            }
            if ($this->tokenStream->consumeOrFalse(TokenType::OpenSquare)) {
                $lookups[] = $this->tokenStream->expression();
                $this->tokenStream->consume(TokenType::CloseSquare);

                continue;
            }

            break;
        }

        return $lookups;
    }

    protected function parseRange(): RangeLookup
    {
        try {
            $this->tokenStream->consume(TokenType::OpenRound);

            $start = $this->tokenStream->expression();
            $this->tokenStream->consume(TokenType::DotDot);
            $end = $this->tokenStream->expression();

            $this->tokenStream->consume(TokenType::CloseRound);
        } catch (SyntaxException $exception) {
            throw new SyntaxException('Invalid range syntax, correct syntax is (start..end)');
        }

        return new RangeLookup($start, $end);
    }

    protected function parseString(): string
    {
        $token = $this->tokenStream->consume(TokenType::String);

        if (
            (str_starts_with($token->data, '"') && str_ends_with($token->data, '"')) ||
            (str_starts_with($token->data, "'") && str_ends_with($token->data, "'"))
        ) {
            return substr($token->data, 1, -1);
        }

        return $token->data;
    }

    protected function parseNumber(): int|float
    {
        $token = $this->tokenStream->consume(TokenType::Number);

        return str_contains($token->data, '.') ? (float) $token->data : (int) $token->data;
    }

    protected function parseLiteral(): mixed
    {
        $token = $this->tokenStream->consume(TokenType::Identifier);

        return self::LITERALS[$token->data];
    }
}
