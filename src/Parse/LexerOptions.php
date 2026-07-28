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

}
