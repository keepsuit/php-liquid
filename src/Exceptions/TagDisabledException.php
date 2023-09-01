<?php

namespace Keepsuit\Liquid\Exceptions;

use Keepsuit\Liquid\Support\I18n;

class TagDisabledException extends LiquidException
{
    public function __construct(string $tagName, I18n $locale)
    {
        parent::__construct($locale->translate('errors.disabled.tag', ['tag' => $tagName]));
    }
}
