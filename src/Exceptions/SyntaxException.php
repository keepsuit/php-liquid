<?php

namespace Keepsuit\Liquid\Exceptions;

use Keepsuit\Liquid\Parse\LexerOptions;
use Keepsuit\Liquid\Parse\Token;
use Keepsuit\Liquid\Parse\TokenType;

class SyntaxException extends LiquidException
{
    public ?string $tagName = null;

    public static function missingTagTerminator(): self
    {
        return new SyntaxException(sprintf('Tag was not properly terminated with: %s', LexerOptions::TagBlockEnd->value));
    }

    public static function tagBlockNeverClosed(?string $tagName): SyntaxException
    {
        return new SyntaxException(sprintf("'%s' tag was never closed", $tagName));
    }

    public static function missingVariableTerminator(): SyntaxException
    {
        return new SyntaxException(sprintf('Variable was not properly terminated with: %s', LexerOptions::TagVariableEnd->value));
    }

    public static function unknownTag(string $tagName, ?string $blockTagName = null): SyntaxException
    {
        $exception = match (true) {
            $blockTagName !== null && str_starts_with($tagName, 'end') => new SyntaxException(sprintf("'%s' is not a valid delimiter for %s tag. use end%s", $tagName, $blockTagName, $blockTagName)),
            default => new SyntaxException(sprintf("Unknown tag '%s'", $tagName)),
        };

        $exception->tagName = $tagName;

        return $exception;
    }

    public static function unexpectedTokenType(TokenType $expectedToken, TokenType $givenToken): SyntaxException
    {
        return new SyntaxException(sprintf(
            'Expected %s, got %s',
            $expectedToken->toString(),
            $givenToken->toString()
        ));
    }

    public static function unexpectedIdentifier(string $expected, string $given): SyntaxException
    {
        return new SyntaxException(sprintf(
            'Expected identifier %s, got %s',
            $expected,
            $given
        ));
    }

    public static function invalidExpression(string $expression): SyntaxException
    {
        return new SyntaxException(sprintf('%s is not a valid expression', $expression));
    }

    public static function unexpectedCharacter(string $character): SyntaxException
    {
        return new SyntaxException(sprintf('Unexpected character %s', $character));
    }

    public static function unexpectedEndOfTemplate(): SyntaxException
    {
        return new SyntaxException('Unexpected end of template');
    }

    public static function unexpectedToken(Token $token): SyntaxException
    {
        return new SyntaxException(sprintf(
            'Unexpected token %s: "%s"',
            $token->type->toString(),
            $token->data,
        ));
    }

    protected function messagePrefix(): string
    {
        return 'Liquid syntax error';
    }
}
