<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\TagBlock;
use RuntimeException;

class Lexer
{
    protected string $source;

    protected int $cursor;

    protected int $end;

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
     * @var array<int, array{0:string,1:int}>
     */
    protected array $positions;

    protected int $position;

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
        $this->lineNumber = 1;
        $this->end = strlen($this->source);
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

        $this->positions = $this->extractTokenStarts($this->source);
        $this->position = -1;

        while ($this->cursor < $this->end) {
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
        // if no matches are left we return the rest of the template as simple text token
        if ($this->position == count($this->positions) - 1) {
            $this->pushToken(TokenType::TextData, substr($this->source, $this->cursor));
            $this->cursor = $this->end;

            return;
        }

        // Find the first token after the current cursor
        $position = $this->positions[++$this->position];
        while ($position[1] < $this->cursor) {
            if ($this->position == count($this->positions) - 1) {
                return;
            }
            $position = $this->positions[++$this->position];
        }

        // push the template text before the token first
        $text = $textBeforeToken = substr($this->source, $this->cursor, $position[1] - $this->cursor);

        // trim?
        if (($this->positions[$this->position][0][2] ?? null) === LexerOptions::WhitespaceTrim->value) {
            $textBeforeToken = rtrim($textBeforeToken);
        }

        $this->pushToken(TokenType::TextData, $textBeforeToken);
        $this->moveCursor($text.$position[0]);

        switch (rtrim($this->positions[$this->position][0], LexerOptions::WhitespaceTrim->value)) {
            case LexerOptions::TagBlockStart->value:
                // {% comment %}
                if (preg_match(LexerOptions::blockCommentStartRegex(), $this->source, $matches, offset: $this->cursor) === 1) {
                    $this->moveCursor($matches[0]);
                    $this->lexComment();
                    break;
                }

                $this->pushToken(TokenType::BlockStart);
                $this->pushState(LexerState::Block);
                $this->currentVarBlockLine = $this->lineNumber;
                break;
            case LexerOptions::TagVariableStart->value:
                $this->pushToken(TokenType::VariableStart);
                $this->pushState(LexerState::Variable);
                $this->currentVarBlockLine = $this->lineNumber;
                break;
        }
    }

    /**
     * @throws SyntaxException
     */
    protected function lexVariable(): void
    {
        if (preg_match(LexerOptions::variableEndRegex(), $this->source, $matches, offset: $this->cursor) === 1) {
            $this->pushToken(TokenType::VariableEnd);
            $this->moveCursor($matches[0]);
            $this->popState();

            // trim?
            if (trim($matches[0])[0] === LexerOptions::WhitespaceTrim->value) {
                $this->trimWhitespaces();
            }
        } else {
            $this->lexExpression();
        }
    }

    /**
     * @throws SyntaxException
     */
    protected function lexBlock(): void
    {
        $tag = null;

        // Parse the full expression inside {% ... %}
        while (preg_match(LexerOptions::blockEndRegex(), $this->source, $matches, offset: $this->cursor) !== 1) {
            $this->lexExpression();

            $lastToken = $this->tokens[array_key_last($this->tokens)];

            if ($tag === null && $lastToken->type === TokenType::Identifier) {
                $tag = $lastToken;
            }
        }

        // Move the cursor to the end of the block
        $this->moveCursor($matches[0]);

        // trim?
        if (trim($matches[0])[0] === LexerOptions::WhitespaceTrim->value) {
            $this->trimWhitespaces();
        }

        // If the last token is a block start, we remove the node
        $lastToken = $this->tokens[array_key_last($this->tokens)];
        if ($lastToken->type === TokenType::BlockStart) {
            array_pop($this->tokens);
        } else {
            $this->pushToken(TokenType::BlockEnd);
        }

        $this->popState();

        // If the tag is a raw body tag, we need to lex the body as raw data instead of liquid blocks
        if ($tag !== null && in_array($tag->data, $this->rawBodyTags, true)) {
            $this->laxRawBodyTag($tag->data);
        }
    }

