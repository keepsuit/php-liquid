<?php

namespace Keepsuit\Liquid\Condition;

use Keepsuit\Liquid\Render\RenderContext;

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

    public function evaluate(RenderContext $context): bool
    {
        return true;
    }
}
