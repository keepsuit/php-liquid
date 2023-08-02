<?php

use Keepsuit\Liquid\Tags\ForTag;
use Keepsuit\Liquid\Template;

test('for nodelist', function () {
    $template = Template::parse('{% for item in items %}FOR{% endfor %}');

    expect($template->root->nodeList())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(ForTag::class)
        ->{0}->nodeList()->toHaveCount(1)
        ->{0}->nodeList()->{0}->nodeList()->toBe(['FOR']);
});

test('for else nodelist', function () {
    $template = Template::parse('{% for item in items %}FOR{% else %}ELSE{% endfor %}');

    expect($template->root->nodeList())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(ForTag::class)
        ->{0}->nodeList()->toHaveCount(2)
        ->{0}->nodeList()->{0}->nodeList()->toBe(['FOR'])
        ->{0}->nodeList()->{1}->nodeList()->toBe(['ELSE']);
});
