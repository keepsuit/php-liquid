<?php

namespace Keepsuit\Liquid\Parse;

use Exception;
use Keepsuit\Liquid\Exceptions\SyntaxException;

class Lexer
{
    protected const IDENTIFIER = "/\G[a-zA-Z_][\w-]*\??/";

    protected const STRING_LITERAL = '/\G("[^\"]*")|\\G(\'[^\']*\')/';

    protected const NUMBER_LITERAL = '/\G-?\d+(\.\d+)?/';

    protected const DOTDOT = '/\G\.\./';

    protected const COMPARISON_OPERATOR = '/\G(==|!=|<>|<=?|>=?|contains(?=\s))/';

    protected const SPECIAL_CHARACTERS = [
        '|' => TokenType::Pipe,
        '.' => TokenType::Dot,
        ':' => TokenType::Colon,
        ',' => TokenType::Comma,
        '[' => TokenType::OpenSquare,
        ']' => TokenType::CloseSquare,
        '(' => TokenType::OpenRound,
        ')' => TokenType::CloseRound,
        '?' => TokenType::QuestionMark,
        '-' => TokenType::Dash,
        '=' => TokenType::Equals,
    ];

    protected const WHITESPACE_OR_NOTHING = '/\G\s*/';

    protected array|Exception|null $result = null;

    public function __construct(
        protected readonly string $input
    ) {
    }

    /**
     * @return array<array{0:TokenType, 1:string, 2:int}>
     *
     * @throws SyntaxException
     */
    public function tokenize(): array
    {
        if ($this->result instanceof Exception) {
            throw $this->result;
        }

        if (is_array($this->result)) {
            return $this->result;
        }

        $output = [];

        $currentIndex = 0;
        while ($currentIndex < strlen($this->input)) {
            preg_match(self::WHITESPACE_OR_NOTHING, $this->input, $matches, offset: $currentIndex);
            $currentIndex += strlen($matches[0]);

            if ($currentIndex >= strlen($this->input)) {
                break;
            }

            $token = match (true) {
                preg_match(self::COMPARISON_OPERATOR, $this->input, $matches, offset: $currentIndex) === 1 => [TokenType::Comparison, $matches[0], $currentIndex],
                preg_match(self::STRING_LITERAL, $this->input, $matches, offset: $currentIndex) === 1 => [TokenType::String, $matches[0], $currentIndex],
                preg_match(self::NUMBER_LITERAL, $this->input, $matches, offset: $currentIndex) === 1 => [TokenType::Number, $matches[0], $currentIndex],
                preg_match(self::IDENTIFIER, $this->input, $matches, offset: $currentIndex) === 1 => [TokenType::Identifier, $matches[0], $currentIndex],
                preg_match(self::DOTDOT, $this->input, $matches, offset: $currentIndex) === 1 => [TokenType::DotDot, $matches[0], $currentIndex],
                array_key_exists($this->input[$currentIndex], self::SPECIAL_CHARACTERS) => [self::SPECIAL_CHARACTERS[$this->input[$currentIndex]], $this->input[$currentIndex], $currentIndex],
                default => SyntaxException::unexpectedCharacter($this->input[$currentIndex]),
            };

            if ($token instanceof Exception) {
                $this->result = $token;
                throw $token;
            }

            $output[] = $token;
            $currentIndex += strlen($token[1]);
        }

        $output[] = [TokenType::EndOfString, '', $currentIndex];
        $this->result = $output;

        return $output;
    }
}
