<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\Token;

test('lexer preserves tricky token boundaries', function (string $source, array $expected) {
    expect(lexerQuirkSerializeTokens($source))->toBe($expected);
})->with([
    'a-5 stays one identifier' => [
        '{{ a-5 }}',
        [['VariableStart', ''], ['Identifier', 'a-5'], ['VariableEnd', '']],
    ],
    'a--b splits at the second dash' => [
        '{{ a--b }}',
        [['VariableStart', ''], ['Identifier', 'a'], ['Dash', '-'], ['Dash', '-'], ['Identifier', 'b'], ['VariableEnd', '']],
    ],
    'a- leaves the dash behind' => [
        '{{ a- }}',
        [['VariableStart', ''], ['Identifier', 'a'], ['Dash', '-'], ['VariableEnd', '']],
    ],
    'a-b? stays one identifier' => [
        '{{ a-b? }}',
        [['VariableStart', ''], ['Identifier', 'a-b?'], ['VariableEnd', '']],
    ],
    'negative numbers stay atomic' => [
        '{{ -5 }}',
        [['VariableStart', ''], ['Number', '-5'], ['VariableEnd', '']],
    ],
    'ranges do not become floats' => [
        '{{ 1..5 }}',
        [['VariableStart', ''], ['Number', '1'], ['DotDot', '..'], ['Number', '5'], ['VariableEnd', '']],
    ],
    'contains is only a comparison before whitespace' => [
        '{{ contains foo }}',
        [['VariableStart', ''], ['Comparison', 'contains'], ['Identifier', 'foo'], ['VariableEnd', '']],
    ],
    'contains-like identifiers stay identifiers' => [
        '{{ containsfoo contains? }}',
        [['VariableStart', ''], ['Identifier', 'containsfoo'], ['Identifier', 'contains?'], ['VariableEnd', '']],
    ],
    'bare equals stays equals' => [
        '{{ = }}',
        [['VariableStart', ''], ['Equals', '='], ['VariableEnd', '']],
    ],
    'bare less-than stays a comparison' => [
        '{{ < }}',
        [['VariableStart', ''], ['Comparison', '<'], ['VariableEnd', '']],
    ],
    'vertical tab counts as whitespace' => [
        "{{\vfoo\v}}",
        [['VariableStart', ''], ['Identifier', 'foo'], ['VariableEnd', '']],
    ],
]);

test('non-ascii identifiers still fail on the first non-ascii byte', function () {
    try {
        tokenize('{{ café }}');
        $this->fail('Expected tokenize() to throw.');
    } catch (SyntaxException $exception) {
        expect($exception->getMessage())->toBe("Unexpected character \xC3");
    }
});

test('lexer preserves tricky syntax errors', function (string $source, string $message) {
    expect(fn () => tokenize($source))->toThrow(SyntaxException::class, $message);
})->with([
    'bare exclamation points still fail' => ['{% if ! %}', 'Unexpected character !'],
    'unterminated strings still fail on the opening quote' => ['{{ "abc }}', 'Unexpected character "'],
]);

test('VariableEnd reports the line the trailing whitespace starts on, not the line of }}', function () {
    // The lexer skips whitespace before probing for the terminator, so it has to
    // capture the line number up front to keep this numbering. Guard it here: the
    // quirk is invisible in single-line templates and easy to "fix" by accident.
    $tokens = tokenize("{{\n  a.b\n  |\n  upcase\n}}")->toArray();
    $variableEnd = $tokens[count($tokens) - 1];

    expect($variableEnd->type->name)->toBe('VariableEnd')
        ->and($variableEnd->lineNumber)->toBe(4);
});

test('block and variable tokens keep their line numbers across newlines', function () {
    $tokens = tokenize("a\n{%\n  assign\n  x\n  =\n  1\n%}\nb")->toArray();

    expect(array_map(fn (Token $token) => [$token->type->name, $token->lineNumber], $tokens))
        ->toBe([
            ['TextData', 1],
            ['BlockStart', 2],
            ['Identifier', 3],
            ['Identifier', 4],
            ['Equals', 5],
            ['Number', 6],
            ['BlockEnd', 7],
            ['TextData', 7],
        ]);
});

function lexerQuirkSerializeTokens(string $source): array
{
    return array_map(
        fn (Token $token) => [$token->type->name, $token->data],
        tokenize($source)->toArray(),
    );
}
