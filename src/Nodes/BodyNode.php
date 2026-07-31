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
    private const MAX_BUFFERED_BYTES = 4096;

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

    public function compile(CompilerContext $context): void
    {
        $context
            ->write('$output = \\Keepsuit\\Liquid\\Compiler\\CompiledTemplate::renderCompiledBody(')
            ->indent()
            ->write('$context,')
            ->write('function (\\Keepsuit\\Liquid\\Render\\RenderContext $context): string {')
            ->indent()
            ->write('$output = \'\';');

        foreach ($this->children as $child) {
            $context
                ->write('if (! $context->hasInterrupt()) {')
                ->indent()
                ->subcompile($child)
                ->outdent()
                ->write('}');
        }

        $context
            ->write('return $output;')
            ->outdent()
            ->write('},')
            ->write(count($this->children).',')
            ->outdent()
            ->write(');');
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

        $buffer = '';

        foreach ($this->children as $node) {
            // Text is the majority of children and cannot fail or interrupt.
            if ($node instanceof Text) {
                $buffer .= $node->value;

                continue;
            }

            if (strlen($buffer) >= self::MAX_BUFFERED_BYTES) {
                yield $buffer;
                $buffer = '';
            }

            try {
                if ($node instanceof Disableable && $node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }

                if ($node instanceof CanBeStreamed) {
                    foreach ($node->stream($context) as $output) {
                        $buffer .= $output;

                        if (strlen($buffer) >= self::MAX_BUFFERED_BYTES) {
                            yield $buffer;
                            $buffer = '';
                        }
                    }
                } else {
                    $buffer .= $node->render($context);
                }
            } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
                if ($buffer !== '') {
                    yield $buffer;
                    $buffer = '';
                }

                $context->handleError($exception, $node->lineNumber);
            } catch (\Throwable $exception) {
                if ($buffer !== '') {
                    yield $buffer;
                    $buffer = '';
                }

                $buffer .= $context->handleError($exception, $node->lineNumber);
            }

            if ($context->hasInterrupt()) {
                break;
            }
        }

        if ($buffer !== '') {
            yield $buffer;
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
