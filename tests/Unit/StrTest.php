<?php

use Keepsuit\Liquid\Support\Str;

test('remembered values stay bounded', function () {
    $cache = [];

    for ($i = 0; $i < 5000; $i++) {
        expect(Str::remember($cache, 'name'.$i, $i))->toBe($i);
    }

    expect(count($cache))->toBeLessThanOrEqual(1024)
        ->and(Str::remember($cache, str_repeat('a', 257), 'long'))->toBe('long')
        ->and($cache)->not->toHaveKey(str_repeat('a', 257));
});

test('snake case caches per delimiter', function () {
    expect(Str::snake('fooBar'))->toBe('foo_bar')
        ->and(Str::snake('fooBar', '-'))->toBe('foo-bar')
        ->and(Str::snake('fooBar'))->toBe('foo_bar');
});
