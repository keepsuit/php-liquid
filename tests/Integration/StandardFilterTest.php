<?php

use Keepsuit\Liquid\StandardFilters;
use Keepsuit\Liquid\Tests\Stubs\TestDrop;

test('size', function () {
    expect(StandardFilters::size([1, 2, 3]))->toBe(3);
    expect(StandardFilters::size([]))->toBe(0);
    expect(StandardFilters::size(null))->toBe(0);
});

test('downcase', function () {
    expect(StandardFilters::downcase('Testing'))->toBe('testing');
    expect(StandardFilters::downcase(null))->toBe('');
});

test('upcase', function () {
    expect(StandardFilters::upcase('Testing'))->toBe('TESTING');
    expect(StandardFilters::upcase(null))->toBe('');
});

test('slice', function () {
    expect(StandardFilters::slice('foobar', 1, 3))->toBe('oob');
    expect(StandardFilters::slice('foobar', 1, 1000))->toBe('oobar');
    expect(StandardFilters::slice('foobar', 1, 0))->toBe('');
    expect(StandardFilters::slice('foobar', 1, 1))->toBe('o');
    expect(StandardFilters::slice('foobar', 3, 3))->toBe('bar');
    expect(StandardFilters::slice('foobar', -2, 2))->toBe('ar');
    expect(StandardFilters::slice('foobar', -2, 1000))->toBe('ar');
    expect(StandardFilters::slice('foobar', -1))->toBe('r');
    expect(StandardFilters::slice(null, 0))->toBe('');
    expect(StandardFilters::slice('foobar', 100, 10))->toBe('');
    expect(StandardFilters::slice('foobar', -100, 10))->toBe('');
    expect(StandardFilters::slice('foobar', '1', '3'))->toBe('oob');
});

test('slice on arrays', function () {
    $input = mb_str_split('foobar');
    expect(StandardFilters::slice($input, 1, 3))->toBe(['o', 'o', 'b']);
    expect(StandardFilters::slice($input, 1, 1000))->toBe(['o', 'o', 'b', 'a', 'r']);
    expect(StandardFilters::slice($input, 1, 0))->toBe([]);
    expect(StandardFilters::slice($input, 1, 1))->toBe(['o']);
    expect(StandardFilters::slice($input, 3, 3))->toBe(['b', 'a', 'r']);
    expect(StandardFilters::slice($input, -2, 2))->toBe(['a', 'r']);
    expect(StandardFilters::slice($input, -2, 1000))->toBe(['a', 'r']);
    expect(StandardFilters::slice($input, -1))->toBe(['r']);
    expect(StandardFilters::slice($input, 100, 10))->toBe([]);
    expect(StandardFilters::slice($input, -100, 10))->toBe([]);
});

test('truncate', function () {
    expect(StandardFilters::truncate('1234567890', 7))->toBe('1234...');
    expect(StandardFilters::truncate('1234567890', 20))->toBe('1234567890');
    expect(StandardFilters::truncate('1234567890', 0))->toBe('...');
    expect(StandardFilters::truncate('1234567890'))->toBe('1234567890');
    expect(StandardFilters::truncate('测试测试测试测试', 5))->toBe('测试...');
    expect(StandardFilters::truncate('1234567890', 5, 1))->toBe('12341');
});

test('split', function () {
    expect(StandardFilters::split('12~34', '~'))->toBe(['12', '34']);
    expect(StandardFilters::split('A? ~ ~ ~ ,Z', '~ ~ ~'))->toBe(['A? ', ' ,Z']);
    expect(StandardFilters::split('A?Z', '~'))->toBe(['A?Z']);
    expect(StandardFilters::split(null, ' '))->toBe([]);
    expect(StandardFilters::split('A1Z', 1))->toBe(['A', 'Z']);
});

test('escape', function () {
    expect(StandardFilters::escape('<strong>'))->toBe('&lt;strong&gt;');
    expect(StandardFilters::escape(1))->toBe('1');
    //expect(StandardFilters::escape((new DateTime())->setDate(2001, 02, 03)))->toBe('2001-02-03');
    expect(StandardFilters::escape(null))->toBeNull;
});

test('escape once', function () {
    expect(StandardFilters::escapeOnce('&lt;strong&gt;Hulk</strong>'))->toBe('&lt;strong&gt;Hulk&lt;/strong&gt;');
});

test('base64 encode', function () {
    expect(StandardFilters::base64Encode('one two three'))->toBe('b25lIHR3byB0aHJlZQ==');
    expect(StandardFilters::base64Encode(null))->toBe('');
});

test('base64 decode', function () {
    expect(StandardFilters::base64Decode('b25lIHR3byB0aHJlZQ=='))->toBe('one two three');
    expect(StandardFilters::base64Decode(null))->toBe('');
    expect(fn () => StandardFilters::base64Decode('invalidbase64'))->toThrow(InvalidArgumentException::class);
});

