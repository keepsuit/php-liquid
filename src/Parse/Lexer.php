<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\TagBlock;
use RuntimeException;

class Lexer
{
    private const WHITESPACE = " \t\n\r\v\f";

    protected string $source;

    protected int $cursor;

    protected int $end;

    protected ?string $current;

    protected int $lineNumber;

    protected int $currentVarBlockLine;

    /**
     * @var LexerState[]
     */
    protected array $states;

    protected LexerState $state;

    /**
     * @var Token[]
     */
    protected array $tokens;

    /**
     * @var string[]
     */
    protected array $rawBodyTags;

    public function __construct(
        protected ParseContext $parseContext,
    ) {}

    /**
     * @throws SyntaxException
     */
    public function tokenize(string $source): TokenStream
    {
        $this->source = str_replace(["\r\n", "\r"], "\n", $source);
        $this->cursor = 0;
        $this->end = strlen($this->source);
        $this->current = $this->charAt(0);
        $this->lineNumber = 1;
        $this->states = [];
        $this->state = LexerState::Data;
        $this->tokens = [];

        $this->rawBodyTags = array_keys(array_filter($this->parseContext->environment->tagRegistry->all(), function ($tag) {
            if (! is_subclass_of($tag, TagBlock::class)) {
                return false;
            }

            return $tag::hasRawBody();
        }));

        $this->parseContext->lineNumber = 1;

        while ($this->current !== null) {
            switch ($this->state) {
                case LexerState::Data:
                    $this->lexData();
                    break;
                case LexerState::Variable:
                    $this->lexVariable();
                    break;
                case LexerState::Block:
                    $this->lexBlock();
                    break;
            }
        }

        return new TokenStream($this->tokens, $this->source);
    }

    protected function lexData(): void
    {
        $offset = $this->cursor;

        while (true) {
            $offset += strcspn($this->source, '{', $offset);

            if ($offset >= $this->end) {
                $this->pushToken(TokenType::TextData, substr($this->source, $this->cursor));
                $this->skip($this->end - $this->cursor);

                return;
            }

            $next = $this->charAt($offset + 1);
            if ($next === '{' || $next === '%') {
                break;
            }

            $offset++;
        }

        $text = substr($this->source, $this->cursor, $offset - $this->cursor);
        $trim = $this->charAt($offset + 2) === LexerOptions::WhitespaceTrim->value;

        $this->pushToken(TokenType::TextData, $trim ? rtrim($text) : $text);
        $this->skip($offset - $this->cursor + 2 + ($trim ? 1 : 0));

        if ($next === '%') {
            $commentStartLength = $this->commentStartLength();
            if ($commentStartLength !== null) {
                $this->skip($commentStartLength);
                $this->lexComment();

                return;
            }

            $this->pushToken(TokenType::BlockStart);
            $this->pushState(LexerState::Block);
            $this->currentVarBlockLine = $this->lineNumber;

            return;
        }

        $this->pushToken(TokenType::VariableStart);
        $this->pushState(LexerState::Variable);
        $this->currentVarBlockLine = $this->lineNumber;
    }

    /**
     * @throws SyntaxException
     */
    protected function lexVariable(): void
    {
        $terminator = $this->terminatorLength(LexerOptions::TagVariableEnd->value);
        if ($terminator !== null) {
            $this->pushToken(TokenType::VariableEnd);
            $this->skip($terminator['length']);
            $this->popState();

            if ($terminator['trim']) {
                $this->trimWhitespaces();
            }

            return;
        }

        $this->lexExpression();
    }

