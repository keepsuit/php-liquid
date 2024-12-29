<?php

use Keepsuit\Liquid\EnvironmentFactory;

test('register & delete custom tags', function () {
    $environment = EnvironmentFactory::new()
        ->registerTag(\Keepsuit\Liquid\Tests\Stubs\TestTagBlockTag::class)
        ->build();

    expect($environment->tagRegistry->all())->toHaveKey('testblock');

    $environment->tagRegistry->delete('testblock');

    expect($environment->tagRegistry->all())->not->toHaveKey('testblock');
});

test('add extension', function () {
    $environment = EnvironmentFactory::new()
        ->addExtension(new \Keepsuit\Liquid\Tests\Stubs\StubExtension)
        ->build();

    expect($environment)
        ->getExtensions()->toHaveCount(1)
        ->getExtensionNodeVisitors()->toHaveCount(1)
        ->getExtensionNodeVisitors()->{0}->toBeInstanceOf(\Keepsuit\Liquid\Tests\Stubs\StubNodeVisitor::class)
        ->getExtensionRegisters()->toHaveKey('test');
});

test('get registered tags', function () {
    $environment = EnvironmentFactory::new()
        ->registerTag(\Keepsuit\Liquid\Tests\Stubs\TestTagBlockTag::class)
        ->build();

    expect($environment->tagRegistry->all())->toHaveKey('testblock');
    expect($environment->tagRegistry->all()['testblock'])->toBe(\Keepsuit\Liquid\Tests\Stubs\TestTagBlockTag::class);
});

test('default render options settings', function () {
    $environment = EnvironmentFactory::new()
        ->setRethrowErrors()
        ->setStrictVariables()
        ->setStrictFilters()
        ->build();

    expect($environment)
        ->defaultRenderContextOptions->rethrowErrors->toBeTrue()
        ->defaultRenderContextOptions->strictVariables->toBeTrue()
        ->defaultRenderContextOptions->strictFilters->toBeTrue();
});
