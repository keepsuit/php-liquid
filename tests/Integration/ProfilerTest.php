<?php

use Keepsuit\Liquid\Profiler\Profiler;
use Keepsuit\Liquid\Profiler\Timing;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TemplateFactory;
use Keepsuit\Liquid\Tests\Stubs\ProfilingFileSystem;
use Keepsuit\Liquid\Tests\Stubs\SleepTag;

beforeEach(function () {
    $this->templateFactory = TemplateFactory::new()
        ->setFilesystem(new ProfilingFileSystem)
        ->registerTag(SleepTag::class)
        ->setProfile();
});

test('context allows flagging profiling', function () {
    $template = parseTemplate("{{ 'a string' | upcase }}");
    $template->render(new RenderContext);
    expect($template->getProfiler())->toBeNull();

    $template->render(new RenderContext(profile: true));
    expect($template->getProfiler())->toBeInstanceOf(Profiler::class);
});

test('simple profiling', function () {
    $profiler = profileTemplate("{{ 'a string' | upcase }}");

    expect($profiler->getTiming())
        ->toBeInstanceOf(Timing::class)
        ->getChildren()->toHaveCount(1);
});

test('profiler ignore raw strings', function () {
    $profiler = profileTemplate("This is raw string\nstuff\nNewline");

    expect($profiler->getTiming())
        ->getChildren()->toHaveCount(0);
});

test('profiler include line numbers of nodes', function () {
    $profiler = profileTemplate("{{ 'a string' | upcase }}\n{% increment test %}");

    expect($profiler->getTiming())
        ->getChildren()->toHaveCount(2)
        ->getChildren()->{0}->lineNumber->toBe(1)
        ->getChildren()->{1}->lineNumber->toBe(2);
});

test('profile render tag', function () {
    $profiler = profileTemplate("{% render 'a_template' %}");

    expect($profiler->getTiming())
        ->getChildren()->toHaveCount(1);

    $renderChildren = $profiler->getTiming()->getChildren()[0]->getChildren();

    expect($renderChildren)
        ->toHaveCount(2)
        ->{0}->lineNumber->toBe(1)
        ->{1}->lineNumber->toBe(2);

    foreach ($renderChildren as $child) {
        expect($child->templateName)->toBe('a_template');
    }
});

test('profile rendering time', function () {
    $profiler = profileTemplate("{% render 'a_template' %}");

    expect($profiler->getTotalTime())
        ->toBeGreaterThan(0);

    expect($profiler->getTiming()->getTotalTime())
        ->toBeGreaterThan(0);

    expect($profiler->getTotalTime())
        ->toBeGreaterThan($profiler->getTiming()->getChildren()[0]->getTotalTime());
});

test('profiling multiple renders', function () {
    $context = new RenderContext(profile: true, fileSystem: new ProfilingFileSystem);
    $template = parseTemplate('{% sleep 0.001 %}', factory: $this->templateFactory);

    invade($context)->templateName = 'index';
    $template->render($context);
    $firstRenderTime = $context->getProfiler()->getTotalTime();
    invade($context)->templateName = 'layout';
    $template->render($context);

    $profiler = $context->getProfiler();
    $rootTimings = $profiler->getAllTimings();

    expect($firstRenderTime)
        ->toBeGreaterThan(1_000_000);
    expect($profiler->getTotalTime())->toBeGreaterThan(1_000_000 + $firstRenderTime);

    expect($rootTimings)
        ->toHaveCount(2)
        ->{0}->templateName->toBe('index')
        ->{0}->code->toBeNull()
        ->{1}->templateName->toBe('layout')
        ->{1}->code->toBeNull();

    $rootTotalTiming = array_sum(Arr::map($rootTimings, fn ($timing) => $timing->getTotalTime()));

    expect($rootTotalTiming)
        ->toEqual($profiler->getTotalTime());
});

test('profiling supports multiple templates', function () {
    $profiler = profileTemplate("{{ 'a string' | upcase }}\n{% render 'a_template' %}\n{% render 'b_template' %}");

    expect($profiler->getTiming())
        ->getChildren()->toHaveCount(3);

    $aTemplate = $profiler->getTiming()->getChildren()[1];
    expect($aTemplate->getChildren())->toHaveCount(2);
    foreach ($aTemplate->getChildren() as $child) {
        expect($child->templateName)->toBe('a_template');
    }

    $bTemplate = $profiler->getTiming()->getChildren()[2];
    expect($bTemplate->getChildren())->toHaveCount(2);
    foreach ($bTemplate->getChildren() as $child) {
        expect($child->templateName)->toBe('b_template');
    }
});

test('profiling supports rendering the same partial multiple times', function () {
    $profiler = profileTemplate("{{ 'a string' | upcase }}\n{% render 'a_template' %}\n{% render 'a_template' %}");

    expect($profiler->getTiming())
        ->getChildren()->toHaveCount(3);

    $aTemplate = $profiler->getTiming()->getChildren()[1];
    expect($aTemplate->getChildren())->toHaveCount(2);
    foreach ($aTemplate->getChildren() as $child) {
        expect($child->templateName)->toBe('a_template');
    }

    $aTemplate2 = $profiler->getTiming()->getChildren()[2];
    expect($aTemplate2->getChildren())->toHaveCount(2);
    foreach ($aTemplate2->getChildren() as $child) {
        expect($child->templateName)->toBe('a_template');
    }
});

test('profiling marks children of if blocks', function () {
    $profiler = profileTemplate('{% if true %} {% increment test %} {{ test }} {% endif %}');

    expect($profiler->getTiming())
        ->getChildren()->toHaveCount(1)
        ->getChildren()->{0}->getChildren()->toHaveCount(2);
});

test('profiling marks children of for blocks', function () {
    $profiler = profileTemplate('{% for item in collection %} {{ item }} {% endfor %}', [
        'collection' => ['one', 'two'],
    ]);

    expect($profiler->getTiming())
        ->getChildren()->toHaveCount(1)
        ->getChildren()->{0}->getChildren()->toHaveCount(2);
});

test('profiling support self time', function () {
    $profiler = profileTemplate('{% for item in collection %} {% sleep item %} {% endfor %}', [
        'collection' => [0.001, 0.002],
    ]);

    $node = $profiler->getTiming()->getChildren()[0];
    $leaf = $node->getChildren()[0];

    expect($leaf->getSelfTime())->toBeGreaterThan(0);
    expect($node->getSelfTime())
        ->toBeLessThanOrEqual($node->getTotalTime() - $leaf->getTotalTime());
});

test('profiling support total time', function () {
    $profiler = profileTemplate('{% if true %} {% sleep 0.001 %} {% endif %}');

    expect($profiler->getTotalTime())->toBeGreaterThan(0);
    expect($profiler->getTiming()->getTotalTime())->toBeGreaterThan(0);
});

function profileTemplate(string $source, array $assigns = []): ?Profiler
{
    /** @var TemplateFactory $factory */
    $factory = test()->templateFactory;
    $template = $factory->setProfile()->parseString($source);
    $template->render($factory->newRenderContext(
        staticEnvironment: $assigns,
    ));

    return $template->getProfiler();
}
