<?php

namespace Keepsuit\Liquid;

interface CanBeEvaluated
{
    public function evaluate(Context $context): mixed;
}
