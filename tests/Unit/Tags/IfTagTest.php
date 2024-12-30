<?php

use Keepsuit\Liquid\Tags\IfTag;

test('if children', function () {
    $template = parseTemplate('{% if true %}IF{% else %}ELSE{% endif %}');

    expect($template->root->body->children())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(IfTag::class)
        ->{0}->children()->toHaveCount(2)
        ->{0}->children()->{0}->children()->{0}->value->toBe('IF')
        ->{0}->children()->{1}->children()->{0}->value->toBe('ELSE');
});
