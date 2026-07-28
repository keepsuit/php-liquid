<?php

namespace Keepsuit\Liquid\Parse;

/**
 * A materialised token.
 *
 * The lexer no longer produces these: it emits raw `array{TokenType, string, int}`
 * triples, because allocating one object per token measurably dominated tokenize
 * time. Token is built on demand by TokenStream::current(), consume(), next() and
 * toArray(), for callers that want a value object.
 *
 * Hot internal paths use the allocation-free accessors instead
 * (TokenStream::consumeData(), currentType(), nextRaw(), ...).
 *
 * @phpstan-type RawToken array{0: TokenType, 1: string, 2: int}
 */
final class Token
{
    public function __construct(
        public readonly TokenType $type,
        public readonly string $data,
        public readonly int $lineNumber,
    ) {}

    /**
     * @param  RawToken  $token
     */
    public static function fromRaw(array $token): self
    {
        return new self($token[0], $token[1], $token[2]);
    }
}
