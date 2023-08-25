<?php

use Keepsuit\Liquid\Parser\Regex;

test('empty')
    ->expect(regexMatch('', Regex::QuotedFragment))
    ->toBe([]);

test('quote')
    ->expect(regexMatch('"arg 1"', Regex::QuotedFragment))
    ->toBe(['"arg 1"']);

test('words')
    ->expect(regexMatch('arg1 arg2', Regex::QuotedFragment))
    ->toBe(['arg1', 'arg2']);

test('tags', function () {
    expect(regexMatch('<tr> </tr>', Regex::QuotedFragment))->toBe(['<tr>', '</tr>']);
    expect(regexMatch('<tr></tr>', Regex::QuotedFragment))->toBe(['<tr></tr>']);
    expect(regexMatch('<style class="hello"> </style>', Regex::QuotedFragment))->toBe(['<style', 'class="hello">', '</style>']);
});

test('double quoted words')
    ->expect(regexMatch('arg1 arg2 "arg 3"', Regex::QuotedFragment))
    ->toBe(['arg1', 'arg2', '"arg 3"']);

test('single quoted words')
    ->expect(regexMatch('arg1 arg2 \'arg 3\'', Regex::QuotedFragment))
    ->toBe(['arg1', 'arg2', "'arg 3'"]);

test('quoted words in the middle')
    ->expect(regexMatch('arg1 arg2 "arg 3" arg4   ', Regex::QuotedFragment))
    ->toBe(['arg1', 'arg2', '"arg 3"', 'arg4']);

test('variable parser', function () {
    expect(regexMatch('var', Regex::VariableParser))->toBe(['var']);
    expect(regexMatch('[var]', Regex::VariableParser))->toBe(['[var]']);
    expect(regexMatch('var.method', Regex::VariableParser))->toBe(['var', 'method']);
    expect(regexMatch('var[method]', Regex::VariableParser))->toBe(['var', '[method]']);
    expect(regexMatch('var[method][0]', Regex::VariableParser))->toBe(['var', '[method]', '[0]']);
    expect(regexMatch('var["method"][0]', Regex::VariableParser))->toBe(['var', '["method"]', '[0]']);
    expect(regexMatch('var[method][0].method', Regex::VariableParser))->toBe(['var', '[method]', '[0]', 'method']);
});

test('variable parser with large input', function () {
    $veryLongString = str_repeat('foo', 1000);

    // Valid dynamic lookup
    expect(regexMatch(sprintf('[%s]', $veryLongString), Regex::VariableParser))->toBe([sprintf('[%s]', $veryLongString)]);

    //Invalid dynamic lookup
    expect(regexMatch(sprintf('[%s', $veryLongString), Regex::VariableParser))->toBe([$veryLongString]);
});

function regexMatch(string $input, string $regex): array
{
    if (preg_match_all(sprintf('/%s/', $regex), $input, $matches) !== false) {
        return $matches[0];
    }

    return [];
}
