<?php

use Keepsuit\Liquid\Extensions\ProfilerExtension;
use Keepsuit\Liquid\Profiler\Profile;
use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Profiler\ProfileType;
use Keepsuit\Liquid\Tests\Stubs\ProfilingFileSystem;
use Keepsuit\Liquid\Tests\Stubs\SleepTag;

describe('rendering with template backends', function () {
    test('profiling can be enabled with extension', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)->build();
        $template = testParseString($environment, "{{ 'a string' | upcase }}");

        $template->render($context = $environment->newRenderContext());
        expect($context->getRegister('profiler'))->toBeNull();

        $environment->addExtension(new ProfilerExtension($profiler = new Profiler));
        $template->render($context = $environment->newRenderContext());
        expect($context->getRegister('profiler'))->toBe($profiler);
    });

    test('simple profiling', function (bool $compiled) {
        $profile = profileTemplate("{{ 'a string' | upcase }}", compiled: $compiled);

        expect($profile)
            ->type->toBe(ProfileType::Template)
            ->name->toBe('template')
            ->getChildren()->toHaveCount(1);

        expect($profile->getChildren()[0])
            ->type->toBe(ProfileType::Variable)
            ->name->toBe('a string');
    });

    test('profiler ignore raw strings', function (bool $compiled) {
        $profile = profileTemplate("This is raw string\nstuff\nNewline", compiled: $compiled);

        expect($profile->getChildren())
            ->toHaveCount(0);
    });

    test('profile render tag', function (bool $compiled) {
        $profile = profileTemplate("{% render 'a_template' %}", compiled: $compiled);

        expect($profile)
            ->getChildren()->toHaveCount(1);

        $renderTag = $profile->getChildren()[0];

        expect($renderTag)
            ->type->toBe(ProfileType::Tag)
            ->name->toBe('render')
            ->getChildren()->toHaveCount(1);

        expect($renderTag->getChildren()[0])
            ->type->toBe(ProfileType::Template)
            ->name->toBe('a_template')
            ->getChildren()->toHaveCount(2)
            ->getChildren()->{0}->type->toBe(ProfileType::Tag)
            ->getChildren()->{1}->type->toBe(ProfileType::Variable);
    });

    test('profile rendering time', function (bool $compiled) {
        $profile = profileTemplate("{% render 'a_template' %}", compiled: $compiled);

        expect($profile->getDuration())->toBeGreaterThan(0);

        expect($profile->getStartTime())->toBeLessThan($profile->getEndTime());

        expect($profile->getDuration())->toBeGreaterThan($profile->getChildren()[0]->getDuration());
    });

    test('profiling multiple renders', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem(new ProfilingFileSystem)
            ->registerTag(SleepTag::class)
            ->addExtension(new ProfilerExtension($profiler = new Profiler, tags: true, variables: true))->build();

        $context = $environment->newRenderContext();
        $template = testParseString($environment, '{% sleep 0.001 %}', 'index');

        $template->render($context);
        expect($profiler->getProfiles())->toHaveCount(1);
        $firstRenderProfile = $profiler->getProfiles()[0];

        $template = testParseString($environment, '{% sleep 0.001 %}', 'layout');
        $template->render($context);
        expect($profiler->getProfiles())->toHaveCount(2);
        $secondRenderProfile = $profiler->getProfiles()[1];

        expect($firstRenderProfile)
            ->name->toBe('index')
            ->getDuration()->toBeGreaterThan(0.001);

        expect($secondRenderProfile)
            ->name->toBe('layout')
            ->getDuration()->toBeGreaterThan(0.001);

        expect($profiler)
            ->getStartTime()->toBe($firstRenderProfile->getStartTime())
            ->getEndTime()->toBe($secondRenderProfile->getEndTime())
            ->getDuration()->toBe($firstRenderProfile->getDuration() + $secondRenderProfile->getDuration());
    });

    test('profiling supports multiple templates', function (bool $compiled) {
        $profile = profileTemplate("{{ 'a string' | upcase }}\n{% render 'a_template' %}\n{% render 'b_template' %}", compiled: $compiled);

        expect($profile)
            ->getChildren()->toHaveCount(3);

        $renderTagA = $profile->getChildren()[1];
        expect($renderTagA)
            ->type->toBe(ProfileType::Tag)
            ->name->toBe('render')
            ->getChildren()->toHaveCount(1)
            ->getChildren()->{0}->type->toBe(ProfileType::Template)
            ->getChildren()->{0}->name->toBe('a_template');

        $renderTagB = $profile->getChildren()[2];
        expect($renderTagB)
            ->type->toBe(ProfileType::Tag)
            ->name->toBe('render')
            ->getChildren()->toHaveCount(1)
            ->getChildren()->{0}->type->toBe(ProfileType::Template)
            ->getChildren()->{0}->name->toBe('b_template');
    });

    test('profiling supports rendering the same partial multiple times', function (bool $compiled) {
        $profile = profileTemplate("{{ 'a string' | upcase }}\n{% render 'a_template' %}\n{% render 'a_template' %}", compiled: $compiled);

        $renderTagA = $profile->getChildren()[1];
        expect($renderTagA)
            ->type->toBe(ProfileType::Tag)
            ->name->toBe('render')
            ->getChildren()->toHaveCount(1)
            ->getChildren()->{0}->type->toBe(ProfileType::Template)
            ->getChildren()->{0}->name->toBe('a_template');

        $renderTagB = $profile->getChildren()[2];
        expect($renderTagB)
            ->type->toBe(ProfileType::Tag)
            ->name->toBe('render')
            ->getChildren()->toHaveCount(1)
            ->getChildren()->{0}->type->toBe(ProfileType::Template)
            ->getChildren()->{0}->name->toBe('a_template');
    });

    test('profiling marks children of if blocks', function (bool $compiled) {
        $profile = profileTemplate('{% if true %} {% increment test %} {{ test }} {% endif %}', compiled: $compiled);

        expect($profile->getChildren())
            ->toHaveCount(1)
            ->{0}->type->toBe(ProfileType::Tag)
            ->{0}->name->toBe('if')
            ->{0}->getChildren()->toHaveCount(2);

        expect($profile->getChildren()[0]->getChildren())
            ->{0}->type->toBe(ProfileType::Tag)
            ->{0}->name->toBe('increment')
            ->{1}->type->toBe(ProfileType::Variable)
            ->{1}->name->toBe('test');
    });

    test('profiling marks children of for blocks', function (bool $compiled) {
        $profile = profileTemplate('{% for item in collection %} {{ item }} {% endfor %}', [
            'collection' => ['one', 'two'],
        ], compiled: $compiled);

        expect($profile->getChildren())
            ->toHaveCount(1)
            ->{0}->type->toBe(ProfileType::Tag)
            ->{0}->name->toBe('for')
            ->{0}->getChildren()->toHaveCount(2);

        expect($profile->getChildren()[0]->getChildren())
            ->{1}->type->toBe(ProfileType::Variable)
            ->{1}->name->toBe('item');
    });

    test('profiling support self duration', function (bool $compiled) {
        $profile = profileTemplate('{% for item in collection %} {% sleep item %} {% endfor %}', [
            'collection' => [0.001, 0.002],
        ], compiled: $compiled);

        $node = $profile->getChildren()[0];
        $leaf = $node->getChildren()[0];

        expect($leaf->getSelfDuration())->toBeGreaterThan(0);
        expect($node->getSelfDuration())->toBeLessThanOrEqual($node->getDuration() - $leaf->getDuration());
    });

    test('profiling support duration', function (bool $compiled) {
        $profile = profileTemplate('{% if true %} {% sleep 0.001 %} {% endif %}', compiled: $compiled);

        expect($profile->getDuration())->toBeGreaterThan(0);
        expect($profile->getChildren()[0]->getDuration())->toBeGreaterThan(0);
    });
})->with('template backends');

function profileTemplate(string $source, array $assigns = [], bool $compiled = false): Profile
{
    $environment = testEnvironmentFactory($compiled)
        ->setFilesystem(new ProfilingFileSystem)
        ->registerTag(SleepTag::class)
        ->addExtension(new ProfilerExtension($profiler = new Profiler, tags: true, variables: true))->build();

    $template = testParseString($environment, $source);

    $context = $environment->newRenderContext(
        staticData: $assigns,
    );
    $template->render($context);

    return $profiler->getProfiles()[0];
}
