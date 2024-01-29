<?php

use Keepsuit\Liquid\TemplateFactory;

test('register & delete custom tags', function () {
    $factory = TemplateFactory::new()
        ->registerTag(\Keepsuit\Liquid\Tests\Stubs\TestTagBlockTag::class);

    expect($factory->getTagRegistry()->all())->toHaveKey('testblock');

    $factory->getTagRegistry()->delete('testblock');

    expect($factory->getTagRegistry()->all())->not->toHaveKey('testblock');
});

test('get registered tags', function () {
    $factory = TemplateFactory::new()
        ->registerTag(\Keepsuit\Liquid\Tests\Stubs\TestTagBlockTag::class);

    expect($factory->getTagRegistry()->all())->toHaveKey('testblock');
    expect($factory->getTagRegistry()->all()['testblock'])->toBe(\Keepsuit\Liquid\Tests\Stubs\TestTagBlockTag::class);
});
