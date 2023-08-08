<?php

use Keepsuit\Liquid\Tests\Stubs\TestDrop;

beforeEach(function () {
    $this->filters = new \Keepsuit\Liquid\FilterRegistry(new \Keepsuit\Liquid\Context());
});

test('size', function () {
    expect($this->filters->invoke('size', [1, 2, 3]))->toBe(3);
    expect($this->filters->invoke('size', []))->toBe(0);
    expect($this->filters->invoke('size', null))->toBe(0);
});

test('downcase', function () {
    expect($this->filters->invoke('downcase', 'Testing'))->toBe('testing');
    expect($this->filters->invoke('downcase', null))->toBe('');
});

test('upcase', function () {
    expect($this->filters->invoke('upcase', 'Testing'))->toBe('TESTING');
    expect($this->filters->invoke('upcase', null))->toBe('');
});

test('slice', function () {
    expect($this->filters->invoke('slice', 'foobar', 1, 3))->toBe('oob');
    expect($this->filters->invoke('slice', 'foobar', 1, 1000))->toBe('oobar');
    expect($this->filters->invoke('slice', 'foobar', 1, 0))->toBe('');
    expect($this->filters->invoke('slice', 'foobar', 1, 1))->toBe('o');
    expect($this->filters->invoke('slice', 'foobar', 3, 3))->toBe('bar');
    expect($this->filters->invoke('slice', 'foobar', -2, 2))->toBe('ar');
    expect($this->filters->invoke('slice', 'foobar', -2, 1000))->toBe('ar');
    expect($this->filters->invoke('slice', 'foobar', -1))->toBe('r');
    expect($this->filters->invoke('slice', null, 0))->toBe('');
    expect($this->filters->invoke('slice', 'foobar', 100, 10))->toBe('');
    expect($this->filters->invoke('slice', 'foobar', -100, 10))->toBe('');
    expect($this->filters->invoke('slice', 'foobar', '1', '3'))->toBe('oob');
});

test('slice on arrays', function () {
    $input = mb_str_split('foobar');
    expect($this->filters->invoke('slice', $input, 1, 3))->toBe(['o', 'o', 'b']);
    expect($this->filters->invoke('slice', $input, 1, 1000))->toBe(['o', 'o', 'b', 'a', 'r']);
    expect($this->filters->invoke('slice', $input, 1, 0))->toBe([]);
    expect($this->filters->invoke('slice', $input, 1, 1))->toBe(['o']);
    expect($this->filters->invoke('slice', $input, 3, 3))->toBe(['b', 'a', 'r']);
    expect($this->filters->invoke('slice', $input, -2, 2))->toBe(['a', 'r']);
    expect($this->filters->invoke('slice', $input, -2, 1000))->toBe(['a', 'r']);
    expect($this->filters->invoke('slice', $input, -1))->toBe(['r']);
    expect($this->filters->invoke('slice', $input, 100, 10))->toBe([]);
    expect($this->filters->invoke('slice', $input, -100, 10))->toBe([]);
});

test('truncate', function () {
    expect($this->filters->invoke('truncate', '1234567890', 7))->toBe('1234...');
    expect($this->filters->invoke('truncate', '1234567890', 20))->toBe('1234567890');
    expect($this->filters->invoke('truncate', '1234567890', 0))->toBe('...');
    expect($this->filters->invoke('truncate', '1234567890'))->toBe('1234567890');
    expect($this->filters->invoke('truncate', '测试测试测试测试', 5))->toBe('测试...');
    expect($this->filters->invoke('truncate', '1234567890', 5, 1))->toBe('12341');
});

test('split', function () {
    expect($this->filters->invoke('split', '12~34', '~'))->toBe(['12', '34']);
    expect($this->filters->invoke('split', 'A? ~ ~ ~ ,Z', '~ ~ ~'))->toBe(['A? ', ' ,Z']);
    expect($this->filters->invoke('split', 'A?Z', '~'))->toBe(['A?Z']);
    expect($this->filters->invoke('split', null, ' '))->toBe([]);
    expect($this->filters->invoke('split', 'A1Z', 1))->toBe(['A', 'Z']);
});

test('escape', function () {
    expect($this->filters->invoke('escape', '<strong>'))->toBe('&lt;strong&gt;');
    expect($this->filters->invoke('escape', 1))->toBe('1');
    //expect($this->filters->invoke('escape',(new DateTime())->setDate(2001, 02, 03)))->toBe('2001-02-03');
    expect($this->filters->invoke('escape', null))->toBeNull;
});

