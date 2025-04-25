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

    public static function tokenStartRegex(): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{(%s|%s)(%s)?}sx',
                preg_quote(LexerOptions::TagVariableStart->value),
                preg_quote(LexerOptions::TagBlockStart->value),
                preg_quote(LexerOptions::WhitespaceTrim->value)
            );
        }

        return $regex;
    }

    public static function variableEndRegex(): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{\s*(?:%s|%s)}Ax',
                preg_quote(LexerOptions::WhitespaceTrim->value.LexerOptions::TagVariableEnd->value),
                preg_quote(LexerOptions::TagVariableEnd->value),
            );
        }

        return $regex;
    }

    public static function blockEndRegex(): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{\s*(?:%s|%s)}Ax',
                preg_quote(LexerOptions::WhitespaceTrim->value.LexerOptions::TagBlockEnd->value),
                preg_quote(LexerOptions::TagBlockEnd->value),
            );
        }

        return $regex;
    }

    public static function blockRawBodyTagDataRegex(string $tag): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{%s(%s)?\s*end%s\s*(%s)?%s}sx',
                preg_quote(LexerOptions::TagBlockStart->value),
                LexerOptions::WhitespaceTrim->value,
                preg_quote($tag),
                LexerOptions::WhitespaceTrim->value,
                preg_quote(LexerOptions::TagBlockEnd->value),
            );
        }

        return $regex;
    }

    public static function blockRawStartRegex(): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{\s*raw\s*(?:%s|%s)}Ax',
                preg_quote(LexerOptions::WhitespaceTrim->value.LexerOptions::TagBlockEnd->value),
                preg_quote(LexerOptions::TagBlockEnd->value),
            );
        }

        return $regex;
    }

    public static function blockRawDataRegex(): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{%s(%s)?\s*endraw\s*(%s)?%s}sx',
                preg_quote(LexerOptions::TagBlockStart->value),
                LexerOptions::WhitespaceTrim->value,
                LexerOptions::WhitespaceTrim->value,
                preg_quote(LexerOptions::TagBlockEnd->value),
            );
        }

        return $regex;
    }

    public static function blockCommentStartRegex(): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{\s*comment\s*(?:%s|%s)}Ax',
                preg_quote(LexerOptions::WhitespaceTrim->value.LexerOptions::TagBlockEnd->value),
                preg_quote(LexerOptions::TagBlockEnd->value),
            );
        }

        return $regex;
    }

    public static function blockCommentDataRegex(): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{%s(%s)?\s*endcomment\s*(?:%s|%s)}sx',
                preg_quote(LexerOptions::TagBlockStart->value),
                LexerOptions::WhitespaceTrim->value,
                preg_quote(LexerOptions::WhitespaceTrim->value.LexerOptions::TagBlockEnd->value),
                preg_quote(LexerOptions::TagBlockEnd->value),
            );
        }

        return $regex;
    }

    public static function inlineCommentDataRegex(): string
    {
        static $regex;

        if ($regex === null) {
            $regex = sprintf(
                '{\s*(%s|%s|\n)}x',
                preg_quote(LexerOptions::WhitespaceTrim->value.LexerOptions::TagBlockEnd->value),
                preg_quote(LexerOptions::TagBlockEnd->value),
            );
        }

        return $regex;
    }

    public static function comparisonOperatorRegex(): string
    {
        return '{\G(==|!=|<>|<=?|>=?|contains(?=\s))}As';
    }

    public static function stringLiteralRegex(): string
    {
        return '{\G("[^"]*")|(\'[^\']*\')}As';
    }

    public static function numberLiteralRegex(): string
    {
        return '{\G-?\d+(?:\.\d+)?}As';
    }

    public static function identifierRegex(): string
    {
        return "{\G[a-zA-Z_](?:\w|-\w)*\??}As";
    }

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
