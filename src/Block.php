<?php

namespace Keepsuit\Liquid;

abstract class Block extends Tag
{
    protected const MAX_DEPTH = 100;

    protected BlockBody $body;

    public function __construct(string $tagName, string $markup, ParseContext $parseContext)
    {
        parent::__construct($tagName, $markup, $parseContext);

        $this->body = $parseContext->newBlockBody();
    }

    public function parse(Tokenizer $tokenizer): static
    {
        $this->body = self::parseBody($tokenizer);

        return $this;
    }

    public static function blockDelimiter(): string
    {
        return 'end'.static::name();
    }

    protected function parseBody(Tokenizer $tokenizer): BlockBody
    {
        if ($this->parseContext->depth >= self::MAX_DEPTH) {
            throw new \RuntimeException('Nesting too deep');
        }

        $this->parseContext->depth += 1;

        $blockBody = BlockBody::parse($tokenizer, $this->parseContext, function (string $tagName, string $markup) {
            if ($tagName === static::blockDelimiter()) {
                return false;
            }

            return static::unknownTagHandler($tagName, $markup);
        });

        $this->parseContext->depth -= 1;

        return $blockBody;
    }

    public function blank(): bool
    {
        return $this->body->blank;
    }

    /**
     * @throws SyntaxException
     */
    protected function unknownTagHandler(string $tagName, string $markup): bool
    {
        throw SyntaxException::unknownTag(
            parseContext: $this->parseContext,
            tagName: $tagName,
            blockName: $markup,
            blockDelimiter: static::blockDelimiter()
        );
    }
}
