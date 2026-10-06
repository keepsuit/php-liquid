<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Tests\Stubs\CanadianMoneyFilter;
use Keepsuit\Liquid\Tests\Stubs\HtmlAttributesFilter;
use Keepsuit\Liquid\Tests\Stubs\MoneyFilters;
use Keepsuit\Liquid\Tests\Stubs\SubstituteFilter;
use Keepsuit\Liquid\Tests\Stubs\TestObject;

describe('rendering with template backends', function () {
    test('local filter', function (bool $compiled) {
        $context = EnvironmentFactory::new()
            ->registerFilters(MoneyFilters::class)
            ->build()
            ->newRenderContext();
        $context->set('var', 1000);

        expect(parseTemplate('{{ var | money }}', compiled: $compiled)->render($context))
            ->toBe(' 1000$');
    });

    test('underscore in filter name', function (bool $compiled) {
        $context = EnvironmentFactory::new()
            ->registerFilters(MoneyFilters::class)
            ->build()
            ->newRenderContext();
        $context->set('var', 1000);

        expect(parseTemplate('{{ var | money_with_underscore }}', compiled: $compiled)->render($context))
            ->toBe(' 1000$');
    });

    test('second filter override first', function (bool $compiled) {
        $context = EnvironmentFactory::new()
            ->registerFilters(MoneyFilters::class)
            ->registerFilters(CanadianMoneyFilter::class)
            ->build()
            ->newRenderContext();
        $context->set('var', 1000);

        expect(parseTemplate('{{ var | money }}', compiled: $compiled)->render($context))
            ->toBe(' 1000$ CAD');
    });

    test('size', function (bool $compiled) {
        assertTemplateResult('4', '{{ var | size }}', ['var' => [1, 2, 3, 4]], compiled: $compiled);
        assertTemplateResult('4', '{{ var | size }}', ['var' => 'abcd'], compiled: $compiled);
    });

    test('join', function (bool $compiled) {
        assertTemplateResult('1 2 3 4', '{{var | join}}', ['var' => [1, 2, 3, 4]], compiled: $compiled);
    });

    test('sort', function (bool $compiled) {
        assertTemplateResult('1 2 3 4', '{{numbers | sort | join}}', ['numbers' => [2, 1, 4, 3]], compiled: $compiled);
        assertTemplateResult(
            'alphabetic as expected',
            '{{words | sort | join}}',
            ['words' => ['expected', 'as', 'alphabetic']],
            compiled: $compiled);
        assertTemplateResult('3', '{{value | sort}}', ['value' => 3], compiled: $compiled);
        assertTemplateResult('are flower', '{{arrays | sort | join}}', ['arrays' => ['flower', 'are']], compiled: $compiled);
        assertTemplateResult(
            'Expected case sensitive',
            '{{case_sensitive | sort | join}}',
            ['case_sensitive' => ['sensitive', 'Expected', 'case']],
            compiled: $compiled);
    });

    test('sort natural', function (bool $compiled) {
        assertTemplateResult(
            'Assert case Insensitive',
            '{{words | sort_natural | join}}',
            ['words' => ['case', 'Assert', 'Insensitive']],
            compiled: $compiled);
        assertTemplateResult(
            'A b C',
            "{{hashes | sort_natural: 'a' | map: 'a' | join}}",
            ['hashes' => [['a' => 'A'], ['a' => 'b'], ['a' => 'C']]],
            compiled: $compiled);
        assertTemplateResult(
            'A b C',
            "{{objects | sort_natural: 'a' | map: 'a' | join}}",
            ['objects' => [new TestObject('A'), new TestObject('b'), new TestObject('C')]],
            compiled: $compiled);
    });

    test('compact', function (bool $compiled) {
        assertTemplateResult(
            'a b c',
            '{{words | compact | join}}',
            ['words' => ['a', null, 'b', null, 'c']],
            compiled: $compiled);
        assertTemplateResult(
            'A C',
            "{{hashes | compact: 'a' | map: 'a' | join}}",
            ['hashes' => [['a' => 'A'], ['a' => null], ['a' => 'C']]],
            compiled: $compiled);
        assertTemplateResult(
            'A C',
            "{{objects | compact: 'a' | map: 'a' | join}}",
            ['objects' => [new TestObject('A'), new TestObject(null), new TestObject('C')]],
            compiled: $compiled);
    });

    test('strip html', function (bool $compiled) {
        assertTemplateResult('bla blub', '{{ var | strip_html }}', ['var' => '<b>bla blub</a>'], compiled: $compiled);
    });

    test('strip html ignore comments with html', function (bool $compiled) {
        assertTemplateResult(
            'bla blub',
            '{{ var | strip_html }}',
            ['var' => '<!-- split and some <ul> tag --><b>bla blub</a>'],
            compiled: $compiled);
    });

    test('capitalize', function (bool $compiled) {
        assertTemplateResult('Blub', '{{ var | capitalize }}', ['var' => 'blub'], compiled: $compiled);
    });

    test('non existent filter is ignored', function (bool $compiled) {
        assertTemplateResult('1000', '{{ var | xyzzy }}', ['var' => 1000], compiled: $compiled);
    });

    test('filter with keyword arguments', function (bool $compiled) {
        $context = EnvironmentFactory::new()
            ->registerFilters(SubstituteFilter::class)
            ->build()
            ->newRenderContext();
        $context->set('surname', 'john');
        $context->set('input', 'hello %{first_name}, %{last_name}');

        expect(parseTemplate("{{ input | substitute: first_name: surname, last_name: 'doe' }}", compiled: $compiled)->render($context))
            ->toBe('hello john, doe');
    });

    test('can parse data keyword args', function (bool $compiled) {
        $context = EnvironmentFactory::new()
            ->registerFilters(HtmlAttributesFilter::class)
            ->build()
            ->newRenderContext();

        expect(parseTemplate("{{ 'img' | html_tag: data-src: 'src', data-widths: '100, 200' }}", compiled: $compiled)->render($context))
            ->toBe("data-src='src' data-widths='100, 200'");
    });
})->with('template backends');

test('filter strict parsing rejects trailing commas', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected end of template',
        '{{ value | default: "fallback", }}',
        ['value' => null]);
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected end of template',
        '{{ value | substitute: first_name: "john", }}',
        ['value' => 'hello %{first_name}']);
});
