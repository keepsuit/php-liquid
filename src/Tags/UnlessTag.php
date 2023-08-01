<?php

namespace Keepsuit\Liquid\Tags;

class UnlessTag extends IfTag
{
    public static function tagName(): string
    {
        return 'unless';
    }
}
