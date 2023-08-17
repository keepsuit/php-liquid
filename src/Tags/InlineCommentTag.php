<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Tag;

class InlineCommentTag extends Tag
{
    public static function tagName(): string
    {
        return '#';
    }

    public function blank(): bool
    {
        return true;
    }
}
