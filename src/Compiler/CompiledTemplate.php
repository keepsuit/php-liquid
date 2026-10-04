<?php

namespace Keepsuit\Liquid\Compiler;

use Closure;
use Keepsuit\Liquid\AbstractTemplate;
use Keepsuit\Liquid\Contracts\AsLiquidValue;
use Keepsuit\Liquid\Drops\ForLoopDrop;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tags\RenderTag;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplateSharedState;
use Throwable;

/** @phpstan-import-type CompiledLookup from VariableLookup */
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
     * @param  Closure(): iterable<string>  $node
     * @return \Generator<string>
     */
    protected function yieldNode(RenderContext $context, ?int $lineNumber, Closure $node): \Generator
    {
        try {
            foreach ($node() as $chunk) {
                yield (string) $chunk;
            }
        } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
            $context->handleError($exception, $lineNumber);
        } catch (Throwable $exception) {
            yield $context->handleError($exception, $lineNumber);
        }
    }

    protected function compiledErrorOutput(Throwable $exception, string $output): ?string
    {
        // Missing values are recorded and sent to the handler, but its output
        // is suppressed just as it is by the interpreted body's typed catches.
        return $exception instanceof UndefinedVariableException
            || $exception instanceof UndefinedDropMethodException
            || $exception instanceof UndefinedFilterException ? null : $output;
    }

    protected function conditionValue(mixed $value): mixed
    {
        return $value instanceof AsLiquidValue ? $value->toLiquidValue() : $value;
    }

    protected function conditionTruthy(mixed $value): bool
    {
        $value = $value instanceof AsLiquidValue ? $value->toLiquidValue() : $value;

        return $value !== false && $value !== null;
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
     * @return \Generator<string>
     */
    protected function yieldPartialLoop(
        RenderContext $context,
        string $templateName,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): \Generator {
        $partial = $context->loadPartial($templateName);
        $name = $partial->name() ?? '';
        $alias = $aliasName ?? RenderTag::defaultVariableName($name);
        $variable = $variable ? $context->evaluate($variable) : null;
        $values = RenderTag::loopValues($variable, true);

        if ($values === null) {
            yield from $partial->stream($this->partialLoopContext($context, $name, $alias, $variable, $attributes));

            return;
        }

        $loop = new ForLoopDrop($name, count($values));
        foreach ($values as $value) {
            yield from $partial->stream($this->partialLoopContext($context, $name, $alias, $value, $attributes, $loop));
            $loop->increment();
        }
    }

    /** @param array<string,mixed> $attributes */
    protected function renderPartialLoop(
        RenderContext $context,
        string $templateName,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): string {
        $partial = $context->loadPartial($templateName);
        $name = $partial->name() ?? '';
        $alias = $aliasName ?? RenderTag::defaultVariableName($name);
        $variable = $variable ? $context->evaluate($variable) : null;
        $values = RenderTag::loopValues($variable, true);

        if ($values === null) {
            return $partial->render($this->partialLoopContext($context, $name, $alias, $variable, $attributes));
        }

        $loop = new ForLoopDrop($name, count($values));
        $output = '';
        foreach ($values as $value) {
            $output .= $partial->render($this->partialLoopContext($context, $name, $alias, $value, $attributes, $loop));
            $loop->increment();
        }

        return $output;
    }

    /**
     * @param  CompiledLookup  $variable
     * @param  array<string,CompiledLookup>  $attributes
     */
    protected function yieldPartialWithLookups(
        RenderContext $context,
        string $templateName,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): \Generator {
        $partial = $context->loadPartial($templateName);

        yield from $partial->stream($this->partialContextWithLookups($context, $partial, $variable, $aliasName, $attributes));
    }

    /**
     * @param  CompiledLookup  $variable
     * @param  array<string,CompiledLookup>  $attributes
     */
    protected function renderPartialWithLookups(
        RenderContext $context,
        string $templateName,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): string {
        $partial = $context->loadPartial($templateName);

        return $partial->render($this->partialContextWithLookups($context, $partial, $variable, $aliasName, $attributes));
    }

    /**
     * @param  CompiledLookup  $variable
     * @param  array<string,CompiledLookup>  $attributes
     */
    protected function yieldPartialLoopWithLookups(
        RenderContext $context,
        string $templateName,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): \Generator {
        $partial = $context->loadPartial($templateName);
        $name = $partial->name() ?? '';
        $alias = $aliasName ?? RenderTag::defaultVariableName($name);
        $variable = $variable ? VariableLookup::evaluateDescriptor($context, $variable) : null;
        $values = RenderTag::loopValues($variable, true);

        if ($values === null) {
            yield from $partial->stream($this->partialLoopContextWithLookups($context, $name, $alias, $variable, $attributes));

            return;
        }

        $loop = new ForLoopDrop($name, count($values));
        foreach ($values as $value) {
            yield from $partial->stream($this->partialLoopContextWithLookups($context, $name, $alias, $value, $attributes, $loop));
            $loop->increment();
        }
    }

    /**
     * @param  CompiledLookup  $variable
     * @param  array<string,CompiledLookup>  $attributes
     */
    protected function renderPartialLoopWithLookups(
        RenderContext $context,
        string $templateName,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): string {
        $partial = $context->loadPartial($templateName);
        $name = $partial->name() ?? '';
        $alias = $aliasName ?? RenderTag::defaultVariableName($name);
        $variable = $variable ? VariableLookup::evaluateDescriptor($context, $variable) : null;
        $values = RenderTag::loopValues($variable, true);

        if ($values === null) {
            return $partial->render($this->partialLoopContextWithLookups($context, $name, $alias, $variable, $attributes));
        }

        $loop = new ForLoopDrop($name, count($values));
        $output = '';
        foreach ($values as $value) {
            $output .= $partial->render($this->partialLoopContextWithLookups($context, $name, $alias, $value, $attributes, $loop));
            $loop->increment();
        }

        return $output;
    }

    /** @param array<string,mixed> $attributes */
    private function partialLoopContext(
        RenderContext $context,
        string $name,
        string $alias,
        mixed $value,
        array $attributes,
        ?ForLoopDrop $loop = null,
    ): RenderContext {
        $partialContext = $context->newIsolatedSubContext($name);
        if ($loop !== null && $alias !== 'forloop') {
            $partialContext->set('forloop', $loop);
        }
        $partialContext->set($alias, $value);

        foreach ($attributes as $key => $expression) {
            $partialContext->set($key, $context->evaluate($expression));
        }

        return $partialContext;
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

        $contextVariableName = $aliasName ?? RenderTag::defaultVariableName($partialName);

        $value = $variable ? $context->evaluate($variable) : null;
        $partialContext = $context->newIsolatedSubContext($partialName);
        $partialContext->set($contextVariableName, $value);

        foreach ($attributes as $key => $value) {
            $partialContext->set($key, $context->evaluate($value));
        }

        return $partialContext;
    }

    /**
     * @param  array<string,CompiledLookup>  $attributes
     */
    private function partialLoopContextWithLookups(
        RenderContext $context,
        string $name,
        string $alias,
        mixed $value,
        array $attributes,
        ?ForLoopDrop $loop = null,
    ): RenderContext {
        $partialContext = $context->newIsolatedSubContext($name);
        if ($loop !== null && $alias !== 'forloop') {
            $partialContext->set('forloop', $loop);
        }
        $partialContext->set($alias, $value);

        foreach ($attributes as $key => $expression) {
            $partialContext->set($key, VariableLookup::evaluateDescriptor($context, $expression));
        }

        return $partialContext;
    }

    /**
     * @param  CompiledLookup  $variable
     * @param  array<string,CompiledLookup>  $attributes
     */
    private function partialContextWithLookups(
        RenderContext $context,
        Template $partial,
        mixed $variable,
        ?string $aliasName,
        array $attributes,
    ): RenderContext {
        $partialName = $partial->name() ?? '';

        $contextVariableName = $aliasName ?? RenderTag::defaultVariableName($partialName);

        $value = $variable ? VariableLookup::evaluateDescriptor($context, $variable) : null;
        $partialContext = $context->newIsolatedSubContext($partialName);
        $partialContext->set($contextVariableName, $value);

        foreach ($attributes as $key => $value) {
            $partialContext->set($key, VariableLookup::evaluateDescriptor($context, $value));
        }

        return $partialContext;
    }

    abstract public function name(): ?string;

    abstract protected function renderCompiled(RenderContext $context): iterable;

    abstract protected function renderCompiledString(RenderContext $context): string;
}
