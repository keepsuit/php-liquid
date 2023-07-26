<?php

namespace Keepsuit\Liquid\Lexer;

enum Token
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
}
