<?php

use Keepsuit\Liquid\TemplateFactory;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('parse template from string', function () {
    $factory = TemplateFactory::new();

    $template = $factory->parseString('Hello World', 'foo');

    expect($template->name)->toBe('foo');
    expect($template->render($factory->newRenderContext()))->toBe('Hello World');
});

test('parse template from file', function () {
    $factory = TemplateFactory::new()
        ->setFilesystem(new StubFileSystem([
            'foo' => 'Hello World',
        ]));

    $template = $factory->parseTemplate('foo');

    expect($template->name)->toBe('foo');
    expect($template->render($factory->newRenderContext()))->toBe('Hello World');
});
