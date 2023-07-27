<?php

use Keepsuit\Liquid\TranslationException;

beforeEach(function () {
    $this->i18n = new Keepsuit\Liquid\I18n(fixture('en_locale.yml'));
});

test('translate simple string', function () {
    expect($this->i18n->translate('simple'))->toBe('less is more');
});

test('translate nested string', function () {
    expect($this->i18n->translate('errors.syntax.oops'))->toBe("something wasn't right");
});

test('single string interpolation', function () {
    expect($this->i18n->translate('whatever', ['something' => 'different']))->toBe('something different');
});

test('throw unknown translation', function () {
    expect(fn () => $this->i18n->translate('doesnt_exist'))
        ->toThrow(TranslationException::class, sprintf("Translation for '%s' does not exist in locale: '%s'", 'doesnt_exist', fixture('en_locale.yml')));
});
