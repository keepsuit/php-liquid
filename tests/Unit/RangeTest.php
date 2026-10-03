<?php

use Keepsuit\Liquid\Nodes\Range;

test('range slices match array slices including negative offsets and lengths', function (int $start, int $end) {
    $range = new Range($start, $end);
    $values = $range->toArray();

    foreach ([PHP_INT_MIN, -12, -5, -1, 0, 1, 4, 8, 12, PHP_INT_MAX] as $offset) {
        foreach ([null, PHP_INT_MIN, -12, -5, -1, 0, 1, 4, 12, PHP_INT_MAX] as $length) {
            expect($range->slice($offset, $length))->toBe(array_slice($values, $offset, $length));
        }
    }
})->with([
    [-4, 4], [7, 3],
    [PHP_INT_MIN, PHP_INT_MIN + 5], [PHP_INT_MAX - 5, PHP_INT_MAX],
]);

test('range slices handle intervals whose full length exceeds PHP_INT_MAX', function () {
    $range = new Range(PHP_INT_MIN, PHP_INT_MAX);

    expect($range->slice(0, 3))->toBe([PHP_INT_MIN, PHP_INT_MIN + 1, PHP_INT_MIN + 2])
        ->and($range->slice(PHP_INT_MAX, 3))->toBe([-1, 0, 1])
        ->and($range->slice(PHP_INT_MIN, 3))->toBe([0, 1, 2])
        ->and($range->slice(-2))->toBe([PHP_INT_MAX - 1, PHP_INT_MAX])
        ->and($range->slice(PHP_INT_MAX, PHP_INT_MIN))->toBe([-1]);
});
