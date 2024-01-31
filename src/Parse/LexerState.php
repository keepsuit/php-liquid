<?php

namespace Keepsuit\Liquid\Parse;

enum LexerState
{
    case Data;
    case Block;
    case Variable;
}
