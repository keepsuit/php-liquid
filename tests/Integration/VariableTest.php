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

function generator(): Generator
{
    yield '1';
    yield '2';
    yield '3';
}
