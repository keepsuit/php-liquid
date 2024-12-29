<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Render\RenderContext;

class Template
{
    protected TemplateSharedState $state;

    public function __construct(
        public readonly Document $root,
        public readonly ?string $name = null,
    ) {
        $this->state = new TemplateSharedState;
    }

    /**
     * @throws LiquidException
     */
    public static function parse(ParseContext $parseContext, string $source, ?string $name = null): Template
    {
        try {
            $root = $parseContext->parse($parseContext->tokenize($source));

            $template = new Template(
                root: $root,
                name: $name,
            );

            if (! $parseContext->isPartial()) {
                $template->state->partialsCache = $parseContext->getPartialsCache()->all();
                $template->state->outputs = $parseContext->getOutputs()->all();
            }

            return $template;
        } catch (LiquidException $e) {
            $e->templateName = $e->templateName ?? $name;
            $e->lineNumber = $e->lineNumber ?? $parseContext->lineNumber;
            throw $e;
        } catch (\Throwable $e) {
            $exception = new InternalException($e);
            $exception->templateName = $exception->templateName ?? $name;
            $exception->lineNumber = $exception->lineNumber ?? $parseContext->lineNumber;
            throw $exception;
        }
    }

    /**
     * @throws LiquidException
     */
    public function render(RenderContext $context): string
    {
        try {
            $context->mergePartialsCache($this->state->partialsCache);
            $context->mergeOutputs($this->state->outputs);

            return $this->root->render($context);
        } catch (LiquidException $e) {
            $e->templateName = $e->templateName ?? $this->name;
            throw $e;
        } finally {
            $this->state->errors = $context->getErrors();
            $this->state->outputs = $context->getOutputs()->all();
        }
    }

    /**
     * @return \Generator<string>
     */
    public function stream(RenderContext $context): \Generator
    {
        try {
            $context->mergePartialsCache($this->state->partialsCache);
            $context->mergeOutputs($this->state->outputs);

            yield from $this->root->stream($context);
        } catch (LiquidException $e) {
            $e->templateName = $e->templateName ?? $this->name;
            throw $e;
        } finally {
            $this->state->errors = $context->getErrors();
            $this->state->outputs = $context->getOutputs()->all();
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
}
