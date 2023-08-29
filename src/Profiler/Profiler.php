<?php

namespace Keepsuit\Liquid\Profiler;

use Closure;

class Profiler
{
    protected array $rootTimings = [];

    protected int $totalTime = 0;

    protected ?Timing $currentRootTiming = null;

    protected ?Timing $currentTiming = null;

    /**
     * @param  Closure(): string  $renderFunction
     */
    public function profile(?string $templateName, Closure $renderFunction): string
    {
        if ($this->currentTiming != null) {
            return $renderFunction();
        }

        try {
            $this->currentRootTiming = null;

            return $this->profileNode($templateName, $renderFunction);
        } finally {
            $this->currentTiming = null;

            if ($this->currentRootTiming !== null) {
                $this->rootTimings[] = $this->currentRootTiming;
                $this->totalTime += $this->currentRootTiming->getTotalTime();
            }
        }
    }

    /**
     * @param  Closure(): string  $renderFunction
     */
    public function profileNode(?string $templateName, Closure $renderFunction, string $code = null, int $lineNumber = null): string
    {
        $timing = new Timing(
            templateName: $templateName,
            code: $code,
            lineNumber: $lineNumber,
        );

        $this->currentRootTiming ??= $timing;

        $parentTiming = $this->currentTiming;
        $this->currentTiming = $timing;

        $output = $timing->measure($renderFunction);

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
