<?php

use Keepsuit\Liquid\Tests\Stubs\BooleanDrop;
use Keepsuit\Liquid\Tests\Stubs\NumberDrop;
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

test('map on hashes', function () {
    assertTemplateResult(
        '4217',
        '{{ thing | map: "foo" | map: "bar" }}',
        assigns: ['thing' => ['foo' => [['bar' => 42], ['bar' => 17]]]]
    );
});

test('legacy map on hashes with dynamic key', function () {
    assertTemplateResult(
        '42',
        '{% assign key = \'foo\' %}{{ thing | map: key | map: \'bar\' }}',
        assigns: ['thing' => ['foo' => ['bar' => 42]]]
    );
});

test('sort calls to liquid', function () {
    $t = new \Keepsuit\Liquid\Tests\Stubs\ThingWithParamToLiquid();

    assertTemplateResult(
        'woot: 1',
        '{{ foo | sort: "whatever" }}',
        assigns: ['foo' => [$t]]
    );

    expect($t->value)->toBe(1);
});

test('map over Closure', function () {
    $d = new TestDrop('testfoo');
    $c = fn () => $d;

    assertTemplateResult(
        'testfoo',
        '{{ closures | map: "value" }}',
        assigns: ['closures' => [$c]]
    );
});

test('map over drops returning Closures', function () {
    $drops = [
        ['closure' => fn () => 'foo'],
        ['closure' => fn () => 'bar'],
    ];

    assertTemplateResult(
        'foobar',
        '{{ drops | map: "closure" }}',
        assigns: ['drops' => $drops]
    );
});

test('map works on iterator', function () {
    assertTemplateResult(
        '123',
        '{{ foo | map: "foo" }}',
        assigns: ['foo' => new \Keepsuit\Liquid\Tests\Stubs\IteratorDrop()]
    );
});

test('map returns empty on 2d input array', function () {
    $foo = [
        [1],
        [2],
        [3],
    ];

    expect(fn () => $this->filters->invoke('map', $foo, 'bar'))->toThrow(InvalidArgumentException::class);
});

test('map returns empty with no property', function () {
    $foo = [
        [1],
        [2],
        [3],
    ];

    expect(fn () => $this->filters->invoke('map', $foo, null))->toThrow(TypeError::class);
});

test('sort works on iterator', function () {
    assertTemplateResult(
        '213',
        '{{ foo | sort: "bar" | map: "foo" }}',
        assigns: ['foo' => new \Keepsuit\Liquid\Tests\Stubs\IteratorDrop()]
    );
});

test('first and last calls toLiquid', function () {
    assertTemplateResult(
        'foobar',
        '{{ foo | first }}',
        assigns: ['foo' => [new \Keepsuit\Liquid\Tests\Stubs\ThingWithToLiquid()]]
    );
    assertTemplateResult(
        'foobar',
        '{{ foo | last }}',
        assigns: ['foo' => [new \Keepsuit\Liquid\Tests\Stubs\ThingWithToLiquid()]]
    );
});

test('truncate calls toLiquid', function () {
    assertTemplateResult(
        'wo...',
        '{{ foo | truncate: 5 }}',
        assigns: ['foo' => new \Keepsuit\Liquid\Tests\Stubs\ThingWithParamToLiquid()]
    );
});

test('date', function () {
    expect($this->filters->invoke('date', new DateTime('2006-05-05 10:00:00'), '%B'))->toBe('May');
    expect($this->filters->invoke('date', new DateTime('2006-06-05 10:00:00'), '%B'))->toBe('June');
    expect($this->filters->invoke('date', new DateTime('2006-07-05 10:00:00'), '%B'))->toBe('July');

    expect($this->filters->invoke('date', '2006-05-05 10:00:00', '%B'))->toBe('May');
    expect($this->filters->invoke('date', '2006-06-05 10:00:00', '%B'))->toBe('June');
    expect($this->filters->invoke('date', '2006-07-05 10:00:00', '%B'))->toBe('July');

    expect($this->filters->invoke('date', '2006-07-05 10:00:00', ''))->toBe('2006-07-05 10:00:00');
    expect($this->filters->invoke('date', '2006-07-05 10:00:00', null))->toBe('2006-07-05 10:00:00');

    expect($this->filters->invoke('date', '2006-07-05 10:00:00', '%m/%d/%Y'))->toBe('07/05/2006');

    expect($this->filters->invoke('date', 'Fri Jul 16 01:00:00 2004', '%m/%d/%Y'))->toBe('07/16/2004');
    expect($this->filters->invoke('date', 'now', '%Y'))->toBe(date('Y'));
    expect($this->filters->invoke('date', 'today', '%Y'))->toBe(date('Y'));

    expect($this->filters->invoke('date', null, '%B'))->toBeNull();
    expect($this->filters->invoke('date', '', '%B'))->toBe('');

    expect($this->filters->invoke('date', 1152098955, '%m/%d/%Y'))->toBe('07/05/2006');
    expect($this->filters->invoke('date', '1152098955', '%m/%d/%Y'))->toBe('07/05/2006');
});

