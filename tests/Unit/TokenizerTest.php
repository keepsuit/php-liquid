<?php

use Keepsuit\Liquid\Parse\ParseContext;

test('tokenize strings', function () {
    expect(tokenize(' '))->toBe([' ']);
    expect(tokenize('hello world'))->toBe(['hello world']);
});

it('tokenize variables', function () {
    expect(tokenize('{{funk}}'))->toBe(['{{funk}}']);
    expect(tokenize(' {{funk}} '))->toBe([' ', '{{funk}}', ' ']);
    expect(tokenize(' {{funk}} {{so}} {{brother}} '))->toBe([' ', '{{funk}}', ' ', '{{so}}', ' ', '{{brother}}', ' ']);
    expect(tokenize(' {{  funk  }} '))->toBe([' ', '{{  funk  }}', ' ']);
});

it('tokenize blocks', function () {
    expect(tokenize('{%comment%}'))->toBe(['{%comment%}']);
    expect(tokenize(' {%comment%} '))->toBe([' ', '{%comment%}', ' ']);

    expect(tokenize(' {%comment%} {%endcomment%} '))->toBe([' ', '{%comment%}', ' ', '{%endcomment%}', ' ']);
    expect(tokenize('  {% comment %} {% endcomment %} '))->toBe(['  ', '{% comment %}', ' ', '{% endcomment %}', ' ']);
});

it('calculate line numbers per token with profiling', function () {
    expect(tokenizeLineNumbers('{{funk}}'))->toBe([1]);
    expect(tokenizeLineNumbers(' {{funk}} '))->toBe([1, 1, 1]);
    expect(tokenizeLineNumbers("\n{{funk}}\n"))->toBe([1, 2, 2]);
    expect(tokenizeLineNumbers(" {{\n funk \n}} "))->toBe([1, 1, 3]);
});

function tokenize(string $source): array
{
    $tokenizer = (new ParseContext())->newTokenizer($source);

    $tokens = [];
    foreach ($tokenizer->shift() as $token) {
        $tokens[] = $token;
    }

    return $tokens;
}

function tokenizeLineNumbers(string $source): array
{
    $tokenizer = (new ParseContext(startLineNumber: 1))->newTokenizer($source);

    $lineNumbers = [];
    foreach ($tokenizer->shift() as $ignored) {
        $lineNumbers[] = $tokenizer->getStartLineNumber();
    }

    return $lineNumbers;
}
