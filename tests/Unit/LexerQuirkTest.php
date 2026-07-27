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

function lexerQuirkSerializeTokens(string $source): array
{
    return array_map(
        fn (Token $token) => [$token->type->name, $token->data],
        tokenize($source)->toArray(),
    );
}