test('first last', function () {
    expect($this->filters->invoke('first', [1, 2, 3]))->toBe(1);
    expect($this->filters->invoke('last', [1, 2, 3]))->toBe(3);

    expect($this->filters->invoke('first', []))->toBeNull();
    expect($this->filters->invoke('last', []))->toBeNull();
});

test('replace', function () {
    expect($this->filters->invoke('replace', 'a a a a', 'a', 'b'))->toBe('b b b b');
    expect($this->filters->invoke('replace', '1 1 1 1', 1, 2))->toBe('2 2 2 2');
    expect($this->filters->invoke('replace', '1 1 1 1', 2, 3))->toBe('1 1 1 1');
    assertTemplateResult(
        '2 2 2 2',
        "{{ '1 1 1 1' | replace: '1', 2 }}",
    );

    expect($this->filters->invoke('replace_first', 'a a a a', 'a', 'b'))->toBe('b a a a');
    expect($this->filters->invoke('replace_first', '1 1 1 1', 1, 2))->toBe('2 1 1 1');
    expect($this->filters->invoke('replace_first', '1 1 1 1', 2, 3))->toBe('1 1 1 1');
    assertTemplateResult(
        '2 1 1 1',
        "{{ '1 1 1 1' | replace_first: '1', 2 }}",
    );

    expect($this->filters->invoke('replace_last', 'a a a a', 'a', 'b'))->toBe('a a a b');
    expect($this->filters->invoke('replace_last', '1 1 1 1', 1, 2))->toBe('1 1 1 2');
    expect($this->filters->invoke('replace_last', '1 1 1 1', 2, 3))->toBe('1 1 1 1');
    assertTemplateResult(
        '1 1 1 2',
        "{{ '1 1 1 1' | replace_last: '1', 2 }}",
    );
});

test('remove', function () {
    expect($this->filters->invoke('remove', 'a a a a', 'a'))->toBe('   ');
    assertTemplateResult(
        '   ',
        "{{ '1 1 1 1' | remove: 1 }}",
    );

    expect($this->filters->invoke('remove_first', 'a b a a', 'a '))->toBe('b a a');
    assertTemplateResult(
        ' 1 1 1',
        "{{ '1 1 1 1' | remove_first: 1 }}",
    );

    expect($this->filters->invoke('remove_last', 'a a b a', ' a'))->toBe('a a b');
    assertTemplateResult(
        '1 1 1 ',
        "{{ '1 1 1 1' | remove_last: 1 }}",
    );
});

test('pipes in string arguments', function () {
    assertTemplateResult('foobar', "{{ 'foo|bar' | remove: '|' }}");
});

test('strip', function () {
    assertTemplateResult('ab c', '{{ source | strip }}', assigns: ['source' => ' ab c  ']);
    assertTemplateResult('ab c', '{{ source | strip }}', assigns: ['source' => " \tab c  \n \t"]);
});

test('lstrip', function () {
    assertTemplateResult('ab c  ', '{{ source | lstrip }}', assigns: ['source' => ' ab c  ']);
    assertTemplateResult("ab c  \n \t", '{{ source | lstrip }}', assigns: ['source' => " \tab c  \n \t"]);
});

test('rstrip', function () {
    assertTemplateResult(' ab c', '{{ source | rstrip }}', assigns: ['source' => ' ab c  ']);
    assertTemplateResult(" \tab c", '{{ source | rstrip }}', assigns: ['source' => " \tab c  \n \t"]);
});

