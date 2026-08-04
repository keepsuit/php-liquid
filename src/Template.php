<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Render\RenderContext;

class Template
{
    private const MAX_BUFFERED_BYTES = 4096;

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

            $output = $this->root->render($context);

            // Partials are already part of the root output
            if (! $context->isPartial()) {
                $context->resourceLimits->incrementWriteScore($output);
            }

            return $output;
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
        $buffer = '';

        try {
            $context->mergeOutputs($this->state->outputs);

            // Partials are streamed through the root template's loop below
            if ($context->isPartial()) {
                yield from $this->root->stream($context);

                return;
            }

            /*
             * The one place every chunk is guaranteed to pass through exactly once,
             * whichever node produced it. Three jobs happen here:
             * - increment the write score, checked against a running total.
             * - Buffer streamed output in larger chunks.
             * - renumbering the keys, since nodes delegate with `yield from`, which passes the inner generators' keys through and restarts them at 0
             */
            $context->resourceLimits->resetStreamWriteScore();

            foreach ($this->root->stream($context) as $output) {
                $context->resourceLimits->incrementStreamWriteScore($output);

                $buffer .= $output;

                if (strlen($buffer) >= self::MAX_BUFFERED_BYTES) {
                    yield $buffer;
                    $buffer = '';
                }
            }

            if ($buffer !== '') {
                yield $buffer;
            }
        } catch (LiquidException $e) {
            if ($buffer !== '') {
                yield $buffer;
            }

            $e->templateName = $e->templateName ?? $this->root->name;
            throw $e;
        } catch (\Throwable $e) {
            if ($buffer !== '') {
                yield $buffer;
            }

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
