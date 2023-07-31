<?php

namespace Keepsuit\Liquid\Tags;

class UnlessTag extends IfTag
{
    public static function name(): string
    {
        return 'unless';
    }
}
