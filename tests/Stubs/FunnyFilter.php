<?php

namespace Keepsuit\Liquid\Tests\Stubs;

class FunnyFilter
{
    public static function makeFunny(string $input): string
    {
        return 'LOL';
    }

    public static function citeFunny(string $input): string
    {
        return 'LOL: '.$input;
    }

    public static function addSmiley(string $input, string $smiley = ':-)'): string
    {
        return sprintf('%s %s', $input, $smiley);
    }

    public static function addTag(string $input, string $tag = 'p', string $id = 'foo'): string
    {
        return sprintf('<%s id="%s">%s</%s>', $tag, $id, $input, $tag);
    }

    public static function paragraph(string $input): string
    {
        return sprintf('<p>%s</p>', $input);
    }

    public static function linkTo(string $input, string $url): string
    {
        return sprintf('<a href="%s">%s</a>', $url, $input);
    }
}
