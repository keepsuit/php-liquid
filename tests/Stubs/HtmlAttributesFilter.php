<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Support\Arr;

class HtmlAttributesFilter
{
    public function htmlTag(string $tag, ...$attributes): string
    {
        return implode(' ', Arr::map($attributes, fn ($value, $key) => "$key='$value'"));
    }
}
