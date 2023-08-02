<?php

namespace Keepsuit\Liquid;

class ElseCondition extends Condition
{
    public function __construct()
    {
        parent::__construct();
    }

    public function else(): bool
    {
        return true;
    }

    public function evaluate(Context $context): bool
    {
        return true;
    }
}
