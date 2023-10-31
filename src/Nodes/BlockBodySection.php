<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\GeneratorToString;
use Keepsuit\Liquid\Support\Str;
use Keepsuit\Liquid\Tag;

class BlockBodySection implements CanBeRendered
{
    use GeneratorToString;

    public function __construct(
        protected ?BlockBodySectionDelimiter $start = null,
        protected ?BlockBodySectionDelimiter $end = null,
        /** @var array<Tag|Variable|string> */
        protected array $nodeList = [],
    ) {
    }

    public function startDelimiter(): ?BlockBodySectionDelimiter
    {
        return $this->start;
    }

    public function endDelimiter(): ?BlockBodySectionDelimiter
    {
        return $this->end;
    }

    /**
     * @return array<Tag|Variable|string>
     */
    public function nodeList(): array
    {
        return $this->nodeList;
    }

    public function setStart(?BlockBodySectionDelimiter $start): BlockBodySection
    {
        $this->start = $start;

        return $this;
    }

    public function setEnd(?BlockBodySectionDelimiter $end): BlockBodySection
    {
        $this->end = $end;

        return $this;
    }

    public function pushNode(Variable|Tag|string $node): BlockBodySection
    {
        $this->nodeList[] = $node;

        return $this;
    }

    /**
     * @param  array<Tag|Variable|string>  $nodeList
     */
    public function setNodeList(array $nodeList): BlockBodySection
    {
        $this->nodeList = $nodeList;

        return $this;
    }

    /**
     * @throws LiquidException
     */
    public function render(Context $context): string
    {
        return $this->generatorToString($this->renderAsync($context));
    }

    /**
     * @return \Generator<string>
     *
     * @throws LiquidException
     */
    public function renderAsync(Context $context): \Generator
    {
        $context->resourceLimits->incrementRenderScore(count($this->nodeList));

        foreach ($this->nodeList as $node) {
            if (is_string($node)) {
                yield $node;

                continue;
            }

            try {
                if ($node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }

                yield from $this->renderNode($context, $node);
            } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
                $context->handleError($exception, $node->lineNumber);
            } catch (\Throwable $exception) {
                yield $context->handleError($exception, $node->lineNumber);
            }

            if ($context->hasInterrupt()) {
                break;
            }
        }
    }

    /**
     * @return \Generator<string>
     */
    protected function renderNode(Context $context, Variable|Tag $node): \Generator
    {
        if ($context->getProfiler() !== null) {
            yield $context->getProfiler()->profileNode(
                templateName: $context->getTemplateName(),
                renderFunction: fn () => $node->render($context),
                code: $node->raw(),
                lineNumber: $node->lineNumber,
            );

            return;
        }

        yield from $node->renderAsync($context);
    }

    public function blank(): bool
    {
        foreach ($this->nodeList as $node) {
            if (is_string($node) && Str::blank($node)) {
                continue;
            }

            if (is_string($node) || $node instanceof Variable) {
                return false;
            }

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

        $this->nodeList = array_filter($this->nodeList, fn ($node) => ! is_string($node));
    }
}
