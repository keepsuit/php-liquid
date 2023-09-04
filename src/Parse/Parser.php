<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\SyntaxException;

class Parser
{
    /**
     * @var array<array{0:TokenType, 1:string, 2:int}>
     */
    protected array $tokens;

    protected int $pointer;

    /**
     * @throws SyntaxException
     */
    public function __construct(protected string $input)
    {
        $this->tokens = (new Lexer($input))->tokenize();
        $this->pointer = 0;
    }

    public function jump(int $int): void
    {
        $this->pointer = $int;
    }

    /**
     * @throws SyntaxException
     */
    public function consume(TokenType $type = null): string
    {
        $token = $this->tokens[$this->pointer];

        if ($type != null && $token[0] !== $type) {
            throw SyntaxException::unexpectedTokenType($type, $token[0]);
        }

        $this->pointer += 1;

        return $token[1] ?? '';
    }

    public function consumeOrFalse(TokenType $type): string|false
    {
        try {
            return $this->consume($type);
        } catch (SyntaxException) {
            return false;
        }
    }

    /**
     * @throws SyntaxException
     */
    public function id(string $identifier): string|false
    {
        $token = $this->tokens[$this->pointer];

        if ($token === null || $token[0] !== TokenType::Identifier) {
            throw SyntaxException::unexpectedTokenType(TokenType::Identifier, $token[0]);
        }

        if ($token[1] !== $identifier) {
            return false;
        }

        $this->pointer += 1;

        return $token[1];
    }

    public function idOrFalse(string $identifier): string|false
    {
        try {
            return $this->id($identifier);
        } catch (SyntaxException) {
            return false;
        }
    }

    public function look(TokenType $type, int $offset = 0): bool
    {
        $token = $this->tokens[$this->pointer + $offset] ?? null;

        if ($token === null) {
            return false;
        }

        return $token[0] === $type;
    }

    /**
     * @throws SyntaxException
     */
    public function expression(): string
    {
        $token = $this->tokens[$this->pointer];

        return match ($token[0]) {
            TokenType::Identifier => $this->consume()
                .$this->variableLookups(),
            TokenType::OpenSquare => $this->consume()
                .$this->expression()
                .$this->consume(TokenType::CloseSquare)
                .$this->variableLookups(),
            TokenType::String, TokenType::Number => $this->consume(),
            TokenType::OpenRound => $this->consume()
                .$this->expression()
                .$this->consume(TokenType::DotDot)
                .$this->expression()
                .$this->consume(TokenType::CloseRound),
            default => throw SyntaxException::invalidExpression($token[1] ?? ''),
        };
    }

    /**
     * @return array<string, string>
     */
    public function attributes(TokenType $separator = null): array
    {
        $attributes = [];

        if ($this->look(TokenType::EndOfString) !== false) {
            return $attributes;
        }

        do {
            $attribute = $this->consume(TokenType::Identifier);
            $this->consume(TokenType::Colon);
            $attributes[$attribute] = $this->expression();

            $shouldContinue = match (true) {
                $separator === null => $this->look(TokenType::EndOfString) === false,
                default => $this->consumeOrFalse($separator) !== false
            };
        } while ($shouldContinue);

        return $attributes;
    }

    /**
     * @return array<string, string>|string
     *
     * @throws SyntaxException
     */
    public function argument(): string|array
    {
        if ($this->look(TokenType::Identifier) && $this->look(TokenType::Colon, 1)) {
            $identifier = $this->consume(TokenType::Identifier);
            $this->consume(TokenType::Colon);

            return [$identifier => $this->expression()];
        }

        return $this->expression();
    }

    /**
     * @throws SyntaxException
     */
    protected function variableLookups(): string
    {
        $output = match (true) {
            $this->look(TokenType::OpenSquare) => $this->consume()
                .$this->expression()
                .$this->consume(TokenType::CloseSquare),
            $this->look(TokenType::Dot) => $this->consume()
                .$this->consume(TokenType::Identifier),
            default => '',
        };

        if ($output === '') {
            return $output;
        }

        return $output.$this->variableLookups();
    }

    public function toString(): string
    {
        $current = $this->tokens[$this->pointer];

        return substr($this->input, $current[2] ?? 0);
    }
}
