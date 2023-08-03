<?php

namespace Keepsuit\Liquid;

class SyntaxException extends \Exception
{
    public ?string $markupContext = null;

    public ?string $tagName = null;

    public static function missingTagTerminator(string $token, ParseContext $parseContext): self
    {
        return new SyntaxException($parseContext->locale->translate('errors.syntax.tag_termination', [
            'token' => $token,
            'tagEnd' => Regex::TagEnd,
        ]));
    }

    public static function missingVariableTerminator(string $token, ParseContext $parseContext): SyntaxException
    {
        return new SyntaxException($parseContext->locale->translate('errors.syntax.variable_termination', [
            'token' => $token,
            'variableEnd' => Regex::VariableEnd,
        ]));
    }

    public static function unexpectedOuterTag(ParseContext $parseContext, string $tagName): SyntaxException
    {
        return new SyntaxException($parseContext->locale->translate('errors.syntax.unexpected_outer_tag', [
            'tag' => $tagName,
        ]));
    }

    public static function unknownTag(ParseContext $parseContext, string $tagName, string $blockTagName): SyntaxException
    {
        $exception = match (true) {
            $tagName === 'else' && $blockTagName !== '' => new SyntaxException($parseContext->locale->translate('errors.syntax.unexpected_else', [
                'block_name' => $blockTagName,
            ])),
            str_starts_with($tagName, 'end') && $blockTagName !== '' => new SyntaxException($parseContext->locale->translate('errors.syntax.invalid_delimiter', [
                'tag' => $tagName,
                'block_name' => $blockTagName,
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
            'Unexpected token type: expected %s, got %s',
            $expectedToken->toString(),
            $givenToken->toString()
        ));
    }

    public static function invalidExpression(string $expression): SyntaxException
    {
        return new SyntaxException(sprintf('%s is not a valid expression', $expression));
    }

    public static function unexpectedCharacter(string $character): SyntaxException
    {
        return new SyntaxException(sprintf('Unexpected character: %s', $character));
    }

    public function setLineNumber(?int $lineNumber): void
    {
        $this->line = $lineNumber ?? $this->line;
    }

    public function setMarkupContext(string $markupContext): void
    {
        $this->markupContext = $markupContext;
    }
}
