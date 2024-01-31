<?php

use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Tags\IfTag;

test('blankspace', function () {
    $template = parseTemplate('  ');

    expect($template->root->children())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(Text::class)
        ->{0}->value->toBe('  ');
});

test('variable beginning', function () {
    $template = parseTemplate('{{funk}}  ');

    expect($template->root->children())
        ->toHaveCount(2)
        ->{0}->toBeInstanceOf(Variable::class)
        ->{1}->toBeInstanceOf(Text::class);
});

test('variable end', function () {
    $template = parseTemplate('  {{funk}}');

    expect($template->root->children())
        ->toHaveCount(2)
        ->{0}->toBeInstanceOf(Text::class)
        ->{1}->toBeInstanceOf(Variable::class);
});

test('variable middle', function () {
    $template = parseTemplate('  {{funk}}  ');

    expect($template->root->children())
        ->toHaveCount(3)
        ->{0}->toBeInstanceOf(Text::class)
        ->{1}->toBeInstanceOf(Variable::class)
        ->{2}->toBeInstanceOf(Text::class);
});

test('variable many embedded fragments', function () {
    $template = parseTemplate('  {{funk}} {{so}} {{brother}} ');

    expect($template->root->children())
        ->toHaveCount(7)
        ->{0}->toBeInstanceOf(Text::class)
        ->{1}->toBeInstanceOf(Variable::class)
        ->{2}->toBeInstanceOf(Text::class)
        ->{3}->toBeInstanceOf(Variable::class)
        ->{4}->toBeInstanceOf(Text::class)
        ->{5}->toBeInstanceOf(Variable::class)
        ->{6}->toBeInstanceOf(Text::class);
});

test('with block', function () {
    $template = parseTemplate('  {% if hi %} hi {% endif %} ');

    expect($template->root->children())
        ->toHaveCount(3)
        ->{0}->toBeInstanceOf(Text::class)
        ->{1}->toBeInstanceOf(IfTag::class)
        ->{1}->children()->{0}->children()->toHaveCount(1)
        ->{1}->children()->{0}->children()->{0}->toBeInstanceOf(Text::class)
        ->{2}->toBeInstanceOf(Text::class);
});