    /**
     * @throws SyntaxException
     */
    protected function lexBlock(): void
    {
        $tag = null;

        while (($terminator = $this->terminatorLength(LexerOptions::TagBlockEnd->value)) === null) {
            $this->lexExpression();

            $lastToken = array_last($this->tokens);
            if ($lastToken === null) {
                throw SyntaxException::unexpectedEndOfTemplate();
            }

            if ($tag === null && $lastToken->type === TokenType::Identifier) {
                $tag = $lastToken;
            }
        }

        $this->skip($terminator['length']);

        if ($terminator['trim']) {
            $this->trimWhitespaces();
        }

        $lastToken = array_last($this->tokens);
        if ($lastToken === null) {
            throw SyntaxException::unexpectedEndOfTemplate();
        }

        if ($lastToken->type === TokenType::BlockStart) {
            array_pop($this->tokens);
        } else {
            $this->pushToken(TokenType::BlockEnd);
        }

        $this->popState();

        if ($tag !== null && in_array($tag->data, $this->rawBodyTags, true)) {
            $this->lexRawBodyTag($tag->data);
        }
    }

    /**
     * @throws SyntaxException
     */
    protected function lexExpression(): void
    {
        $this->skipWhitespace();

        $this->ensureStreamNotEnded();

        if ($this->current === LexerOptions::InlineComment->value) {
            $this->lexInlineComment();

            return;
        }

        $specialCharacters = LexerOptions::specialCharacters();

        match (true) {
            $this->current === '"' || $this->current === '\'' => $this->lexString(),
            $this->isDigit($this->current) => $this->lexNumber(),
            $this->current === '-' && $this->isDigit($this->seek(1)) => $this->lexNumber(),
            $this->isAsciiLetterOrUnderscore($this->current) => $this->lexIdentifier(),
            $this->current === '=' && $this->comesNext('==') => $this->pushToken(TokenType::Comparison, $this->consume(2)),
            $this->current === '=' => $this->pushToken(TokenType::Equals, $this->consume()),
            $this->current === '!' && $this->comesNext('!=') => $this->pushToken(TokenType::Comparison, $this->consume(2)),
            $this->current === '!' => throw SyntaxException::unexpectedCharacter('!'),
            $this->current === '<' && $this->comesNext('<>') => $this->pushToken(TokenType::Comparison, $this->consume(2)),
            $this->current === '<' && $this->comesNext('<=') => $this->pushToken(TokenType::Comparison, $this->consume(2)),
            $this->current === '<' => $this->pushToken(TokenType::Comparison, $this->consume()),
            $this->current === '>' && $this->comesNext('>=') => $this->pushToken(TokenType::Comparison, $this->consume(2)),
            $this->current === '>' => $this->pushToken(TokenType::Comparison, $this->consume()),
            $this->current === '.' && $this->comesNext('..') => $this->pushToken(TokenType::DotDot, $this->consume(2)),
            $this->current !== null && array_key_exists($this->current, $specialCharacters) => $this->pushToken($specialCharacters[$this->current], $this->consume()),
            default => throw SyntaxException::unexpectedCharacter($this->current ?? ''),
        };

        $this->ensureStreamNotEnded();
    }

    /**
     * @throws SyntaxException
     */
    protected function ensureStreamNotEnded(): void
    {
        if ($this->current === null) {
            $exception = match ($this->state) {
                LexerState::Variable => SyntaxException::missingVariableTerminator(),
                LexerState::Block => SyntaxException::missingTagTerminator(),
                default => SyntaxException::unexpectedEndOfTemplate(),
            };

            if ($this->state !== LexerState::Data) {
                $exception->lineNumber = $this->currentVarBlockLine;
            }

            throw $exception;
        }
    }

    protected function lexRawBodyTag(string $tag): void
    {
        $endTag = $this->findEndTag($tag);
        if ($endTag === null) {
            throw SyntaxException::tagBlockNeverClosed($tag);
        }

        $rawBody = substr($this->source, $this->cursor, $endTag['start'] - $this->cursor);
        $this->skip($endTag['start'] - $this->cursor);

        if ($endTag['innerTrim']) {
            $rawBody = rtrim($rawBody);
        }

        $this->pushToken(TokenType::RawData, $rawBody);

        if ($endTag['outerTrim']) {
            $this->trimWhitespaces();
        }
    }

    protected function lexComment(): void
    {
        $endTag = $this->findEndTag('comment');
        if ($endTag === null) {
            throw SyntaxException::tagBlockNeverClosed('comment');
        }

        $this->skip($endTag['end'] - $this->cursor);

        if ($endTag['outerTrim']) {
            $this->trimWhitespaces();
        }
    }

