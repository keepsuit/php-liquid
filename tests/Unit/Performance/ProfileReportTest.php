<?php

use Keepsuit\Liquid\Performance\ProfileReport;
use Keepsuit\Liquid\Profiler\Profile;
use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Profiler\ProfileType;

test('it exports the profiler tree with self duration', function () {
    $profiler = new Profiler;

    $profiler->enter(new Profile(ProfileType::Template, 'collection'));
    $profiler->enter(new Profile(ProfileType::Variable, 'product.title'));
    $profiler->leave();
    $profiler->leave();

    $report = ProfileReport::fromProfiler($profiler);
    /** @var array<string, mixed> $rootProfile */
    $rootProfile = $report['profiles'][0];
    /** @var list<array<string, mixed>> $children */
    $children = $rootProfile['children'];

    expect($report)
        ->toHaveKeys(['schema_version', 'generated_at', 'php_version', 'profiles'])
        ->and($report['schema_version'])->toBe(1)
        ->and($report['profiles'])->toHaveCount(1)
        ->and($rootProfile)
        ->toMatchArray([
            'type' => 'template',
            'name' => 'collection',
        ])
        ->and($children)
        ->toHaveCount(1)
        ->and($children[0])
        ->toMatchArray([
            'type' => 'variable',
            'name' => 'product.title',
            'children' => [],
        ])
        ->and($rootProfile['duration'])->toBeGreaterThanOrEqual(0)
        ->and($rootProfile['self_duration'])->toBeGreaterThanOrEqual(0)
        ->and($rootProfile['memory_usage'])->toBeInt()
        ->and($rootProfile['peak_memory_usage'])->toBeInt();
});
