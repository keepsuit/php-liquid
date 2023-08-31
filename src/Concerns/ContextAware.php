<?php

namespace Keepsuit\Liquid\Concerns;

use Keepsuit\Liquid\Render\Context;

trait ContextAware
{
    protected Context $context;

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }
}
