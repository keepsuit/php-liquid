<?php

use Keepsuit\Liquid\I18n;
use Keepsuit\Liquid\Tags\CommentTag;
use Keepsuit\Liquid\Template;

it('sets default localization in document', function () {
    $template = Template::parse('{%comment%}{%endcomment%}');

    expect($template)
        ->root->nodeList()->toHaveCount(1)
        ->root->nodeList()->{0}->toBeInstanceOf(CommentTag::class)
        ->root->nodeList()->{0}->parseContext->locale->toBeInstanceOf(I18n::class);
});

it('sets default localization in context with quick initialization', function () {
    $template = Template::parse('{%comment%}{%endcomment%}', [
        'locale' => $i18n = new I18n(fixture('en_locale.yml')),
    ]);

    expect($template)
        ->root->nodeList()->{0}->parseContext->locale->toBe($i18n)
        ->root->nodeList()->{0}->parseContext->locale->path->toBe(fixture('en_locale.yml'));
});

test('register & delete custom tags', function () {
    Template::registerTag(\Keepsuit\Liquid\Tests\Stubs\TestBlockTag::class);

    expect(Template::registeredTags())->toHaveKey('fake');

    Template::deleteTag('fake');

    expect(Template::registeredTags())->not->toHaveKey('fake');
});

test('get registered tags', function () {
    Template::registerTag(\Keepsuit\Liquid\Tests\Stubs\TestBlockTag::class);

    expect(Template::registeredTags())->toHaveKey('fake');
    expect(Template::registeredTags()['fake'])->toBe(\Keepsuit\Liquid\Tests\Stubs\TestBlockTag::class);
});
