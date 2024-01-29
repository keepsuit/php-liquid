<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Render\RenderContext;

interface CanBeEvaluated
{
    public function evaluate(RenderContext $context): mixed;
}
