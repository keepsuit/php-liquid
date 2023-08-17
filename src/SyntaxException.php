<?php

namespace Keepsuit\Liquid;

class SyntaxException extends \Exception
{
    public ?int $markupLine = null;

    public ?string $tagName = null;

    public static function missingTagTerminator(string $token, ParseContext $parseContext): self
    {
        return new SyntaxException($parseContext->locale->translate('errors.syntax.tag_termination', [
            'token' => $token,
            'tag_end' => Regex::TagEnd,
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
            'variable_end' => Regex::VariableEnd,
        ]));
    }

    public static function unexpectedOuterTag(ParseContext $parseContext, string $tagName): SyntaxException
    {
        return new SyntaxException($parseContext->locale->translate('errors.syntax.unexpected_outer_tag', [
            'tag' => $tagName,
        ]));
    }

    public static function unknownTag(ParseContext $parseContext, string $tagName, string $blockTagName, string $blockDelimiter = null): SyntaxException
    {
        $exception = match (true) {
            $tagName === 'else' => new SyntaxException($parseContext->locale->translate('errors.syntax.unexpected_else', [
                'block_name' => $blockTagName,
            ])),
            str_starts_with($tagName, 'end') => new SyntaxException($parseContext->locale->translate('errors.syntax.invalid_delimiter', [
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

    public function setLineNumber(?int $lineNumber): static
    {
        $this->markupLine = $lineNumber;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            'Liquid syntax error%s: %s',
            $this->markupLine ? sprintf(' (line %s)', $this->markupLine) : '',
            $this->getMessage()
        );
    }
}
