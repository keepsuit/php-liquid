<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\SyntaxException;

class Lexer
{
    private const WHITESPACE = " \t\n\r\v\f";

    private const WORD = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';

    private const VARIABLE_END = '}}';

    private const BLOCK_START = '{%';

    private const BLOCK_END = '%}';

    private const TRIM = '-';

    private const TRIM_BLOCK_END = self::TRIM.self::BLOCK_END;

    private const INLINE_COMMENT = '#';

    protected string $source;

    protected int $cursor;

    protected int $end;

    protected ?string $current;

    protected int $lineNumber;

    protected int $currentVarBlockLine;

    protected LexerState $state;

    /**
     * @var Token[]
     */
    protected array $tokens;

    /**
     * @var array<string, true>
     */
    protected array $rawBodyTags;

    /**
     * Set by terminatorLength() when it matches; read by the caller that matched.
     */
    protected bool $terminatorTrim = false;

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
        $this->state = LexerState::Data;
        $this->tokens = [];

        $this->rawBodyTags = $this->parseContext->environment->tagRegistry->rawBodyTags();

        $this->parseContext->lineNumber = 1;

        // Each state advances the shared cursor and either remains in that state
        // for the next token or returns to Data after consuming its terminator.
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

        // A lone "{" is ordinary text; only "{{" and "{%" start Liquid markup.
        while (true) {
            $offset += strcspn($this->source, '{', $offset);

            if ($offset >= $this->end) {
                $this->pushToken(TokenType::TextData, substr($this->source, $this->cursor));
                $this->skip($this->end - $this->cursor);

                return;
            }

            $next = $this->source[$offset + 1] ?? null;
            if ($next === '{' || $next === '%') {
                break;
            }

            $offset++;
        }

        $text = substr($this->source, $this->cursor, $offset - $this->cursor);
        $trim = ($this->source[$offset + 2] ?? null) === self::TRIM;

        // An opening trim marker belongs to the delimiter and trims the text
        // immediately before it.
        $this->pushToken(TokenType::TextData, $trim ? rtrim($text) : $text);
        $this->skip($offset - $this->cursor + 2 + ($trim ? 1 : 0));

        if ($next === '%') {
            // Full comment blocks never expose their contents as tokens, so skip
            // them directly instead of entering the normal Block state.
            $commentStartLength = $this->commentStartLength();
            if ($commentStartLength !== null) {
                $this->skip($commentStartLength);
                $this->lexComment();

                return;
            }

            $this->pushToken(TokenType::BlockStart);
            $this->state = LexerState::Block;
            $this->currentVarBlockLine = $this->lineNumber;

            return;
        }

