<?php

use Keepsuit\Liquid\Environment;

test('default environment is static', function () {
    $env1 = Environment::default();
    $env2 = Environment::default();

    expect($env1)->toBe($env2);
});

test('default environment has standard tags registered', function () {
    $env = Environment::default();

    $tags = $env->tagRegistry->all();

    expect($tags)->toHaveCount(16)
        ->toHaveKey('assign')
        ->toHaveKey('break')
        ->toHaveKey('capture')
        ->toHaveKey('case')
        ->toHaveKey('cycle')
        ->toHaveKey('decrement')
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

test('add extension', function () {
    $environment = Environment::default();

    $environment->addExtension(new \Keepsuit\Liquid\Tests\Stubs\StubExtension);

    expect($environment)
        ->getExtensions()->toHaveCount(1)
        ->getExtensionNodeVisitors()->toHaveCount(1)
        ->getExtensionNodeVisitors()->{0}->toBeInstanceOf(\Keepsuit\Liquid\Tests\Stubs\StubNodeVisitor::class)
        ->getExtensionRegisters()->toHaveKey('test');
});

test('remove extension', function () {
    $environment = Environment::default();

    $environment->addExtension(new \Keepsuit\Liquid\Tests\Stubs\StubExtension);
    expect($environment)->getExtensions()->toHaveCount(1);

    $environment->removeExtension(\Keepsuit\Liquid\Tests\Stubs\StubExtension::class);

    expect($environment)
        ->getExtensions()->toHaveCount(0)
        ->getExtensionNodeVisitors()->toHaveCount(0)
        ->getExtensionRegisters()->not->toHaveKey('test');
});
