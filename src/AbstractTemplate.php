<?php

namespace Keepsuit\Liquid;

abstract class AbstractTemplate implements TemplateInterface
{
    public function __construct(
        public readonly TemplateSharedState $state = new TemplateSharedState,
    ) {}

    public function getState(): TemplateSharedState
    {
        return $this->state;
    }

    public function getErrors(): array
    {
        return $this->state->errors;
    }
}
