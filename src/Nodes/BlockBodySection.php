<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\Str;
use Keepsuit\Liquid\Tag;

class BlockBodySection implements CanBeRendered
{
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
        $context->resourceLimits->incrementRenderScore(count($this->nodeList));

        $output = '';

        foreach ($this->nodeList as $node) {
            if (is_string($node)) {
                $output .= $node;

                continue;
            }

            try {
                if ($node instanceof Tag) {
                    $node->ensureTagIsEnabled($context);
                }

                $output .= $this->renderNode($context, $node);
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

    protected function renderNode(Context $context, Variable|Tag $node): string
    {
        if ($context->getProfiler() !== null) {
            return $context->getProfiler()->profileNode(
                templateName: $context->getTemplateName(),
                renderFunction: fn () => $node->render($context),
                code: $node->raw(),
                lineNumber: $node->lineNumber,
            );
        }

        return $node->render($context);
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
