<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Exceptions\LiquidException;

interface LiquidErrorHandler
{
    /**
     * Handle the given error.
     * The returned string will be printed to the output.
     */
    public function handle(LiquidException $error): string;
}
