<?php

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\Tests\Stubs\FunnyFilter;

beforeEach(function () {
    $this->assigns = [
        'car' => [
            'bmw' => 'good',
            'gm' => 'bad',
        ],
    ];
});

test('variable', function () {
    assertTemplateResult(' bmw ', ' {{best_cars}} ', ['best_cars' => 'bmw']);
});

test('variable traversing with two brackets', function () {
    $source = '{{ site.data.menu[include.menu][include.locale] }}';

    assertTemplateResult('it works!', $source, [
        'site' => ['data' => ['menu' => ['foo' => ['bar' => 'it works!']]]],
        'include' => ['menu' => 'foo', 'locale' => 'bar'],
    ]);
});

test('variable traversing', function () {
    $source = ' {{car.bmw}} {{car.gm}} {{car.bmw}} ';

    assertTemplateResult(' good bad good ', $source, $this->assigns);
});

test('variable piping', function () {
    $context = new Context(
        staticEnvironment: $this->assigns,
        filters: [FunnyFilter::class]
    );

    expect(Template::parse(' {{ car.gm | make_funny }} ')->render($context))
        ->toBe(' LOL ');
});
