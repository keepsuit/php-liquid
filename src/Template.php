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
            $this->prepareContext($context);

            $output = $this->root->render($context);

            // Partials are already part of the root output
            if (! $context->isPartial()) {
                $context->resourceLimits->incrementWriteScore($output);
            }

            return $output;
        } catch (LiquidException $e) {
            $this->attachTemplateName($e);
            throw $e;
        } finally {
            $this->persistContext($context);
        }
    }

    /**
     * @return \Generator<string>
     */
    public function stream(RenderContext $context): \Generator
    {
        try {
            $this->prepareContext($context);

            // Partials are streamed through the root template's loop below
            if ($context->isPartial()) {
                yield from $this->root->stream($context);

                return;
            }

            /*
             * The one place every chunk is guaranteed to pass through exactly once,
             * whichever node produced it. Two jobs happen here:
             * - increment the write score, checked against a running total.
             * - renumbering the keys, since nodes delegate with `yield from`, which passes the inner generators' keys through and restarts them at 0
             */
            $context->resourceLimits->resetStreamWriteScore();

            foreach ($this->root->stream($context) as $output) {
                $context->resourceLimits->incrementStreamWriteScore($output);
                yield $output;
            }
        } catch (LiquidException $e) {
            $this->attachTemplateName($e);
            throw $e;
        } finally {
            $this->persistContext($context);
        }
    }

    public function name(): ?string
    {
        return $this->root->name;
    }
}
