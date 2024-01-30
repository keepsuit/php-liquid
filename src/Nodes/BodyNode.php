<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class BodyNode extends Node implements CanBeStreamed
{
    public function __construct(
        /** @var array<Node> */
        protected array $children = [],
    ) {
    }

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
     * @param  array<Tag|Variable|Text>  $children
     */
    public function setChildren(array $children): BodyNode
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @throws LiquidException
     */
    public function render(RenderContext $context): string
    {
        $context->resourceLimits->incrementRenderScore(count($this->children));

        $output = '';

        foreach ($this->children as $node) {
            try {
                if ($node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }

                $output .= $this->renderChild($context, $node);
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

    public function stream(RenderContext $context): \Generator
    {
        $context->resourceLimits->incrementRenderScore(count($this->children));

        foreach ($this->children as $node) {
            try {
                if ($node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }

                foreach ($this->streamChild($context, $node) as $output) {
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

    protected function renderChild(RenderContext $context, Node $node): string
    {
        if ($context->getProfiler() !== null) {
            return $context->getProfiler()->profileNode(
                node: $node,
                context: $context,
                templateName: $context->getTemplateName(),
            );
        }

        return $node->render($context);
    }

    /**
     * @return \Generator<string>
     */
    public function streamChild(RenderContext $context, Node $node): \Generator
    {
        if ($context->getProfiler() !== null) {
            yield $this->renderChild($context, $node);

            return;
        }

        if ($node instanceof CanBeStreamed) {
            yield from $node->stream($context);

            return;
        }

        yield $node->render($context);
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

    public function parseTreeVisitorChildren(): array
    {
        return $this->children;
    }
}
