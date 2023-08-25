<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\Nodes\BlockBodySection;
use Keepsuit\Liquid\Parse\BlockParser;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;

abstract class TagBlock extends Tag
{
    const MAX_DEPTH = 100;

    /**
     * @var array<BlockBodySection>
     */
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
            throw new StackLevelException();
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
        return $context->withDisabledTags($this->disabledTags(), function (Context $context) {
            $output = '';

            foreach ($this->bodySections as $bodySection) {
                $output .= $bodySection->render($context);
            }

            return $output;
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
