<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Render\Context;

class Template
{
    protected TemplateSharedState $state;

    protected ?Profiler $profiler = null;

    public function __construct(
        public readonly Document $root,
        public readonly ?string $name = null,
    ) {
        $this->state = new TemplateSharedState();
    }

    /**
     * @throws LiquidException
     */
    public static function parse(ParseContext $parseContext, string $source, ?string $name = null): Template
    {
        try {
            $tokenizer = $parseContext->newTokenizer($source);
            $root = Document::parse($parseContext, $tokenizer);

            $template = new Template(
                root: $root,
                name: $name,
            );

            if (! $parseContext->isPartial()) {
                $template->state->partialsCache = $parseContext->getPartialsCache();
                $template->state->outputs = $parseContext->getOutputs()->all();
            }

            return $template;
        } catch (LiquidException $e) {
            $e->templateName = $e->templateName ?? $name;
            throw $e;
        }
    }

    /**
     * @throws LiquidException
     */
    public function render(Context $context): string
    {
        $this->profiler = $context->getProfiler();

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

    public function getState(): TemplateSharedState
    {
        return $this->state;
    }

    public function getErrors(): array
    {
        return $this->state->errors;
    }

    public function getProfiler(): ?Profiler
    {
        return $this->profiler;
    }
}
