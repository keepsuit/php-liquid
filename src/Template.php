<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Render\RenderContext;

class Template
{
    public function __construct(
        public readonly Document $root,
        public readonly TemplateSharedState $state = new TemplateSharedState
    ) {}

    /**
     * @throws LiquidException
     */
    public function render(RenderContext $context): string
    {
        try {
            $context->mergeOutputs($this->state->outputs);

            return $this->root->render($context);
        } catch (LiquidException $e) {
            $e->templateName = $e->templateName ?? $this->root->name;
            throw $e;
        } finally {
            $this->state->errors = $context->getErrors();
            $this->state->outputs = $context->getOutputs();
        }
    }

    /**
     * @return \Generator<string>
     */
    public function stream(RenderContext $context): \Generator
    {
        try {
            $context->mergeOutputs($this->state->outputs);

            yield from $this->root->stream($context);
        } catch (LiquidException $e) {
            $e->templateName = $e->templateName ?? $this->root->name;
            throw $e;
        } finally {
            $this->state->errors = $context->getErrors();
            $this->state->outputs = $context->getOutputs();
        }
    }

    public function getState(): TemplateSharedState
    {
        return $this->state;
    }

    public function getErrors(): array
    {
        return $this->state->errors;
    }

    public function name(): ?string
    {
        return $this->root->name;
    }
}
