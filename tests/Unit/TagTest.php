<?php

use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tests\Stubs\TestTag;

test('tag', function () {
    $tag = (new TestTag('', new ParseContext()))->parse(new Tokenizer(''));

    expect($tag)
        ->name()->toBe(TestTag::class)
        ->render(new Context())->toBe('');
});

test('return raw text of tag', function () {
    $tag = (new TestTag('param1, param2, param3', new ParseContext()))->parse(new Tokenizer(''));

    expect($tag)
        ->raw()->toBe('test param1, param2, param3');
});

test('tag name should return name of tag', function () {
    expect(TestTag::tagName())->toBe('test');
});
