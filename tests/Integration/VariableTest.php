<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Tests\Stubs\BooleanDrop;
use Keepsuit\Liquid\Tests\Stubs\IntegerDrop;
use Keepsuit\Liquid\Tests\Stubs\ThingWithToLiquid;

describe('rendering with template backends', function () {
    test('simple variable', function (bool $compiled) {
        assertTemplateResult('worked', '{{test}}', ['test' => 'worked'], compiled: $compiled);
        assertTemplateResult('worked wonderfully', '{{test}}', ['test' => 'worked wonderfully'], compiled: $compiled);
    });

    test('variable render calls to liquid', function (bool $compiled) {
        assertTemplateResult('foobar', '{{ foo }}', ['foo' => new ThingWithToLiquid], compiled: $compiled);
    });

    test('variable lookup evaluate value as liquid', function (bool $compiled) {
        assertTemplateResult('1', '{{ foo }}', ['foo' => new IntegerDrop('1')], compiled: $compiled);
        assertTemplateResult('2', '{{ list[foo] }}', ['foo' => new IntegerDrop('1'), 'list' => [1, 2, 3]], compiled: $compiled);
        assertTemplateResult('one', '{{ list[foo] }}', ['foo' => new IntegerDrop('1'), 'list' => [1 => 'one']], compiled: $compiled);
        assertTemplateResult('Yay', '{{ foo }}', ['foo' => new BooleanDrop(true)], compiled: $compiled);
        assertTemplateResult('YAY', '{{ foo | upcase }}', ['foo' => new BooleanDrop(true)], compiled: $compiled);
    });

    test('generator variable', function (bool $compiled) {
        assertTemplateResult('123', '{{test}}', ['test' => generator()], compiled: $compiled);
    });

    test('generator variable with lookup', function (bool $compiled) {
        assertTemplateResult('1', '{{test.first}}', ['test' => generator()], compiled: $compiled);
    });

    test('variable override', function (bool $compiled) {
        $templateFactory = new \Keepsuit\Liquid\EnvironmentFactory;
        $templateFactory->registerTag(\Keepsuit\Liquid\Tests\Stubs\VariableOverrideTag::class);

        assertTemplateResult('old|new|old', <<<'LIQUID'
        {{ test }}|
        {%- override test "new" -%}
        {{ test }}|
        {%- endoverride -%}
        {{ test }}
        LIQUID, [
            'test' => 'old',
        ], factory: $templateFactory, compiled: $compiled);
    });

    test('variable nested override', function (bool $compiled) {
        $templateFactory = new \Keepsuit\Liquid\EnvironmentFactory;
        $templateFactory->registerTag(\Keepsuit\Liquid\Tests\Stubs\VariableOverrideTag::class);

        assertTemplateResult('old_a,old_b|old_a,new_b|old_a,old_b', <<<'LIQUID'
        {{ test.a }},{{ test.b }}|
        {%- override test.b "new_b" -%}
        {{ test.a }},{{ test.b }}|
        {%- endoverride -%}
        {{ test.a }},{{ test.b }}
        LIQUID, [
            'test' => [
                'a' => 'old_a',
                'b' => 'old_b',
            ],
        ], factory: $templateFactory, compiled: $compiled);
    });

    test('nested variable lookup', function (bool $compiled) {
        assertTemplateResult('1', '{{ c.b }}', [
            'a' => ['b' => 1],
            'c' => new \Keepsuit\Liquid\Nodes\VariableLookup('a'),
        ], compiled: $compiled);

        assertTemplateResult('1', '{{ c.d.b }}', [
            'a' => ['b' => 1],
            'c' => ['d' => new \Keepsuit\Liquid\Nodes\VariableLookup('a')],
        ], compiled: $compiled);
    });
})->with('template backends');

test('bracket lookup with a literal key throw syntax exception', function () {
    $environment = EnvironmentFactory::new()
        ->setRethrowErrors(false)
        ->setStrictVariables(true)->build();

    expect(fn () => parseTemplate('{{ a[empty] }}', $environment))
        ->toThrow(\Keepsuit\Liquid\Exceptions\SyntaxException::class, 'Invalid variable lookup: a[empty]');
});

function generator(): Generator
{
    yield '1';
    yield '2';
    yield '3';
}
