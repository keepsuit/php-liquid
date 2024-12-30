<?php

namespace Keepsuit\Liquid\Extensions;

use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Profiler\ProfilerNodeVisitor;

class ProfilerExtension extends Extension
{
    public function __construct(
        protected Profiler $profiler,
        /** Enable tags profiling */
        protected bool $tags = false,
        /** Enable variables profiling */
        protected bool $variables = false,
    ) {}

    public function getNodeVisitors(): array
    {
        return [new ProfilerNodeVisitor(tags: $this->tags, variables: $this->variables)];
    }

    public function getRegisters(): array
    {
        return [
            'profiler' => $this->profiler,
        ];
    }
}
