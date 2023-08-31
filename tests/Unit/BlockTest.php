<?php

use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Tags\CommentTag;

test('blankspace', function () {
    $template = parseTemplate('  ');

    expect($template->root->nodeList())->toBe(['  ']);
});

test('variable beginning', function () {
    $template = parseTemplate('{{funk}}  ');

    expect($template->root->nodeList())
        ->toHaveCount(2)
        ->{0}->toBeInstanceOf(Variable::class)
        ->{1}->toBeString();
});

test('variable end', function () {
    $template = parseTemplate('  {{funk}}');

    expect($template->root->nodeList())
        ->toHaveCount(2)
        ->{0}->toBeString()
        ->{1}->toBeInstanceOf(Variable::class);
});

test('variable middle', function () {
    $template = parseTemplate('  {{funk}}  ');

    expect($template->root->nodeList())
        ->toHaveCount(3)
        ->{0}->toBeString()
        ->{1}->toBeInstanceOf(Variable::class)
        ->{2}->toBeString();
});

test('variable many embedded fragments', function () {
    $template = parseTemplate('  {{funk}} {{so}} {{brother}} ');

    expect($template->root->nodeList())
        ->toHaveCount(7)
        ->{0}->toBeString()
        ->{1}->toBeInstanceOf(Variable::class)
        ->{2}->toBeString()
        ->{3}->toBeInstanceOf(Variable::class)
        ->{4}->toBeString()
        ->{5}->toBeInstanceOf(Variable::class)
        ->{6}->toBeString();
});

test('with block', function () {
    $template = parseTemplate('  {% comment %} {% endcomment %} ');

    expect($template->root->nodeList())
        ->toHaveCount(3)
        ->{0}->toBeString()
        ->{1}->toBeInstanceOf(CommentTag::class)
        ->{2}->toBeString();
});
