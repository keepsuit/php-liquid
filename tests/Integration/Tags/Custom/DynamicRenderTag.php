<?php

use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

describe('rendering with template backends', function () {
    test('dynamically template name', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem(new StubFileSystem(partials: ['snippet' => 'echo']))
            ->setRethrowErrors(true)->build();

        $environment->tagRegistry->register(\Keepsuit\Liquid\Tags\Custom\DynamicRenderTag::class);

        expect($environment->tagRegistry->get('render'))
            ->toBe(\Keepsuit\Liquid\Tags\Custom\DynamicRenderTag::class);

        $template = testParseString($environment, "{% assign name = 'snippet' %}{% render name %}");

        expect($template->render($environment->newRenderContext()))->toBe('echo');
    });
})->with('template backends');
