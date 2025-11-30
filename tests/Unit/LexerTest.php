<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\TokenType;

test('[variable] strings', function () {
    $tokens = tokenize('{{ \'this is a test""\' "wat \'lol\'" }}');

    $tokens->consume(TokenType::VariableStart);

    expect($tokens->consume(TokenType::String))
        ->data->toBe('\'this is a test""\'')
        ->lineNumber->toBe(1);

    expect($tokens->consume(TokenType::String))
        ->data->toBe('"wat \'lol\'"')
        ->lineNumber->toBe(1);

    $tokens->consume(TokenType::VariableEnd);

    expect($tokens->isEnd())->toBeTrue();
});

test('[variable] integer', function () {
    $tokens = tokenize('{{ 50 -10 }}');

    $tokens->consume(TokenType::VariableStart);

    expect($tokens->consume(TokenType::Number))
        ->data->toBe('50')
        ->lineNumber->toBe(1);

    expect($tokens->consume(TokenType::Number))
        ->data->toBe('-10')
        ->lineNumber->toBe(1);

    $tokens->consume(TokenType::VariableEnd);

    expect($tokens->isEnd())->toBeTrue();
});

test('[variable] float', function () {
    $tokens = tokenize('{{ 5.0 -2.7 }}');

    $tokens->consume(TokenType::VariableStart);

    expect($tokens->consume(TokenType::Number))
        ->data->toBe('5.0')
        ->lineNumber->toBe(1);

    expect($tokens->consume(TokenType::Number))
        ->data->toBe('-2.7')
        ->lineNumber->toBe(1);

    $tokens->consume(TokenType::VariableEnd);

    expect($tokens->isEnd())->toBeTrue();
});

test('[variable] specials', function () {
    $tokens = tokenize('{{ | .: [,] }}');

    $tokens->consume(TokenType::VariableStart);

    expect($tokens->consume(TokenType::Pipe))->data->toBe('|');
    expect($tokens->consume(TokenType::Dot))->data->toBe('.');
    expect($tokens->consume(TokenType::Colon))->data->toBe(':');
    expect($tokens->consume(TokenType::OpenSquare))->data->toBe('[');
    expect($tokens->consume(TokenType::Comma))->data->toBe(',');
    expect($tokens->consume(TokenType::CloseSquare))->data->toBe(']');

    $tokens->consume(TokenType::VariableEnd);

    expect($tokens->isEnd())->toBeTrue();
});

test('[variable] fancy identifiers', function () {
    $tokens = tokenize('{{ hi five? 2foo }}');

    $tokens->consume(TokenType::VariableStart);

    expect($tokens->consume(TokenType::Identifier))
        ->data->toBe('hi')
        ->lineNumber->toBe(1);

    expect($tokens->consume(TokenType::Identifier))
        ->data->toBe('five?')
        ->lineNumber->toBe(1);

    expect($tokens->consume(TokenType::Number))
        ->data->toBe('2')
        ->lineNumber->toBe(1);

    expect($tokens->consume(TokenType::Identifier))
        ->data->toBe('foo')
        ->lineNumber->toBe(1);

    $tokens->consume(TokenType::VariableEnd);

    expect($tokens->isEnd())->toBeTrue();
});

test('[variable] unexpected character', function () {
    expect(fn () => tokenize('{{ % }}'))
        ->toThrow(SyntaxException::class, 'Unexpected character %');
});

it('[blocks]', function () {
    $tokens = tokenize('{% if hi %} {% endif %}');

    $tokens->consume(TokenType::BlockStart);

    expect($tokens->consume())
        ->type->toBe(TokenType::Identifier)
        ->data->toBe('if');

    expect($tokens->consume())
        ->type->toBe(TokenType::Identifier)
        ->data->toBe('hi');

    $tokens->consume(TokenType::BlockEnd);

    expect($tokens->consume())
        ->type->toBe(TokenType::TextData)
        ->data->toBe(' ');

    $tokens->consume(TokenType::BlockStart);

    expect($tokens->consume())
        ->type->toBe(TokenType::Identifier)
        ->data->toBe('endif');

    $tokens->consume(TokenType::BlockEnd);
});

test('[comment] whitespace trim', function () {
    expect(tokenize('{%- comment -%}123{%- endcomment -%}     Hello!')->consume())
        ->type->toBe(TokenType::TextData)
        ->data->toBe('Hello!');

    expect(tokenize("{%- comment -%}123{%- endcomment -%}\nHello!")->consume())
        ->type->toBe(TokenType::TextData)
        ->data->toBe('Hello!');
});

test('[comment] without whitespace trim', function () {
    expect(tokenize('{% comment %}123{% endcomment %}     Hello!')->consume())
        ->type->toBe(TokenType::TextData)
        ->data->toBe('     Hello!');

    expect(tokenize("{% comment %}123{% endcomment %}\nHello!")->consume())
        ->type->toBe(TokenType::TextData)
        ->data->toBe("\nHello!");
});

test('text', function () {
    expect(tokenize(' ')->consume())
        ->type->toBe(TokenType::TextData)
        ->data->toBe(' ');

    expect(tokenize('hello world')->consume())
        ->type->toBe(TokenType::TextData)
        ->data->toBe('hello world');
});

test('unclosed expression', function () {
    expect(fn () => tokenize('{{ hi'))
        ->toThrow(SyntaxException::class, 'Variable was not properly terminated with: }}');

    expect(fn () => tokenize('{% if'))
        ->toThrow(SyntaxException::class, 'Tag was not properly terminated with: %}');
});

test('full source', function () {
    $tokens = tokenize(<<<'LIQUID'
        This is a test
        {{- hi | filter: 5.0 }}
        {%- if hi == 5 %}
            {{ hi }}
        {%- endif %}
        end
        LIQUID
    );

    expect($tokens->consume(TokenType::TextData))
        ->data->toBe('This is a test')
        ->lineNumber->toBe(1);

    $tokens->consume(TokenType::VariableStart);
    expect($tokens->consume(TokenType::Identifier))
        ->data->toBe('hi')
        ->lineNumber->toBe(2);
    $tokens->consume(TokenType::Pipe);
    expect($tokens->consume(TokenType::Identifier))
        ->data->toBe('filter');
    $tokens->consume(TokenType::Colon);
    expect($tokens->consume(TokenType::Number))
        ->data->toBe('5.0');
    $tokens->consume(TokenType::VariableEnd);

    $tokens->consume(TokenType::BlockStart);
    expect($tokens->consume(TokenType::Identifier))
        ->data->toBe('if')
        ->lineNumber->toBe(3);
    expect($tokens->consume(TokenType::Identifier))
        ->data->toBe('hi');
    expect($tokens->consume(TokenType::Comparison))
        ->data->toBe('==')
        ->lineNumber->toBe(3);
    expect($tokens->consume(TokenType::Number))
        ->data->toBe('5');
    $tokens->consume(TokenType::BlockEnd);

    expect($tokens->consume(TokenType::TextData))
        ->data->toBe("\n    ");

    $tokens->consume(TokenType::VariableStart);
    expect($tokens->consume(TokenType::Identifier))
        ->data->toBe('hi')
        ->lineNumber->toBe(4);
    $tokens->consume(TokenType::VariableEnd);

    $tokens->consume(TokenType::BlockStart);
    expect($tokens->consume(TokenType::Identifier))
        ->data->toBe('endif')
        ->lineNumber->toBe(5);
    $tokens->consume(TokenType::BlockEnd);

    expect($tokens->consume(TokenType::TextData))
        ->data->toBe("\nend")
        ->lineNumber->toBe(5);

    expect($tokens->isEnd())->toBeTrue();
});
