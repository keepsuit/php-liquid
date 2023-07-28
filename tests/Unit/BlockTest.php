<?php

use Keepsuit\Liquid\Tags\Comment;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\Variable;

test('blankspace', function () {
    $template = Template::parse('  ');

    expect($template->root->nodeList())->toBe(['  ']);
});

test('variable beginning', function () {
    $template = Template::parse('{{funk}}  ');

    expect($template->root->nodeList())
        ->toHaveCount(2)
        ->{0}->toBeInstanceOf(Variable::class)
        ->{1}->toBeString();
});

test('variable end', function () {
    $template = Template::parse('  {{funk}}');

    expect($template->root->nodeList())
        ->toHaveCount(2)
        ->{0}->toBeString()
        ->{1}->toBeInstanceOf(Variable::class);
});

test('variable middle', function () {
    $template = Template::parse('  {{funk}}  ');

    expect($template->root->nodeList())
        ->toHaveCount(3)
        ->{0}->toBeString()
        ->{1}->toBeInstanceOf(Variable::class)
        ->{2}->toBeString();
});

test('variable many embedded fragments', function () {
    $template = Template::parse('  {{funk}} {{so}} {{brother}} ');

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
    $template = Template::parse('  {% comment %} {% endcomment %} ');

    expect($template->root->nodeList())
        ->toHaveCount(3)
        ->{0}->toBeString()
        ->{1}->toBeInstanceOf(Comment::class)
        ->{2}->toBeString();
});
