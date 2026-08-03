<?php

namespace Keepsuit\Liquid\Compiler;

use Generator;
use Keepsuit\Liquid\AbstractTemplate;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TemplateSharedState;
use Throwable;

abstract class CompiledTemplate extends AbstractTemplate
{
    public function __construct(TemplateSharedState $state = new TemplateSharedState)
    {
        parent::__construct($state);
    }

    final public function render(RenderContext $context): string
    {
        $output = '';

        foreach ($this->stream($context) as $chunk) {
            $output .= $chunk;
        }

        return $output;
    }

    /**
     * @return \Generator<string>
     */
    final public function stream(RenderContext $context): \Generator
    {
        try {
            $this->prepareContext($context);

            foreach ($this->renderCompiled($context) as $chunk) {
                $chunk = (string) $chunk;
                $context->resourceLimits->incrementWriteScore($chunk);

                yield $chunk;
            }
        } catch (LiquidException $e) {
            $this->attachTemplateName($e);
            throw $e;
        } finally {
            $this->persistContext($context);
        }
    }

    /**
     * Execute one lazily-created compiled node under Liquid's configured error
     * handling policy. The generator is created by the generated template but
     * does not execute until this method iterates it.
     *
     * @param  Generator<string>  $node
     * @return \Generator<string>
     */
    protected function yieldNode(RenderContext $context, ?int $lineNumber, Generator $node): \Generator
    {
        try {
            foreach ($node as $chunk) {
                yield (string) $chunk;
            }
        } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
            $context->handleError($exception, $lineNumber);
        } catch (Throwable $exception) {
            yield $context->handleError($exception, $lineNumber);
        }
    }

    /**
     * Account for a compiled body and forward its chunks unchanged.
     *
     * @param  Generator<string>  $body
     * @return \Generator<string>
     */
    protected function yieldBody(RenderContext $context, int $renderScore, Generator $body): \Generator
    {
        $context->resourceLimits->incrementRenderScore($renderScore);

        foreach ($body as $chunk) {
            yield (string) $chunk;
        }
    }

    /**
     * Collect a compiled body when a runtime tag still owns a string-based
     * render loop.
     *
     * @param  Generator<int, string>  $body
     */
    protected function collectCompiled(RenderContext $context, Generator $body): string
    {
        $output = '';

        foreach ($body as $chunk) {
            $output .= (string) $chunk;
        }

        return $output;
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

    abstract protected function renderCompiled(RenderContext $context): iterable;
}