    /**
     * @throws SyntaxException
     */
    protected function lexExpression(): void
    {
        if (preg_match('/\G\s+/A', $this->source, $matches, offset: $this->cursor) === 1) {
            $this->moveCursor($matches[0] ?? '');
        }

        $this->ensureStreamNotEnded();

        if ($this->source[$this->cursor] === '#') {
            $this->lexInlineComment();

            return;
        }

        $token = match (true) {
            preg_match(LexerOptions::comparisonOperatorRegex(), $this->source, $matches, offset: $this->cursor) === 1 => [TokenType::Comparison, $matches[0]],
            preg_match(LexerOptions::identifierRegex(), $this->source, $matches, offset: $this->cursor) === 1 => [TokenType::Identifier, $matches[0]],
            preg_match(LexerOptions::stringLiteralRegex(), $this->source, $matches, offset: $this->cursor) === 1 => [TokenType::String, $matches[0]],
            preg_match(LexerOptions::numberLiteralRegex(), $this->source, $matches, offset: $this->cursor) === 1 => [TokenType::Number, $matches[0]],
            $this->cursor + 1 < $this->end && $this->source[$this->cursor] === '.' && $this->source[$this->cursor + 1] === '.' => [TokenType::DotDot, '..'],
            array_key_exists($this->source[$this->cursor], LexerOptions::specialCharacters()) => [LexerOptions::specialCharacters()[$this->source[$this->cursor]], $this->source[$this->cursor]],
            default => throw SyntaxException::unexpectedCharacter($this->source[$this->cursor]),
        };

        $this->pushToken($token[0], $token[1]);
        $this->moveCursor($token[1]);

        $this->ensureStreamNotEnded();
    }

    /**
     * @throws SyntaxException
     */
    protected function ensureStreamNotEnded(): void
    {
        if ($this->cursor >= $this->end) {
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

    private function laxRawBodyTag(string $tag): void
    {
        if (preg_match(LexerOptions::blockRawBodyTagDataRegex($tag), $this->source, $matches, flags: PREG_OFFSET_CAPTURE, offset: $this->cursor) !== 1) {
            throw SyntaxException::tagBlockNeverClosed($tag);
        }

        $rawBody = substr($this->source, $this->cursor, $matches[0][1] - $this->cursor);

        $this->moveCursor($rawBody);

        // inner trim?
        if (($matches[1][0][2] ?? null) === LexerOptions::WhitespaceTrim->value) {
            $rawBody = rtrim($rawBody);
        }

        $this->pushToken(TokenType::RawData, $rawBody);

        // trim?
        if ($matches[2][0][0] === LexerOptions::WhitespaceTrim->value) {
            $this->trimWhitespaces();
        }
    }

    protected function lexComment(): void
    {
        if (preg_match(LexerOptions::blockCommentDataRegex(), $this->source, $matches, flags: PREG_OFFSET_CAPTURE, offset: $this->cursor) !== 1) {
            throw SyntaxException::tagBlockNeverClosed('comment');
        }

        $text = substr($this->source, $this->cursor, $matches[0][1] - $this->cursor);

        $this->moveCursor($text.$matches[0][0]);
    }

    protected function lexInlineComment(): void
    {
        if (preg_match(LexerOptions::inlineCommentDataRegex(), $this->source, $matches, flags: PREG_OFFSET_CAPTURE, offset: $this->cursor) !== 1) {
            throw SyntaxException::tagBlockNeverClosed('#');
        }

        $text = substr($this->source, $this->cursor, $matches[0][1] - $this->cursor);

        $this->moveCursor($text);
    }

    protected function pushToken(TokenType $type, string $value = ''): void
    {
        if ($type === TokenType::TextData && $value === '') {
            return;
        }

        $this->tokens[] = new Token($type, $value, $this->lineNumber);
    }

    protected function moveCursor(string $text): void
    {
        if ($text === '') {
            return;
        }

        $this->cursor += strlen($text);
        $this->lineNumber += substr_count($text, "\n");

        $this->parseContext->lineNumber = $this->lineNumber;
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
        preg_match('/\s+/A', $this->source, $matches, offset: $this->cursor);
        $this->moveCursor($matches[0] ?? '');
    }

    /**
     * @return array<int,array{0:string,1:int}>
     */
    protected function extractTokenStarts(string $source): array
    {
        preg_match_all(LexerOptions::blockStartRegex(), $source, $blocks, PREG_OFFSET_CAPTURE);
        preg_match_all(LexerOptions::variableStartRegex(), $source, $variables, PREG_OFFSET_CAPTURE);

        $positions = array_merge($blocks[0], $variables[0]);
        usort($positions, fn (array $a, array $b) => $a[1] <=> $b[1]);

        return $positions;
    }
}
