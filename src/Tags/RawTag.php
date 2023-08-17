<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\TagBlock;
use Keepsuit\Liquid\Tokenizer;

class RawTag extends TagBlock
{
    const FullTokenPossiblyInvalid = '/\A(.*)'.Regex::TagStart.Regex::WhitespaceControl.'?\s*(\w+)\s*(.*)?'.Regex::WhitespaceControl.'?'.Regex::TagEnd.'\z/m';

    protected string $body;

    public static function tagName(): string
    {
        return 'raw';
    }

    public function parse(Tokenizer $tokenizer): static
    {
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
}
