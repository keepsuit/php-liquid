<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\TagBlock;
use RuntimeException;

class Lexer
{
    private const WHITESPACE = " \t\n\r\v\f";

    private const WORD = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';

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

            $lastToken = $this->tokens[count($this->tokens) - 1] ?? null;
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

        $lastToken = $this->tokens[count($this->tokens) - 1] ?? null;
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

        match ($this->current) {
            '"', '\'' => $this->lexString(),
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' => $this->lexNumber(),
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '_' => $this->lexIdentifier(),
            '-' => $this->isDigit($this->seek(1))
                ? $this->lexNumber()
                : $this->pushToken(TokenType::Dash, $this->consume()),
            '=' => $this->comesNext('==')
                ? $this->pushToken(TokenType::Comparison, $this->consume(2))
                : $this->pushToken(TokenType::Equals, $this->consume()),
            '!' => $this->comesNext('!=')
                ? $this->pushToken(TokenType::Comparison, $this->consume(2))
                : throw SyntaxException::unexpectedCharacter('!'),
            '<' => $this->comesNext('<>') || $this->comesNext('<=')
                ? $this->pushToken(TokenType::Comparison, $this->consume(2))
                : $this->pushToken(TokenType::Comparison, $this->consume()),
            '>' => $this->comesNext('>=')
                ? $this->pushToken(TokenType::Comparison, $this->consume(2))
                : $this->pushToken(TokenType::Comparison, $this->consume()),
            '.' => $this->comesNext('..')
                ? $this->pushToken(TokenType::DotDot, $this->consume(2))
                : $this->pushToken(TokenType::Dot, $this->consume()),
            '|' => $this->pushToken(TokenType::Pipe, $this->consume()),
            ':' => $this->pushToken(TokenType::Colon, $this->consume()),
            ',' => $this->pushToken(TokenType::Comma, $this->consume()),
            '[' => $this->pushToken(TokenType::OpenSquare, $this->consume()),
            ']' => $this->pushToken(TokenType::CloseSquare, $this->consume()),
            '(' => $this->pushToken(TokenType::OpenRound, $this->consume()),
            ')' => $this->pushToken(TokenType::CloseRound, $this->consume()),
            '?' => $this->pushToken(TokenType::QuestionMark, $this->consume()),
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

        $newLines = $length === 1
            ? ($this->source[$this->cursor] === "\n" ? 1 : 0)
            : substr_count($this->source, "\n", $this->cursor, $length);

        $this->cursor += $length;
        $this->current = $this->cursor < $this->end ? $this->source[$this->cursor] : null;

        if ($newLines !== 0) {
            $this->lineNumber += $newLines;
            $this->parseContext->lineNumber = $this->lineNumber;
        }
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
        $source = $this->source;

        $trim = ($source[$offset] ?? null) === LexerOptions::WhitespaceTrim->value;
        $at = $trim ? $offset + 1 : $offset;

        if (($source[$at] ?? null) !== $terminator[0] || ($source[$at + 1] ?? null) !== $terminator[1]) {
            return null;
        }

        return [
            'length' => $at + 2 - $this->cursor,
            'trim' => $trim,
        ];
    }

    protected function lexIdentifier(): void
    {
        $start = $this->cursor;
        $offset = $start + 1;

        $offset += strspn($this->source, self::WORD, $offset);

        while (
            ($this->source[$offset] ?? null) === '-'
            && strspn($this->source, self::WORD, $offset + 1, 1) === 1
        ) {
            $offset += 1 + strspn($this->source, self::WORD, $offset + 1);
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
