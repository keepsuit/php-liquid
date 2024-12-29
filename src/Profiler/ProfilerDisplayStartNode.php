<?php

namespace Keepsuit\Liquid\Profiler;

use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Render\RenderContext;

class ProfilerDisplayStartNode extends Node
{
    public function __construct(
        protected ProfileType $type,
        protected ?string $name = null,
    ) {}

    public function render(RenderContext $context): string
    {
        $this->startProfile($context);

        return '';
    }

    protected function startProfile(RenderContext $context): ?Profile
    {
        $profiler = $context->getRegister('profiler');

        if (! $profiler instanceof Profiler) {
            return null;
        }

        $name = match ($this->type) {
            ProfileType::Template => $context->getTemplateName(),
            default => $this->name,
        };

        $profiler->enter($profile = new Profile($this->type, name: $name));

        return $profile;
    }
}