test('url encode', function () {
    expect(StandardFilters::urlEncode('foo+1@example.com'))->toBe('foo%2B1%40example.com');
    expect(StandardFilters::urlEncode(1))->toBe('1');
    expect(StandardFilters::urlEncode(null))->toBe('');
});

test('url decode', function () {
    expect(StandardFilters::urlDecode('foo+bar'))->toBe('foo bar');
    expect(StandardFilters::urlDecode('foo%20bar'))->toBe('foo bar');
    expect(StandardFilters::urlDecode('foo%2B1%40example.com'))->toBe('foo+1@example.com');
    expect(StandardFilters::urlDecode(1))->toBe('1');
    expect(StandardFilters::urlDecode(null))->toBe('');
});

test('truncatewords', function () {
    expect(StandardFilters::truncatewords('one two three', 4))->toBe('one two three');
    expect(StandardFilters::truncatewords('one two three', 2))->toBe('one two...');
    expect(StandardFilters::truncatewords('one two three'))->toBe('one two three');
    expect(StandardFilters::truncatewords('Two small (13&#8221; x 5.5&#8221; x 10&#8221; high) baskets fit inside one large basket (13&#8221; x 16&#8221; x 10.5&#8221; high) with cover.', 15))
        ->toBe('Two small (13&#8221; x 5.5&#8221; x 10&#8221; high) baskets fit inside one large basket (13&#8221;...');
    expect(StandardFilters::truncatewords('测试测试测试测试', 5))->toBe('测试测试测试测试');
    expect(StandardFilters::truncatewords('one two three', 2, 1))->toBe('one two1');
    expect(StandardFilters::truncatewords("one  two\tthree\nfour", 3))->toBe('one two three...');
    expect(StandardFilters::truncatewords('one two three four', 2))->toBe('one two...');
    expect(StandardFilters::truncatewords('one two three four', 0))->toBe('one...');
});

test('strip html', function () {
    expect(StandardFilters::stripHtml('<div>test</div>'))->toBe('test');
    expect(StandardFilters::stripHtml("<div id='test'>test</div>"))->toBe('test');
    expect(StandardFilters::stripHtml("<script type='text/javascript'>document.write('some stuff');</script>"))->toBe('');
    expect(StandardFilters::stripHtml("<style type='text/css'>foo bar</style>"))->toBe('');
    expect(StandardFilters::stripHtml("<div\nclass='multiline'>test</div>"))->toBe('test');
    expect(StandardFilters::stripHtml("<!-- foo bar \n test -->test"))->toBe('test');
    expect(StandardFilters::stripHtml(null))->toBe('');
    expect(StandardFilters::stripHtml('<<<script </script>script>foo;</script>'))->toBe('foo;');
});

test('join', function () {
    expect(StandardFilters::join([1, 2, 3, 4]))->toBe('1 2 3 4');
    expect(StandardFilters::join([1, 2, 3, 4], ' - '))->toBe('1 - 2 - 3 - 4');
    expect(StandardFilters::join([1, 2, 3, 4], 1))->toBe('1121314');
});

test('sort', function () {
    expect(StandardFilters::sort([4, 3, 2, 1]))->toBe([1, 2, 3, 4]);
    expect(StandardFilters::sort([['a' => 4], ['a' => 3], ['a' => 1], ['a' => 2]], 'a'))->toBe([['a' => 1], ['a' => 2], ['a' => 3], ['a' => 4]]);
    expect(StandardFilters::sort([null, 4, 3, 2, 1]))->toBe([1, 2, 3, 4, null]);
    expect(StandardFilters::sort([['a' => 4], ['a' => 3], [], ['a' => 1], ['a' => 2]], 'a'))->toBe([['a' => 1], ['a' => 2], ['a' => 3], ['a' => 4], []]);
});

test('sort when property is sometimes missing puts nulls last', function () {
    expect(StandardFilters::sort([
        ['price' => 4, 'handle' => 'alpha'],
        ['handle' => 'beta'],
        ['price' => 1, 'handle' => 'gamma'],
        ['handle' => 'delta'],
        ['price' => 2, 'handle' => 'epsilon'],
    ], 'price'))->toBe([
        ['price' => 1, 'handle' => 'gamma'],
        ['price' => 2, 'handle' => 'epsilon'],
        ['price' => 4, 'handle' => 'alpha'],
        ['handle' => 'beta'],
        ['handle' => 'delta'],
    ]);
});

