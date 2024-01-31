<?php

namespace Keepsuit\Liquid\Parse;

/**
 * @internal
 */
enum LexerState
{
    case Data;
    case Block;
    case Variable;
}
