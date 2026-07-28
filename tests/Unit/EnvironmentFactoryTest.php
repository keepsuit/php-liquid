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

test('raw body tags include a tag registered after the cache is populated', function () {
    $registry = EnvironmentFactory::new()->build()->tagRegistry;
    $registry->delete('raw');

    expect($registry->rawBodyTags())->not->toHaveKey('raw');

    $registry->register(\Keepsuit\Liquid\Tags\RawTag::class);

    expect($registry->rawBodyTags())->toHaveKey('raw');
});

test('raw body tags exclude a tag deleted after the cache is populated', function () {
    $registry = EnvironmentFactory::new()->build()->tagRegistry;

    expect($registry->rawBodyTags())->toHaveKey('raw');

    $registry->delete('raw');

    expect($registry->rawBodyTags())->not->toHaveKey('raw');
});

test('add extension', function () {
    $environment = EnvironmentFactory::new()
        ->addExtension(new \Keepsuit\Liquid\Tests\Stubs\StubExtension)
        ->build();

    expect($environment)
        ->getExtensions()->toHaveCount(2)
        ->getNodeVisitors()->toHaveCount(1)
        ->getNodeVisitors()->{0}->toBeInstanceOf(\Keepsuit\Liquid\Tests\Stubs\StubNodeVisitor::class)
        ->getRegisters()->toHaveKey('test');
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
        ->setRethrowErrors(true)
        ->setStrictVariables(true)
        ->setStrictFilters(true)
        ->build();

    expect($environment)
        ->defaultRenderContextOptions->rethrowErrors->toBeTrue()
        ->defaultRenderContextOptions->strictVariables->toBeTrue()
        ->defaultRenderContextOptions->strictFilters->toBeTrue();
});
