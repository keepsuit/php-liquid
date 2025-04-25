<?php

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;

test('default environment is static', function () {
    $env1 = Environment::default();
    $env2 = Environment::default();

    expect($env1)->toBe($env2);
});

test('default environment has standard tags registered', function () {
    $env = Environment::default();

    $tags = $env->tagRegistry->all();

    expect($tags)->toHaveCount(17)
        ->toHaveKey('assign')
        ->toHaveKey('break')
        ->toHaveKey('capture')
        ->toHaveKey('case')
        ->toHaveKey('cycle')
        ->toHaveKey('decrement')
        ->toHaveKey('doc')
        ->toHaveKey('echo')
        ->toHaveKey('for')
        ->toHaveKey('ifchanged')
        ->toHaveKey('if')
        ->toHaveKey('increment')
        ->toHaveKey('liquid')
        ->toHaveKey('render')
        ->toHaveKey('tablerow')
        ->toHaveKey('unless');
});

test('default environment has standard filters registered', function () {
    $env = Environment::default();

    $filters = $env->filterRegistry->all();

    expect($filters)
        ->toContain('abs')
        ->toContain('append')
        ->toContain('at_least')
        ->toContain('at_most')
        ->toContain('capitalize')
        ->toContain('ceil')
        ->toContain('date')
        ->toContain('default')
        ->toContain('divided_by')
        ->toContain('downcase')
        ->toContain('escape')
        ->toContain('escape_once')
        ->toContain('first')
        ->toContain('floor')
        ->toContain('join')
        ->toContain('last');
});

test('standard extension can be removed', function () {
    $environment = EnvironmentFactory::new()->build();

    expect($environment)
        ->getExtensions()->toHaveCount(1)
        ->tagRegistry->all()->toBeGreaterThan(0)
        ->filterRegistry->all()->toBeGreaterThan(0);

    $environment->removeExtension(\Keepsuit\Liquid\Extensions\StandardExtension::class);

    expect($environment)
        ->getExtensions()->toHaveCount(0)
        ->tagRegistry->all()->toHaveCount(0)
        ->filterRegistry->all()->toHaveCount(0);
});

test('add extension', function () {
    $environment = EnvironmentFactory::new()->build();

    $environment->addExtension(new \Keepsuit\Liquid\Tests\Stubs\StubExtension);

    expect($environment)
        ->getExtensions()->toHaveCount(2)
        ->getNodeVisitors()->toHaveCount(1)
        ->getNodeVisitors()->{0}->toBeInstanceOf(\Keepsuit\Liquid\Tests\Stubs\StubNodeVisitor::class)
        ->getRegisters()->toHaveKey('test');
});

test('remove extension', function () {
    $environment = EnvironmentFactory::new()->build();

    $environment->addExtension(new \Keepsuit\Liquid\Tests\Stubs\StubExtension);
    expect($environment)->getExtensions()->toHaveCount(2);

    $environment->removeExtension(\Keepsuit\Liquid\Tests\Stubs\StubExtension::class);

    expect($environment)
        ->getExtensions()->toHaveCount(1)
        ->getNodeVisitors()->toHaveCount(0)
        ->getRegisters()->not->toHaveKey('test');
});
