<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\TokenType;

test('consume', function () {
    $tokenStream = tokenize('{{ wat: 7 }}');

    expect($tokenStream)
        ->consume(TokenType::VariableStart)->data->toBe('')
        ->consume(TokenType::Identifier)->data->toBe('wat')
        ->consume(TokenType::Colon)->data->toBe(':')
        ->consume(TokenType::Number)->data->toBe('7')
        ->consume(TokenType::VariableEnd)->data->toBe('')
        ->isEnd()->toBeTrue();
});

test('jump', function () {
    $tokenStream = tokenize('{{ wat: 7 }}');

    $tokenStream->jump(3);

    expect($tokenStream)
        ->consume(TokenType::Number)->data->toBe('7');
});

test('consumeOrFalse', function () {
    $tokenStream = tokenize('{{ wat: 7 }}');

    $tokenStream->consume(TokenType::VariableStart);

    expect($tokenStream)
        ->consumeOrFalse(TokenType::Identifier)->data->toBe('wat')
        ->consumeOrFalse(TokenType::Dot)->toBeFalse()
        ->consumeOrFalse(TokenType::Colon)->data->toBe(':')
        ->consumeOrFalse(TokenType::Number)->data->toBe('7');
});

test('idOrFalse', function () {
    $tokenStream = tokenize('{{ wat 6 Peter Hegemon }}');

    $tokenStream->consume(TokenType::VariableStart);

    expect($tokenStream)
        ->idOrFalse('wat')->data->toBe('wat')
        ->idOrFalse('endgame')->toBeFalse()
        ->consume(TokenType::Number)->data->toBe('6')
        ->idOrFalse('Peter')->data->toBe('Peter')
        ->idOrFalse('Achilles')->toBeFalse();
});

test('look', function () {
    $tokenStream = tokenize('{{ wat 6 Peter Hegemon }}');

    $tokenStream->consume(TokenType::VariableStart);

    expect($tokenStream)
        ->look(TokenType::Identifier)->toBeTrue()
        ->consume(TokenType::Identifier)->data->toBe('wat')
        ->look(TokenType::Comparison)->toBeFalse()
        ->look(TokenType::Number)->toBeTrue()
        ->look(TokenType::Identifier, 1)->toBeTrue()
        ->look(TokenType::Number, 1)->toBeFalse();
});

test('expressions', function () {
    $tokenStream = tokenize('{{ hi.there hi?[5].there? hi.there.bob }}');

    $tokenStream->consume(TokenType::VariableStart);

    expect($tokenStream)
        ->expression()->toString()->toBe('hi.there')
        ->expression()->toString()->toBe('hi?.5.there?')
        ->expression()->toString()->toBe('hi.there.bob');

    $tokenStream = tokenize('{{ 567 6.0 \'lol\' "wut" }}');

    $tokenStream->consume(TokenType::VariableStart);

    expect($tokenStream)
        ->expression()->toBe(567)
        ->expression()->toBe(6.0)
        ->expression()->toBe('lol')
        ->expression()->toBe('wut');
});

test('ranges', function () {
    $tokenStream = tokenize('{{ (5..7) (1.5..9.6) (young..old) (hi[5].wat..old) }}');

    $tokenStream->consume(TokenType::VariableStart);

    expect($tokenStream)
        ->expression()->toString()->toBe('(5..7)')
        ->expression()->toString()->toBe('(1.5..9.6)')
        ->expression()->toString()->toBe('(young..old)')
        ->expression()->toString()->toBe('(hi.5.wat..old)');
});

test('arguments', function () {
    $tokenStream = tokenize('{{ filter: hi.there[5], keyarg: 7 }}');

    $tokenStream->consume(TokenType::VariableStart);

    expect($tokenStream)
        ->consume(TokenType::Identifier)->data->toBe('filter')
        ->consume(TokenType::Colon)->data->toBe(':')
        ->argument()->toString()->toBe('hi.there.5')
        ->consume(TokenType::Comma)->data->toBe(',')
        ->argument()->toBe(['keyarg' => 7]);
});

test('invalid expression', function () {
    $tokenStream = tokenize('{{ == }}');

    $tokenStream->consume(TokenType::VariableStart);

    expect(fn () => $tokenStream->expression())
        ->toThrow(SyntaxException::class, '== is not a valid expression');
});
