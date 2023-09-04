<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\Lexer;
use Keepsuit\Liquid\Parse\TokenType;

test('strings', function () {
    $tokens = (new Lexer(' \'this is a test""\' "wat \'lol\'"'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::String, '\'this is a test""\'', 1],
            [TokenType::String, '"wat \'lol\'"', 20],
            [TokenType::EndOfString, '', 31],
        ]);
});

test('integer', function () {
    $tokens = (new Lexer('hi 50'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::Identifier, 'hi', 0],
            [TokenType::Number, '50', 3],
            [TokenType::EndOfString, '', 5],
        ]);
});

test('float', function () {
    $tokens = (new Lexer('hi 5.0'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::Identifier, 'hi', 0],
            [TokenType::Number, '5.0', 3],
            [TokenType::EndOfString, '', 6],
        ]);
});

test('specials', function () {
    $tokens = (new Lexer('| .:'))->tokenize();

    expect($tokens)
        ->toHaveCount(4)
        ->toBe([
            [TokenType::Pipe, '|', 0],
            [TokenType::Dot, '.', 2],
            [TokenType::Colon, ':', 3],
            [TokenType::EndOfString, '', 4],
        ]);

    $tokens = (new Lexer('[,]'))->tokenize();

    expect($tokens)
        ->toHaveCount(4)
        ->toBe([
            [TokenType::OpenSquare, '[', 0],
            [TokenType::Comma, ',', 1],
            [TokenType::CloseSquare, ']', 2],
            [TokenType::EndOfString, '', 3],
        ]);
});

test('fancy identifiers', function () {
    $tokens = (new Lexer('hi five?'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::Identifier, 'hi', 0],
            [TokenType::Identifier, 'five?', 3],
            [TokenType::EndOfString, '', 8],
        ]);

    $tokens = (new Lexer('2foo'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [TokenType::Number, '2', 0],
            [TokenType::Identifier, 'foo', 1],
            [TokenType::EndOfString, '', 4],
        ]);
});

test('unexpected character', function () {
    expect(fn () => (new Lexer('%'))->tokenize())
        ->toThrow(SyntaxException::class, 'Unexpected character %');
});
