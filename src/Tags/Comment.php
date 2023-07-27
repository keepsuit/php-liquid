<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Block;

class Comment extends Block
{
    public static function name(): string
    {
        return 'comment';
    }

    public function blank(): bool
    {
        return true;
    }
}
