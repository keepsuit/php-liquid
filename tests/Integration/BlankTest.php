<?php

use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\Tests\Stubs\FooBarTag;

afterEach(function () {
    Template::deleteTag(FooBarTag::class);
});

test('new tags are not blank by default', function () {
    Template::registerTag(FooBarTag::class);

    expect(renderTemplate(wrapInFor('{% foobar %}')))->toBe(str_repeat(' ', 10));
});

test('loops are blank', function () {
    expect(renderTemplate(wrapInFor(' ')))->toBe('');
});

test('if else are blank', function () {
    expect(renderTemplate(wrapInFor('{% if true %} {% elsif false %} {% else %} {% endif %}')))->toBe('');
});

test('unless is blank', function () {
    expect(renderTemplate(wrap('{% unless true %} {% endunless %}')))->toBe('');
});

test('mark as blank only during parsing', function () {
    expect(renderTemplate(wrap(' {% if false %} this never happens, but still, this block is not blank {% endif %}')))->toBe(str_repeat(' ', 11));
});

test('comments are blank', function () {
    expect(renderTemplate(wrap(' {% comment %} whatever {% endcomment %} ')))->toBe('');
});

test('captures are blank', function () {
    expect(renderTemplate(wrap(' {% capture foo %} whatever {% endcapture %} ')))->toBe('');
});

test('nested blocks are blank only if all children are blank', function () {
    expect(renderTemplate(wrap(wrap(' '))))->toBe('');

    expect(
        renderTemplate(wrap(<<<'LIQUID'
        {% if true %} {% comment %} this is blank {% endcomment %} {% endif %}
              {% if true %} but this is not {% endif %}
        LIQUID
        ))
    )->toBe(str_repeat("\n       but this is not ", 11));
});

test('assigns are blank', function () {
    expect(renderTemplate(wrap(' {% assign foo = "bar" %} ')))->toBe('');
});

test('whitespaces are blank', function () {
    expect(renderTemplate(wrap(' ')))->toBe('');
    expect(renderTemplate(wrap("\t")))->toBe('');
});

test('whitespaces are not blank if other stuff are present', function () {
    expect(renderTemplate(wrap('     x ')))->toBe(str_repeat('     x ', 11));
});

test('increment is not blank', function () {
    expect(renderTemplate(wrap('{% assign foo = 0 %} {% increment foo %} {% decrement foo %}')))->toBe(str_repeat(' 0 -1', 11));
});

test('cycle is not blank', function () {
    expect(renderTemplate(wrap("{% cycle ' ', ' ' %}")))->toBe(str_repeat(' ', 11));
});

test('raw is not blank', function () {
    expect(renderTemplate(wrap(' {% raw %} {% endraw %}')))->toBe(str_repeat('  ', 11));
});

test('case is blank', function () {
    expect(renderTemplate(wrap(" {% assign foo = 'bar' %} {% case foo %} {% when 'bar' %} {% when 'whatever' %} {% else %} {% endcase %} ")))->toBe('');
    expect(renderTemplate(wrap(" {% assign foo = 'else' %} {% case foo %} {% when 'bar' %} {% when 'whatever' %} {% else %} {% endcase %} ")))->toBe('');
    expect(renderTemplate(wrap(" {% assign foo = 'else' %} {% case foo %} {% when 'bar' %} {% when 'whatever' %} {% else %} x {% endcase %} ")))->toBe(str_repeat('   x  ', 11));
});

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
