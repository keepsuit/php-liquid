<?php

namespace Keepsuit\Liquid;

class ResourceLimitException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Memory limit exceeded');
    }
}
