<?php

namespace Keepsuit\Liquid\Concerns;

use Keepsuit\Liquid\Render\RenderContext;

trait ContextAware
{
    protected RenderContext $context;

    public function setContext(RenderContext $context): void
    {
        $this->context = $context;
    }
}
