<?php

namespace Keepsuit\Liquid\Parse;

/**
 * @internal
 */
enum LexerOptions: string
{
    case TagVariableStart = '{{';
    case TagVariableEnd = '}}';

    case TagBlockStart = '{%';
    case TagBlockEnd = '%}';

    case InlineComment = '#';

    case WhitespaceTrim = '-';

    public static function variableLookupRegex(): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{%s|%s|%s|%s}',
                '\.([\w\-]+)',
                '\["([\w\-]+)"\]',
                "\['([\w\-]+)'\]",
                '\[(\d+)\]'
            );
        }

        return $regex;
    }

    public static function specialCharacters(): array
    {
        static $specialCharacters;

        if ($specialCharacters === null) {
            $specialCharacters = [
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
        }

        return $specialCharacters;
    }
}
