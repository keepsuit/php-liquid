<?php

namespace Keepsuit\Liquid\Exceptions;

class TagDisabledException extends LiquidException
{
    public function __construct(string $tagName)
    {
        parent::__construct(sprintf('%s usage is not allowed in this context', $tagName));
    }
}
