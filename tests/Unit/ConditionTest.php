<?php

use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Render\RenderContext;

beforeEach(function () {
    $this->context = new RenderContext();
});

afterEach(function () {
    Condition::resetOperators();
});

test('basic condition', function () {
    expect(new Condition(1, '==', 2))->evaluate($this->context)->toBeFalse();
    expect(new Condition(1, '==', 1))->evaluate($this->context)->toBeTrue();
});

test('default operators evaluate true', function (mixed $left, string $operator, mixed $right) {
    expect(new Condition($left, $operator, $right))->evaluate($this->context)->toBeTrue();
})->with([
    [1, '==', 1],
    [1, '!=', 2],
    [1, '<>', 2],
    [1, '<', 2],
    [2, '>', 1],
    [1, '>=', 1],
    [2, '>=', 1],
    [1, '<=', 2],
    [1, '<=', 1],
    [1, '>', -1],
    [-1, '<', 1],
    [1.0, '>', -1.0],
    [-1.0, '<', 1.0],
]);

test('default operators evaluate false', function (mixed $left, string $operator, mixed $right) {
    expect(new Condition($left, $operator, $right))->evaluate($this->context)->toBeFalse();
})->with([
    [1, '==', 2],
    [1, '!=', 1],
    [1, '<>', 1],
    [1, '<', 0],
    [2, '>', 4],
    [1, '>=', 3],
    [2, '>=', 4],
    [1, '<=', 0],
    [1, '<=', 0],
]);

test('contains works on strings', function (mixed $left, string $operator, mixed $right, bool $result) {
    expect(new Condition($left, $operator, $right))->evaluate($this->context)->toBe($result);
})->with([
    ['bob', 'contains', 'o', true],
    ['bob', 'contains', 'b', true],
    ['bob', 'contains', 'bo', true],
    ['bob', 'contains', 'ob', true],
    ['bob', 'contains', 'bob', true],
    ['bob', 'contains', 'bob2', false],
    ['bob', 'contains', 'a', false],
    ['bob', 'contains', '---', false],
]);

test('invalid comparison operator', function () {
    expect(fn () => (new Condition(1, '~~', 0))->evaluate($this->context))->toThrow(SyntaxException::class);
});

test('comparison of int and string', function (mixed $left, string $operator, mixed $right, bool $result) {
    expect((new Condition($left, $operator, $right))->evaluate($this->context))->toBe($result);
})->with([
    ['1', '>', 0, true],
    ['1', '<', 0, false],
    ['1', '>=', 0, true],
    ['1', '<=', 0, false],
]);

test('contains works on arrays', function (mixed $value, bool $result) {
    $this->context->set('array', [1, 2, 3, 4, 5]);
    $expression = new VariableLookup('array');

    expect((new Condition($expression, 'contains', $value))->evaluate($this->context))->toBe($result);
})->with([
    [0, false],
    [1, true],
    [2, true],
    [3, true],
    [4, true],
    [5, true],
    [6, false],
    ['1', false],
]);

test('contains returns false for null operands', function () {
    expect((new Condition(new VariableLookup('not_assigned'), 'contains', '0'))->evaluate($this->context))->toBeFalse();
    expect((new Condition(0, 'contains', new VariableLookup('not_assigned')))->evaluate($this->context))->toBeFalse();
});

test('contains returns false on wrong data type', function () {
    expect((new Condition(1, 'contains', '0'))->evaluate($this->context))->toBeFalse();
});

test('contains with string left operand coerces right operand to string', function () {
    expect((new Condition(' 1 ', 'contains', 1))->evaluate($this->context))->toBeTrue();
    expect((new Condition(' 1 ', 'contains', 2))->evaluate($this->context))->toBeFalse();
});

test('or condition', function () {
    $a = new Condition(1, '==', 2);
    expect($a->evaluate(new RenderContext()))->toBeFalse();

    $a->or(new Condition(2, '==', 1));
    expect($a->evaluate(new RenderContext()))->toBeFalse();

    $a->or(new Condition(1, '==', 1));
    expect($a->evaluate(new RenderContext()))->toBeTrue();
});

test('and condition', function () {
    $a = new Condition(1, '==', 1);
    expect($a->evaluate(new RenderContext()))->toBeTrue();

    $a->and(new Condition(2, '==', 2));
    expect($a->evaluate(new RenderContext()))->toBeTrue();

    $a->and(new Condition(2, '==', 1));
    expect($a->evaluate(new RenderContext()))->toBeFalse();
});

test('should allow custom operators', function () {
    Condition::registerOperator('starts_with', fn (mixed $left, mixed $right) => str_starts_with($left, $right));

    expect((new Condition('bob', 'starts_with', 'b'))->evaluate($this->context))->toBeTrue();
    expect((new Condition('bob', 'starts_with', 'o'))->evaluate($this->context))->toBeFalse();
});

test('compare two variable', function () {
    $this->context->set('one', 'gnomeslab-and-or-liquid');
    $this->context->set('another', 'gnomeslab-and-or-liquid');

    expect((new Condition(new VariableLookup('one'), '==', new VariableLookup('another')))->evaluate($this->context))->toBeTrue();
});
