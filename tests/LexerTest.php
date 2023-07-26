<?php

use Keepsuit\Liquid\Lexer\Lexer;
use Keepsuit\Liquid\Lexer\Token;

test('strings', function () {
    $tokens = (new Lexer(' \'this is a test""\' "wat \'lol\'"'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [Token::String, '\'this is a test""\''],
            [Token::String, '"wat \'lol\'"'],
            [Token::EndOfString],
        ]);
});

test('integer', function () {
    $tokens = (new Lexer('hi 50'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [Token::Identifier, 'hi'],
            [Token::Number, '50'],
            [Token::EndOfString],
        ]);
});

test('float', function () {
    $tokens = (new Lexer('hi 5.0'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [Token::Identifier, 'hi'],
            [Token::Number, '5.0'],
            [Token::EndOfString],
        ]);
});

test('specials', function () {
    $tokens = (new Lexer('| .:'))->tokenize();

    expect($tokens)
        ->toHaveCount(4)
        ->toBe([
            [Token::Pipe, '|'],
            [Token::Dot, '.'],
            [Token::Colon, ':'],
            [Token::EndOfString],
        ]);

    $tokens = (new Lexer('[,]'))->tokenize();

    expect($tokens)
        ->toHaveCount(4)
        ->toBe([
            [Token::OpenSquare, '['],
            [Token::Comma, ','],
            [Token::CloseSquare, ']'],
            [Token::EndOfString],
        ]);
});

test('fancy identifiers', function () {
    $tokens = (new Lexer('hi five?'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [Token::Identifier, 'hi'],
            [Token::Identifier, 'five?'],
            [Token::EndOfString],
        ]);

    $tokens = (new Lexer('2foo'))->tokenize();

    expect($tokens)
        ->toHaveCount(3)
        ->toBe([
            [Token::Number, '2'],
            [Token::Identifier, 'foo'],
            [Token::EndOfString],
        ]);
});

test('unexpected character', function () {
    expect(fn() => (new Lexer('%'))->tokenize())
        ->toThrow(Exception::class, 'Unexpected character %');
});