    protected function lexInlineComment(): void
    {
        $offset = $this->cursor;

        while (true) {
            $offset += strcspn($this->source, "\n%", $offset);

            if ($offset >= $this->end) {
                throw SyntaxException::tagBlockNeverClosed('#');
            }

            if ($this->charAt($offset) === "\n") {
                $terminator = $offset;
                break;
            }

            if ($this->comesNext(LexerOptions::TagBlockEnd->value, $offset)) {
                $terminator = $this->charAt($offset - 1) === LexerOptions::WhitespaceTrim->value
                    ? $offset - 1
                    : $offset;

                break;
            }

            $offset++;
        }

        $start = $terminator;
        while ($start > $this->cursor && $this->isWhitespace($this->charAt($start - 1))) {
            $start--;
        }

        $this->skip($start - $this->cursor);
    }

    protected function pushToken(TokenType $type, string $value = ''): void
    {
        if ($type === TokenType::TextData && $value === '') {
            return;
        }

        $this->tokens[] = new Token($type, $value, $this->lineNumber);
    }

    protected function comesNext(string $needle, ?int $at = null): bool
    {
        $at ??= $this->cursor;

        return $at >= 0
            && $at + strlen($needle) <= $this->end
            && substr_compare($this->source, $needle, $at, strlen($needle)) === 0;
    }

    protected function seek(int $offset = 0): ?string
    {
        return $this->charAt($this->cursor + $offset);
    }

    protected function consume(int $length = 1): string
    {
        if ($length === 0) {
            return '';
        }

        $text = substr($this->source, $this->cursor, $length);
        $this->skip($length);

        return $text;
    }

    protected function skip(int $length): void
    {
        if ($length === 0) {
            return;
        }

        $this->lineNumber += substr_count($this->source, "\n", $this->cursor, $length);
        $this->cursor += $length;
        $this->current = $this->charAt($this->cursor);

        $this->parseContext->lineNumber = $this->lineNumber;
    }

    protected function skipWhitespace(): void
    {
        $this->skip(strspn($this->source, self::WHITESPACE, $this->cursor));
    }

    protected function pushState(LexerState $state): void
    {
        $this->states[] = $this->state;
        $this->state = $state;
    }

    protected function popState(): void
    {
        $state = array_pop($this->states);

        if ($state === null) {
            throw new RuntimeException('Cannot pop state without a previous state');
        }

        $this->state = $state;
    }

    protected function trimWhitespaces(): void
    {
        $this->skipWhitespace();
    }

    /**
     * @return array{start:int, end:int, innerTrim:bool, outerTrim:bool}|null
     */
    protected function findEndTag(string $tag): ?array
    {
        $offset = $this->cursor;

        while (true) {
            $offset += strcspn($this->source, '{', $offset);

            if ($offset >= $this->end) {
                return null;
            }

            if (! $this->comesNext(LexerOptions::TagBlockStart->value, $offset)) {
                $offset++;

                continue;
            }

            $probe = $offset + strlen(LexerOptions::TagBlockStart->value);
            $innerTrim = false;

            if ($this->charAt($probe) === LexerOptions::WhitespaceTrim->value) {
                $innerTrim = true;
                $probe++;
            }

            $probe += strspn($this->source, self::WHITESPACE, $probe);

            if (! $this->comesNext('end'.$tag, $probe)) {
                $offset++;

                continue;
            }

            $probe += strlen('end'.$tag);
            $probe += strspn($this->source, self::WHITESPACE, $probe);
            $outerTrim = false;

            if ($this->charAt($probe) === LexerOptions::WhitespaceTrim->value) {
                $outerTrim = true;
                $probe++;
            }

            if (! $this->comesNext(LexerOptions::TagBlockEnd->value, $probe)) {
                $offset++;

                continue;
            }

            return [
                'start' => $offset,
                'end' => $probe + strlen(LexerOptions::TagBlockEnd->value),
                'innerTrim' => $innerTrim,
                'outerTrim' => $outerTrim,
            ];
        }
    }

