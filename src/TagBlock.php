<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;

abstract class TagBlock extends Tag implements HasParseTreeVisitorChildren
{
    public static function blockDelimiter(): string
    {
        return 'end'.static::tagName();
    }

    public function isSubTag(string $tagName): bool
    {
        return false;
    }

    public function parseTreeVisitorChildren(): array
    {
        return [];
    }
}
