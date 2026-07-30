<?php

namespace Keepsuit\Liquid\Performance;

use Keepsuit\Liquid\Profiler\Profile;
use Keepsuit\Liquid\Profiler\Profiler;

final class ProfileReport
{
    /**
     * Convert the profiler tree into a stable, machine-readable diagnostic report.
     *
     * Durations are measured in seconds; memory values are byte deltas.
     *
     * @return array{
     *     schema_version: int,
     *     generated_at: string,
     *     php_version: string,
     *     profiles: array<int, array<string, mixed>>
     * }
     */
    public static function fromProfiler(Profiler $profiler): array
    {
        return [
            'schema_version' => 1,
            'generated_at' => date(DATE_ATOM),
            'php_version' => PHP_VERSION,
            'profiles' => array_map(self::profile(...), $profiler->getProfiles()),
        ];
    }

    /**
     * @return array{
     *     type: string,
     *     name: string,
     *     duration: float,
     *     self_duration: float,
     *     memory_usage: int,
     *     peak_memory_usage: int,
     *     children: array<int, array<string, mixed>>
     * }
     */
    private static function profile(Profile $profile): array
    {
        return [
            'type' => $profile->type->value,
            'name' => $profile->name,
            'duration' => $profile->getDuration(),
            'self_duration' => $profile->getSelfDuration(),
            'memory_usage' => $profile->getMemoryUsage(),
            'peak_memory_usage' => $profile->getPeakMemoryUsage(),
            'children' => array_map(self::profile(...), $profile->getChildren()),
        ];
    }
}
