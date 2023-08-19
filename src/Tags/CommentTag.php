<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Context;
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

    protected function isSubTag(string $tagName): bool
    {
        return true;
    }

    public function render(Context $context): string
    {
        return '';
    }
}
