<?php

namespace Keepsuit\Liquid\Profiler;

use Keepsuit\Liquid\Nodes\Node;

class Timing
{
    public readonly ?int $lineNumber;

    protected ?int $startTime = null;

    protected ?int $totalTime = null;

    protected ?int $selfTime = null;

    /**
     * @var array<Timing>
     */
    protected array $children = [];

    public function __construct(
        public readonly Node $node,
        public readonly ?string $templateName = null,
    ) {
        $this->lineNumber = $node->lineNumber();
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
     * @param  \Closure(): string  $renderFunction
     */
    public function measure(\Closure $renderFunction): string
    {
        if ($this->startTime !== null) {
            throw new \RuntimeException('Timing::measure() called while already measuring');
        }

        $this->startTime = $this->time();

        try {
            $output = $renderFunction();
        } finally {
            $this->totalTime = $this->time() - $this->startTime;
        }

        return $output;
    }

    public function addChild(Timing $timing): void
    {
        $this->children[] = $timing;
    }

    protected function time(): int
    {
        $time = hrtime(true);
        assert(is_int($time));

        return $time;
    }
}
