<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\TagBlock;

class CommentTag extends TagBlock
{
    public static function tagName(): string
    {
        return 'comment';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        foreach ($tokenizer->shift() as $token) {
            if (preg_match(Regex::FullTagToken, $token, $matches) !== 1) {
                continue;
            }

            $tagName = $matches[2];
            if ($tagName === 'end'.static::tagName()) {
                break;
            }
        }

        return $this;
    }

    public function render(Context $context): string
    {
        return '';
    }

    public function blank(): bool
    {
        return true;
    }

    public function nodeList(): array
    {
        return [];
    }
}
