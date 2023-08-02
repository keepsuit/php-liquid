<?php

use Keepsuit\Liquid\Tags\CaseTag;
use Keepsuit\Liquid\Template;

test('case nodelist', function () {
    $template = Template::parse('{% case var %}{% when true %}WHEN{% else %}ELSE{% endcase %}');

    expect($template->root->nodeList())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(CaseTag::class)
        ->{0}->nodeList()->toHaveCount(2)
        ->{0}->nodeList()->{0}->nodeList()->toBe(['WHEN'])
        ->{0}->nodeList()->{1}->nodeList()->toBe(['ELSE']);
});
