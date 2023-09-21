<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Nodes\BlockBodySection;
use Keepsuit\Liquid\Parse\BlockParser;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\GeneratorToString;

abstract class TagBlock extends Tag
{
    use GeneratorToString;

    /**
     * @var array<BlockBodySection>
     */
    protected array $bodySections = [];

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        $this->bodySections = self::parseBody($parseContext, $tokenizer);

        return $this;
    }

    /**
     * @return array<BlockBodySection>
     */
    protected function parseBody(ParseContext $parseContext, Tokenizer $tokenizer): array
    {
        return $parseContext->nested(function () use ($parseContext, $tokenizer) {
            return BlockParser::forTag(static::tagName(), $this->markup)
                ->subTagsHandler(fn (string $tagName) => $this->isSubTag($tagName))
                ->parse($tokenizer, $parseContext);
        });
    }

    public function blank(): bool
    {
        foreach ($this->bodySections as $bodySection) {
            if (! $bodySection->blank()) {
                return false;
            }
        }

        return true;
    }

    public function nodeList(): array
    {
        return $this->bodySections;
    }

    public function render(Context $context): string
    {
        return $this->generatorToString($this->renderBody($context));
    }

    /**
     * @return \Generator<string>
     */
    protected function renderBody(Context $context): \Generator
    {
        return $context->withDisabledTagsAsync($this->disabledTags(), function (Context $context) {
            foreach ($this->bodySections as $bodySection) {
                yield from $bodySection->renderAsync($context);
            }
        });
    }

    protected function isSubTag(string $tagName): bool
    {
        return false;
    }

    protected static function blockDelimiter(): ?string
    {
        return 'end'.static::tagName();
    }
}
