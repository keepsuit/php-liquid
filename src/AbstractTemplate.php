<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Render\RenderContext;

// @phpstan-ignore-next-line
abstract class AbstractTemplate implements Template, TemplateInterface
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

    protected function prepareContext(RenderContext $context): void
    {
        $context->mergeOutputs($this->state->outputs);
    }

    protected function persistContext(RenderContext $context): void
    {
        $this->state->errors = $context->getErrors();
        $this->state->outputs = $context->getOutputs();
    }

    protected function attachTemplateName(LiquidException $exception): void
    {
        $exception->templateName = $exception->templateName ?? $this->name();
    }
}