test('strip new lines', function () {
    assertTemplateResult('abc', '{{ source | strip_newlines }}', assigns: ['source' => "a\nb\nc"]);
    assertTemplateResult('abc', '{{ source | strip_newlines }}', assigns: ['source' => "a\r\nb\nc"]);
});

test('new lines to br', function () {
    assertTemplateResult("a<br />\nb<br />\nc", '{{ source | newline_to_br }}', assigns: ['source' => "a\nb\nc"]);
    assertTemplateResult("a<br />\nb<br />\nc", '{{ source | newline_to_br }}', assigns: ['source' => "a\r\nb\nc"]);
});

test('plus', function () {
    assertTemplateResult('2', '{{ 1 | plus:1 }}');
    assertTemplateResult('2.1', "{{ '1' | plus:'1.1' }}");

    assertTemplateResult('5', "{{ price | plus:'2' }}", assigns: ['price' => new NumberDrop(3)]);
});

test('minus', function () {
    assertTemplateResult('4', '{{ 5 | minus:1 }}');
    assertTemplateResult('2.3', "{{ '4.3' | minus:'2' }}");

    assertTemplateResult('5', "{{ price | minus:'2' }}", assigns: ['price' => new NumberDrop(7)]);
});

test('abs', function () {
    assertTemplateResult('17', '{{ 17 | abs }}');
    assertTemplateResult('17', '{{ -17 | abs }}');
    assertTemplateResult('17', "{{ '17' | abs }}");
    assertTemplateResult('17', "{{ '-17' | abs }}");
    assertTemplateResult('0', '{{ 0 | abs }}');
    assertTemplateResult('0', "{{ '0' | abs }}");
    assertTemplateResult('17.42', '{{ 17.42 | abs }}');
    assertTemplateResult('17.42', '{{ -17.42 | abs }}');
    assertTemplateResult('17.42', "{{ '17.42' | abs }}");
    assertTemplateResult('17.42', "{{ '-17.42' | abs }}");
});

test('times', function () {
    assertTemplateResult('12', '{{ 3 | times:4 }}');
    assertTemplateResult('7.25', '{{ 0.0725 | times:100 }}');
    assertTemplateResult('-7.25', '{{ "-0.0725" | times:100 }}');
    assertTemplateResult('7.25', '{{ "-0.0725" | times: -100 }}');
    assertTemplateResult('4', '{{ price | times:2 }}', ['price' => new NumberDrop(2)]);
});

test('divided by', function () {
    assertTemplateResult('4', '{{ 12 | divided_by:3 }}');
    assertTemplateResult('4', '{{ 14 | divided_by:3 }}');

    assertTemplateResult('5', '{{ 15 | divided_by:3 }}');
    expect(fn () => parseTemplate('{{ 5 | divided_by:0 }}'))->toThrow(DivisionByZeroError::class);

    assertTemplateResult('0.5', '{{ 2.0 | divided_by:4 }}');
    assertTemplateResult('5', '{{ price | divided_by:2 }}', ['price' => new NumberDrop(10)]);
});

test('modulo', function () {
    assertTemplateResult('1', '{{ 3 | modulo:2 }}');
    expect(fn () => parseTemplate('{{ 1 | modulo: 0 }}'))->toThrow(DivisionByZeroError::class);
    assertTemplateResult('1', '{{ price | modulo:2 }}', ['price' => new NumberDrop(3)]);
});

test('round', function () {
    assertTemplateResult('5', '{{ input | round }}', ['input' => 4.6]);
    assertTemplateResult('4', "{{ '4.3' | round }}");
    assertTemplateResult('4.56', '{{ input | round: 2 }}', ['input' => 4.5612]);
    assertTemplateResult('5', '{{ price | round }}', ['price' => new NumberDrop(4.6)]);
    assertTemplateResult('4', '{{ price | round }}', ['price' => new NumberDrop(4.3)]);
});

test('ceil', function () {
    assertTemplateResult('5', '{{ input | ceil }}', ['input' => 4.6]);
    assertTemplateResult('5', "{{ '4.3' | ceil }}");
    assertTemplateResult('5', '{{ price | ceil }}', ['price' => new NumberDrop(4.6)]);
});

