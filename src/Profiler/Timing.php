<?php

namespace Keepsuit\Liquid\Profiler;

use Keepsuit\Liquid\Support\GeneratorToString;

class Timing
{
    use GeneratorToString;

    protected ?int $startTime = null;

    protected ?int $totalTime = null;

    protected ?int $selfTime = null;

    /**
     * @var array<Timing>
     */
    protected array $children = [];

    public function __construct(
        public readonly ?string $templateName = null,
        public readonly ?string $code = null,
        public readonly ?int $lineNumber = null,
    ) {
    }

    public function getTotalTime(): int
    {
        if ($this->totalTime === null) {
            throw new \RuntimeException('Timing::getTotalTime() called before timing was complete');
        }

        return $this->totalTime;
    }

    public function getSelfTime(): int
    {
        if ($this->selfTime !== null) {
            return $this->selfTime;
        }

        $totalChildrenTime = 0;
        foreach ($this->children as $child) {
            $totalChildrenTime += $child->getTotalTime();
        }
        $this->selfTime = $this->totalTime - $totalChildrenTime;

        return $this->selfTime;
    }

    /**
     * @return array<Timing>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param  \Closure(): (string|\Generator<string>)  $renderFunction
     */
    public function measure(\Closure $renderFunction): string
    {
        if ($this->startTime !== null) {
            throw new \RuntimeException('Timing::measure() called while already measuring');
        }

        $this->startTime = hrtime(true);

        try {
            $output = $renderFunction();

            $output = $output instanceof \Generator ? $this->generatorToString($output) : $output;
        } finally {
            $this->totalTime = hrtime(true) - $this->startTime;
        }

        return $output;
    }

    public function addChild(Timing $timing): void
    {
        $this->children[] = $timing;
    }
}
