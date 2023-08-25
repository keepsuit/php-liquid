<?php

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Parser\ParseContext;
use Keepsuit\Liquid\Parser\Tokenizer;
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
