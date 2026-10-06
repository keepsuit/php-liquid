<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Tests\Stubs\FooBarTag;

describe('rendering with template backends', function () {
    test('new tags are not blank by default', function (bool $compiled) {
        $factory = EnvironmentFactory::new()->registerTag(FooBarTag::class);

        expect(renderTemplate(wrapInFor('{% foobar %}'), factory: $factory, compiled: $compiled))
            ->toBe(str_repeat(' ', 10));
    });

    test('loops are blank', function (bool $compiled) {
        expect(renderTemplate(wrapInFor(' '), compiled: $compiled))->toBe('');
    });

    test('if else are blank', function (bool $compiled) {
        expect(renderTemplate(wrapInFor('{% if true %} {% elsif false %} {% else %} {% endif %}'), compiled: $compiled))->toBe('');
    });

    test('unless is blank', function (bool $compiled) {
        expect(renderTemplate(wrap('{% unless true %} {% endunless %}'), compiled: $compiled))->toBe('');
    });

    test('mark as blank only during parsing', function (bool $compiled) {
        expect(renderTemplate(wrap(' {% if false %} this never happens, but still, this block is not blank {% endif %}'), compiled: $compiled))->toBe(str_repeat(' ', 11));
    });

    test('comments are blank', function (bool $compiled) {
        expect(renderTemplate(wrap(' {% comment %} whatever {% endcomment %} '), compiled: $compiled))->toBe('');
    });

    test('captures are blank', function (bool $compiled) {
        expect(renderTemplate(wrap(' {% capture foo %} whatever {% endcapture %} '), compiled: $compiled))->toBe('');
    });

    test('nested blocks are blank only if all children are blank', function (bool $compiled) {
        expect(renderTemplate(wrap(wrap(' ')), compiled: $compiled))->toBe('');

        expect(
            renderTemplate(wrap(<<<'LIQUID'
        {% if true %} {% comment %} this is blank {% endcomment %} {% endif %}
              {% if true %} but this is not {% endif %}
        LIQUID
            ), compiled: $compiled)
        )->toBe(str_repeat("\n       but this is not ", 11));
    });

    test('assigns are blank', function (bool $compiled) {
        expect(renderTemplate(wrap(' {% assign foo = "bar" %} '), compiled: $compiled))->toBe('');
    });

    test('whitespaces are blank', function (bool $compiled) {
        expect(renderTemplate(wrap(' '), compiled: $compiled))->toBe('');
        expect(renderTemplate(wrap("\t"), compiled: $compiled))->toBe('');
    });

    test('whitespaces are not blank if other stuff are present', function (bool $compiled) {
        expect(renderTemplate(wrap('     x '), compiled: $compiled))->toBe(str_repeat('     x ', 11));
    });

    test('increment is not blank', function (bool $compiled) {
        expect(renderTemplate(wrap('{% assign foo = 0 %} {% increment foo %} {% decrement foo %}'), compiled: $compiled))->toBe(str_repeat(' 0 0', 11));
    });

    test('cycle is not blank', function (bool $compiled) {
        expect(renderTemplate(wrap("{% cycle ' ', ' ' %}"), compiled: $compiled))->toBe(str_repeat(' ', 11));
    });

    test('raw is not blank', function (bool $compiled) {
        expect(renderTemplate(wrap(' {% raw %} {% endraw %}'), compiled: $compiled))->toBe(str_repeat('  ', 11));
    });

    test('case is blank', function (bool $compiled) {
        expect(renderTemplate(wrap(" {% assign foo = 'bar' %} {% case foo %} {% when 'bar' %} {% when 'whatever' %} {% else %} {% endcase %} "), compiled: $compiled))->toBe('');
        expect(renderTemplate(wrap(" {% assign foo = 'else' %} {% case foo %} {% when 'bar' %} {% when 'whatever' %} {% else %} {% endcase %} "), compiled: $compiled))->toBe('');
        expect(renderTemplate(wrap(" {% assign foo = 'else' %} {% case foo %} {% when 'bar' %} {% when 'whatever' %} {% else %} x {% endcase %} "), compiled: $compiled))->toBe(str_repeat('   x  ', 11));
    });
})->with('template backends');

function wrapInFor(string $content): string
{
    return "{% for i in (1..10) %}$content{% endfor %}";
}

function wrapInIf(string $content): string
{
    return "{% if true %}$content{% endif %}";
}

function wrap(string $content): string
{
    return wrapInFor($content).wrapInIf($content);
}
