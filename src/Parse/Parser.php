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

    public function idOrFalse(string $identifier): string|false
    {
        $token = $this->tokens[$this->pointer];

        if ($token === null || $token[0] !== TokenType::Identifier) {
            return false;
        }

        if ($token[1] !== $identifier) {
            return false;
        }

        $this->pointer += 1;

        return $token[1];
    }

    public function look(TokenType $type, int $offset = 0): bool
    {
        $token = $this->tokens[$this->pointer + $offset];

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
     * @throws SyntaxException
     */
    public function argument(): string
    {
        $output = match (true) {
            $this->look(TokenType::Identifier) && $this->look(TokenType::Colon, 1) => $this->consume().$this->consume().' ',
            default => ''
        };

        return $output.$this->expression();
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
