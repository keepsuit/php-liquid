<?php

use Keepsuit\Liquid\Tests\Stubs\FunnyFilter;

beforeEach(function () {
    $this->assigns = [
        'car' => [
            'bmw' => 'good',
            'gm' => 'bad',
        ],
    ];
});

describe('rendering with template backends', function () {
    test('variable', function (bool $compiled) {
        assertTemplateResult(' bmw ', ' {{best_cars}} ', ['best_cars' => 'bmw'], compiled: $compiled);
    });

    test('variable traversing with two brackets', function (bool $compiled) {
        $source = '{{ site.data.menu[include.menu][include.locale] }}';

        assertTemplateResult('it works!', $source, [
            'site' => ['data' => ['menu' => ['foo' => ['bar' => 'it works!']]]],
            'include' => ['menu' => 'foo', 'locale' => 'bar'],
        ], compiled: $compiled);
    });

    test('variable traversing', function (bool $compiled) {
        $source = ' {{car.bmw}} {{car.gm}} {{car.bmw}} ';

        assertTemplateResult(' good bad good ', $source, $this->assigns, compiled: $compiled);
    });

    test('variable piping', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->registerFilters(FunnyFilter::class)->build();
        $context = $environment->newRenderContext(
            staticData: $this->assigns,
        );

        expect(testParseString($environment, ' {{ car.gm | make_funny }} ')->render($context))
            ->toBe(' LOL ');
    });

    test('variable piping with input', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->registerFilters(FunnyFilter::class)->build();
        $context = $environment->newRenderContext(
            staticData: $this->assigns,
        );

        expect(testParseString($environment, ' {{ car.gm | cite_funny }} ')->render($context))
            ->toBe(' LOL: bad ');
    });

    test('variable piping with args', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->registerFilters(FunnyFilter::class)->build();
        $context = $environment->newRenderContext(
            staticData: $this->assigns,
        );

        expect(testParseString($environment, " {{ car.gm | add_smiley : ':-(' }} ")->render($context))
            ->toBe(' bad :-( ');
    });

    test('variable piping with no args', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->registerFilters(FunnyFilter::class)->build();
        $context = $environment->newRenderContext(
            staticData: $this->assigns,
        );

        expect(testParseString($environment, ' {{ car.gm | add_smiley }} ')->render($context))
            ->toBe(' bad :-) ');
    });

    test('multiple variable piping with args', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->registerFilters(FunnyFilter::class)->build();
        $context = $environment->newRenderContext(
            staticData: $this->assigns,
        );

        expect(testParseString($environment, " {{ car.gm | add_smiley : ':-(' | add_smiley : ':-('}} ")->render($context))
            ->toBe(' bad :-( :-( ');
    });

    test('variable piping with multiple args', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->registerFilters(FunnyFilter::class)->build();
        $context = $environment->newRenderContext(
            staticData: $this->assigns,
        );

        expect(testParseString($environment, " {{ car.gm | add_tag : 'span', 'bar'}} ")->render($context))
            ->toBe(' <span id="bar">bad</span> ');
    });

    test('variable piping with variable args', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->registerFilters(FunnyFilter::class)->build();
        $context = $environment->newRenderContext(
            staticData: $this->assigns,
        );

        expect(testParseString($environment, " {{ car.gm | add_tag : 'span', car.bmw}} ")->render($context))
            ->toBe(' <span id="good">bad</span> ');
    });

    test('multiple pipings', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->registerFilters(FunnyFilter::class)->build();
        $context = $environment->newRenderContext(
            staticData: ['best_cars' => 'bmw']
        );

        expect(testParseString($environment, ' {{ best_cars | cite_funny | paragraph }} ')->render($context))
            ->toBe(' <p>LOL: bmw</p> ');
    });

    test('link to', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->registerFilters(FunnyFilter::class)->build();

        $context = $environment->newRenderContext(
            staticData: $this->assigns,
        );

        expect(testParseString($environment, " {{ 'Typo' | link_to: 'http://typo.leetsoft.com' }} ")->render($context))
            ->toBe(' <a href="http://typo.leetsoft.com">Typo</a> ');
    });
})->with('template backends');
