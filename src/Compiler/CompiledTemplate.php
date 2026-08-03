<?php

namespace Keepsuit\Liquid\Compiler;

use Generator;
use Keepsuit\Liquid\AbstractTemplate;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
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
        $partialName = $partial->name() ?? '';

        $contextVariableName = $aliasName ?? Arr::last(explode('/', $partialName));
        assert(is_string($contextVariableName));

        $partialContext = $context->newIsolatedSubContext($partialName);
        $partialContext->set($contextVariableName, $context->evaluate($variable));

        foreach ($attributes as $key => $value) {
            $partialContext->set($key, $context->evaluate($value));
        }

        yield from $partial->stream($partialContext);
    }

    abstract public function name(): ?string;

    abstract protected function renderCompiled(RenderContext $context): iterable;
}
