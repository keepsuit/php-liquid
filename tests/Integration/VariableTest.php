<?php

use Keepsuit\Liquid\Tests\Stubs\BooleanDrop;
use Keepsuit\Liquid\Tests\Stubs\IntegerDrop;
use Keepsuit\Liquid\Tests\Stubs\ThingWithToLiquid;

test('simple variable', function () {
    assertTemplateResult('worked', '{{test}}', ['test' => 'worked']);
    assertTemplateResult('worked wonderfully', '{{test}}', ['test' => 'worked wonderfully']);
});

test('variable render calls to liquid', function () {
    assertTemplateResult('foobar', '{{ foo }}', ['foo' => new ThingWithToLiquid()]);
});

test('variable lookup evaluate value as liquid', function () {
    assertTemplateResult('1', '{{ foo }}', ['foo' => new IntegerDrop('1')]);
    assertTemplateResult('2', '{{ list[foo] }}', ['foo' => new IntegerDrop('1'), 'list' => [1, 2, 3]]);
    assertTemplateResult('one', '{{ list[foo] }}', ['foo' => new IntegerDrop('1'), 'list' => [1 => 'one']]);
    assertTemplateResult('Yay', '{{ foo }}', ['foo' => new BooleanDrop(true)]);
    assertTemplateResult('YAY', '{{ foo | upcase }}', ['foo' => new BooleanDrop(true)]);
});

test('generator variable', function () {
    assertTemplateResult('123', '{{test}}', ['test' => generator()]);
});

test('generator variable with lookup', function () {
    assertTemplateResult('1', '{{test.first}}', ['test' => generator()]);
});

test('variable override', function () {
    $templateFactory = new \Keepsuit\Liquid\TemplateFactory();
    $templateFactory->registerTag(\Keepsuit\Liquid\Tests\Stubs\VariableOverrideTag::class);

    assertTemplateResult('old|new|old', <<<'LIQUID'
        {{ test }}|
        {%- override test "new" -%}
        {{ test }}|
        {%- endoverride -%}
        {{ test }}
        LIQUID, [
        'test' => 'old',
    ], factory: $templateFactory);
});

test('variable nested override', function () {
    $templateFactory = new \Keepsuit\Liquid\TemplateFactory();
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
    ], factory: $templateFactory);
});

function generator(): Generator
{
    yield '1';
    yield '2';
    yield '3';
}
