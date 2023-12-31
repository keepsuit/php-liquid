<?php

use Keepsuit\Liquid\Support\I18n;
use Keepsuit\Liquid\TemplateFactory;

it('sets default localization in context', function () {
    $factory = TemplateFactory::new();

    $parseContext = $factory->newParseContext();

    expect($parseContext->locale)->toBeInstanceOf(I18n::class);
});

it('sets default localization in context with quick initialization', function () {
    $factory = TemplateFactory::new()
        ->setLocale($i18n = new I18n(fixture('en_locale.yml')));

    $parseContext = $factory->newParseContext();
    expect($parseContext->locale)->toBe($i18n);
});

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
