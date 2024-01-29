<?php

namespace Keepsuit\Liquid\Exceptions;

use Keepsuit\Liquid\Parse\LexerOptions;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Token;
use Keepsuit\Liquid\Parse\TokenType;

class SyntaxException extends LiquidException
{
    public ?string $tagName = null;

    public static function missingTagTerminator(string $token, ParseContext $parseContext): self
    {
        return new SyntaxException($parseContext->locale->translate('errors.syntax.tag_termination', [
            'token' => $token,
            'regexp' => LexerOptions::TagBlockEnd->value,
        ]));
    }

    public static function tagNeverClosed(?string $tagName, ParseContext $parseContext): SyntaxException
    {
        return new SyntaxException($parseContext->locale->translate('errors.syntax.tag_never_closed', [
            'block_name' => $tagName,
        ]));
    }

    public static function missingVariableTerminator(string $token, ParseContext $parseContext): SyntaxException
    {
        return new SyntaxException($parseContext->locale->translate('errors.syntax.variable_termination', [
            'token' => $token,
            'regexp' => LexerOptions::TagVariableEnd->value,
        ]));
    }

    public static function unexpectedOuterTag(ParseContext $parseContext, string $tagName): SyntaxException
    {
        return new SyntaxException($parseContext->locale->translate('errors.syntax.unexpected_outer_tag', [
            'tag' => $tagName,
        ]));
    }

    public static function unknownTag(ParseContext $parseContext, string $tagName, string $blockTagName, ?string $blockDelimiter = null): SyntaxException
    {
        $exception = match (true) {
            $tagName === 'else' || $tagName === 'end' => $blockTagName === ''
                ? static::unexpectedOuterTag($parseContext, $tagName)
                : new SyntaxException($parseContext->locale->translate('errors.syntax.unexpected_else', ['block_name' => $blockTagName])),
            str_starts_with($tagName, 'end') && $blockTagName !== '' => new SyntaxException($parseContext->locale->translate('errors.syntax.invalid_delimiter', [
                'tag' => $tagName,
                'block_name' => $blockTagName,
                'block_delimiter' => $blockDelimiter ?? 'end'.$blockTagName,
            ])),
            default => new SyntaxException($parseContext->locale->translate('errors.syntax.unknown_tag', [
                'tag' => $tagName,
            ])),
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

    public static function unclosedVariable(): SyntaxException
    {
        return new SyntaxException('Unclosed variable');
    }

    public static function unclosedBlock(): SyntaxException
    {
        return new SyntaxException('Unclosed block');
    }

    protected function messagePrefix(): string
    {
        return 'Liquid syntax error';
    }
}
