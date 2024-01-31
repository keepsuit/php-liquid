<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Render\RenderContext;

interface CanBeRendered
{
    public function render(RenderContext $context): string;
}