test('escape once', function () {
    expect($this->filters->invoke('escape_once', '&lt;strong&gt;Hulk</strong>'))->toBe('&lt;strong&gt;Hulk&lt;/strong&gt;');
});

test('base64 encode', function () {
    expect($this->filters->invoke('base64_encode', 'one two three'))->toBe('b25lIHR3byB0aHJlZQ==');
    expect($this->filters->invoke('base64_encode', null))->toBe('');
});

test('base64 decode', function () {
    expect($this->filters->invoke('base64_decode', 'b25lIHR3byB0aHJlZQ=='))->toBe('one two three');
    expect($this->filters->invoke('base64_decode', null))->toBe('');
    expect(fn () => $this->filters->invoke('base64_decode', 'invalidbase64'))->toThrow(InvalidArgumentException::class);
});

test('url encode', function () {
    expect($this->filters->invoke('url_encode', 'foo+1@example.com'))->toBe('foo%2B1%40example.com');
    expect($this->filters->invoke('url_encode', 1))->toBe('1');
    expect($this->filters->invoke('url_encode', null))->toBe('');
});

test('url decode', function () {
    expect($this->filters->invoke('url_decode', 'foo+bar'))->toBe('foo bar');
    expect($this->filters->invoke('url_decode', 'foo%20bar'))->toBe('foo bar');
    expect($this->filters->invoke('url_decode', 'foo%2B1%40example.com'))->toBe('foo+1@example.com');
    expect($this->filters->invoke('url_decode', 1))->toBe('1');
    expect($this->filters->invoke('url_decode', null))->toBe('');
});

test('truncatewords', function () {
    expect($this->filters->invoke('truncatewords', 'one two three', 4))->toBe('one two three');
    expect($this->filters->invoke('truncatewords', 'one two three', 2))->toBe('one two...');
    expect($this->filters->invoke('truncatewords', 'one two three'))->toBe('one two three');
    expect($this->filters->invoke('truncatewords', 'Two small (13&#8221; x 5.5&#8221; x 10&#8221; high) baskets fit inside one large basket (13&#8221; x 16&#8221; x 10.5&#8221; high) with cover.', 15))
        ->toBe('Two small (13&#8221; x 5.5&#8221; x 10&#8221; high) baskets fit inside one large basket (13&#8221;...');
    expect($this->filters->invoke('truncatewords', '测试测试测试测试', 5))->toBe('测试测试测试测试');
    expect($this->filters->invoke('truncatewords', 'one two three', 2, 1))->toBe('one two1');
    expect($this->filters->invoke('truncatewords', "one  two\tthree\nfour", 3))->toBe('one two three...');
    expect($this->filters->invoke('truncatewords', 'one two three four', 2))->toBe('one two...');
    expect($this->filters->invoke('truncatewords', 'one two three four', 0))->toBe('one...');
});

test('strip html', function () {
    expect($this->filters->invoke('strip_html', '<div>test</div>'))->toBe('test');
    expect($this->filters->invoke('strip_html', "<div id='test'>test</div>"))->toBe('test');
    expect($this->filters->invoke('strip_html', "<script type='text/javascript'>document.write('some stuff');</script>"))->toBe('');
    expect($this->filters->invoke('strip_html', "<style type='text/css'>foo bar</style>"))->toBe('');
    expect($this->filters->invoke('strip_html', "<div\nclass='multiline'>test</div>"))->toBe('test');
    expect($this->filters->invoke('strip_html', "<!-- foo bar \n test -->test"))->toBe('test');
    expect($this->filters->invoke('strip_html', null))->toBe('');
    expect($this->filters->invoke('strip_html', '<<<script </script>script>foo;</script>'))->toBe('foo;');
});

test('join', function () {
    expect($this->filters->invoke('join', [1, 2, 3, 4]))->toBe('1 2 3 4');
    expect($this->filters->invoke('join', [1, 2, 3, 4], ' - '))->toBe('1 - 2 - 3 - 4');
    expect($this->filters->invoke('join', [1, 2, 3, 4], 1))->toBe('1121314');
});

test('sort', function () {
    expect($this->filters->invoke('sort', [4, 3, 2, 1]))->toBe([1, 2, 3, 4]);
    expect($this->filters->invoke('sort', [['a' => 4], ['a' => 3], ['a' => 1], ['a' => 2]], 'a'))->toBe([['a' => 1], ['a' => 2], ['a' => 3], ['a' => 4]]);
    expect($this->filters->invoke('sort', [null, 4, 3, 2, 1]))->toBe([1, 2, 3, 4, null]);
    expect($this->filters->invoke('sort', [['a' => 4], ['a' => 3], [], ['a' => 1], ['a' => 2]], 'a'))->toBe([['a' => 1], ['a' => 2], ['a' => 3], ['a' => 4], []]);
});

