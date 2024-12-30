<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Filters\FiltersProvider;

class FunnyFilter extends FiltersProvider
{
    public function makeFunny(string $input): string
    {
        return 'LOL';
    }

    public function citeFunny(string $input): string
    {
        return 'LOL: '.$input;
    }

    public function addSmiley(string $input, string $smiley = ':-)'): string
    {
        return sprintf('%s %s', $input, $smiley);
    }

    public function addTag(string $input, string $tag = 'p', string $id = 'foo'): string
    {
        return sprintf('<%s id="%s">%s</%s>', $tag, $id, $input, $tag);
    }

    public function paragraph(string $input): string
    {
        return sprintf('<p>%s</p>', $input);
    }

    public function linkTo(string $input, string $url): string
    {
        return sprintf('<a href="%s">%s</a>', $url, $input);
    }
}
