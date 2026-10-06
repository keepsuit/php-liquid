<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

describe('rendering with template backends', function () {
    test('parse template from string', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)->build();

        $template = testParseString($environment, 'Hello World', 'foo');

        expect($template->name())->toBe('foo');
        expect($template->render($environment->newRenderContext()))->toBe('Hello World');
    });

    test('parse template from file', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem(new StubFileSystem([
                'foo' => 'Hello World',
            ]))->build();

        $template = $environment->parseTemplate('foo');

        expect($template->name())->toBe('foo');
        expect($template->render($environment->newRenderContext()))->toBe('Hello World');
    });
})->with('template backends');

test('parse template returns the parsed result when the cache does not retain it', function () {
    $cache = new class extends \Keepsuit\Liquid\TemplatesCache\MemoryTemplatesCache
    {
        public function set(string $name, \Keepsuit\Liquid\Template $template): void {}
    };
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem(['foo' => 'Hello World']))
        ->setTemplatesCache($cache)
        ->build();

    $template = $environment->parseTemplate('foo');

    expect($template)->toBeInstanceOf(\Keepsuit\Liquid\ParsedTemplate::class)
        ->and($template->render($environment->newRenderContext()))->toBe('Hello World')
        ->and($cache->get('foo'))->toBeNull();
});