        $this->pushToken(TokenType::VariableStart);
        $this->state = LexerState::Variable;
        $this->currentVarBlockLine = $this->lineNumber;
    }

    /**
     * @throws SyntaxException
     */
    protected function lexVariable(): void
    {
        // VariableEnd was historically pushed before the trailing whitespace was
        // consumed, so for a multi-line "{{ x \n }}" it reports the line the
        // whitespace starts on rather than the line of "}}". Whitespace is now
        // skipped up front, so capture the line first to keep that numbering.
        $lineNumber = $this->lineNumber;

        $this->skipWhitespace();

        $terminator = $this->terminatorLength(self::VARIABLE_END);
        if ($terminator !== null) {
            $this->tokens[] = new Token(TokenType::VariableEnd, '', $lineNumber);
            $this->skip($terminator);
            $this->state = LexerState::Data;

            if ($this->terminatorTrim) {
                $this->skipWhitespace();
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

        $this->skipWhitespace();

        while (($terminator = $this->terminatorLength(self::BLOCK_END)) === null) {
            $this->lexExpression();
            $this->skipWhitespace();

            $lastToken = $this->tokens[count($this->tokens) - 1] ?? null;
            if ($lastToken === null) {
                throw SyntaxException::unexpectedEndOfTemplate();
            }

            // Inside a liquid tag a line starting with `comment` opens a comment
            // block, which must not reach the expression lexer any more than a
            // `{% comment %}` block does.
            if ($tag !== null
                && $tag->data === 'liquid'
                && $lastToken->type === TokenType::Identifier
                && $lastToken->data === 'comment'
                && $this->startsLiquidTagLine($tag, $lastToken)
            ) {
                array_pop($this->tokens);
                $this->skipLiquidComment($lastToken->lineNumber);
                $this->skipWhitespace();
            } elseif ($tag === null && $lastToken->type === TokenType::Identifier) {
                // The first identifier names the tag and determines whether its
                // body must later be treated as opaque raw data.
                $tag = $lastToken;
            }
        }

        $this->skip($terminator);

        if ($this->terminatorTrim) {
            $this->skipWhitespace();
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

        $this->state = LexerState::Data;

        if ($tag !== null && isset($this->rawBodyTags[$tag->data])) {
            $this->lexRawBodyTag($tag->data);
        }
    }

    /**
     * Callers (lexVariable/lexBlock) skip whitespace before probing for the
     * terminator, so the cursor is already on a non-whitespace character here.
     *
     * @throws SyntaxException
     */
    protected function lexExpression(): void
    {
        if ($this->current === null) {
            $this->throwUnexpectedEnd();
        }

        if ($this->current === self::INLINE_COMMENT) {
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
                : $this->pushPunctuation(TokenType::Dash),
            '=' => $this->comesNext('==')
                ? $this->pushToken(TokenType::Comparison, $this->consume(2))
                : $this->pushPunctuation(TokenType::Equals),
            '!' => $this->comesNext('!=')
                ? $this->pushToken(TokenType::Comparison, $this->consume(2))
                : throw SyntaxException::unexpectedCharacter('!'),
            '<' => $this->comesNext('<>') || $this->comesNext('<=')
                ? $this->pushToken(TokenType::Comparison, $this->consume(2))
                : $this->pushPunctuation(TokenType::Comparison),
            '>' => $this->comesNext('>=')
                ? $this->pushToken(TokenType::Comparison, $this->consume(2))
                : $this->pushPunctuation(TokenType::Comparison),
            '.' => $this->comesNext('..')
                ? $this->pushToken(TokenType::DotDot, $this->consume(2))
                : $this->pushPunctuation(TokenType::Dot),
            '|' => $this->pushPunctuation(TokenType::Pipe),
            ':' => $this->pushPunctuation(TokenType::Colon),
            ',' => $this->pushPunctuation(TokenType::Comma),
            '[' => $this->pushPunctuation(TokenType::OpenSquare),
            ']' => $this->pushPunctuation(TokenType::CloseSquare),
            '(' => $this->pushPunctuation(TokenType::OpenRound),
            ')' => $this->pushPunctuation(TokenType::CloseRound),
            '?' => $this->pushPunctuation(TokenType::QuestionMark),
            default => throw SyntaxException::unexpectedCharacter($this->current ?? ''),
        };

        if ($this->current === null) {
            $this->throwUnexpectedEnd();
        }
    }

    /**
     * Callers check $current themselves before calling: this runs twice per
     * expression token, and the check is a property compare while the call is not.
     *
     * @throws SyntaxException
     */
    protected function throwUnexpectedEnd(): never
    {
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

    protected function lexRawBodyTag(string $tag): void
    {
        $endTag = $this->findEndTag($tag);
        if ($endTag === null) {
            throw SyntaxException::tagBlockNeverClosed($tag);
        }

        // Leave the closing tag at the cursor: the normal Data/Block flow still
        // needs to emit its tokens for the parser.
        $rawBody = substr($this->source, $this->cursor, $endTag['start'] - $this->cursor);
        $this->skip($endTag['start'] - $this->cursor);

        if ($endTag['innerTrim']) {
            $rawBody = rtrim($rawBody);
        }

        $this->pushToken(TokenType::RawData, $rawBody);
    }

    protected function lexComment(): void
    {
        $endTag = $this->findEndTag('comment');
        if ($endTag === null) {
            throw SyntaxException::tagBlockNeverClosed('comment');
        }

        // Comments emit no tokens, including for their closing tag.
        $this->skip($endTag['end'] - $this->cursor);

        if ($endTag['outerTrim']) {
            $this->skipWhitespace();
        }
    }

    /**
     * A liquid tag is line based: only the first word of a line names a tag.
     */
    protected function startsLiquidTagLine(Token $tag, Token $token): bool
    {
        $previous = $this->tokens[count($this->tokens) - 2] ?? null;

        return $previous === null
            || $previous === $tag
            || $previous->lineNumber < $token->lineNumber;
    }

    /**
     * Skip a `comment`/`endcomment` block inside a liquid tag.
     *
     * Same contract as lexComment(): the body emits no tokens and is never
     * lexed, so it may hold anything (apostrophes, stray delimiters), and the
     * first `endcomment` closes the block without tracking nesting.
     */
    protected function skipLiquidComment(int $lineNumber): void
    {
        // The liquid tag ends at its own `%}`, so an `endcomment` past that point
        // belongs to something else and the comment is never closed.
        $blockEnd = strpos($this->source, self::BLOCK_END, $this->cursor);
        $offset = $this->cursor;

        while (true) {
            if ($blockEnd === false || $offset > $blockEnd) {
                // Report the `comment` line, like an unclosed `{% comment %}` does.
                $exception = SyntaxException::tagBlockNeverClosed('comment');
                $exception->lineNumber = $lineNumber;

                throw $exception;
            }

            $offset += strspn($this->source, " \t", $offset);

            if ($this->comesNext('endcomment', $offset)
                && strspn($this->source, self::WORD, $offset + strlen('endcomment'), 1) === 0
            ) {
                $this->skip($offset + strlen('endcomment') - $this->cursor);

                return;
            }

            $offset += strcspn($this->source, "\n", $offset) + 1;
        }
    }

    protected function lexInlineComment(): void
    {
        $offset = $this->cursor;

        // Inline comments stop at a block terminator or newline. Leave that
        // boundary untouched so lexBlock() can resume normal tokenization.
        while (true) {
            $offset += strcspn($this->source, "\n%", $offset);

            if ($offset >= $this->end) {
                throw SyntaxException::tagBlockNeverClosed('#');
            }

            if ($this->charAt($offset) === "\n") {
                $terminator = $offset;
                break;
            }

            if ($this->comesNext(self::BLOCK_END, $offset)) {
                $terminator = $this->charAt($offset - 1) === self::TRIM
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

    /**
     * Emit a single-character punctuation token and advance one byte.
     *
     * Punctuation is never a newline and never needs a substring copy, so this
     * skips the consume() + skip() pair (and their line bookkeeping) that the
     * generic path would run. Roughly one in six tokens takes this path.
     */
    protected function pushPunctuation(TokenType $type): void
    {
        $cursor = $this->cursor;
        $this->tokens[] = new Token($type, $this->source[$cursor], $this->lineNumber);

        $cursor++;
        $this->cursor = $cursor;
        $this->current = $cursor < $this->end ? $this->source[$cursor] : null;
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
        $length = strspn($this->source, self::WHITESPACE, $this->cursor);

        // Probed after every expression token, and most of the time there is
        // nothing to skip ("a.b", "x|filter"), so bail before calling skip().
        if ($length !== 0) {
            $this->skip($length);
        }
    }

    /**
     * @return array{start:int, end:int, innerTrim:bool, outerTrim:bool}|null
     */
    protected function findEndTag(string $tag): ?array
    {
        $offset = $this->cursor;

        // Return both sides of the closing tag: raw bodies stop at `start`, while
        // comments skip through `end`. The trim flags apply inside and outside it.
        while (true) {
            $offset += strcspn($this->source, '{', $offset);

            if ($offset >= $this->end) {
                return null;
            }

            if (! $this->comesNext(self::BLOCK_START, $offset)) {
                $offset++;

                continue;
            }

            $probe = $offset + strlen(self::BLOCK_START);
            $innerTrim = false;

            if ($this->charAt($probe) === self::TRIM) {
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

            if ($this->charAt($probe) === self::TRIM) {
                $outerTrim = true;
                $probe++;
            }

            if (! $this->comesNext(self::BLOCK_END, $probe)) {
                $offset++;

                continue;
            }

            return [
                'start' => $offset,
                'end' => $probe + strlen(self::BLOCK_END),
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

        if ($this->comesNext(self::TRIM_BLOCK_END, $offset)) {
            return $offset + strlen(self::TRIM_BLOCK_END) - $this->cursor;
        }

        if ($this->comesNext(self::BLOCK_END, $offset)) {
            return $offset + strlen(self::BLOCK_END) - $this->cursor;
        }

        return null;
    }

    /**
     * Returns the length to skip, or null when the terminator is not next.
     * Probed once per expression token, so it avoids allocating a result array
     * (the trim flag goes to $terminatorTrim) and assumes the caller already
     * skipped whitespace rather than re-scanning it here.
     */
    protected function terminatorLength(string $terminator): ?int
    {
        $source = $this->source;
        $offset = $this->cursor;

        $trim = ($source[$offset] ?? null) === self::TRIM;
        $at = $trim ? $offset + 1 : $offset;

        if (($source[$at] ?? null) !== $terminator[0] || ($source[$at + 1] ?? null) !== $terminator[1]) {
            return null;
        }

        $this->terminatorTrim = $trim;

        return $at + 2 - $this->cursor;
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

        if (($this->source[$offset] ?? null) === '?') {
            $offset++;
        }

        $value = substr($this->source, $start, $offset - $start);
        $type = $value === 'contains' && $this->isWhitespace($this->source[$offset] ?? null)
            ? TokenType::Comparison
            : TokenType::Identifier;

        // Identifiers are the most common token and can never span a newline, so
        // advance directly rather than paying for pushToken() + skip() and the
        // newline scan skip() would run.
        $this->tokens[] = new Token($type, $value, $this->lineNumber);
        $this->cursor = $offset;
        $this->current = $offset < $this->end ? $this->source[$offset] : null;
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

    protected function isDigit(?string $character): bool
    {
        return $character !== null && $character >= '0' && $character <= '9';
    }

    protected function isWhitespace(?string $character): bool
    {
        return $character !== null && str_contains(self::WHITESPACE, $character);
    }
}
