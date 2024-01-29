<?php

namespace Keepsuit\Liquid\Profiler;

use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Render\RenderContext;

class Profiler
{
    protected array $rootTimings = [];

    protected int $totalTime = 0;

    protected ?Timing $currentRootTiming = null;

    protected ?Timing $currentTiming = null;

    public function profile(Node $node, RenderContext $context, ?string $templateName): string
    {
        if ($this->currentTiming != null) {
            return $node->render($context);
        }

        try {
            $this->currentRootTiming = null;

            return $this->profileNode($node, $context, $templateName);
        } finally {
            $this->currentTiming = null;

            if ($this->currentRootTiming !== null) {
                $this->rootTimings[] = $this->currentRootTiming;
                $this->totalTime += $this->currentRootTiming->getTotalTime();
            }
        }
    }

    public function profileNode(Node $node, RenderContext $context, ?string $templateName): string
    {
        if ($node instanceof Text) {
            return $node->render($context);
        }

        $timing = new Timing(
            $node,
            templateName: $templateName,
        );

        $this->currentRootTiming ??= $timing;

        $parentTiming = $this->currentTiming;
        $this->currentTiming = $timing;

        $output = $timing->measure(fn () => $node->render($context));

        $parentTiming?->addChild($timing);
        $this->currentTiming = $parentTiming;

        return $output;
    }

    public function getTotalTime(): int
    {
        return $this->totalTime;
    }

    public function getTiming(): ?Timing
    {
        return $this->currentRootTiming;
    }

    /**
     * @return array<Timing>
     */
    public function getAllTimings(): array
    {
        return $this->rootTimings;
    }
}
