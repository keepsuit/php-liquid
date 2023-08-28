<?php

use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\Tests\Stubs\CanadianMoneyFilter;
use Keepsuit\Liquid\Tests\Stubs\HtmlAttributesFilter;
use Keepsuit\Liquid\Tests\Stubs\MoneyFilters;
use Keepsuit\Liquid\Tests\Stubs\SubstituteFilter;
use Keepsuit\Liquid\Tests\Stubs\TestObject;

test('local filter', function () {
    $context = new Context(
        filters: [MoneyFilters::class]
    );
    $context->set('var', 1000);

    expect(Template::parse('{{ var | money }}')->render($context))
        ->toBe(' 1000$');
});

test('underscore in filter name', function () {
    $context = new Context(
        filters: [MoneyFilters::class]
    );
    $context->set('var', 1000);

    expect(Template::parse('{{ var | money_with_underscore }}')->render($context))
        ->toBe(' 1000$');
});

test('second filter override first', function () {
    $context = new Context(
        filters: [MoneyFilters::class, CanadianMoneyFilter::class]
    );
    $context->set('var', 1000);

    expect(Template::parse('{{ var | money }}')->render($context))
        ->toBe(' 1000$ CAD');
});

test('size', function () {
    assertTemplateResult('4', '{{ var | size }}', ['var' => [1, 2, 3, 4]]);
    assertTemplateResult('4', '{{ var | size }}', ['var' => 'abcd']);
});

test('join', function () {
    assertTemplateResult('1 2 3 4', '{{var | join}}', ['var' => [1, 2, 3, 4]]);
});

test('sort', function () {
    assertTemplateResult('1 2 3 4', '{{numbers | sort | join}}', ['numbers' => [2, 1, 4, 3]]);
    assertTemplateResult(
        'alphabetic as expected',
        '{{words | sort | join}}',
        ['words' => ['expected', 'as', 'alphabetic']],
    );
    assertTemplateResult('3', '{{value | sort}}', ['value' => 3]);
    assertTemplateResult('are flower', '{{arrays | sort | join}}', ['arrays' => ['flower', 'are']]);
    assertTemplateResult(
        'Expected case sensitive',
        '{{case_sensitive | sort | join}}',
        ['case_sensitive' => ['sensitive', 'Expected', 'case']],
    );
});

test('sort natural', function () {
    assertTemplateResult(
        'Assert case Insensitive',
        '{{words | sort_natural | join}}',
        ['words' => ['case', 'Assert', 'Insensitive']],
    );
    assertTemplateResult(
        'A b C',
        "{{hashes | sort_natural: 'a' | map: 'a' | join}}",
        ['hashes' => [['a' => 'A'], ['a' => 'b'], ['a' => 'C']]],
    );
    assertTemplateResult(
        'A b C',
        "{{objects | sort_natural: 'a' | map: 'a' | join}}",
        ['objects' => [new TestObject('A'), new TestObject('b'), new TestObject('C')]],
    );
});

test('compact', function () {
    assertTemplateResult(
        'a b c',
        '{{words | compact | join}}',
        ['words' => ['a', null, 'b', null, 'c']],
    );
    assertTemplateResult(
        'A C',
        "{{hashes | compact: 'a' | map: 'a' | join}}",
        ['hashes' => [['a' => 'A'], ['a' => null], ['a' => 'C']]],
    );
    assertTemplateResult(
        'A C',
        "{{objects | compact: 'a' | map: 'a' | join}}",
        ['objects' => [new TestObject('A'), new TestObject(null), new TestObject('C')]],
    );
});

test('strip html', function () {
    assertTemplateResult('bla blub', '{{ var | strip_html }}', ['var' => '<b>bla blub</a>']);
});

test('strip html ignore comments with html', function () {
    assertTemplateResult(
        'bla blub',
        '{{ var | strip_html }}',
        ['var' => '<!-- split and some <ul> tag --><b>bla blub</a>'],
    );
});

test('capitalize', function () {
    assertTemplateResult('Blub', '{{ var | capitalize }}', ['var' => 'blub']);
});

test('non existent filter is ignored', function () {
    assertTemplateResult('1000', '{{ var | xyzzy }}', ['var' => 1000]);
});

test('filter with keyword arguments', function () {
    $context = new Context(
        filters: [SubstituteFilter::class]
    );
    $context->set('surname', 'john');
    $context->set('input', 'hello %{first_name}, %{last_name}');

    expect(Template::parse("{{ input | substitute: first_name: surname, last_name: 'doe' }}")->render($context))
        ->toBe('hello john, doe');
});

test('can parse data keyword args', function () {
    $context = new Context(
        filters: [HtmlAttributesFilter::class]
    );

    expect(Template::parse("{{ 'img' | html_tag: data-src: 'src', data-widths: '100, 200' }}")->render($context))
        ->toBe("data-src='src' data-widths='100, 200'");
});
