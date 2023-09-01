<?php

use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tests\Stubs\TestTag;

test('tag', function () {
    $parseContext = new ParseContext();
    $tag = (new TestTag(''))->parse($parseContext, $parseContext->newTokenizer(''));

    expect($tag)
        ->name()->toBe(TestTag::class)
        ->render(new Context())->toBe('');
});

test('return raw text of tag', function () {
    $parseContext = new ParseContext();
    $tag = (new TestTag('param1, param2, param3'))->parse($parseContext, $parseContext->newTokenizer(''));

    expect($tag)
        ->raw()->toBe('test param1, param2, param3');
});

test('tag name should return name of tag', function () {
    expect(TestTag::tagName())->toBe('test');
});
