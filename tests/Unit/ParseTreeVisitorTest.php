<?php

use Keepsuit\Liquid\Arr;
use Keepsuit\Liquid\ParseTreeVisitor;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\VariableLookup;

test('variable', function () {
    expect(visit('{{ test }}'))->toBe(['test']);
});

test('variable with filter', function () {
    expect(visit('{{ test | split: infilter }}'))->toBe(['test', 'infilter']);
});

test('dynamic variable', function () {
    expect(visit('{{ test[inlookup] }}'))->toBe(['test', 'inlookup']);
});

test('echo', function () {
    expect(visit('{% echo test %}'))->toBe(['test']);
});

test('if condition', function () {
    expect(visit('{% if test %}{% endif %}'))->toBe(['test']);
});

function traversal(string $source): ParseTreeVisitor
{
    return ParseTreeVisitor::for(Template::parse($source)->root)
        ->addCallbackFor(VariableLookup::class, fn (VariableLookup $node) => [$node->name]);
}

function visit(string $source): array
{
    return Arr::compact(Arr::flatten(traversal($source)->visit()));
}
