<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\TagBlock;

class RawTag extends TagBlock
{
    const MarkupSyntax = '/\A\s*\z/';

    const FullTokenPossiblyInvalid = '/\A(.*)'.Regex::TagStart.Regex::WhitespaceControl.'?\s*(\w+)\s*(.*)?'.Regex::WhitespaceControl.'?'.Regex::TagEnd.'\z/m';

    protected string $body;

    public static function tagName(): string
    {
        return 'raw';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        if (preg_match(self::MarkupSyntax, $this->markup, $matches) !== 1) {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.tag_unexpected_args', ['tag' => static::tagName()]));
        }

        $this->body = '';

        while ($token = $tokenizer->shift()) {
            if (preg_match(self::FullTokenPossiblyInvalid, $token, $matches) === 1 && static::blockDelimiter() === $matches[2]) {
                $this->parseContext->trimWhitespace = $token[-3] === Regex::WhitespaceControl;

                if ($matches[1] !== '') {
                    $this->body .= $matches[1];
                }

                return $this;
            }
            $this->body .= $token;
        }

        throw SyntaxException::tagNeverClosed(static::tagName(), $this->parseContext);
    }

    public function render(Context $context): string
    {
        return $this->body;
    }

    public function blank(): bool
    {
        return strlen($this->body) === 0;
    }

    protected function isSubTag(string $tagName): bool
    {
        return true;
    }
}
