<?php

namespace Keepsuit\Liquid;

class ElseCondition extends Condition
{
    public function evaluate(Context $context): bool
    {
        return true;
    }
}
