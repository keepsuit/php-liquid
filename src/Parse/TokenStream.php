<?php

namespace Keepsuit\Liquid\Parse;

use Closure;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Variable;

/**
 * Tokens are stored as raw [TokenType, data, lineNumber] triples rather than
 * Token objects: the lexer emits thousands per template and the allocation was
 * measurable. Token instances are materialised on demand by current(), next(),
 * consume() and toArray(); hot paths use the allocation-free accessors
 * (consumeData(), currentType(), nextRaw(), consumeIf()).
 *
 * @phpstan-import-type Argument from ArgumentParser
 * @phpstan-import-type Expression from ExpressionParser
 *
 * @phpstan-type RawToken array{0: TokenType, 1: string, 2: int}
 */
class TokenStream
{
    protected int $cursor = 0;

    protected ExpressionParser $expressionParser;

    protected ArgumentParser $argumentParser;

    protected VariableParser $variableParser;

    public function __construct(
        /** @var list<RawToken> */
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
        return ($this->tokens[$this->cursor + $offset][0] ?? null) === $type;
    }

    /**
     * @throws SyntaxException
     */
    public function next(): Token
    {
        return Token::fromRaw($this->nextRaw());
    }

    /**
     * Allocation-free next(): returns the raw triple.
     *
     * @return RawToken
     *
     * @throws SyntaxException
     */
    public function nextRaw(): array
    {
        $token = $this->tokens[$this->cursor++] ?? null;

        if ($token === null) {
            throw SyntaxException::unexpectedEndOfTemplate();
        }

        return $token;
    }

    /**
     * @phpstan-impure
     *
     * @throws SyntaxException
     */
    public function consume(?TokenType $type = null): Token
    {
        return Token::fromRaw($this->consumeRaw($type));
    }

    /**
     * Allocation-free consume(): returns the raw triple.
     *
     * @return RawToken
     *
     * @throws SyntaxException
     */
    public function consumeRaw(?TokenType $type = null): array
    {
        $token = $this->tokens[$this->cursor++] ?? null;

        if ($token === null) {
            throw SyntaxException::unexpectedEndOfTemplate();
        }

        if ($type !== null && $token[0] !== $type) {
            throw SyntaxException::unexpectedTokenType($type, $token[0]);
        }

        return $token;
    }

    /**
     * Consume a token and return only its data. The common case in the parsers,
     * and the reason none of them need a Token instance.
     *
     * @throws SyntaxException
     */
    public function consumeData(?TokenType $type = null): string
    {
        return $this->consumeRaw($type)[1];
    }

    /**
     * Allocation-free consumeOrFalse() for callers that only need to know
     * whether the token was there.
     */
    public function consumeIf(TokenType $type): bool
    {
        if (($this->tokens[$this->cursor][0] ?? null) !== $type) {
            return false;
        }

        $this->cursor++;

        return true;
    }

    public function consumeOrFalse(TokenType $type): Token|false
    {
        return $this->look($type) ? $this->consume($type) : false;
    }

    /**
     * @throws SyntaxException
     */
    public function id(string $identifier): Token
    {
        $token = $this->consumeRaw(TokenType::Identifier);

        if ($token[1] !== $identifier) {
            throw SyntaxException::unexpectedIdentifier($identifier, $token[1]);
        }

        return Token::fromRaw($token);
    }

    /**
     * Allocation-free idOrFalse() for callers that only need a yes/no.
     */
    public function idIf(string $identifier): bool
    {
        $token = $this->tokens[$this->cursor] ?? null;

        if ($token === null || $token[0] !== TokenType::Identifier || $token[1] !== $identifier) {
            return false;
        }

        $this->cursor++;

        return true;
    }

    public function idOrFalse(string $identifier): Token|false
    {
        if (($this->tokens[$this->cursor][0] ?? null) !== TokenType::Identifier) {
            return false;
        }

        $token = $this->tokens[$this->cursor];

        if ($token[1] !== $identifier) {
            return false;
        }

        $this->cursor++;

        return Token::fromRaw($token);
    }

    public function current(): ?Token
    {
        $token = $this->tokens[$this->cursor] ?? null;

        return $token === null ? null : Token::fromRaw($token);
    }

    /**
     * @return RawToken|null
     */
    public function currentRaw(): ?array
    {
        return $this->tokens[$this->cursor] ?? null;
    }

    public function currentType(): ?TokenType
    {
        return $this->tokens[$this->cursor][0] ?? null;
    }

    public function currentLineNumber(): ?int
    {
        return $this->tokens[$this->cursor][2] ?? null;
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
     * @throws SyntaxException
     */
    public function simpleVariableName(): string
    {
        return $this->consumeData(TokenType::Identifier);
    }

    /**
     * @return Argument
     *
     * @throws SyntaxException
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

    /**
     * @return list<Token>
     */
    public function toArray(): array
    {
        return array_map(Token::fromRaw(...), $this->tokens);
    }

    /**
     * @return list<RawToken>
     */
    public function toRawArray(): array
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
            $start = $this->cursor;
            $cursor = $start;
            $end = count($this->tokens);

            while ($cursor < $end && $this->tokens[$cursor][0] !== $check) {
                $cursor++;
            }

            $this->cursor = $cursor;

            return new TokenStream(array_slice($this->tokens, $start, $cursor - $start));
        }

        // Closure form receives a materialised Token; it is only used by tags
        // such as {% liquid %}, so the allocation is not on a hot path.
        $tokens = [];

        while (! $this->isEnd()) {
            $token = $this->tokens[$this->cursor++];

            if ($check(Token::fromRaw($token))) {
                $this->cursor--;
                break;
            }

            $tokens[] = $token;
        }

        return new TokenStream($tokens);
    }
}
