<?php

namespace Keepsuit\Liquid\Parse;

use Closure;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Variable;

/**
 * @phpstan-import-type Argument from ArgumentParser
 * @phpstan-import-type Expression from ExpressionParser
 */
class TokenStream
{
    protected int $cursor = 0;

    protected int $end;

    protected ?ExpressionParser $expressionParser = null;

    protected ?ArgumentParser $argumentParser = null;

    protected ?VariableParser $variableParser = null;

    public function __construct(
        /** @var Token[] */
        protected array $tokens,
        protected ?string $source = null,
    ) {
        $this->end = count($tokens);
    }

    /**
     * @phpstan-impure
     *
     * @throws SyntaxException
     */
    public function jump(int $offset): void
    {
        $newCursor = $this->cursor + $offset;

        if ($newCursor < 0 || $newCursor > $this->end) {
            throw new SyntaxException("Invalid jump offset: $offset");
        }

        $this->cursor = $newCursor;
    }

    public function look(TokenType $type, int $offset = 0): bool
    {
        $token = $this->tokens[$this->cursor + $offset] ?? null;

        if ($token === null) {
            return false;
        }

        return $token->type === $type;
    }

    /**
     * @throws SyntaxException
     */
    public function next(): Token
    {
        return $this->consume();
    }

    /**
     * @phpstan-impure
     *
     * @throws SyntaxException
     */
    public function consume(?TokenType $type = null): Token
    {
        $token = $this->tokens[$this->cursor++] ?? null;

        if ($token === null) {
            throw SyntaxException::unexpectedEndOfTemplate();
        }

        if ($type !== null && $token->type !== $type) {
            throw SyntaxException::unexpectedTokenType($type, $token->type);
        }

        return $token;
    }

    public function consumeOrFalse(TokenType $type): Token|false
    {
        $token = $this->tokens[$this->cursor] ?? null;

        if ($token === null || $token->type !== $type) {
            return false;
        }

        $this->cursor++;

        return $token;
    }

    /**
     * @throws SyntaxException
     */
    public function id(string $identifier): Token
    {
        $token = $this->consume(TokenType::Identifier);

        if ($token->data !== $identifier) {
            throw SyntaxException::unexpectedIdentifier($identifier, $token->data);
        }

        return $token;
    }

    public function idOrFalse(string $identifier): Token|false
    {
        $token = $this->tokens[$this->cursor] ?? null;

        if ($token === null || $token->type !== TokenType::Identifier || $token->data !== $identifier) {
            return false;
        }

        $this->cursor++;

        return $token;
    }

    /**
     * @param  TokenType|Closure(Token $token):bool  $check
     *
     * @throws SyntaxException
     */
    public function sliceUntil(Closure|TokenType $check): TokenStream
    {
        $start = $this->cursor;
        $cursor = $start;
        $end = $this->end;
        $tokens = $this->tokens;

        if ($check instanceof TokenType) {
            while ($cursor < $end && $tokens[$cursor]->type !== $check) {
                $cursor++;
            }
        } else {
            while ($cursor < $end && ! $check($tokens[$cursor])) {
                $cursor++;
            }
        }

        $this->cursor = $cursor;

        return new TokenStream(array_slice($tokens, $start, $cursor - $start));
    }

    public function current(): ?Token
    {
        return $this->tokens[$this->cursor] ?? null;
    }

    public function isEnd(): bool
    {
        return $this->cursor >= $this->end;
    }

    /**
     * @throws SyntaxException
     */
    public function assertEnd(): void
    {
        if (! $this->isEnd()) {
            $token = $this->current();
            assert($token !== null);
            throw SyntaxException::unexpectedToken($token);
        }
    }

    /**
     * @return Expression
     *
     * @throws SyntaxException
     */
    public function expression(): mixed
    {
        return ($this->expressionParser ??= new ExpressionParser($this))->parseExpression();
    }

    /**
     * @throws SyntaxException
     */
    public function simpleVariableName(): string
    {
        return $this->consume(TokenType::Identifier)->data;
    }

    /**
     * @return Argument
     *
     * @throws SyntaxException
     */
    public function argument(): mixed
    {
        return ($this->argumentParser ??= new ArgumentParser($this))->parseArgument();
    }

    public function variable(): Variable
    {
        return ($this->variableParser ??= new VariableParser($this))->parseVariable();
    }

    public function toArray(): array
    {
        return $this->tokens;
    }
}
