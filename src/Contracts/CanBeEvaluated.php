<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Render\Context;

interface CanBeEvaluated
{
    public function evaluate(Context $context): mixed;
}
