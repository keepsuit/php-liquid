<?php

namespace Keepsuit\Liquid;

abstract class TagBlock extends Tag
{
    protected const MAX_DEPTH = 100;

    protected array $bodySections = [];

    public function parse(Tokenizer $tokenizer): static
    {
        $this->bodySections = self::parseBody($tokenizer);

        return $this;
    }

    /**
     * @return array<BlockBodySection>
     */
    protected function parseBody(Tokenizer $tokenizer): array
    {
        if ($this->parseContext->depth >= self::MAX_DEPTH) {
            throw new \RuntimeException('Nesting too deep');
        }

        $this->parseContext->depth += 1;

        $sections = BlockParser::forTag(static::tagName(), $this->markup)
            ->subTagsHandler(fn (string $tagName) => $this->isSubTag($tagName))
            ->parse($tokenizer, $this->parseContext);

        $this->parseContext->depth -= 1;

        return $sections;
    }

    public function blank(): bool
    {
        return $this->bodySections === [];
    }

    public function nodeList(): array
    {
        return BlockBody::fromSections($this->bodySections)->nodeList();
    }

    protected function isSubTag(string $tagName): bool
    {
        return false;
    }
}
