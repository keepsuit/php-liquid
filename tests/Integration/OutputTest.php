<?php

use Keepsuit\Liquid\TemplateFactory;
use Keepsuit\Liquid\Tests\Stubs\FunnyFilter;

beforeEach(function () {
    $this->templateFactory = TemplateFactory::new();

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
    $context = $this->templateFactory
        ->registerFilter(FunnyFilter::class)
        ->newRenderContext(
            staticEnvironment: $this->assigns,
        );

    expect(parseTemplate(' {{ car.gm | make_funny }} ')->render($context))
        ->toBe(' LOL ');
});

test('variable piping with input', function () {
    $context = $this->templateFactory
        ->registerFilter(FunnyFilter::class)
        ->newRenderContext(
            staticEnvironment: $this->assigns,
        );

    expect(parseTemplate(' {{ car.gm | cite_funny }} ')->render($context))
        ->toBe(' LOL: bad ');
});

test('variable piping with args', function () {
    $context = $this->templateFactory
        ->registerFilter(FunnyFilter::class)
        ->newRenderContext(
            staticEnvironment: $this->assigns,
        );

    expect(parseTemplate(" {{ car.gm | add_smiley : ':-(' }} ")->render($context))
        ->toBe(' bad :-( ');
});

test('variable piping with no args', function () {
    $context = $this->templateFactory
        ->registerFilter(FunnyFilter::class)
        ->newRenderContext(
            staticEnvironment: $this->assigns,
        );

    expect(parseTemplate(' {{ car.gm | add_smiley }} ')->render($context))
        ->toBe(' bad :-) ');
});

test('multiple variable piping with args', function () {
    $context = $this->templateFactory
        ->registerFilter(FunnyFilter::class)
        ->newRenderContext(
            staticEnvironment: $this->assigns,
        );

    expect(parseTemplate(" {{ car.gm | add_smiley : ':-(' | add_smiley : ':-('}} ")->render($context))
        ->toBe(' bad :-( :-( ');
});

test('variable piping with multiple args', function () {
    $context = $this->templateFactory
        ->registerFilter(FunnyFilter::class)
        ->newRenderContext(
            staticEnvironment: $this->assigns,
        );

    expect(parseTemplate(" {{ car.gm | add_tag : 'span', 'bar'}} ")->render($context))
        ->toBe(' <span id="bar">bad</span> ');
});

test('variable piping with variable args', function () {
    $context = $this->templateFactory
        ->registerFilter(FunnyFilter::class)
        ->newRenderContext(
            staticEnvironment: $this->assigns,
        );

    expect(parseTemplate(" {{ car.gm | add_tag : 'span', car.bmw}} ")->render($context))
        ->toBe(' <span id="good">bad</span> ');
});

test('multiple pipings', function () {
    $context = $this->templateFactory
        ->registerFilter(FunnyFilter::class)
        ->newRenderContext(
            staticEnvironment: ['best_cars' => 'bmw']
        );

    expect(parseTemplate(' {{ best_cars | cite_funny | paragraph }} ')->render($context))
        ->toBe(' <p>LOL: bmw</p> ');
});

test('link to', function () {
    $context = $this->templateFactory
        ->registerFilter(FunnyFilter::class)
        ->newRenderContext(
            staticEnvironment: $this->assigns,
        );

    expect(parseTemplate(" {{ 'Typo' | link_to: 'http://typo.leetsoft.com' }} ")->render($context))
        ->toBe(' <a href="http://typo.leetsoft.com">Typo</a> ');
});
