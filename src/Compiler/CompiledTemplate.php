<?php

namespace Keepsuit\Liquid\Compiler;

use Closure;
use Generator;
use Keepsuit\Liquid\AbstractTemplate;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Template;
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
        try {
            $this->prepareContext($context);
            $output = $this->renderCompiledString($context);

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
     * Older artifacts only provide the generator method. New artifacts override
     * this with a string path that avoids per-node generator allocation.
     */
    protected function renderCompiledString(RenderContext $context): string
    {
        $output = '';

        foreach ($this->renderCompiled($context) as $chunk) {
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

            if ($context->isPartial()) {
                foreach ($this->renderCompiled($context) as $chunk) {
                    yield (string) $chunk;
                }

                return;
            }

            $context->resourceLimits->resetStreamWriteScore();

            foreach ($this->renderCompiled($context) as $chunk) {
                $chunk = (string) $chunk;
                $context->resourceLimits->incrementStreamWriteScore($chunk);
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
     * handling policy. The generated closure is only invoked while this method
     * owns the node-level error boundary.
     *
     * @param  (Closure(): iterable<string>)|Generator<string>  $node
     * @return \Generator<string>
     */
    protected function yieldNode(RenderContext $context, ?int $lineNumber, Closure|Generator $node): \Generator
    {
        try {
            foreach ($node instanceof Closure ? $node() : $node as $chunk) {
                yield (string) $chunk;
            }
        } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
            $context->handleError($exception, $lineNumber);
        } catch (Throwable $exception) {
            yield $context->handleError($exception, $lineNumber);
        }
    }

    protected function incrementCompiledRenderScore(RenderContext $context, int $renderScore): void
    {
        $context->resourceLimits->incrementRenderScore($renderScore);
    }

    /**
     * Stream a statically compiled render tag while retaining Liquid's partial
     * isolation and runtime partial lookup semantics.
     *
     * @param  array<string,mixed>  $attributes
     * @return \Generator<string>
     */
    protected function yieldPartial(
        RenderContext $context,
        string $templateName,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): \Generator {
        $partial = $context->loadPartial($templateName);

        yield from $partial->stream($this->partialContext($context, $partial, $variable, $aliasName, $attributes));
    }

    /**
     * @param  array<string,mixed>  $attributes
     */
    protected function renderPartial(
        RenderContext $context,
        string $templateName,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): string {
        $partial = $context->loadPartial($templateName);

        return $partial->render($this->partialContext($context, $partial, $variable, $aliasName, $attributes));
    }

    /**
     * @param  array<string,mixed>  $attributes
     */
    private function partialContext(
        RenderContext $context,
        Template $partial,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): RenderContext {
        $partialName = $partial->name() ?? '';

        $contextVariableName = $aliasName ?? Arr::last(explode('/', $partialName));
        assert(is_string($contextVariableName));

        $value = $variable ? $context->evaluate($variable) : null;
        $partialContext = $context->newIsolatedSubContext($partialName);
        $partialContext->set($contextVariableName, $value);

        foreach ($attributes as $key => $value) {
            $partialContext->set($key, $context->evaluate($value));
        }

        return $partialContext;
    }

    abstract public function name(): ?string;

    abstract protected function renderCompiled(RenderContext $context): iterable;
}
