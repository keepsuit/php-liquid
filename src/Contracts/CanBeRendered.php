<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Context;

interface CanBeRendered
{
    public function render(Context $context): string;
}
