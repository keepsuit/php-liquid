<?php

namespace Keepsuit\Liquid;

enum TokenType
{
    case String;
    case Number;
    case Identifier;
    case Comparison;
    case DotDot;
    case EndOfString;
    case Pipe;
    case Dot;
    case Colon;
    case Comma;
    case OpenSquare;
    case CloseSquare;
    case OpenRound;
    case CloseRound;
    case QuestionMark;
    case Dash;

    public function toString(): string
    {
        return match ($this) {
            self::String => 'String',
            self::Number => 'Number',
            self::Identifier => 'Identifier',
            self::Comparison => 'Comparison',
            self::DotDot => 'DotDot',
            self::EndOfString => 'EndOfString',
            self::Pipe => 'Pipe',
            self::Dot => 'Dot',
            self::Colon => 'Colon',
            self::Comma => 'Comma',
            self::OpenSquare => 'OpenSquare',
            self::CloseSquare => 'CloseSquare',
            self::OpenRound => 'OpenRound',
            self::CloseRound => 'CloseRound',
            self::QuestionMark => 'QuestionMark',
            self::Dash => 'Dash',
        };
    }
}
