<?php

namespace Keepsuit\Liquid\Profiler;

use Keepsuit\Liquid\Exceptions\StandardException;
use Keepsuit\Liquid\Support\Arr;

class Profiler
{
    /**
     * @var array<int,Profile>
     */
    protected array $rootProfiles = [];

    /**
     * @var array<int,Profile>
     */
    protected array $activeProfiles = [];

    public function enter(Profile $profile): void
    {
        if (count($this->activeProfiles) === 0) {
            $this->rootProfiles[] = $profile;
        } else {
            $this->activeProfiles[0]->addChild($profile);
        }

        array_unshift($this->activeProfiles, $profile);
    }

    public function leave(): Profile
    {
        $activeProfile = array_shift($this->activeProfiles);

        if (! $activeProfile instanceof Profile) {
            throw new StandardException('There is no active profile to leave');
        }

        $activeProfile->leave();

        return $activeProfile;
    }

    /**
     * @return Profile[]
     */
    public function getProfiles(): array
    {
        return $this->rootProfiles;
    }

    public function reset(): void
    {
        $this->rootProfiles = [];
        $this->activeProfiles = [];
    }

    public function serialize(): array
    {
        return Arr::map($this->rootProfiles, fn (Profile $profile) => $profile->serialize());
    }

    public function getDuration(): float
    {
        return array_sum(Arr::map($this->rootProfiles, fn (Profile $profile) => $profile->getDuration()));
    }

    public function getStartTime(): float
    {
        if ($this->rootProfiles === []) {
            return 0;
        }

        return $this->rootProfiles[0]->getStartTime();
    }

    public function getEndTime(): float
    {
        if ($this->rootProfiles === []) {
            return 0;
        }

        return $this->rootProfiles[count($this->rootProfiles) - 1]->getEndTime();
    }
}
