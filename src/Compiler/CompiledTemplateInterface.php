<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Render\RenderContext;

interface CompiledTemplateInterface
{
    public function render(RenderContext $context): string;
}
