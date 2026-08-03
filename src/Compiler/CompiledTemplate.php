<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\AbstractTemplate;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TemplateSharedState;

abstract class CompiledTemplate extends AbstractTemplate
{
    public function __construct(TemplateSharedState $state = new TemplateSharedState)
    {
        parent::__construct($state);
    }

    final public function render(RenderContext $context): string
    {
        try {
            $this->prepareContext($context);

            return $this->renderCompiled($context);
        } catch (LiquidException $e) {
            $this->attachTemplateName($e);
            throw $e;
        } finally {
            $this->persistContext($context);
        }
    }

    /**
     * A compiled body builds one string, so there is nothing to stream
     * incrementally: streaming it would only add a Generator per nesting level.
     *
     * @return \Generator<string>
     */
    final public function stream(RenderContext $context): \Generator
    {
        yield $this->render($context);
    }

    /**
     * Render a common variable directly while retaining Liquid lookup semantics.
     *
     * @param  array<string|int>  $lookups
     * @param  array<array{0:string,1:array,2:array<string,mixed>}>  $filters
     */
    protected function renderCompiledVariable(RenderContext $context, string $name, array $lookups, array $filters): string
    {
        return Variable::renderParts($context, $name, $lookups, $filters);
    }

    abstract public function name(): ?string;

    abstract protected function renderCompiled(RenderContext $context): string;
}
