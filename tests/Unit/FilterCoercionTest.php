<?php

use Keepsuit\Liquid\Support\FilterCoercion;

test('filter numeric coercion follows Liquid instead of PHP casts', function (mixed $input, int|float $expected) {
    expect(FilterCoercion::toNumber($input))->toBe($expected);
})->with([
    [null, 0],
    [true, 0],
    [false, 0],
    ['abc', 0],
    ['1abc', 1],
    ['1.2abc', 1],
    ['  -1.25  ', -1.25],
    ['+1.25', 1],
    ['.5', 0],
    ['1e2', 1],
    ['0x10', 0],
    [42, 42],
    [4.5, 4.5],
    ['1_000', 1000],
    ['1__2', 1],
    ['9223372036854775808', 9223372036854775808.0],
]);

test('filter string coercion uses Liquid boolean and nil representations', function (mixed $input, string $expected) {
    expect(FilterCoercion::toString($input))->toBe($expected);
})->with([
    [null, ''],
    [true, 'true'],
    [false, 'false'],
    [42, '42'],
    [4.5, '4.5'],
    ['text', 'text'],
]);

test('filter integer coercion requires a complete integer', function (mixed $input, int $expected) {
    expect(FilterCoercion::toInteger($input))->toBe($expected);
})->with([
    [42, 42],
    [' +42 ', 42],
    ['-1_000', -1000],
    ['0x10', 16],
    ['010', 8],
    ['0b10', 2],
    [strval(PHP_INT_MAX), PHP_INT_MAX],
    [strval(PHP_INT_MIN), PHP_INT_MIN],
]);

test('filter integer coercion rejects invalid integers with a Liquid error', function (mixed $input) {
    expect(fn () => FilterCoercion::toInteger($input))
        ->toThrow(\Keepsuit\Liquid\Exceptions\InvalidArgumentException::class, 'invalid integer');
})->with([null, true, false, 'abc', '1abc', '1.5', 4.5, 4.0, 0.0, '1__2', '09']);
