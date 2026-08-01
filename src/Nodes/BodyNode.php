<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class BodyNode extends Node implements CanBeCompiled, CanBeStreamed
{
    public function __construct(
        /** @var array<Node> */
        protected array $children = [],
    ) {}

    /**
     * @return array<Node>
     */
    public function children(): array
    {
        return $this->children;
    }

    public function pushChild(Node $node): BodyNode
    {
        $this->children[] = $node;

        return $this;
    }

    /**
     * @param  array<Node>  $children
     */
    public function setChildren(array $children): BodyNode
    {
        $this->children = $children;

        return $this;
    }

    /**
     * The body is inlined instead of wrapped in a closure: a closure costs an
     * allocation and a call frame on every render, and a body carries no state
     * that needs its own scope beyond the accumulator.
     *
     * Mirrors render(): Text cannot fail or interrupt, so it needs no guard, and
     * every other child is followed by a bail-out rather than the whole body
     * being wrapped in a per-child hasInterrupt() check.
     */
    public function compile(CompilerContext $context): void
    {
        $lastIndex = count($this->children) - 1;

        $interruptible = false;
        foreach ($this->children as $index => $child) {
            if (! $child instanceof Text && $index !== $lastIndex) {
                $interruptible = true;

                break;
            }
        }

        $parentOutput = $context->outputVariable();
        $output = $context->pushOutputScope();

        $context
            ->write('$context->resourceLimits->incrementRenderScore('.count($this->children).');')
            ->write($output.' = \'\';');

        // A do/while(false) gives the bail-out a target without a closure.
        if ($interruptible) {
            $context->write('do {')->indent();
        }

        foreach ($this->children as $index => $child) {
            $context->subcompile($child);

            if ($child instanceof Text || $index === $lastIndex) {
                continue;
            }

            $context
                ->write('if ($context->hasInterrupt()) {')
                ->indent()
                ->write('break;')
                ->outdent()
                ->write('}');
        }

        if ($interruptible) {
            $context->outdent()->write('} while (false);');
        }

        $context
            ->write('$context->resourceLimits->incrementWriteScore('.$output.');')
            ->write($parentOutput.' .= '.$output.';');

        $context->popOutputScope();
    }

    /**
     * @throws LiquidException
     */
    public function render(RenderContext $context): string
    {
        $context->resourceLimits->incrementRenderScore(count($this->children));

        $output = '';

        foreach ($this->children as $node) {
            // Text is the majority of children and cannot fail or interrupt.
            if ($node instanceof Text) {
                $output .= $node->value;

                continue;
            }

            try {
                if ($node instanceof Disableable && $node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }

                $output .= $node->render($context);
            } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
                $context->handleError($exception, $node->lineNumber);
            } catch (\Throwable $exception) {
                $output .= $context->handleError($exception, $node->lineNumber);
            }

            if ($context->hasInterrupt()) {
                break;
            }
        }

        $context->resourceLimits->incrementWriteScore($output);

        return $output;
    }

    /**
     * @return \Generator<string>
     *
     * @throws LiquidException
     */
    public function stream(RenderContext $context): \Generator
    {
        $context->resourceLimits->incrementRenderScore(count($this->children));

        foreach ($this->children as $node) {
            // Text is the majority of children and cannot fail or interrupt.
            if ($node instanceof Text) {
                $context->resourceLimits->incrementWriteScore($node->value);
                yield $node->value;

                continue;
            }

            try {
                if ($node instanceof Disableable && $node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }

                if ($node instanceof CanBeStreamed) {
                    foreach ($node->stream($context) as $output) {
                        $context->resourceLimits->incrementWriteScore($output);
                        yield $output;
                    }
                } else {
                    $output = $node->render($context);
                    $context->resourceLimits->incrementWriteScore($output);
                    yield $output;
                }
            } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
                $context->handleError($exception, $node->lineNumber);
            } catch (\Throwable $exception) {
                $output = $context->handleError($exception, $node->lineNumber);
                $context->resourceLimits->incrementWriteScore($output);
                yield $output;
            }

            if ($context->hasInterrupt()) {
                break;
            }
        }
    }

    public function blank(): bool
    {
        foreach ($this->children as $node) {
            if ($node->blank()) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function removeBlankStrings(): void
    {
        if (! $this->blank()) {
            throw new \RuntimeException('Cannot remove blank strings from non-blank section');
        }

        $this->children = array_filter($this->children, fn (Node $node) => ! ($node instanceof Text));
    }
}
