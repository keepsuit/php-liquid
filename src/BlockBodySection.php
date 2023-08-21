<?php

namespace Keepsuit\Liquid;

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
                $output .= $node->render($context);
            } catch (\Throwable $exception) {
                return $output.$context->handleError($exception, $node->lineNumber);
            }

            if ($context->hasInterrupt()) {
                break;
            }
        }

        $context->resourceLimits->incrementWriteScore($output);

        return $output;
    }
}
