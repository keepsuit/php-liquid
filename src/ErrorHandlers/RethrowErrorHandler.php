<?php

namespace Keepsuit\Liquid\ErrorHandlers;

use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\Exceptions\LiquidException;

class RethrowErrorHandler implements LiquidErrorHandler
{
    public function handle(LiquidException $error): string
    {
        throw $error;
    }
}
