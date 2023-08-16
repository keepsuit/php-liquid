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

test('variable piping with input', function () {
    $context = new Context(
        staticEnvironment: $this->assigns,
        filters: [FunnyFilter::class]
    );

    expect(Template::parse(' {{ car.gm | cite_funny }} ')->render($context))
        ->toBe(' LOL: bad ');
});

test('variable piping with args', function () {
    $context = new Context(
        staticEnvironment: $this->assigns,
        filters: [FunnyFilter::class]
    );

    expect(Template::parse(" {{ car.gm | add_smiley : ':-(' }} ")->render($context))
        ->toBe(' bad :-( ');
});

test('variable piping with no args', function () {
    $context = new Context(
        staticEnvironment: $this->assigns,
        filters: [FunnyFilter::class]
    );

    expect(Template::parse(' {{ car.gm | add_smiley }} ')->render($context))
        ->toBe(' bad :-) ');
});

test('multiple variable piping with args', function () {
    $context = new Context(
        staticEnvironment: $this->assigns,
        filters: [FunnyFilter::class]
    );

    expect(Template::parse(" {{ car.gm | add_smiley : ':-(' | add_smiley : ':-('}} ")->render($context))
        ->toBe(' bad :-( :-( ');
});

test('variable piping with multiple args', function () {
    $context = new Context(
        staticEnvironment: $this->assigns,
        filters: [FunnyFilter::class]
    );

    expect(Template::parse(" {{ car.gm | add_tag : 'span', 'bar'}} ")->render($context))
        ->toBe(' <span id="bar">bad</span> ');
});

test('variable piping with variable args', function () {
    $context = new Context(
        staticEnvironment: $this->assigns,
        filters: [FunnyFilter::class]
    );

    expect(Template::parse(" {{ car.gm | add_tag : 'span', car.bmw}} ")->render($context))
        ->toBe(' <span id="good">bad</span> ');
});

test('multiple pipings', function () {
    $context = new Context(
        staticEnvironment: ['best_cars' => 'bmw'],
        filters: [FunnyFilter::class]
    );

    expect(Template::parse(' {{ best_cars | cite_funny | paragraph }} ')->render($context))
        ->toBe(' <p>LOL: bmw</p> ');
});

test('link to', function () {
    $context = new Context(
        staticEnvironment: $this->assigns,
        filters: [FunnyFilter::class]
    );

    expect(Template::parse(" {{ 'Typo' | link_to: 'http://typo.leetsoft.com' }} ")->render($context))
        ->toBe(' <a href="http://typo.leetsoft.com">Typo</a> ');
});