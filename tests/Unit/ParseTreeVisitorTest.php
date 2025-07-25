<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ParseTreeVisitor;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

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

test('complex if condition', function () {
    expect(visit('{% if 1 == 1 and 2 == test %}{% endif %}'))->toBe(['test']);
});

test('if body', function () {
    expect(visit('{% if 1 == 1 %}{{ test }}{% endif %}'))->toBe(['test']);
});

test('unless condition', function () {
    expect(visit('{% unless test %}{% endunless %}'))->toBe(['test']);
});

test('complex unless condition', function () {
    expect(visit('{% unless 1 == 1 and 2 == test %}{% endunless %}'))->toBe(['test']);
});

test('unless body', function () {
    expect(visit('{% unless 1 == 1 %}{{ test }}{% endunless %}'))->toBe(['test']);
});

test('elseif condition', function () {
    expect(visit('{% if 1 == 1 %}{% elsif test %}{% endif %}'))->toBe(['test']);
});

test('complex elseif condition', function () {
    expect(visit('{% if 1 == 1 %}{% elsif 1 == 1 and 2 == test %}{% endif %}'))->toBe(['test']);
});

test('elseif body condition', function () {
    expect(visit('{% if 1 == 1 %}{% elsif 2 == 2 %}{{ test }}{% endif %}'))->toBe(['test']);
});

test('else body condition', function () {
    expect(visit('{% if 1 == 1 %}{% else %}{{ test }}{% endif %}'))->toBe(['test']);
});

test('case left', function () {
    expect(visit('{% case test %}{% endcase %}'))->toBe(['test']);
});

test('case condition', function () {
    expect(visit('{% case 1 %}{% when test %}{% endcase %}'))->toBe(['test']);
});

test('case when body', function () {
    expect(visit('{% case 1 %}{% when 2 %}{{ test }}{% endcase %}'))->toBe(['test']);
});

test('case else body', function () {
    expect(visit('{% case 1 %}{% else %}{{ test }}{% endcase %}'))->toBe(['test']);
});

test('for in', function () {
    expect(visit('{% for x in test %}{% endfor %}'))->toBe(['test']);
});

test('for limit', function () {
    expect(visit('{% for x in (1..5) limit: test %}{% endfor %}'))->toBe(['test']);
});

test('for offset', function () {
    expect(visit('{% for x in (1..5) offset: test %}{% endfor %}'))->toBe(['test']);
});

test('for body', function () {
    expect(visit('{% for x in (1..5) %}{{ test }}{% endfor %}'))->toBe(['test']);
});

test('for range', function () {
    expect(visit('{% for x in (1..test) %}{% endfor %}'))->toBe(['test']);
});

test('tablerow in', function () {
    expect(visit('{% tablerow x in test %}{% endtablerow %}'))->toBe(['test']);
});

test('tablerow limit', function () {
    expect(visit('{% tablerow x in (1..5) limit: test %}{% endtablerow %}'))->toBe(['test']);
});

test('tablerow offset', function () {
    expect(visit('{% tablerow x in (1..5) offset: test %}{% endtablerow %}'))->toBe(['test']);
});

test('tablerow body', function () {
    expect(visit('{% tablerow x in (1..5) %}{{ test }}{% endtablerow %}'))->toBe(['test']);
});

test('cycle', function () {
    $traversal = traversal('{% cycle "test" %}')
        ->addCallbackFor('string', fn (string $node) => [$node, null]);

    expect(Arr::compact(Arr::flatten($traversal->visit())))->toBe(['test']);
});

test('assign', function () {
    expect(visit('{% assign x = test %}'))->toBe(['test']);
});

test('capture', function () {
    expect(visit('{% capture x %}{{ test }}{% endcapture %}'))->toBe(['test']);
});

test('render for', function () {
    expect(visit('{% render "hai" for test %}'))->toBe(['test']);
});

test('preserve tree structure', function () {
    expect(traversal('{% for x in xs offset: test %}{{ other }}{% endfor %}')->visit())
        ->toBe([
            [
                null,
                [
                    [
                        null,
                        [
                            [null, [[null, [['other', [[null, []]]]]]]],
                            ['test', [[null, []]]],
                            ['xs', [[null, []]]],
                        ],
                    ],
                ],
            ],
        ]);
});

test('doc', function () {
    expect(visit('{% doc %}{{ test }}{% enddoc %}'))->toBe([]);
});

function traversal(string $source): ParseTreeVisitor
{
    $environment = EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem(['hai' => 'hei']))
        ->build();

    return ParseTreeVisitor::for($environment->parseString($source))
        ->addCallbackFor(VariableLookup::class, fn (VariableLookup $node) => [$node->name, null]);
}

function visit(string $source): array
{
    return Arr::compact(Arr::flatten(traversal($source)->visit()));
}
