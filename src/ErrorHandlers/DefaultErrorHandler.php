<?php

namespace Keepsuit\Liquid\ErrorHandlers;

use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\Exceptions\LiquidException;

class DefaultErrorHandler implements LiquidErrorHandler
{
    public function handle(LiquidException $error): string
    {
        return $error->toLiquidErrorMessage();
    }
}
