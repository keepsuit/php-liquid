<?php

namespace Keepsuit\Liquid;

class SyntaxException extends \Exception
{
    public static function missingTagTerminator(string $token, ParseContext $parseContext): self
    {
        return new SyntaxException($parseContext->locale->translate("errors.syntax.tag_termination", [
            'token' => $token,
            'tagEnd' => Regex::TagEnd,
        ]));
    }

    public static function missingVariableTerminator(string $token, ParseContext $parseContext): SyntaxException
    {
        return new SyntaxException($parseContext->locale->translate("errors.syntax.variable_termination", [
            'token' => $token,
            'variableEnd' => Regex::VariableEnd,
        ]));
    }

    public static function unknownTag(string $tagName, string $markup, ParseContext $parseContext): SyntaxException
    {
        dd('unknownTag', $tagName, $markup);

        if ($tagName === 'else') {
            return new SyntaxException($parseContext->locale->translate("errors.syntax.unexpected_else", [
                'block_name' => $markup,
            ]));
        }

        if (str_starts_with($tagName, 'end')) {
            return new SyntaxException($parseContext->locale->translate("errors.syntax.invalid_delimiter", [
                'tag' => $tagName,
                'block_name' => $markup,
            ]));
        }
    }
}