    protected function commentStartLength(): ?int
    {
        $offset = $this->cursor + strspn($this->source, self::WHITESPACE, $this->cursor);

        if (! $this->comesNext('comment', $offset)) {
            return null;
        }

        $offset += strlen('comment');
        $offset += strspn($this->source, self::WHITESPACE, $offset);

        if ($this->comesNext(LexerOptions::WhitespaceTrim->value.LexerOptions::TagBlockEnd->value, $offset)) {
            return $offset + strlen(LexerOptions::WhitespaceTrim->value.LexerOptions::TagBlockEnd->value) - $this->cursor;
        }

        if ($this->comesNext(LexerOptions::TagBlockEnd->value, $offset)) {
            return $offset + strlen(LexerOptions::TagBlockEnd->value) - $this->cursor;
        }

        return null;
    }

    /**
     * @return array{length:int, trim:bool}|null
     */
    protected function terminatorLength(string $terminator): ?array
    {
        $offset = $this->cursor + strspn($this->source, self::WHITESPACE, $this->cursor);

        if ($this->comesNext(LexerOptions::WhitespaceTrim->value.$terminator, $offset)) {
            return [
                'length' => $offset + strlen(LexerOptions::WhitespaceTrim->value.$terminator) - $this->cursor,
                'trim' => true,
            ];
        }

        if ($this->comesNext($terminator, $offset)) {
            return [
                'length' => $offset + strlen($terminator) - $this->cursor,
                'trim' => false,
            ];
        }

        return null;
    }

    protected function lexIdentifier(): void
    {
        $start = $this->cursor;
        $offset = $start + 1;

        while (true) {
            $current = $this->charAt($offset);

            if ($this->isAsciiWord($current)) {
                $offset++;

                continue;
            }

            if ($current === '-' && $this->isAsciiWord($this->charAt($offset + 1))) {
                $offset += 2;

                continue;
            }

            break;
        }

        if ($this->charAt($offset) === '?') {
            $offset++;
        }

        $value = substr($this->source, $start, $offset - $start);
        $type = $value === 'contains' && $this->isWhitespace($this->charAt($offset))
            ? TokenType::Comparison
            : TokenType::Identifier;

        $this->pushToken($type, $value);
        $this->skip($offset - $start);
    }

    protected function lexString(): void
    {
        $quote = $this->current;
        assert($quote !== null);

        $offset = $this->cursor + 1;
        $offset += strcspn($this->source, $quote, $offset);

        if ($this->charAt($offset) !== $quote) {
            throw SyntaxException::unexpectedCharacter($quote);
        }

        $value = substr($this->source, $this->cursor, $offset + 1 - $this->cursor);

        $this->pushToken(TokenType::String, $value);
        $this->skip(strlen($value));
    }

    protected function lexNumber(): void
    {
        $start = $this->cursor;
        $offset = $start;

        if ($this->charAt($offset) === '-') {
            $offset++;
        }

        $offset += strspn($this->source, '0123456789', $offset);

        if ($this->charAt($offset) === '.' && $this->isDigit($this->charAt($offset + 1))) {
            $offset++;
            $offset += strspn($this->source, '0123456789', $offset);
        }

        $value = substr($this->source, $start, $offset - $start);

        $this->pushToken(TokenType::Number, $value);
        $this->skip($offset - $start);
    }

    protected function charAt(int $offset): ?string
    {
        if ($offset < 0 || $offset >= $this->end) {
            return null;
        }

        return $this->source[$offset];
    }

    protected function isAsciiLetterOrUnderscore(?string $character): bool
    {
        return $character !== null && (
            ($character >= 'a' && $character <= 'z')
            || ($character >= 'A' && $character <= 'Z')
            || $character === '_'
        );
    }

    protected function isAsciiWord(?string $character): bool
    {
        return $this->isAsciiLetterOrUnderscore($character) || $this->isDigit($character);
    }

    protected function isDigit(?string $character): bool
    {
        return $character !== null && $character >= '0' && $character <= '9';
    }

    protected function isWhitespace(?string $character): bool
    {
        return $character !== null && str_contains(self::WHITESPACE, $character);
    }
}
