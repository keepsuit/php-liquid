<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Lexer;
use Keepsuit\Liquid\Parser\TokenType;

test('strings', function () {
    $tokens = (new Lexer(' \'this is a test""\' "wat \'lol\'"'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::String, '\'this is a test""\''],
            [TokenType::String, '"wat \'lol\'"'],
            [TokenType::EndOfString],
        ]);
});

test('integer', function () {
    $tokens = (new Lexer('hi 50'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::Identifier, 'hi'],
            [TokenType::Number, '50'],
            [TokenType::EndOfString],
        ]);
});

test('float', function () {
    $tokens = (new Lexer('hi 5.0'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::Identifier, 'hi'],
            [TokenType::Number, '5.0'],
            [TokenType::EndOfString],
        ]);
});

test('specials', function () {
    $tokens = (new Lexer('| .:'))->tokenize();

    expect($tokens)
        ->toHaveCount(4)
        ->toBe([
            [TokenType::Pipe, '|'],
            [TokenType::Dot, '.'],
            [TokenType::Colon, ':'],
            [TokenType::EndOfString],
        ]);

    $tokens = (new Lexer('[,]'))->tokenize();

    expect($tokens)
        ->toHaveCount(4)
        ->toBe([
            [TokenType::OpenSquare, '['],
            [TokenType::Comma, ','],
            [TokenType::CloseSquare, ']'],
            [TokenType::EndOfString],
        ]);
});

test('fancy identifiers', function () {
    $tokens = (new Lexer('hi five?'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::Identifier, 'hi'],
            [TokenType::Identifier, 'five?'],
            [TokenType::EndOfString],
        ]);

    $tokens = (new Lexer('2foo'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::Number, '2'],
            [TokenType::Identifier, 'foo'],
            [TokenType::EndOfString],
        ]);
});

test('unexpected character', function () {
    expect(fn () => (new Lexer('%'))->tokenize())
        ->toThrow(SyntaxException::class, 'Unexpected character %');
});
