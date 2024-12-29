<?php

namespace Keepsuit\Liquid\Profiler;

use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Render\RenderContext;

class ProfilerDisplayEndNode extends Node
{
    public function render(RenderContext $context): string
    {
        $this->endProfile($context);

        return '';
    }

    protected function endProfile(RenderContext $context): ?Profile
    {
        $profiler = $context->getRegister('profiler');

        if (! $profiler instanceof Profiler) {
            return null;
        }

        return $profiler->leave();
    }
}
