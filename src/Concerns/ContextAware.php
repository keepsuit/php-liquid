<?php

namespace Keepsuit\Liquid\Concerns;

use Keepsuit\Liquid\Attributes\Hidden;
use Keepsuit\Liquid\Render\RenderContext;

trait ContextAware
{
    protected RenderContext $context;

    #[Hidden]
    public function setContext(RenderContext $context): void
    {
        $this->context = $context;
    }
}
