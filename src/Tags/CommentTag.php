<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\TagBlock;

class CommentTag extends TagBlock
{
    public static function tagName(): string
    {
        return 'comment';
    }

    public function blank(): bool
    {
        return true;
    }
}
