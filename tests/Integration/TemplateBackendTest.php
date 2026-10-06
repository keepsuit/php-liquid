<?php

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('template helpers use the selected backend', function () {
    $expected = getenv('LIQUID_TEST_BACKEND') === 'compiled' ? CompiledTemplate::class : ParsedTemplate::class;
    $environment = testEnvironment(EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem([
            'outer' => "{% render 'inner' %}",
            'inner' => '{{ value }}',
        ]))
        ->build());
    $template = parseTemplate("{% render 'outer', value: 'hello' %}", $environment);

    expect($template)->toBeInstanceOf($expected);
    expect($template->render($environment->newRenderContext(staticData: ['value' => 'hello'])))->toBe('hello');
    expect($environment->templatesCache->get('outer'))->toBeInstanceOf($expected);
    expect($environment->templatesCache->get('inner'))->toBeInstanceOf($expected);
});

test('lazy partials use the selected backend on their first render', function () {
    $expected = getenv('LIQUID_TEST_BACKEND') === 'compiled' ? CompiledTemplate::class : ParsedTemplate::class;
    $environment = testEnvironment(EnvironmentFactory::new()
        ->setFilesystem($filesystem = new StubFileSystem(['p' => '{{ value }}']))
        ->registerTag(\Keepsuit\Liquid\Tags\Custom\DynamicRenderTag::class)
        ->setLazyParsing(true)
        ->build());
    $template = testParseString($environment, "{% assign name = 'p' %}{% render name %}");

    expect($filesystem->fileReadCount)->toBe(0);
    expect(implode('', iterator_to_array($template->stream($environment->newRenderContext(staticData: ['value' => 'hello'])))))
        ->toBe('hello');
    expect($filesystem->fileReadCount)->toBe(1);
    expect($environment->templatesCache->get('p'))->toBeInstanceOf($expected);
});

test('named templates use the selected backend on their first parse and from cache', function () {
    $expected = getenv('LIQUID_TEST_BACKEND') === 'compiled' ? CompiledTemplate::class : ParsedTemplate::class;
    $environment = testEnvironment(EnvironmentFactory::new()
        ->setFilesystem($filesystem = new StubFileSystem(['main' => 'hello']))
        ->build());

    expect($environment->parseTemplate('main'))->toBeInstanceOf($expected);
    expect($environment->parseTemplate('main'))->toBeInstanceOf($expected);
    expect($filesystem->fileReadCount)->toBe(1);
});

test('the first lazy partial returned by the render context uses the selected backend', function () {
    $expected = getenv('LIQUID_TEST_BACKEND') === 'compiled' ? CompiledTemplate::class : ParsedTemplate::class;
    $environment = testEnvironment(EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem(['p' => 'hello']))
        ->build());

    expect($environment->newRenderContext()->loadPartial('p'))->toBeInstanceOf($expected);
});
