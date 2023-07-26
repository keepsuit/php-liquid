<?php

namespace Keepsuit\Liquid\Lexer;

use Exception;

class Lexer
{
    protected const IDENTIFIER = "/\G[a-zA-Z_][\w-]*\??/";
    protected const STRING_LITERAL = '/\G("[^\"]*")|\\G(\'[^\']*\')/';
    protected const NUMBER_LITERAL = '/\G-?\d+(\.\d+)?/';
    protected const DOTDOT = '/\G\.\./';
    protected const COMPARISON_OPERATOR = '/\G(==|!=|<>|<=?|>=?|contains(?=\s))/';

    protected const SPECIAL_CHARACTERS = [
        '|' => Token::Pipe,
        '.' => Token::Dot,
        ':' => Token::Colon,
        ',' => Token::Comma,
        '[' => Token::OpenSquare,
        ']' => Token::CloseSquare,
        '(' => Token::OpenRound,
        ')' => Token::CloseRound,
        '?' => Token::QuestionMark,
        '-' => Token::Dash,
    ];
    protected const WHITESPACE_OR_NOTHING = '/\G\s*/';

    protected array|Exception|null $result = null;

    public function __construct(
        protected readonly string $input
    ) {
    }

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
                preg_match(self::COMPARISON_OPERATOR, $this->input, $matches, offset: $currentIndex) === 1 => [Token::Comparison, $matches[0]],
                preg_match(self::STRING_LITERAL, $this->input, $matches, offset: $currentIndex) === 1 => [Token::String, $matches[0]],
                preg_match(self::NUMBER_LITERAL, $this->input, $matches, offset: $currentIndex) === 1 => [Token::Number, $matches[0]],
                preg_match(self::IDENTIFIER, $this->input, $matches, offset: $currentIndex) === 1 => [Token::Identifier, $matches[0]],
                preg_match(self::DOTDOT, $this->input, $matches, offset: $currentIndex) === 1 => [Token::DotDot, $matches[0]],
                array_key_exists($this->input[$currentIndex], self::SPECIAL_CHARACTERS) => [self::SPECIAL_CHARACTERS[$this->input[$currentIndex]], $this->input[$currentIndex]],
                default => new Exception(sprintf('Unexpected character %s', $this->input[$currentIndex])),
            };

            if ($token instanceof Exception) {
                $this->result = $token;
                throw $token;
            }

            $output[] = $token;
            $currentIndex += strlen($token[1]);
        }

        $output[] = [Token::EndOfString];
        $this->result = $output;

        return $output;
    }
}
