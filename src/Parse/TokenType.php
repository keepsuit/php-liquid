<?php

namespace Keepsuit\Liquid\Parse;

enum TokenType
{
    /**
     * Tag tokens
     */
    case TextData;
    case RawData;
    case VariableStart;
    case VariableEnd;
    case BlockStart;
    case BlockEnd;

    /**
     * Expression tokens
     */
    case String;
    case Number;
    case Identifier;
    case Comparison;
    case DotDot;
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
    case Equals;

    public function toString(): string
    {
        return match ($this) {
            self::String => 'String',
            self::Number => 'Number',
            self::Identifier => 'Identifier',
            self::Comparison => 'Comparison',
            self::DotDot => '..',
            self::Pipe => '|',
            self::Dot => '.',
            self::Colon => ':',
            self::Comma => ',',
            self::OpenSquare => '[',
            self::CloseSquare => ']',
            self::OpenRound => '(',
            self::CloseRound => ')',
            self::QuestionMark => '?',
            self::Dash => '-',
            self::Equals => '==',
            self::VariableStart => '{{',
            self::VariableEnd => '}}',
            self::BlockStart => '{%',
            self::BlockEnd => '%}',
            self::TextData => 'Text',
            self::RawData => 'Raw',
        };
    }
}
