<?php

use Keepsuit\Liquid\Tags\ForTag;

test('for children', function () {
    $template = parseTemplate('{% for item in items %}FOR{% endfor %}');

    expect($template->root->children())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(ForTag::class)
        ->{0}->children()->toHaveCount(1)
        ->{0}->children()->{0}->children()->{0}->value->toBe('FOR');
});

test('for else children', function () {
    $template = parseTemplate('{% for item in items %}FOR{% else %}ELSE{% endfor %}');

    expect($template->root->children())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(ForTag::class)
        ->{0}->children()->toHaveCount(2)
        ->{0}->children()->{0}->children()->{0}->value->toBe('FOR')
        ->{0}->children()->{1}->children()->{0}->value->toBe('ELSE');
});