test('sort when property is sometimes missing puts nulls last', function () {
    expect($this->filters->invoke('sort', [
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
    expect($this->filters->invoke('sort_natural', ['c', 'D', 'a', 'B']))->toBe(['a', 'B', 'c', 'D']);
    expect($this->filters->invoke('sort_natural', [['a' => 'D'], ['a' => 'c'], ['a' => 'a'], ['a' => 'B']], 'a'))->toBe([['a' => 'a'], ['a' => 'B'], ['a' => 'c'], ['a' => 'D']]);
    expect($this->filters->invoke('sort_natural', [null, 'c', 'D', 'a', 'B']))->toBe(['a', 'B', 'c', 'D', null]);
    expect($this->filters->invoke('sort_natural', [['a' => 'D'], ['a' => 'c'], [], ['a' => 'a'], ['a' => 'B']], 'a'))->toBe([['a' => 'a'], ['a' => 'B'], ['a' => 'c'], ['a' => 'D'], []]);
});

test('sort natural when property is sometimes missing puts nulls last', function () {
    expect($this->filters->invoke('sort', [
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
    expect($this->filters->invoke('sort_natural', [
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
    expect($this->filters->invoke('sort_natural', ['X', 'Y', 'Z', 'a', 'b', 'c']))->toBe(['a', 'b', 'c', 'X', 'Y', 'Z']);
});

test('sort empty array', function () {
    expect($this->filters->invoke('sort', [], 'a'))->toBe([]);
    expect($this->filters->invoke('sort_natural', [], 'a'))->toBe([]);
});

test('numerical vs lexicographical sort', function () {
    expect($this->filters->invoke('sort', [10, 2]))->toBe([2, 10]);
    expect($this->filters->invoke('sort', [['a' => 10], ['a' => 2]], 'a'))->toBe([['a' => 2], ['a' => 10]]);
    expect($this->filters->invoke('sort', ['10', '2']))->toBe(['10', '2']);
    expect($this->filters->invoke('sort', [['a' => '10'], ['a' => '2']], 'a'))->toBe([['a' => '10'], ['a' => '2']]);
});

test('uniq', function () {
    expect($this->filters->invoke('uniq', ['foo']))->toBe(['foo']);
    expect($this->filters->invoke('uniq', [1, 1, 3, 2, 3, 1, 4, 3, 2, 1]))->toBe([1, 3, 2, 4]);
    expect($this->filters->invoke('uniq', [['a' => 1], ['a' => 3], ['a' => 1], ['a' => 2]], 'a'))->toBe([['a' => 1], ['a' => 3], ['a' => 2]]);
    expect($this->filters->invoke('uniq', [], 'a'))->toBe([]);

    $testDrop = new TestDrop('test');
    $testDropAlternate = new TestDrop('test');
    expect($this->filters->invoke('uniq', [$testDrop, $testDropAlternate], 'value'))->toBe([$testDrop]);
});

test('compact', function () {
    expect($this->filters->invoke('compact', []))->toBe([]);
    expect($this->filters->invoke('compact', [1, null, 2, 3]))->toBe([1, 2, 3]);
    expect($this->filters->invoke('compact', [['a' => 1], ['a' => 3], [], ['a' => 2]], 'a'))->toBe([['a' => 1], ['a' => 3], ['a' => 2]]);
});

test('reverse', function () {
    expect($this->filters->invoke('reverse', [1, 2, 3, 4]))->toBe([4, 3, 2, 1]);
});

test('map', function () {
    expect($this->filters->invoke('map', [['a' => 1], ['a' => 2], ['a' => 3], ['a' => 4]], 'a'))->toBe([1, 2, 3, 4]);

    assertTemplateResult(
        'abc',
        "{{ ary | map:'foo' | map:'bar' }}",
        ['ary' => [['foo' => ['bar' => 'a']], ['foo' => ['bar' => 'b']], ['foo' => ['bar' => 'c']]]],
    );
});

test('map calls toLiquid', function () {
    $thing = new \Keepsuit\Liquid\Tests\Stubs\ThingWithParamToLiquid();

    assertTemplateResult(
        'woot: 1',
        '{{ foo | map: "whatever" }}',
        ['foo' => [$thing]]
    );
});

test('map calls context', function () {
    $model = new \Keepsuit\Liquid\Tests\Stubs\TestModel('test');

    assertTemplateResult(
        '{test=>1234}',
        '{{ foo | map: "registers" }}',
        assigns: [
            'foo' => $model,
        ],
        registers: [
            'test' => 1234,
        ]
    );
});
