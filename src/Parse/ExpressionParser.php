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
        $token = $this->tokenStream->currentRaw();

        if ($token === null) {
            return null;
        }

        return match ($token[0]) {
            TokenType::OpenRound => $this->parseRange(),
            TokenType::String => $this->parseString(),
            TokenType::Number => $this->parseNumber(),
            TokenType::Identifier => array_key_exists($token[1], self::LITERALS) ? $this->parseLiteral() : $this->parseVariable(),
            TokenType::VariableEnd => null,
            default => throw SyntaxException::invalidExpression($token[1]),
        };
    }

    protected function parseVariable(): VariableLookup
    {
        $name = $this->tokenStream->consumeData(TokenType::Identifier);
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
            if ($this->tokenStream->consumeIf(TokenType::Dot)) {
                $lookups[] = $this->tokenStream->consumeData(TokenType::Identifier);

                continue;
            }
            if ($this->tokenStream->consumeIf(TokenType::OpenSquare)) {
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
        $data = $this->tokenStream->consumeData(TokenType::String);

        if (
            (str_starts_with($data, '"') && str_ends_with($data, '"')) ||
            (str_starts_with($data, "'") && str_ends_with($data, "'"))
        ) {
            return substr($data, 1, -1);
        }

        return $data;
    }

    protected function parseNumber(): int|float
    {
        $data = $this->tokenStream->consumeData(TokenType::Number);

        return str_contains($data, '.') ? (float) $data : (int) $data;
    }

    protected function parseLiteral(): mixed
    {
        return self::LITERALS[$this->tokenStream->consumeData(TokenType::Identifier)];
    }
}
