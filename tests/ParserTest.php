<?php

use Keepsuit\Liquid\Parser\Parser;
use Keepsuit\Liquid\Parser\TokenType;

test('consume', function () {
    $parser = new Parser("wat: 7");

    expect($parser)
        ->consume(TokenType::Identifier)->toBe('wat')
        ->consume(TokenType::Colon)->toBe(':')
        ->consume(TokenType::Number)->toBe('7');
});

test('jump', function () {
    $parser = new Parser("wat: 7");

    $parser->jump(2);

    expect($parser)
        ->consume(TokenType::Number)->toBe('7');
});

test('consumeOrFalse', function () {
    $parser = new Parser("wat: 7");

    expect($parser)
        ->consumeOrFalse(TokenType::Identifier)->toBe('wat')
        ->consumeOrFalse(TokenType::Dot)->toBeFalse()
        ->consumeOrFalse(TokenType::Colon)->toBe(':')
        ->consumeOrFalse(TokenType::Number)->toBe('7');
});

test('idOrFalse', function () {
    $parser = new Parser("wat 6 Peter Hegemon");

    expect($parser)
        ->idOrFalse('wat')->toBe('wat')
        ->idOrFalse('endgame')->toBeFalse()
        ->consume(TokenType::Number)->toBe('6')
        ->idOrFalse('Peter')->toBe('Peter')
        ->idOrFalse('Achilles')->toBeFalse();
});

test('look', function () {
    $parser = new Parser("wat 6 Peter Hegemon");

    expect($parser)
        ->look(TokenType::Identifier)->toBeTrue()
        ->consume(TokenType::Identifier)->toBe('wat')
        ->look(TokenType::Comparison)->toBeFalse()
        ->look(TokenType::Number)->toBeTrue()
        ->look(TokenType::Identifier, 1)->toBeTrue()
        ->look(TokenType::Number, 1)->toBeFalse();
});

test('expressions', function () {
    $parser = new Parser("hi.there hi?[5].there? hi.there.bob");

    expect($parser)
        ->expression()->toBe('hi.there')
        ->expression()->toBe('hi?[5].there?')
        ->expression()->toBe('hi.there.bob');

    $parser = new Parser("567 6.0 'lol' \"wut\"");

    expect($parser)
        ->expression()->toBe('567')
        ->expression()->toBe('6.0')
        ->expression()->toBe("'lol'")
        ->expression()->toBe('"wut"');
});

test('ranges', function () {
    $parser = new Parser("(5..7) (1.5..9.6) (young..old) (hi[5].wat..old)");

    expect($parser)
        ->expression()->toBe('(5..7)')
        ->expression()->toBe('(1.5..9.6)')
        ->expression()->toBe('(young..old)')
        ->expression()->toBe('(hi[5].wat..old)');
});

test('arguments', function () {
    $parser = new Parser("filter: hi.there[5], keyarg: 7");

    expect($parser)
        ->consume(TokenType::Identifier)->toBe('filter')
        ->consume(TokenType::Colon)->toBe(':')
        ->argument()->toBe('hi.there[5]')
        ->consume(TokenType::Comma)->toBe(',')
        ->argument()->toBe('keyarg: 7');
});

test('invalid expression', function () {
    $parser = new Parser("==");

    expect(fn() => $parser->expression())
        ->toThrow(Exception::class, "== is not a valid expression");
});