test('sort natural', function () {
    expect(StandardFilters::sortNatural(['c', 'D', 'a', 'B']))->toBe(['a', 'B', 'c', 'D']);
    expect(StandardFilters::sortNatural([['a' => 'D'], ['a' => 'c'], ['a' => 'a'], ['a' => 'B']], 'a'))->toBe([['a' => 'a'], ['a' => 'B'], ['a' => 'c'], ['a' => 'D']]);
    expect(StandardFilters::sortNatural([null, 'c', 'D', 'a', 'B']))->toBe(['a', 'B', 'c', 'D', null]);
    expect(StandardFilters::sortNatural([['a' => 'D'], ['a' => 'c'], [], ['a' => 'a'], ['a' => 'B']], 'a'))->toBe([['a' => 'a'], ['a' => 'B'], ['a' => 'c'], ['a' => 'D'], []]);
});

test('sort natural when property is sometimes missing puts nulls last', function () {
    expect(StandardFilters::sort([
        ['price' => '4', 'handle' => 'alpha'],
        ['handle' => 'beta'],
        ['price' => '1', 'handle' => 'gamma'],
        ['handle' => 'delta'],
        ['price' => 2, 'handle' => 'epsilon'],
    ], 'price'))->toBe([
        ['price' => '1', 'handle' => 'gamma'],
        ['price' => 2, 'handle' => 'epsilon'],
        ['price' => '4', 'handle' => 'alpha'],
        ['handle' => 'beta'],
        ['handle' => 'delta'],
    ]);
});

test('sort natural case check', function () {
    expect(StandardFilters::sortNatural([
        ['key' => 'X'],
        ['key' => 'Y'],
        ['key' => 'Z'],
        ['fake' => 't'],
        ['key' => 'a'],
        ['key' => 'b'],
        ['key' => 'c'],
    ], 'key'))->toBe([
        ['key' => 'a'],
        ['key' => 'b'],
        ['key' => 'c'],
        ['key' => 'X'],
        ['key' => 'Y'],
        ['key' => 'Z'],
        ['fake' => 't'],
    ]);
    expect(StandardFilters::sortNatural(['X', 'Y', 'Z', 'a', 'b', 'c']))->toBe(['a', 'b', 'c', 'X', 'Y', 'Z']);
});

test('sort empty array', function () {
    expect(StandardFilters::sort([], 'a'))->toBe([]);
    expect(StandardFilters::sortNatural([], 'a'))->toBe([]);
});

test('numerical vs lexicographical sort', function () {
    expect(StandardFilters::sort([10, 2]))->toBe([2, 10]);
    expect(StandardFilters::sort([['a' => 10], ['a' => 2]], 'a'))->toBe([['a' => 2], ['a' => 10]]);
    expect(StandardFilters::sort(['10', '2']))->toBe(['10', '2']);
    expect(StandardFilters::sort([['a' => '10'], ['a' => '2']], 'a'))->toBe([['a' => '10'], ['a' => '2']]);
});

test('uniq', function () {
    expect(StandardFilters::uniq(['foo']))->toBe(['foo']);
    expect(StandardFilters::uniq([1, 1, 3, 2, 3, 1, 4, 3, 2, 1]))->toBe([1, 3, 2, 4]);
    expect(StandardFilters::uniq([['a' => 1], ['a' => 3], ['a' => 1], ['a' => 2]], 'a'))->toBe([['a' => 1], ['a' => 3], ['a' => 2]]);
    expect(StandardFilters::uniq([], 'a'))->toBe([]);

    $testDrop = new TestDrop('test');
    $testDropAlternate = new TestDrop('test');
    expect(StandardFilters::uniq([$testDrop, $testDropAlternate], 'value'))->toBe([$testDrop]);
});

test('compact', function () {
    expect(StandardFilters::compact([]))->toBe([]);
    expect(StandardFilters::compact([1, null, 2, 3]))->toBe([1, 2, 3]);
    expect(StandardFilters::compact([['a' => 1], ['a' => 3], [], ['a' => 2]], 'a'))->toBe([['a' => 1], ['a' => 3], ['a' => 2]]);
});

test('reverse', function () {
    expect(StandardFilters::reverse([1, 2, 3, 4]))->toBe([4, 3, 2, 1]);
});

test('map', function () {
    expect(StandardFilters::map([['a' => 1], ['a' => 2], ['a' => 3], ['a' => 4]], 'a'))->toBe([1, 2, 3, 4]);

    assertTemplateResult(
        'abc',
        "{{ ary | map:'foo' | map:'bar' }}",
        ['ary' => [['foo' => ['bar' => 'a']], ['foo' => ['bar' => 'b']], ['foo' => ['bar' => 'c']]]],
    );
});

test('map calls toLiquid', function () {
    $thing = new ThingWithParamToLiquid();

    assertTemplateResult(
        "woot: 1",
        '{{ foo | map: "whatever" }}',
        ["foo" => [$thing]]
    );
});
