<?php

namespace Keepsuit\Liquid\Profiler;

class ProfileSnapshot
{
    public function __construct(
        public readonly float $time,
        public readonly int $memory,
        public readonly int $peakMemory,
    ) {}

    public static function record(): ProfileSnapshot
    {
        return new ProfileSnapshot(
            time: microtime(true),
            memory: memory_get_usage(),
            peakMemory: memory_get_peak_usage(),
        );
    }
}
