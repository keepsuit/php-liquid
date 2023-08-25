<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Render\Context;

interface CanBeRendered
{
    public function render(Context $context): string;
}
