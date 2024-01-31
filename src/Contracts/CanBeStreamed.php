<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Render\RenderContext;

interface CanBeStreamed extends CanBeRendered
{
    /**
     * @return \Generator<string>
     */
    public function stream(RenderContext $context): \Generator;
}
