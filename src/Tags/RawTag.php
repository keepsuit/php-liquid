<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\TagBlock;

class RawTag extends TagBlock
{
    const FullTokenPossiblyInvalid = '/\A(.*)'.Regex::TagStart.Regex::WhitespaceControl.'?\s*(\w+)\s*(.*)?'.Regex::WhitespaceControl.'?'.Regex::TagEnd.'\z/m';

    protected string $body;

    public static function tagName(): string
    {
        return 'raw';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        if (trim($this->markup) !== '') {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.tag_unexpected_args', ['tag' => static::tagName()]));
        }

        $this->body = '';

        foreach ($tokenizer->shift() as $token) {
            if (preg_match(self::FullTokenPossiblyInvalid, $token, $matches) === 1 && static::blockDelimiter() === $matches[2]) {
                $parseContext->trimWhitespace = $token[-3] === Regex::WhitespaceControl;

                if ($matches[1] !== '') {
                    $this->body .= $matches[1];
                }

                return $this;
            }
            $this->body .= $token;
        }

        throw SyntaxException::tagNeverClosed(static::tagName(), $parseContext);
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
