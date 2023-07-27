<?php

namespace Keepsuit\Liquid;

abstract class Block extends Tag
{
    protected const MAX_DEPTH = 100;

    protected BlockBody $body;

    public static function parse(string $tagName, string $markup, Tokenizer $tokenizer, ParseContext $parseContext): static
    {
        $block = new static($tagName, $markup, $parseContext);

        $block->body = self::parseBody($tokenizer, $parseContext);

        return $block;
    }

    public static function blockDelimiter(): string
    {
        return 'end'.static::name();
    }

    private static function parseBody(Tokenizer $tokenizer, ParseContext $parseContext): BlockBody
    {
        if ($parseContext->depth >= self::MAX_DEPTH) {
            throw new \RuntimeException('Nesting too deep');
        }

        $parseContext->depth += 1;

        $blockBody = BlockBody::parse($tokenizer, $parseContext, function (string $tagName, string $markup) {
            if ($tagName === static::blockDelimiter()) {
                return false;
            }

            dd($tagName, $markup, static::name());
        });

        $parseContext->depth -= 1;

        return $blockBody;
    }

    public function blank(): bool
    {
        return $this->body->blank;
    }
}
