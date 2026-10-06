<?php

use Keepsuit\Liquid\Compiler\CompiledTemplate;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\ParsedTemplate;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

describe('template helpers with each backend', function () {
    test('the helper returns a normal factory for customization before building', function (bool $compiled) {
        $configured = testEnvironmentFactory($compiled)->setStrictFilters(true);

        expect($configured)->toBeInstanceOf(EnvironmentFactory::class);

        $environment = $configured
            ->setFilesystem(new StubFileSystem(['main' => '{{ missing }}']))
            ->setStrictVariables(true)
            ->setRethrowErrors(true)
            ->build();
        $template = $environment->parseTemplate('main');

        expect($environment->templatesCache->get('main'))->toBe($template)
            ->toBeInstanceOf($compiled ? CompiledTemplate::class : ParsedTemplate::class);
        expect(fn () => $template->render($environment->newRenderContext()))
            ->toThrow(\Keepsuit\Liquid\Exceptions\UndefinedVariableException::class);
        expect(fn () => testParseString($environment, '{{ 1 | missing }}')->render($environment->newRenderContext()))
            ->toThrow(\Keepsuit\Liquid\Exceptions\UndefinedFilterException::class);
    });

    test('template helpers use the selected backend', function (bool $compiled) {
        $expected = $compiled ? CompiledTemplate::class : ParsedTemplate::class;
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem(new StubFileSystem([
                'outer' => "{% render 'inner' %}",
                'inner' => '{{ value }}',
            ]))->build();
        $template = parseTemplate("{% render 'outer', value: 'hello' %}", $environment, compiled: $compiled);

        expect($template)->toBeInstanceOf($expected);
        expect($template->render($environment->newRenderContext(staticData: ['value' => 'hello'])))->toBe('hello');
        expect($environment->templatesCache->get('outer'))->toBeInstanceOf($expected);
        expect($environment->templatesCache->get('inner'))->toBeInstanceOf($expected);
    });

    test('lazy partials use the selected backend on their first render', function (bool $compiled) {
        $expected = $compiled ? CompiledTemplate::class : ParsedTemplate::class;
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem($filesystem = new StubFileSystem(['p' => '{{ value }}']))
            ->registerTag(\Keepsuit\Liquid\Tags\Custom\DynamicRenderTag::class)
            ->setLazyParsing(true)->build();
        $template = testParseString($environment, "{% assign name = 'p' %}{% render name %}");

        expect($filesystem->fileReadCount)->toBe(0);
        expect(implode('', iterator_to_array($template->stream($environment->newRenderContext(staticData: ['value' => 'hello'])))))
            ->toBe('hello');
        expect($filesystem->fileReadCount)->toBe(1);
        expect($environment->templatesCache->get('p'))->toBeInstanceOf($expected);
    });

    test('named templates use the selected backend on their first parse and from cache', function (bool $compiled) {
        $expected = $compiled ? CompiledTemplate::class : ParsedTemplate::class;
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem($filesystem = new StubFileSystem(['main' => 'hello']))->build();

        expect($environment->parseTemplate('main'))->toBeInstanceOf($expected);
        expect($environment->parseTemplate('main'))->toBeInstanceOf($expected);
        expect($filesystem->fileReadCount)->toBe(1);
    });

    test('the first lazy partial returned by the render context uses the selected backend', function (bool $compiled) {
        $expected = $compiled ? CompiledTemplate::class : ParsedTemplate::class;
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem(new StubFileSystem(['p' => 'hello']))->build();

        expect($environment->newRenderContext()->loadPartial('p'))->toBeInstanceOf($expected);
    });
})->with('template backends');
