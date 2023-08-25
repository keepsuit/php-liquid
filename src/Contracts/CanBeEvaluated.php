<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Context;

interface CanBeEvaluated
{
    public function evaluate(Context $context): mixed;
}
