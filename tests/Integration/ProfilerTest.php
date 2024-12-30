<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Extensions\ProfilerExtension;
use Keepsuit\Liquid\Profiler\Profile;
use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Profiler\ProfileType;
use Keepsuit\Liquid\Tests\Stubs\ProfilingFileSystem;
use Keepsuit\Liquid\Tests\Stubs\SleepTag;

test('profiling can be enabled with extension', function () {
    $environment = EnvironmentFactory::new()->build();
    $template = $environment->parseString("{{ 'a string' | upcase }}");

    $template->render($context = $environment->newRenderContext());
    expect($context->getRegister('profiler'))->toBeNull();

    $environment->addExtension(new ProfilerExtension($profiler = new Profiler));
    $template->render($context = $environment->newRenderContext());
    expect($context->getRegister('profiler'))->toBe($profiler);
});

test('simple profiling', function () {
    $profile = profileTemplate("{{ 'a string' | upcase }}");

    expect($profile)
        ->type->toBe(ProfileType::Template)
        ->name->toBe('template')
        ->getChildren()->toHaveCount(1);

    expect($profile->getChildren()[0])
        ->type->toBe(ProfileType::Variable)
        ->name->toBe('a string');
});

test('profiler ignore raw strings', function () {
    $profile = profileTemplate("This is raw string\nstuff\nNewline");

    expect($profile->getChildren())
        ->toHaveCount(0);
});

test('profile render tag', function () {
    $profile = profileTemplate("{% render 'a_template' %}");

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

test('profile rendering time', function () {
    $profile = profileTemplate("{% render 'a_template' %}");

    expect($profile->getDuration())->toBeGreaterThan(0);

    expect($profile->getStartTime())->toBeLessThan($profile->getEndTime());

    expect($profile->getDuration())->toBeGreaterThan($profile->getChildren()[0]->getDuration());
});

test('profiling multiple renders', function () {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new ProfilingFileSystem)
        ->registerTag(SleepTag::class)
        ->addExtension(new ProfilerExtension($profiler = new Profiler, tags: true, variables: true))
        ->build();

    $context = $environment->newRenderContext();
    $template = $environment->parseString('{% sleep 0.001 %}');

    invade($context)->templateName = 'index';
    $template->render($context);
    expect($profiler->getProfiles())->toHaveCount(1);
    $firstRenderProfile = $profiler->getProfiles()[0];

    invade($context)->templateName = 'layout';
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

test('profiling supports multiple templates', function () {
    $profile = profileTemplate("{{ 'a string' | upcase }}\n{% render 'a_template' %}\n{% render 'b_template' %}");

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

test('profiling supports rendering the same partial multiple times', function () {
    $profile = profileTemplate("{{ 'a string' | upcase }}\n{% render 'a_template' %}\n{% render 'a_template' %}");

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

test('profiling marks children of if blocks', function () {
    $profile = profileTemplate('{% if true %} {% increment test %} {{ test }} {% endif %}');

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

test('profiling marks children of for blocks', function () {
    $profile = profileTemplate('{% for item in collection %} {{ item }} {% endfor %}', [
        'collection' => ['one', 'two'],
    ]);

    expect($profile->getChildren())
        ->toHaveCount(1)
        ->{0}->type->toBe(ProfileType::Tag)
        ->{0}->name->toBe('for')
        ->{0}->getChildren()->toHaveCount(2);

    expect($profile->getChildren()[0]->getChildren())
        ->{1}->type->toBe(ProfileType::Variable)
        ->{1}->name->toBe('item');
});

test('profiling support self duration', function () {
    $profile = profileTemplate('{% for item in collection %} {% sleep item %} {% endfor %}', [
        'collection' => [0.001, 0.002],
    ]);

    $node = $profile->getChildren()[0];
    $leaf = $node->getChildren()[0];

    expect($leaf->getSelfDuration())->toBeGreaterThan(0);
    expect($node->getSelfDuration())->toBeLessThanOrEqual($node->getDuration() - $leaf->getDuration());
});

test('profiling support duration', function () {
    $profile = profileTemplate('{% if true %} {% sleep 0.001 %} {% endif %}');

    expect($profile->getDuration())->toBeGreaterThan(0);
    expect($profile->getChildren()[0]->getDuration())->toBeGreaterThan(0);
});

function profileTemplate(string $source, array $assigns = []): Profile
{
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new ProfilingFileSystem)
        ->registerTag(SleepTag::class)
        ->addExtension(new ProfilerExtension($profiler = new Profiler, tags: true, variables: true))
        ->build();

    $template = $environment->parseString($source);

    $context = $environment->newRenderContext(
        staticData: $assigns,
    );
    $template->render($context);

    return $profiler->getProfiles()[0];
}
