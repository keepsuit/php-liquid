<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;

test('variable', function () {
    $var = createVariable('hello');

    expect($var->name)
        ->toBeInstanceOf(VariableLookup::class)
        ->name->toBe('hello');
});

test('filters', function () {
    expect(createVariable('hello | textileze'))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('hello')
        ->filters->toBe([['textileze', [], []]]);

    expect(createVariable('hello | textileze | paragraph'))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('hello')
        ->filters->toBe([['textileze', [], []], ['paragraph', [], []]]);

    expect(createVariable(' hello | strftime: \'%Y\''))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('hello')
        ->filters->toBe([['strftime', ['%Y'], []]]);

    expect(createVariable(' \'typo\' | link_to: \'Typo\', true '))
        ->name->toBeString()
        ->name->toBe('typo')
        ->filters->toBe([['link_to', ['Typo', true], []]]);

    expect(createVariable(' \'typo\' | link_to: \'Typo\', false '))
        ->name->toBeString()
        ->name->toBe('typo')
        ->filters->toBe([['link_to', ['Typo', false], []]]);

    expect(createVariable(' \'foo\' | repeat: 3 '))
        ->name->toBeString()
        ->name->toBe('foo')
        ->filters->toBe([['repeat', [3], []]]);

    expect(createVariable(' \'foo\' | repeat: 3, 3 '))
        ->name->toBeString()
        ->name->toBe('foo')
        ->filters->toBe([['repeat', [3, 3], []]]);

    expect(createVariable(' \'foo\' | repeat: 3, 3, 3 '))
        ->name->toBeString()
        ->name->toBe('foo')
        ->filters->toBe([['repeat', [3, 3, 3], []]]);

    expect(createVariable(' hello | strftime: \'%Y, okay?\''))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('hello')
        ->filters->toBe([['strftime', ['%Y, okay?'], []]]);

    expect(createVariable(' hello | things: "%Y, okay?", \'the other one\''))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('hello')
        ->filters->toBe([['things', ['%Y, okay?', 'the other one'], []]]);
});

test('filter with date parameter', function () {
    expect(createVariable(' \'2006-06-06\' | date: "%m/%d/%Y"'))
        ->name->toBeString()
        ->name->toBe('2006-06-06')
        ->filters->toBe([['date', ['%m/%d/%Y'], []]]);
});

test('filters without whitespace', function () {
    expect(createVariable('hello | textileze | paragraph'))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('hello')
        ->filters->toBe([['textileze', [], []], ['paragraph', [], []]]);

    expect(createVariable('hello|textileze|paragraph'))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('hello')
        ->filters->toBe([['textileze', [], []], ['paragraph', [], []]]);

    expect(createVariable("hello|replace:'foo','bar'|textileze"))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('hello')
        ->filters->toBe([['replace', ['foo', 'bar'], []], ['textileze', [], []]]);
});

test('string to filters', function () {
    expect(createVariable("'http://disney.com/logo.gif' | image: 'med' "))
        ->name->toBeString()
        ->name->toBe('http://disney.com/logo.gif')
        ->filters->toBe([['image', ['med'], []]]);
});

test('string single quoted', function () {
    expect(createVariable(" 'hello' "))
        ->name->toBeString()
        ->name->toBe('hello');
});

test('string double quoted', function () {
    expect(createVariable(' "hello" '))
        ->name->toBeString()
        ->name->toBe('hello');
});

test('integer', function () {
    expect(createVariable(' 1000 '))
        ->name->toBeNumeric()
        ->name->toBe(1000);
});

test('float', function () {
    expect(createVariable(' 1000.01 '))
        ->name->toBeNumeric()
        ->name->toBe(1000.01);
});

test('dashes', function () {
    expect(createVariable('foo-bar'))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('foo-bar');

    expect(createVariable('foo-bar-2'))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('foo-bar-2');

    expect(fn () => createVariable('foo - bar'))->toThrow(SyntaxException::class);
    expect(fn () => createVariable('-foo'))->toThrow(SyntaxException::class);
    expect(fn () => createVariable('2foo'))->toThrow(SyntaxException::class);
});

test('string with special chars', function () {
    expect(createVariable(' \'hello! $!@.;"ddasd" \' '))
        ->name->toBeString()
        ->name->toBe('hello! $!@.;"ddasd" ');
});

test('string dot', function () {
    expect(createVariable(' test.test '))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('test')
        ->name->lookups->toBe(['test']);
});

test('filter with keyword arguments', function () {
    expect(createVariable(' hello | things: greeting: "world", farewell: \'goodbye\''))
        ->name->toBeInstanceOf(VariableLookup::class)
        ->name->name->toBe('hello')
        ->filters->toBe([['things', [], ['greeting' => 'world', 'farewell' => 'goodbye']]]);
});

test('string filter argument parsing', function () {
    expect(fn () => createVariable(' number_of_comments | pluralize: \'comment\': \'comments\' '))
        ->toThrow(SyntaxException::class);
});

test('variable lookup interface', function () {
    $variable = createVariable('a.b.c');

    expect($variable->name)
        ->toBeInstanceOf(VariableLookup::class)
        ->name->toBe('a')
        ->lookups->toBe(['b', 'c']);
});

function createVariable(string $markup): Variable
{
    $document = parse(sprintf('{{ %s }}', $markup));

    return $document->body->children()[0];
}
