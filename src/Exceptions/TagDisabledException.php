<?php

namespace Keepsuit\Liquid\Exceptions;

use Keepsuit\Liquid\Parser\ParseContext;

class TagDisabledException extends LiquidException
{
    public function __construct(string $tagName, ParseContext $parseContext)
    {
        parent::__construct($parseContext->locale->translate('errors.disabled.tag', ['tag' => $tagName]));
    }
}
