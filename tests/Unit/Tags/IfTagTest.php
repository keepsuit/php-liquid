<?php

use Keepsuit\Liquid\Tags\IfTag;
use Keepsuit\Liquid\Template;

test('if nodelist', function () {
    $template = Template::parse('{% if true %}IF{% else %}ELSE{% endif %}');

    expect($template->root->nodeList())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(IfTag::class)
        ->{0}->nodeList()->toHaveCount(2)
        ->{0}->nodeList()->{0}->nodeList()->toBe(['IF'])
        ->{0}->nodeList()->{1}->nodeList()->toBe(['ELSE']);
});