test('floor', function () {
    assertTemplateResult('4', '{{ input | floor }}', ['input' => 4.6]);
    assertTemplateResult('4', "{{ '4.3' | floor }}");
    assertTemplateResult('4', '{{ price | floor }}', ['price' => new NumberDrop(4.6)]);
});

test('at most', function () {
    assertTemplateResult('4', '{{ 5 | at_most:4 }}');
    assertTemplateResult('5', '{{ 5 | at_most:5 }}');
    assertTemplateResult('5', '{{ 5 | at_most:6 }}');

    assertTemplateResult('4.5', '{{ 4.5 | at_most:5 }}');
    assertTemplateResult('5', '{{ width | at_most:5 }}', ['width' => new NumberDrop(6)]);
    assertTemplateResult('4', '{{ width | at_most:5 }}', ['width' => new NumberDrop(4)]);
    assertTemplateResult('4', '{{ 5 | at_most:width }}', ['width' => new NumberDrop(4)]);
});

test('at least', function () {
    assertTemplateResult('5', '{{ 5 | at_least:4 }}');
    assertTemplateResult('5', '{{ 5 | at_least:5 }}');
    assertTemplateResult('6', '{{ 5 | at_least:6 }}');

    assertTemplateResult('5', '{{ 4.5 | at_least:5 }}');
    assertTemplateResult('6', '{{ width | at_least:5 }}', ['width' => new NumberDrop(6)]);
    assertTemplateResult('5', '{{ width | at_least:5 }}', ['width' => new NumberDrop(4)]);
    assertTemplateResult('6', '{{ 5 | at_least:width }}', ['width' => new NumberDrop(6)]);
});

test('append', function () {
    assertTemplateResult('bcd', "{{ a | append: 'd'}}", ['a' => 'bc', 'b' => 'd']);
    assertTemplateResult('bcd', '{{ a | append: b}}', ['a' => 'bc', 'b' => 'd']);
});

test('prepend', function () {
    assertTemplateResult('abc', "{{ a | prepend: 'a'}}", ['a' => 'bc', 'b' => 'a']);
    assertTemplateResult('abc', '{{ a | prepend: b}}', ['a' => 'bc', 'b' => 'a']);
});

test('concat', function () {
    expect($this->filters->invoke('concat', [1, 2], [3, 4]))->toBe([1, 2, 3, 4]);
    expect($this->filters->invoke('concat', [1, 2], ['a']))->toBe([1, 2, 'a']);
    expect($this->filters->invoke('concat', [1, 2], [10]))->toBe([1, 2, 10]);

    expect(fn () => $this->filters->invoke('concat', [1, 2], 10))->toThrow(TypeError::class);
});

test('default', function () {
    expect($this->filters->invoke('default', 'foo', 'bar'))->toBe('foo');
    expect($this->filters->invoke('default', null, 'bar'))->toBe('bar');
    expect($this->filters->invoke('default', '', 'bar'))->toBe('bar');
    expect($this->filters->invoke('default', false, 'bar'))->toBe('bar');
    expect($this->filters->invoke('default', [], 'bar'))->toBe('bar');

    assertTemplateResult('bar', "{{ false | default: 'bar' }}");
    assertTemplateResult('bar', "{{ drop | default: 'bar' }}", ['drop' => new BooleanDrop(false)]);
    assertTemplateResult('Yay', "{{ drop | default: 'bar' }}", ['drop' => new BooleanDrop(true)]);
});

test('default handle false', function () {
    expect($this->filters->invoke('default', 'foo', 'bar', allow_false: true))->toBe('foo');
    expect($this->filters->invoke('default', null, 'bar', allow_false: true))->toBe('bar');
    expect($this->filters->invoke('default', '', 'bar', allow_false: true))->toBe('bar');
    expect($this->filters->invoke('default', false, 'bar', allow_false: true))->toBe(false);
    expect($this->filters->invoke('default', [], 'bar', allow_false: true))->toBe('bar');

    assertTemplateResult('false', "{{ false | default: 'bar', allow_false: true }}");
    assertTemplateResult('Nay', "{{ drop | default: 'bar', allow_false: true }}", ['drop' => new BooleanDrop(false)]);
    assertTemplateResult('Yay', "{{ drop | default: 'bar', allow_false: true }}", ['drop' => new BooleanDrop(true)]);
});
