<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('dynamically template name', function () {
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem(partials: ['snippet' => 'echo']))
        ->setRethrowErrors(true)
        ->build();

    $environment->tagRegistry->register(\Keepsuit\Liquid\Tags\Custom\DynamicRenderTag::class);

    expect($environment->tagRegistry->get('render'))
        ->toBe(\Keepsuit\Liquid\Tags\Custom\DynamicRenderTag::class);

    $template = $environment->parseString("{% assign name = 'snippet' %}{% render name %}");

    expect($template->render($environment->newRenderContext()))->toBe('echo');
});
