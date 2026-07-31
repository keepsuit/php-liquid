<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Render\RenderContext;

class Template extends AbstractTemplate
{
    public function __construct(
        public readonly Document $root,
        TemplateSharedState $state = new TemplateSharedState,
    ) {
        parent::__construct($state);
    }

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

    public function name(): ?string
    {
        return $this->root->name;
    }
}
