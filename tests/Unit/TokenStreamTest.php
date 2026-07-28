<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\Token;
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

test('next', function () {
    $tokenStream = tokenize('{{ wat }}');

    expect($tokenStream->next()->type)->toBe(TokenType::VariableStart);

    $tokenStream->consume();
    $tokenStream->consume();

    expect(fn () => $tokenStream->next())
        ->toThrow(SyntaxException::class);
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
        ->toThrow(SyntaxException::class, '`==` is not a valid expression');
});

test('sliceUntil preserves the delimiter for token types and closures', function () {
    $tokenStream = tokenize('{% assign foo = bar %}');
    $tokenStream->consume(TokenType::BlockStart);
    $tokenStream->consume(TokenType::Identifier);

    $slice = $tokenStream->sliceUntil(TokenType::BlockEnd);

    expect(array_map(fn (Token $token) => $token->data, $slice->toArray()))
        ->toBe(['foo', '=', 'bar']);
    expect($tokenStream->current()?->type)->toBe(TokenType::BlockEnd);

    $tokenStream = tokenize('{{ value }}');
    $tokenStream->consume(TokenType::VariableStart);

    $slice = $tokenStream->sliceUntil(fn (Token $token) => $token->type === TokenType::VariableEnd);

    expect(array_map(fn (Token $token) => $token->data, $slice->toArray()))
        ->toBe(['value']);
    expect($tokenStream->current()?->type)->toBe(TokenType::VariableEnd);
});

test('sliceUntil keeps navigation within the slice bounds', function () {
    $tokenStream = tokenize('{{ first second }}');
    $tokenStream->consume(TokenType::VariableStart);

    $slice = $tokenStream->sliceUntil(TokenType::VariableEnd);

    expect($slice->toArray())->toHaveCount(2)
        ->and($slice->current()?->data)->toBe('first');

    $slice->jump(2);

    expect($slice->current())->toBeNull()
        ->and($slice->isEnd())->toBeTrue();
    expect(fn () => $slice->consume())->toThrow(SyntaxException::class);
    $slice->jump(-1);
    expect($slice->isEnd())->toBeTrue();
    expect(fn () => $slice->jump(1))->toThrow(SyntaxException::class);
});

test('nested slices preserve current and relative bounds', function () {
    $tokenStream = tokenize('{{ first second }}');
    $tokenStream->consume(TokenType::VariableStart);

    $slice = $tokenStream->sliceUntil(TokenType::VariableEnd);
    $slice->consume(TokenType::Identifier);
    $nested = $slice->sliceUntil(TokenType::VariableEnd);

    expect(array_map(fn (Token $token) => $token->data, $nested->toArray()))
        ->toBe(['second'])
        ->and($nested->current()?->data)->toBe('second');

    $nested->consume(TokenType::Identifier);

    expect($nested->isEnd())->toBeTrue()
        ->and($tokenStream->current()?->type)->toBe(TokenType::VariableEnd);
    expect(fn () => $nested->consume())->toThrow(SyntaxException::class);
});
