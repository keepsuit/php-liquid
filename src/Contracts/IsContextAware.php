<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Render\RenderContext;

interface IsContextAware
{
    public function setContext(RenderContext $context): void;
}
