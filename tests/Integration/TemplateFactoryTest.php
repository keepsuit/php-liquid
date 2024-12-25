<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('parse template from string', function () {
    $environment = EnvironmentFactory::new()
        ->build();

    $template = $environment->parseString('Hello World', 'foo');

    expect($template->name)->toBe('foo');
    expect($template->render($environment->newRenderContext()))->toBe('Hello World');
});

test('parse template from file', function () {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem([
            'foo' => 'Hello World',
        ]))
        ->build();

    $template = $environment->parseTemplate('foo');

    expect($template->name)->toBe('foo');
    expect($template->render($environment->newRenderContext()))->toBe('Hello World');
});
