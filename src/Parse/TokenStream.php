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

    protected ExpressionParser $expressionParser;

    protected ArgumentParser $argumentParser;

    protected VariableParser $variableParser;

    public function __construct(
        /** @var Token[] */
        protected array $tokens,
        protected ?string $source = null,
    ) {
        $this->expressionParser = new ExpressionParser($this);
        $this->argumentParser = new ArgumentParser($this);
        $this->variableParser = new VariableParser($this);
    }

    /**
     * @phpstan-impure
     *
     * @throws SyntaxException
     */
    public function jump(int $offset): void
    {
        $newCursor = $this->cursor + $offset;

        if ($newCursor < 0 || $newCursor > count($this->tokens)) {
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
        return $this->look($type) ? $this->consume($type) : false;
    }

    /**
     * @throws SyntaxException
     *
     * @phpstan-impure
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
        $token = $this->consumeOrFalse(TokenType::Identifier);

        if ($token === false) {
            return false;
        }

        if ($token->data === $identifier) {
            return $token;
        }

        $this->jump(-1);

        return false;
    }

    public function current(): ?Token
    {
        return $this->tokens[$this->cursor];
    }

    public function isEnd(): bool
    {
        return $this->cursor >= count($this->tokens);
    }

    /**
     * @return Expression
     *
     * @throws SyntaxException
     */
    public function expression(): mixed
    {
        return $this->expressionParser->parseExpression();
    }

    /**
     * @return Argument
     */
    public function argument(): mixed
    {
        return $this->argumentParser->parseArgument();
    }

    public function variable(): Variable
    {
        return $this->variableParser->parseVariable();
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

    public function toArray(): array
    {
        return $this->tokens;
    }

    /**
     * @param  TokenType|Closure(Token $token):bool  $check
     *
     * @throws SyntaxException
     */
    public function sliceUntil(Closure|TokenType $check): TokenStream
    {
        if ($check instanceof TokenType) {
            $tokenType = $check;
            $check = fn (Token $token) => $token->type === $tokenType;
        }

        $tokens = [];

        while (! $this->isEnd()) {
            $token = $this->consume();

            if ($check($token)) {
                $this->jump(-1);
                break;
            }

            $tokens[] = $token;
        }

        return new TokenStream($tokens);
    }
}
