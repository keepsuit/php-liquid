<?php

use Keepsuit\Liquid\Tags\CaseTag;

test('case children', function () {
    $template = parseTemplate('{% case var %}{% when true %}WHEN{% else %}ELSE{% endcase %}');

    expect($template->root->body->children())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(CaseTag::class)
        ->{0}->children()->toHaveCount(2)
        ->{0}->children()->{0}->children()->{0}->value->toBe('WHEN')
        ->{0}->children()->{1}->children()->{0}->value->toBe('ELSE');
});
