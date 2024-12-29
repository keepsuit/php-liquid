<?php

namespace Keepsuit\Liquid\Profiler;

use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Support\Arr;

class Profile
{
    public readonly string $name;

    protected ProfileSnapshot $start;

    protected ?ProfileSnapshot $end = null;

    protected ?float $selfDuration = null;

    /**
     * @var Profile[]
     */
    protected array $children = [];

    public function __construct(
        public readonly ProfileType $type,
        ?string $name = null,
    ) {
        $this->name = $name ?? $type->value;

        $this->enter();
    }

    protected function enter(): void
    {
        $this->start = ProfileSnapshot::record();

        $this->end = null;
        $this->selfDuration = null;
    }

    public function leave(): void
    {
        $this->end = ProfileSnapshot::record();
    }

    public function getStartTime(): float
    {
        return $this->start->time;
    }

    public function getEndTime(): float
    {
        $this->ensureProfileIsClosed();

        return $this->end->time;
    }

    public function getDuration(): float
    {
        return $this->getEndTime() - $this->getStartTime();
    }

    public function getSelfDuration(): float
    {
        $this->ensureProfileIsClosed();

        if ($this->selfDuration !== null) {
            return $this->selfDuration;
        }

        $totalChildrenTime = 0;
        foreach ($this->children as $child) {
            $totalChildrenTime += $child->getDuration();
        }
        $this->selfDuration = $this->getDuration() - $totalChildrenTime;

        return $this->selfDuration;
    }

    public function getMemoryUsage(): int
    {
        $this->ensureProfileIsClosed();

        return $this->end->memory - $this->start->memory;
    }

    public function getPeakMemoryUsage(): int
    {
        $this->ensureProfileIsClosed();

        return $this->end->peakMemory - $this->start->peakMemory;
    }

    /**
     * @phpstan-assert ProfileSnapshot $this->end
     *
     * @throws StandardException
     */
    protected function ensureProfileIsClosed(): void
    {
        if ($this->end === null) {
            throw new StandardException('Profile has not been closed');
        }
    }

    public function addChild(Profile $profile): static
    {
        if ($this->end !== null) {
            throw new StandardException('Cannot add children to a closed profile');
        }

        $this->children[] = $profile;

        return $this;
    }

    /**
     * @return Profile[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function serialize(): array
    {
        return [
            'type' => $this->type->value,
            'name' => $this->name,
            'start' => $this->getStartTime(),
            'end' => $this->getEndTime(),
            'duration' => $this->getDuration(),
            'memory_usage' => $this->getMemoryUsage(),
            'peak_memory_usage' => $this->getPeakMemoryUsage(),
            'children' => Arr::map($this->children, fn (Profile $profile) => $profile->serialize()),
        ];
    }
}
